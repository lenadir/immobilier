# Introduction

API REST complète pour la gestion d'agences immobilières. Authentification via Laravel Sanctum (Bearer token). Rôles disponibles : admin, agent, guest.

<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>

Bienvenue sur la documentation de l'**API Immobilière**.

Cette API permet de gérer des biens immobiliers, des agents et des images via une architecture Controller → Service → Repository. Elle s'accompagne également d'une interface UI administrative basée sur des templates Blade stockés dans `resources/views` (dashboard, formulaires, etc.).

## Prérequis serveurs

L'API est écrite en **Laravel 11** et nécessite un environnement PHP 8.2, Composer 2.x et MySQL 8.0. Avant de lancer les commandes artisan, assurez‑vous que le serveur dispose :

- des extensions PHP : `pdo` (pilote MySQL), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `bcmath`, `gd` (traitement d'images) – plus toutes les autres extensions requises par Laravel 11.
- d'un serveur HTTP (Apache/Nginx) ou utilisez `php artisan serve` pour le développement.


## Authentification

Utilisez `POST /api/auth/login` pour obtenir un token Sanctum, puis ajoutez-le à chaque requête protégée :
```
Authorization: Bearer <votre_token>
```

## Rôles

| Rôle | Droits |
|------|--------|
| `admin` | Accès total, gestion de la corbeille |
| `agent` | CRUD sur ses propres biens |
| `guest` | Lecture seule |

