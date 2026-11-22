# Poke-api

API REST dédiée à l'univers Pokémon, construite avec **Symfony 8.1** et **API Platform 5**.

> Projet en cours de développement (branche `feat/premiere-entite`).

## Sommaire

- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Base de données](#base-de-données)
- [Lancer le projet](#lancer-le-projet)
- [Utilisation de l'API](#utilisation-de-lapi)
- [Structure du projet](#structure-du-projet)
- [Documentation de conception](#documentation-de-conception)
- [Workflow Git](#workflow-git)

## Stack technique

| Outil | Rôle |
|---|---|
| PHP >= 8.4 | Langage |
| Symfony 8.1 | Framework |
| API Platform 5 (Symfony + Doctrine ORM) | Exposition de l'API REST |
| Doctrine ORM 3 + Migrations | Persistance et versionnement du schéma |
| NelmioCorsBundle | Gestion du CORS |
| Symfony Security / Validator / Serializer | Sécurité, validation et sérialisation |
| Twig | Templates |
| Symfony MakerBundle (dev) | Génération de code |

## Prérequis

- PHP **8.4** ou supérieur, avec les extensions `ctype` et `iconv`
- [Composer](https://getcomposer.org/)
- Un serveur de base de données compatible Doctrine (MySQL, PostgreSQL, SQLite…)
- (Optionnel) [Symfony CLI](https://symfony.com/download) pour le serveur local

## Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/Raphael-Vaxelaire/Poke-api-.git
cd Poke-api-

# 2. Installer les dépendances
composer install
```

## Configuration

Les variables d'environnement se trouvent dans `.env` (valeurs par défaut) et `.env.dev` (environnement de développement).

Pour une configuration locale sans toucher aux fichiers versionnés, créez un fichier `.env.local` :

```dotenv
APP_ENV=dev
APP_SECRET=changez-moi
DATABASE_URL="mysql://user:password@127.0.0.1:3306/poke_api?serverVersion=8.0&charset=utf8mb4"
```

> Adaptez `DATABASE_URL` au SGBD que vous utilisez.

## Base de données

```bash
# Créer la base
php bin/console doctrine:database:create

# Appliquer les migrations
php bin/console doctrine:migrations:migrate
```

Pour générer une nouvelle migration après avoir modifié une entité :

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## Lancer le projet

Avec la Symfony CLI :

```bash
symfony serve
```

Ou avec le serveur PHP intégré :

```bash
php -S localhost:8000 -t public
```

L'application est ensuite disponible sur `http://localhost:8000` (ou le port indiqué par la Symfony CLI).

## Utilisation de l'API

API Platform génère automatiquement la documentation interactive et les endpoints à partir des entités exposées.

| URL | Description |
|---|---|
| `/api` | Documentation interactive (Swagger UI / OpenAPI) |
| `/api/docs.json` | Spécification OpenAPI au format JSON |

Exemple de requête :

```bash
curl -H "Accept: application/ld+json" http://localhost:8000/api/pokemon
```

> Les routes disponibles dépendent des entités exposées. Consultez `/api` pour la liste à jour.

## Structure du projet

```
.
├── bin/            # Exécutables (console Symfony)
├── config/         # Configuration (packages, routes, services)
├── migrations/     # Migrations Doctrine
├── public/         # Point d'entrée web (index.php)
├── src/            # Code source (entités, repositories, etc.)
├── templates/      # Templates Twig
├── .env            # Variables d'environnement par défaut
├── .env.dev        # Variables d'environnement de développement
├── composer.json   # Dépendances PHP
└── mapping-architecture-donnees.xlsx   # Mapping de l'architecture des données
```

## Documentation de conception

Le fichier [`mapping-architecture-donnees.xlsx`](./mapping-architecture-donnees.xlsx) décrit le mapping de l'architecture des données (entités, champs et relations) du projet.

## Workflow Git

- Les nouvelles fonctionnalités sont développées sur des branches `feat/...` (ex. `feat/premiere-entite`).
- Une fois la fonctionnalité terminée, ouvrez une Pull Request vers la branche principale.

## Auteur

Raphaël Vaxelaire Jesse Lazare

## Licence

Projet propriétaire (cf. `composer.json`).
