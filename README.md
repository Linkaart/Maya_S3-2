# Maya_S3-2

Application web développée avec le framework **Symfony 6.3**, dans le cadre d'un projet scolaire . Elle intègre une gestion des entités via Doctrine ORM, un système d'authentification, des formulaires, des templates Twig, et un environnement Docker prêt à l'emploi.

---

## 🛠️ Stack technique

- **PHP** >= 8.2
- **Symfony** 6.3
- **Doctrine ORM** + Doctrine Migrations
- **Twig** (moteur de templates)
- **Symfony Security** (authentification)
- **Symfony Mailer** (envoi d'e-mails)
- **Symfony Form** (gestion des formulaires)
- **Docker / Docker Compose**
- **PHPUnit** (tests unitaires)

---

## 📁 Structure du projet
Maya_S3-2/
├── bin/ # Exécutables Symfony (console)
├── config/ # Configuration de l'application
├── docs/ # Documentation
├── migrations/ # Migrations de base de données
├── public/ # Point d'entrée public (index.php)
├── src/
│ ├── Controller/ # Contrôleurs
│ ├── DataFixtures/ # Fixtures (données de test)
│ ├── Entity/ # Entités Doctrine
│ ├── Form/ # Types de formulaires
│ ├── Repository/ # Repositories Doctrine
│ ├── Security/ # Authentification & sécurité
│ └── Kernel.php
├── templates/ # Templates Twig
├── tests/ # Tests PHPUnit
├── translations/ # Fichiers de traduction
├── .env # Variables d'environnement
├── docker-compose.yml # Configuration Docker
└── composer.json


---

## ⚙️ Installation

### Prérequis

- PHP >= 8.2
- Composer
- Docker & Docker Compose (recommandé)

### 1. Cloner le dépôt

```bash
git clone https://github.com/Linkaart/Maya_S3-2.git
cd Maya_S3-2
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l'environnement

Copier le fichier `.env` et adapter les variables :

```bash
cp .env .env.local
```

Modifier notamment la variable `DATABASE_URL` selon votre base de données.

### 4. Lancer avec Docker

```bash
docker-compose up -d
```

### 5. Créer la base de données et exécuter les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. (Optionnel) Charger les fixtures

```bash
php bin/console doctrine:fixtures:load
```

---

##  Tests

```bash
php bin/phpunit
```

Ou en utilisant l'environnement de test :

```bash
APP_ENV=test php bin/phpunit
```

---

## 🐳 Docker

Le projet inclut un `docker-compose.yml` et un `docker-compose.override.yml` pour l'environnement de développement. Les services sont préconfigurés pour démarrer rapidement.

```bash
docker-compose up -d
```

---

## 📄 Licence

Projet propriétaire — tous droits réservés.
