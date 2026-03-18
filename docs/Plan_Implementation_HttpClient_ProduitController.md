# Plan d'implémentation : Intégration du composant HttpClient Symfony dans le contrôleur ProduitController
@author T.Mikail
## Contexte
Le contrôleur ProduitController utilise actuellement Doctrine ORM pour gérer les opérations CRUD et la recherche de produits. Le but est de refactorer ce contrôleur pour consommer l'API REST `maya-api` via le composant HttpClient de Symfony.

## Objectif
Remplacer les accès directs à la base de données par des appels HTTP vers l'API REST, en utilisant HttpClient pour effectuer les opérations CRUD et la recherche sur les produits.

---

## Étapes d'implémentation

### 1. Injection du service HttpClient
- Injecter `HttpClientInterface` dans le constructeur du contrôleur.
- Stocker le client HTTP dans une propriété privée.

### 2. Remplacement des accès aux données par des appels API
- **Lecture (GET) :** Récupérer la liste des produits ou un produit spécifique via des requêtes GET.
- **Recherche :** Envoyer les critères de recherche à l'API (via query params ou POST selon l'API).
- **Création (POST) :** Envoyer les données du formulaire via POST.
- **Modification (PUT/PATCH) :** Envoyer les données modifiées via PUT ou PATCH.
- **Suppression (DELETE) :** Envoyer une requête DELETE.

### 3. Gestion des réponses
- Utiliser `$response->toArray()` pour récupérer les données JSON.
- Gérer les erreurs avec try/catch.
- Afficher des messages flash selon le résultat.

### 4. Adaptation des formulaires et vues
- Les formulaires restent similaires, mais les données sont envoyées à l'API.
- Adapter les redirections et affichages selon les réponses.

---

## Exemple d'intégration dans `ProduitController`

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProduitController extends AbstractController
{
    private HttpClientInterface $client;
    private string $apiBaseUrl = 'http://localhost:8000/api/produits'; // URL de l'API maya-api

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
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            try {
                $response = $this->client->request('POST', $this->apiBaseUrl, [
                    'json' => $data,
                ]);
                if ($response->getStatusCode() === 201) {
                    $this->addFlash('success', 'Produit ajouté avec succès.');
                    return $this->redirectToRoute('app_produit');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'ajout du produit : ' . $e->getMessage());
            }
        }

        return $this->render('produit/ajouter.html.twig');
    }

    #[Route('/produit/modifier/{id}', name: 'app_produit_modifier')]
    public function modifier(Request $request, int $id): Response
    {
        try {
            $response = $this->client->request('GET', $this->apiBaseUrl . '/' . $id);
            $produit = $response->toArray();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Produit non trouvé : ' . $e->getMessage());
            return $this->redirectToRoute('app_produit');
        }

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            try {
                $response = $this->client->request('PUT', $this->apiBaseUrl . '/' . $id, [
                    'json' => $data,
                ]);
                if ($response->getStatusCode() === 200) {
                    $this->addFlash('success', 'Produit modifié avec succès.');
                    return $this->redirectToRoute('app_produit');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la modification : ' . $e->getMessage());
            }
        }

        return $this->render('produit/modifier.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/produit/supprimer/{id}', name: 'app_produit_supprimer')]
    public function supprimer(int $id): Response
    {
        try {
            $response = $this->client->request('DELETE', $this->apiBaseUrl . '/' . $id);
            if ($response->getStatusCode() === 204) {
                $this->addFlash('success', 'Produit supprimé avec succès.');
            } else {
                $this->addFlash('error', 'Erreur lors de la suppression.');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_produit');
    }
}
```

---

## Conclusion
Cette intégration permet de consommer l'API REST maya-api pour la gestion des produits via HttpClient, assurant une architecture découplée et une meilleure maintenabilité.

