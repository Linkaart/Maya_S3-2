<?php
/*
*
*@author T.Mikail
*   
*/
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CategorieController extends AbstractController
{
    private HttpClientInterface $client;
    private string $apiBaseUrl = 'http://localhost:8000/api/categories'; // URL de l'API maya-api

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/categorie', name: 'app_categorie')]
    #[Route('/categorie/demandermodification/{id<\d+>}', name: 'app_categorie_demandermodification')]
    public function index(Request $request, $id = null): Response
    {
        $formCreation = null;
        $formModificationView = null;
        $lesCategories = [];

        try {
            $response = $this->client->request('GET', $this->apiBaseUrl);
            $lesCategories = $response->toArray();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des catégories : ' . $e->getMessage());
        }

        // créer l'objet et le formulaire de création
        $formCreation = $this->createForm('App\Form\CategorieType');

        // si 2e route alors $id est renseigné et on  crée le formulaire de modification
        if ($id != null) {
            // sécurité supplémentaire, on vérifie le token
            if ($this->isCsrfTokenValid('action-item' . $id, $request->get('_token'))) {
                try {
                    $response = $this->client->request('GET', $this->apiBaseUrl . '/' . $id);
                    $categorieModif = $response->toArray();
                    $formModificationView = $this->createForm('App\Form\CategorieType', $categorieModif)->createView();
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de la récupération de la catégorie : ' . $e->getMessage());
                }
            }
        }

        return $this->render('categorie/index.html.twig', [
            'formCreation' => $formCreation->createView(),
            'lesCategories' => $lesCategories,
            'formModification' => $formModificationView,
            'idCategorieModif' => $id,
        ]);
    }

    #[Route('/categorie/ajouter', name: 'app_categorie_ajouter')]
    public function ajouter(Request $request): Response
    {
        $form = $this->createForm('App\Form\CategorieType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $response = $this->client->request('POST', $this->apiBaseUrl, [
                    'json' => $data,
                ]);
                if ($response->getStatusCode() === 201) {
                    $this->addFlash('success', 'La catégorie a été ajoutée avec succès.');
                    return $this->redirectToRoute('app_categorie');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'ajout de la catégorie : ' . $e->getMessage());
            }
        }

        // lire les catégories pour affichage même en cas d'erreur
        $lesCategories = [];
        try {
            $response = $this->client->request('GET', $this->apiBaseUrl);
            $lesCategories = $response->toArray();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des catégories : ' . $e->getMessage());
        }

        return $this->render('categorie/index.html.twig', [
            'formCreation' => $form->createView(),
            'lesCategories' => $lesCategories,
            'formModification' => null,
            'idCategorieModif' => null,
        ]);
    }

    #[Route('/categorie/modifier/{id<\d+>}', name: 'app_categorie_modifier')]
    public function modifier(Request $request, int $id): Response
    {
        try {
            $response = $this->client->request('GET', $this->apiBaseUrl . '/' . $id);
            $categorie = $response->toArray();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Catégorie non trouvée : ' . $e->getMessage());
            return $this->redirectToRoute('app_categorie');
        }

        $form = $this->createForm('App\Form\CategorieType', $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $response = $this->client->request('PUT', $this->apiBaseUrl . '/' . $id, [
                    'json' => $data,
                ]);
                if ($response->getStatusCode() === 200) {
                    $this->addFlash('success', 'La catégorie a été modifiée avec succès.');
                    return $this->redirectToRoute('app_categorie');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la modification : ' . $e->getMessage());
            }
        }

        return $this->render('categorie/modifier.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie,
        ]);
    }

    #[Route('/categorie/supprimer/{id<\d+>}', name: 'app_categorie_supprimer')]
    public function supprimer(Request $request, int $id): Response
    {
        if ($this->isCsrfTokenValid('action-item' . $id, $request->get('_token'))) {
            try {
                $response = $this->client->request('DELETE', $this->apiBaseUrl . '/' . $id);
                if ($response->getStatusCode() === 204) {
                    $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
                } else {
                    $this->addFlash('error', 'Erreur lors de la suppression.');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
            }
        }
        return $this->redirectToRoute('app_categorie');
    }
}
