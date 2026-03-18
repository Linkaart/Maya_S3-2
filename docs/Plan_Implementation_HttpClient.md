# Plan d'implémentation : Intégration du composant HttpClient Symfony dans les contrôleurs back office
@author T.Mikail
## Contexte
Le projet Maya utilise actuellement Doctrine ORM dans les contrôleurs back office (ex : CategorieController, ProduitController) pour gérer les opérations CRUD directement sur la base de données. Le sprint 1 impose de refactorer ces contrôleurs pour consommer l'API REST `maya-api` via le composant HttpClient de Symfony.

## Objectif
Remplacer les accès directs à la base de données par des appels HTTP vers l'API REST, en utilisant HttpClient pour effectuer les opérations CRUD (Create, Read, Update, Delete) sur les entités Catégorie et Produit.

---

## Étapes d'implémentation

### 1. Injection du service HttpClient
- Ajouter l'injection de `HttpClientInterface` dans les constructeurs des contrôleurs concernés.
- Stocker le client HTTP dans une propriété privée pour l'utiliser dans les méthodes.

### 2. Remplacement des accès aux données par des appels API
- **Lecture (GET) :** Utiliser `$client->request('GET', 'url_api')` pour récupérer les listes ou un élément.
- **Création (POST) :** Envoyer les données du formulaire via une requête POST à l'API.
- **Modification (PUT/PATCH) :** Envoyer les données modifiées via PUT ou PATCH.
- **Suppression (DELETE) :** Envoyer une requête DELETE à l'API.

### 3. Gestion des réponses
- Récupérer les données JSON avec `$response->toArray()`.
- Gérer les erreurs HTTP avec des blocs try/catch.
- Afficher des messages flash selon le succès ou l’échec des opérations.

### 4. Adaptation des formulaires et vues
- Les formulaires restent similaires, mais les données sont envoyées à l'API.
- Adapter les redirections et affichages selon les réponses de l'API.

---

## Exemple d'intégration dans `CategorieController`

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    private HttpClientInterface $client;
    private string $apiBaseUrl = 'http://localhost:8000/api/categories'; // URL de l'API maya-api

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/categorie', name: 'app_categorie')]
    public function index(): Response
    {
        try {
            $response = $this->client->request('GET', $this->apiBaseUrl);
            $categories = $response->toArray();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des catégories : ' . $e->getMessage());
            $categories = [];
        }

        return $this->render('categorie/index.html.twig', [
            'lesCategories' => $categories,
        ]);
    }

    #[Route('/categorie/ajouter', name: 'app_categorie_ajouter')]
    public function ajouter(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            try {
                $response = $this->client->request('POST', $this->apiBaseUrl, [
                    'json' => $data,
                ]);
                if ($response->getStatusCode() === 201) {
                    $this->addFlash('success', 'Catégorie ajoutée avec succès.');
                    return $this->redirectToRoute('app_categorie');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'ajout de la catégorie : ' . $e->getMessage());
            }
        }

        return $this->render('categorie/ajouter.html.twig');
    }

    #[Route('/categorie/modifier/{id}', name: 'app_categorie_modifier')]
    public function modifier(Request $request, int $id): Response
    {
        try {
            $response = $this->client->request('GET', $this->apiBaseUrl . '/' . $id);
            $categorie = $response->toArray();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Catégorie non trouvée : ' . $e->getMessage());
            return $this->redirectToRoute('app_categorie');
        }

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            try {
                $response = $this->client->request('PUT', $this->apiBaseUrl . '/' . $id, [
                    'json' => $data,
                ]);
                if ($response->getStatusCode() === 200) {
                    $this->addFlash('success', 'Catégorie modifiée avec succès.');
                    return $this->redirectToRoute('app_categorie');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la modification : ' . $e->getMessage());
            }
        }

        return $this->render('categorie/modifier.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/categorie/supprimer/{id}', name: 'app_categorie_supprimer')]
    public function supprimer(int $id): Response
    {
        try {
            $response = $this->client->request('DELETE', $this->apiBaseUrl . '/' . $id);
            if ($response->getStatusCode() === 204) {
                $this->addFlash('success', 'Catégorie supprimée avec succès.');
            } else {
                $this->addFlash('error', 'Erreur lors de la suppression.');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_categorie');
    }
}
```

---

## Conclusion
Cette approche permet de découpler le back office de la base de données en déléguant la gestion des données à l'API REST maya-api. Le composant HttpClient facilite la communication HTTP et la gestion des erreurs.


