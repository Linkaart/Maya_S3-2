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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProduitController extends AbstractController
{
    private HttpClientInterface $client;
    private string $apiBaseUrl = 'http://localhost:8000/api/docs/produits'; // URL de l'API maya-api

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/produit', name: 'app_produit')]
    public function index(Request $request, SessionInterface $session): Response
    {
        $criteres = $session->get('ProduitCriteres', []);

        try {
            $response = $this->client->request('GET', $this->apiBaseUrl, [
                'query' => $criteres,
            ]);
            $produits = $response->toArray();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des produits : ' . $e->getMessage());
            $produits = [];
        }

        return $this->render('produit/index.html.twig', [
            'lesProduits' => $produits,
        ]);
    }

    #[Route('/produit/ajouter', name: 'app_produit_ajouter')]
    public function ajouter(Request $request): Response
    {
        $form = $this->createForm('App\Form\ProduitType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $response = $this->client->request('POST', $this->apiBaseUrl, [
                    'json' => $data,
                ]);
                if ($response->getStatusCode() === 201) {
                    $this->addFlash('success', 'Le produit a été ajouté avec succès.');
                    return $this->redirectToRoute('app_produit');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'ajout du produit : ' . $e->getMessage());
            }
        }

        return $this->render('produit/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/modifier/{id<\d+>}', name: 'app_produit_modifier')]
    public function modifier(Request $request, int $id): Response
    {
        try {
            $response = $this->client->request('GET', $this->apiBaseUrl . '/' . $id);
            $produit = $response->toArray();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Produit non trouvé : ' . $e->getMessage());
            return $this->redirectToRoute('app_produit');
        }

        $form = $this->createForm('App\Form\ProduitType', $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $response = $this->client->request('PUT', $this->apiBaseUrl . '/' . $id, [
                    'json' => $data,
                ]);
                if ($response->getStatusCode() === 200) {
                    $this->addFlash('success', 'Le produit a été modifié avec succès.');
                    return $this->redirectToRoute('app_produit');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la modification : ' . $e->getMessage());
            }
        }

        return $this->render('produit/modifier.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    #[Route('/produit/supprimer/{id<\d+>}', name: 'app_produit_supprimer')]
    public function supprimer(Request $request, int $id): Response
    {
        if ($this->isCsrfTokenValid('action-item' . $id, $request->get('_token'))) {
            try {
                $response = $this->client->request('DELETE', $this->apiBaseUrl . '/' . $id);
                if ($response->getStatusCode() === 204) {
                    $this->addFlash('success', 'Le produit a été supprimé avec succès.');
                } else {
                    $this->addFlash('error', 'Erreur lors de la suppression.');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
            }
        }
        return $this->redirectToRoute('app_produit');
    }
}
