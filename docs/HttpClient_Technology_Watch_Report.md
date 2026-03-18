# Rapport de veille technologique : Composant Symfony HttpClient
@author T.Mikail

## Introduction
Le composant HttpClient de Symfony est un client HTTP puissant et flexible conçu pour faciliter et optimiser les requêtes HTTP dans les applications PHP. Il offre une API simple et cohérente pour envoyer des requêtes HTTP et gérer les réponses, en supportant les opérations synchrones et asynchrones.

## Fonctionnalités et avantages
- **Facilité d'utilisation :** API simple pour envoyer des requêtes GET, POST, PUT, DELETE, et autres.
- **Requêtes asynchrones :** Supporte les requêtes HTTP concurrentes pour améliorer les performances.
- **Support HTTP/2 :** Permet une communication plus rapide et efficace avec les serveurs.
- **Intégration :** S'intègre parfaitement avec les autres composants et services Symfony.
- **Gestion des erreurs :** Fournit des exceptions détaillées et des mécanismes de gestion des erreurs.
- **Extensibilité :** Permet la personnalisation des en-têtes, de l'authentification et d'autres options de requête.

## Cas d'utilisation
- Consommation d'API REST dans les applications Symfony.(maya-api)
- Communication avec des microservices ou des services web externes.
- Récupération de données distantes de manière asynchrone pour améliorer l'expérience utilisateur.
- Implémentation de clients API au sein de projets Symfony.

## Intégration dans le projet Maya
Dans le projet Maya, le composant HttpClient de Symfony sera utilisé dans l'application web back office pour consommer l'API REST (`maya-api`) pour tous les accès aux données. Cette approche découple le front-end de l'accès direct à la base de données, permettant une meilleure scalabilité, maintenabilité et séparation des responsabilités.

## Références
- Documentation officielle Symfony HttpClient : https://symfony.com/doc/current/http_client.html
- Dépôt GitHub Symfony HttpClient : https://github.com/symfony/http-client

---

Ce rapport présente une vue d'ensemble du composant HttpClient et de sa pertinence pour les objectifs du sprint 1 du projet Maya.
