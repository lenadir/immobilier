# Plateforme Digitale Immobilière — API Laravel | Développé par Nadir Tawfik Moussa

API RESTful pour la gestion de biens immobiliers, avec authentification par token (Sanctum), gestion des rôles et architecture en couches Controller → Service → Repository.

---

## Prérequis

| Outil       | Version minimale |
|-------------|-----------------|
| PHP         | 8.2             |
| Composer    | 2.x             |
| MySQL       | 8.0             |
| Laravel     | 11.x            |

> **Extensions PHP requises** : `pdo` (avec pilote MySQL), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `bcmath`, `gd` (pour traitement d'images) et celles listées par [Laravel](https://laravel.com/docs/11.x#server-requirements).

> **Serveur web** : Un serveur HTTP comme Apache ou Nginx est nécessaire en production. Pour le développement local, vous pouvez utiliser le serveur intégré via `php artisan serve`.

---

## Installation

```bash
# 1. Cloner le projet et installer les dépendances PHP
composer install

# 2. Créer le fichier d'environnement
cp .env.example .env

# 3. Générer la clé d'application Laravel
php artisan key:generate

# 4. Configurer la base de données (voir section Variables d'environnement)
#    Modifier DB_DATABASE, DB_USERNAME, DB_PASSWORD dans .env

# 5. Exécuter les migrations (crée toutes les tables)
php artisan migrate

# 6. (Optionnel) Insérer les données de test
php artisan db:seed

# 7. Créer le lien symbolique pour accéder aux images uploadées
php artisan storage:link

# 8. Démarrer le serveur de développement
php artisan serve
# → API disponible sur http://localhost:8000/api
```

---

## Variables d'environnement

Copiez `.env.example` en `.env` et adaptez les valeurs suivantes :

```dotenv
# ── Application ───────────────────────────────────────────────────────────────
APP_NAME="Immobilier Platform"
APP_ENV=local          # local | production
APP_KEY=               # généré par: php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000

# ── Base de données (MySQL) ───────────────────────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=immobilier   # créer la base manuellement avant la migration
DB_USERNAME=root
DB_PASSWORD=             # mot de passe MySQL

# ── Sanctum (domaines autorisés pour les cookies SPA, optionnel pour API pure) ─
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1

# ── Stockage (images uploadées) ───────────────────────────────────────────────
FILESYSTEM_DISK=public   # les images sont accessibles via /storage/...

# ── Mail (facultatif) ─────────────────────────────────────────────────────────
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="hello@immobilier.local"
MAIL_FROM_NAME="${APP_NAME}"

# ── Cache / Sessions ──────────────────────────────────────────────────────────
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

> **Note :** En production, définir `APP_DEBUG=false`, utiliser un `CACHE_DRIVER=redis` et configurer un vrai serveur SMTP.

---

## Architecture du projet

```
app/
├── DTOs/                          # Objets de transfert de données
│   ├── CreatePropertyDTO.php
│   ├── UpdatePropertyDTO.php
│   └── FilterPropertiesDTO.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php         # Classe de base abstraite (hérite de Laravel BaseController)
│   │   └── Api/                   # Reçoit les requêtes, retourne du JSON
│   │       ├── AuthController.php
│   │       ├── PropertyController.php
│   │       ├── ImageController.php
│   │       └── UserController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php     # Restriction par rôle (admin/agent/guest)
│   ├── Requests/                  # Validation & autorisation
│   │   ├── Auth/
│   │   ├── Property/
│   │   └── Image/
│   └── Resources/                 # Formatage des réponses JSON
│       ├── PropertyResource.php
│       ├── PropertyCollection.php
│       ├── ImageResource.php
│       └── UserResource.php
│
├── Models/                        # Modèles Eloquent
│   ├── User.php
│   ├── Property.php               # Titre généré automatiquement
│   └── Image.php
│
├── Policies/
│   ├── PropertyPolicy.php         # Contrôle d'accès aux biens
│   └── UserPolicy.php             # Contrôle d'accès aux utilisateurs
│
├── Repositories/                  # Interactions avec la base de données
│   ├── Contracts/                 # Interfaces (découplage)
│   │   ├── PropertyRepositoryInterface.php
│   │   ├── UserRepositoryInterface.php
│   │   └── ImageRepositoryInterface.php
│   ├── PropertyRepository.php
│   ├── UserRepository.php
│   └── ImageRepository.php
│
├── Services/                      # Logique métier
│   ├── AuthService.php
│   ├── PropertyService.php
│   ├── ImageService.php
│   └── UserService.php
│
└── Providers/
    ├── AppServiceProvider.php              # Policies, Gates, Rate limiting
    └── RepositoryServiceProvider.php       # Bind interfaces → implémentations
```

---

## Principe d'architecture : Controller → Service → Repository

L'application est organisée en **trois couches strictement séparées**, communiquant via des **DTOs** ou des **modèles Eloquent** selon le besoin.

### 1. Controller
- **Rôle :** Point d'entrée HTTP. Reçoit la requête, délègue la validation au Form Request, construit le DTO et appelle le Service.
- **Ne contient pas** de logique métier ni de requêtes SQL.
- Retourne toujours une réponse JSON (via les API Resources).

> **Note — `app/Http/Controllers/Controller.php`**
> Laravel fournit normalement cette classe de base via `php artisan` ou le template de projet officiel. Comme ce projet a été
> scaffoldé manuellement (sans PHP installé en local), cette classe n'a pas été générée automatiquement.
> Elle est **requise** par tous les contrôleurs API (`AuthController`, `PropertyController`, etc.) qui en héritent via
> `extends Controller`. Elle encapsule deux traits Laravel essentiels :
> - `AuthorizesRequests` — active `$this->authorize()` pour les vérifications de Policy
> - `ValidatesRequests` — active `$this->validate()` pour la validation inline
>
> Sans elle, Laravel lève une `Class "App\Http\Controllers\Controller" not found` à l'exécution.

```php
// Exemple : PropertyController::store()
public function store(CreatePropertyRequest $request): JsonResponse
{
    $this->authorize('create', Property::class);                          // Policy
    $dto = CreatePropertyDTO::fromArray($request->user()->id, $request->validated()); // DTO
    $property = $this->propertyService->create($dto);                    // Service
    return response()->json(new PropertyResource($property), 201);       // Resource
}
```

### 2. Service
- **Rôle :** Contient toute la logique métier (permissions, calculs, orchestration).
- Reçoit un DTO ou un modèle Eloquent depuis le Controller.
- Appelle le Repository pour accéder aux données.
- Lève des exceptions métier (`AuthorizationException`, `ModelNotFoundException`).

```php
// Exemple : PropertyService::update()
public function update(Property $property, UpdatePropertyDTO $dto, User $authUser): Property
{
    $this->authorizeModification($property, $authUser); // logique métier
    return $this->propertyRepository->update($property, $dto);
}
```

### 3. Repository
- **Rôle :** Unique couche d'accès à la base de données. Toutes les requêtes Eloquent sont ici.
- Reçoit un DTO ou un modèle Eloquent et interagit avec la DB.
- Expose des méthodes claires : `create()`, `update()`, `findById()`, `paginate()`, etc.

```php
// Exemple : PropertyRepository::paginate()
public function paginate(FilterPropertiesDTO $filters): LengthAwarePaginator
{
    return Property::with(['images', 'user'])
        ->published()
        ->byCity($filters->city)
        ->byPriceRange($filters->minPrice, $filters->maxPrice)
        ->search($filters->search)
        ->orderBy($filters->sortBy, $filters->sortDir)
        ->paginate($filters->perPage);
}
```

### Utilisation flexible des DTOs et Modèles entre couches

Les couches échangent des **DTOs** lorsqu'il s'agit de transporter des données de la requête (création, mise à jour, filtres), et des **modèles Eloquent** lorsqu'une entité existante est déjà chargée (modification, suppression). Ce choix est fait selon ce qui est le plus pertinent au contexte :

| Flux | Objet transmis |
|------|----------------|
| Controller → Service (création) | `CreatePropertyDTO` |
| Controller → Service (modification) | `Property` (modèle) + `UpdatePropertyDTO` |
| Controller → Service (liste) | `FilterPropertiesDTO` |
| Service → Repository (création) | `CreatePropertyDTO` |
| Service → Repository (modification) | `Property` (modèle) + `UpdatePropertyDTO` |

---

## Endpoints API

### Authentification

| Méthode | URL                  | Description           | Sanctum              |
|---------|----------------------|-----------------------|:--------------------:|
| POST    | `/api/auth/register` | Inscription           | Émet un token        |
| POST    | `/api/auth/login`    | Connexion             | Émet un token        |
| POST    | `/api/auth/logout`   | Déconnexion           | Bearer token requis  |
| GET     | `/api/auth/me`       | Profil connecté       | Bearer token requis  |

### Biens immobiliers

| Méthode   | URL                      | Description               | Rôle         |
|-----------|--------------------------|---------------------------|--------------|
| GET       | `/api/properties`        | Liste paginée + filtres   | tous         |
| GET       | `/api/properties/{id}`   | Détail + images           | tous         |
| POST      | `/api/properties`        | Créer un bien             | admin, agent |
| PUT/PATCH | `/api/properties/{id}`   | Modifier un bien          | admin, agent |
| DELETE    | `/api/properties/{id}`   | Supprimer un bien         | admin, agent |

#### Filtres disponibles — GET `/api/properties`

| Paramètre   | Type    | Description                        |
|-------------|---------|------------------------------------|
| `city`      | string  | Filtrer par ville                  |
| `type`      | string  | appartement, villa, terrain…       |
| `status`    | string  | disponible, vendu, location        |
| `min_price` | number  | Prix minimum                       |
| `max_price` | number  | Prix maximum                       |
| `search`    | string  | Recherche full-text (titre & desc) |
| `per_page`  | integer | Résultats par page (défaut : 15)   |
| `sort_by`   | string  | price, created_at, surface, rooms  |
| `sort_dir`  | string  | asc / desc                         |

### Images

| Méthode | URL                           | Description              | Rôle         |
|---------|-------------------------------|--------------------------|--------------|
| POST    | `/api/properties/{id}/images` | Upload d'images          | admin, agent |
| DELETE  | `/api/images/{id}`            | Supprimer une image      | admin, agent |
| PATCH   | `/api/images/{id}/cover`      | Définir comme couverture | admin, agent |

### Utilisateurs (admin uniquement)

| Méthode | URL               | Description              |
|---------|-------------------|--------------------------|
| GET     | `/api/users`      | Liste des agents         |
| GET     | `/api/users/{id}` | Détail d'un utilisateur  |
| PUT     | `/api/users/{id}` | Mettre à jour un profil  |
| DELETE  | `/api/users/{id}` | Supprimer un utilisateur |

---

## Exemples de requêtes

> Tous les exemples utilisent `curl`. Remplacer `http://localhost:8000` par votre URL de base.
> Les routes protégées nécessitent le header : `Authorization: Bearer <token>`

---

### 1. Inscription

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Karim Benamara",
    "email": "karim@example.com",
    "password": "Secret@123",
    "password_confirmation": "Secret@123",
    "role": "agent",
    "phone": "+213 555 123456"
  }'
```

**Réponse `201` :**
```json
{
  "message": "Inscription réussie.",
  "user": {
    "id": 2,
    "name": "Karim Benamara",
    "email": "karim@example.com",
    "role": "agent",
    "phone": "+213 555 123456",
    "is_active": true,
    "created_at": "2026-03-05T10:00:00+00:00"
  },
  "token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ..."
}
```

---

### 2. Connexion (login)

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@immobilier.dz",
    "password": "Admin@12345"
  }'
```

**Réponse `200` :**
```json
{
  "message": "Connexion réussie.",
  "user": {
    "id": 1,
    "name": "Administrateur",
    "email": "admin@immobilier.dz",
    "role": "admin",
    "is_active": true,
    "created_at": "2026-03-05T08:00:00+00:00"
  },
  "token": "2|xYzAbCdEfGhIjKlMnOpQrStUvWx..."
}
```

---

### 3. Créer un bien immobilier

```bash
curl -X POST http://localhost:8000/api/properties \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|aBcDeFgHiJkLmNoPqRsTuVwXyZ..." \
  -d '{
    "type": "villa",
    "rooms": 4,
    "surface": 250.5,
    "price": 38000000,
    "city": "Alger",
    "address": "Bab Ezzouar, Alger",
    "description": "Belle villa avec jardin, quartier calme et sécurisé.",
    "status": "disponible",
    "is_published": true
  }'
```

**Réponse `201` — le titre est généré automatiquement :**
```json
{
  "id": 5,
  "title": "Villa 4 pièces à Alger",
  "type": "villa",
  "rooms": 4,
  "surface": 250.5,
  "price": 38000000,
  "city": "Alger",
  "address": "Bab Ezzouar, Alger",
  "description": "Belle villa avec jardin, quartier calme et sécurisé.",
  "status": "disponible",
  "is_published": true,
  "created_at": "2026-03-05T10:15:00+00:00",
  "updated_at": "2026-03-05T10:15:00+00:00",
  "agent": { "id": 2, "name": "Karim Benamara", "role": "agent" },
  "images": []
}
```

---

### 4. Liste filtrée des biens (pagination + filtres)

```bash
curl -X GET "http://localhost:8000/api/properties?city=Alger&type=villa&min_price=10000000&max_price=50000000&status=disponible&sort_by=price&sort_dir=asc&per_page=5" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|aBcDeFgHiJkLmNoPqRsTuVwXyZ..."
```

**Réponse `200` :**
```json
{
  "data": [
    {
      "id": 3,
      "title": "Villa 3 pièces à Alger",
      "type": "villa",
      "rooms": 3,
      "surface": 180.0,
      "price": 22000000,
      "city": "Alger",
      "status": "disponible",
      "is_published": true,
      "images": [ { "id": 7, "url": "http://localhost:8000/storage/properties/3/photo.jpg", "is_cover": true } ]
    },
    {
      "id": 5,
      "title": "Villa 4 pièces à Alger",
      "type": "villa",
      "rooms": 4,
      "surface": 250.5,
      "price": 38000000,
      "city": "Alger",
      "status": "disponible",
      "is_published": true,
      "images": []
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 5,
    "total": 2,
    "from": 1,
    "to": 2
  },
  "links": {
    "first": "http://localhost:8000/api/properties?page=1",
    "last":  "http://localhost:8000/api/properties?page=1",
    "prev":  null,
    "next":  null
  }
}
```

---

### 5. Recherche full-text

```bash
curl -X GET "http://localhost:8000/api/properties?search=piscine+jardin" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>"
```

---

### 6. Upload d'images

```bash
curl -X POST http://localhost:8000/api/properties/5/images \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|aBcDeFgHiJkLmNoPqRsTuVwXyZ..." \
  -F "images[]=@/chemin/vers/photo1.jpg" \
  -F "images[]=@/chemin/vers/photo2.jpg"
```

**Réponse `201` :**
```json
{
  "message": "2 image(s) uploadée(s) avec succès.",
  "images": [
    { "id": 8, "url": "http://localhost:8000/storage/properties/5/photo1.jpg", "is_cover": true,  "sort_order": 1 },
    { "id": 9, "url": "http://localhost:8000/storage/properties/5/photo2.jpg", "is_cover": false, "sort_order": 2 }
  ]
}
```

---

### 7. Exemples de réponses d'erreur

**401 — Non authentifié :**
```json
{ "message": "Non authentifié." }
```

**403 — Accès refusé :**
```json
{ "message": "Vous n'êtes pas autorisé à modifier ce bien." }
```

**422 — Validation échouée :**
```json
{
  "message": "Les données fournies sont invalides.",
  "errors": {
    "type":  ["Le type de bien est obligatoire."],
    "price": ["Le prix doit être un nombre."]
  }
}
```

**404 — Ressource introuvable :**
```json
{ "message": "Ressource introuvable." }
```

---

## Rôles et permissions

| Action                             | admin | agent   | guest |
|------------------------------------|:-----:|:-------:|:-----:|
| Voir les biens publiés             | Oui   | Oui     | Oui   |
| Voir ses propres biens non publiés | Oui   | Oui     | Non   |
| Voir tous les biens non publiés    | Oui   | Non     | Non   |
| Créer un bien                      | Oui   | Oui     | Non   |
| Modifier un bien                   | Oui   | Oui (*) | Non   |
| Supprimer un bien                  | Oui   | Oui (*) | Non   |
| Uploader des images                | Oui   | Oui (*) | Non   |
| Gérer les utilisateurs             | Oui   | Non     | Non   |
| Lister tous les agents             | Oui   | Non     | Non   |

> (*) L'agent peut uniquement agir sur **ses propres biens** (`property.user_id === auth.id`).

### Comment le contrôle d'accès est-il appliqué ?

Le contrôle d'accès repose sur **deux mécanismes complémentaires** :

1. **`RoleMiddleware`** — appliqué au niveau des routes. Bloque les rôles non autorisés avant même d'atteindre le controller.
   ```php
   Route::middleware('role:admin,agent')->group(function () {
       Route::post('/properties', [PropertyController::class, 'store']);
   });
   ```

2. **`PropertyPolicy` / `UserPolicy`** — contrôle fin dans le controller via `$this->authorize()`. Vérifie que l'agent est bien le propriétaire du bien.
   ```php
   $this->authorize('update', $property); // déclenche PropertyPolicy::update()
   ```

---

## Génération automatique du titre

Le titre est généré dans le modèle `Property` via les **événements Eloquent** (`creating`, `updating`). Il se recalcule automatiquement si les champs `type`, `rooms`, `city` ou `status` changent.

**Exemples générés :**

| Données                                      | Titre produit                              |
|----------------------------------------------|--------------------------------------------|
| type=villa, rooms=5, city=Alger              | `Villa 5 pièces à Alger`                   |
| type=appartement, rooms=3, city=Oran, status=location | `Appartement 3 pièces à Oran - En location` |
| type=terrain, surface=500, city=Constantine  | `Terrain 500m² à Constantine`              |
| type=bureau, city=Alger, status=location     | `Bureau à Alger - En location`             |
| type=studio, rooms=1, city=Annaba, status=vendu | `Studio 1 pièce à Annaba - Vendu`       |

---

## Comptes de test (après `php artisan db:seed`)

| Rôle  | Email                 | Mot de passe  |
|-------|-----------------------|---------------|
| Admin | admin@immobilier.dz   | Admin@12345   |
| Agent | karim@immobilier.dz   | Agent@12345   |
| Agent | sarah@immobilier.dz   | Agent@12345   |
| Agent | walid@immobilier.dz   | Agent@12345   |
| Guest | visiteur@example.com  | Guest@12345   |

---

## Flux d'appel complet (Architecture)

```
Requête HTTP  (ex: POST /api/properties)
      │
      ▼
┌─────────────────────────────────┐
│        Form Request             │  ← Validation des champs (règles, types, formats)
│  CreatePropertyRequest          │    authorize() vérifie le rôle de base
└──────────────┬──────────────────┘
               │ données validées
      ▼
┌─────────────────────────────────┐
│          Controller             │  ← Construit le DTO, appelle le Service
│  PropertyController::store()   │    $this->authorize() → Policy (contrôle fin)
└──────────────┬──────────────────┘
               │ CreatePropertyDTO
      ▼
┌─────────────────────────────────┐
│            Service              │  ← Logique métier, vérifications business
│  PropertyService::create()      │    Peut recevoir DTO ou modèle Eloquent
└──────────────┬──────────────────┘
               │ CreatePropertyDTO
      ▼
┌─────────────────────────────────┐
│          Repository             │  ← Requêtes DB uniquement (Eloquent)
│  PropertyRepository::create()   │    Appelle Property::create($dto->toArray())
└──────────────┬──────────────────┘
               │ Property (modèle)
      ▼
┌─────────────────────────────────┐
│         Base de données         │  ← MySQL via Eloquent ORM
└──────────────┬──────────────────┘
               │ Property hydraté
      ▲
┌─────────────────────────────────┐
│         API Resource            │  ← Formate la réponse JSON (PropertyResource)
│      PropertyResource           │    Expose uniquement les champs souhaités
└──────────────┬──────────────────┘
               │ JSON
      ▼
  Réponse HTTP 201
```



---

## Soft Deletes — Gestion de la corbeille (biens immobiliers)

Les suppressions de biens sont **non-destructives** par défaut (soft delete) : le bien est marqué `deleted_at` mais reste en base.
Trois endpoints supplémentaires permettent aux administrateurs de gérer la corbeille.

### Endpoints corbeille

| Méthode  | URL                               | Description                              | Rôle    |
|----------|-----------------------------------|------------------------------------------|---------|
| GET      | `/api/properties/trashed`         | Liste des biens supprimés (paginée)      | admin   |
| PATCH    | `/api/properties/{id}/restore`    | Restaurer un bien supprimé               | admin   |
| DELETE   | `/api/properties/{id}/force`      | Supprimer définitivement                 | admin   |

> Ces trois routes exigent le rôle `admin` et un token Sanctum valide.

### Exemple — Lister la corbeille

```bash
curl -X GET http://localhost:8000/api/properties/trashed \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <admin_token>"
```

**Réponse `200` :**
```json
{
  "data": [
    {
      "id": 7,
      "title": "Villa 5 pièces à Alger",
      "deleted_at": "2024-10-15T10:32:00.000000Z"
    }
  ],
  "meta": { "current_page": 1, "total": 1 }
}
```

### Exemple — Restaurer un bien

```bash
curl -X PATCH http://localhost:8000/api/properties/7/restore \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <admin_token>"
```

**Réponse `200` :**
```json
{ "message": "Bien restauré avec succès.", "data": { "id": 7 } }
```

### Exemple — Suppression définitive

```bash
curl -X DELETE http://localhost:8000/api/properties/7/force \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <admin_token>"
```

**Réponse `204 No Content`**

---

## Documentation OpenAPI / Swagger (Scribe)

La documentation interactive est générée automatiquement par [**Knuckles Scribe**](https://scribe.knuckles.wtf/).
Elle liste tous les endpoints, leurs paramètres, exemples de requête/réponse, et permet de tester l'API en ligne.

### Générer la documentation

```bash
# Installer Scribe (déjà dans composer.json — inclus dans composer install)
composer require knuckleswtf/scribe

# Publier le fichier de configuration
php artisan vendor:publish --tag=scribe-config

# Générer la documentation statique
php artisan scribe:generate
```

La documentation est écrite dans `public/docs/`.

### Accéder à la documentation

| URL                              | Contenu                                   |
|----------------------------------|-------------------------------------------|
| `http://localhost:8000/docs`     | Interface HTML interactive (Swagger-like) |
| `public/docs/openapi.yaml`       | Spécification OpenAPI 3.0                 |
| `public/docs/collection.json`    | Collection Postman importable             |

> Après `php artisan serve`, ouvrez `http://localhost:8000/docs` dans votre navigateur.

### Authentification dans l'interface Docs

1. Cliquez **Authorize** (icône cadenas en haut à droite)
2. Dans le champ **Bearer token**, collez votre token Sanctum obtenu via `POST /api/auth/login`
3. Cliquez **Authorize** — vous pouvez désormais tester les routes protégées directement

### Regénérer après modifications

```bash
php artisan scribe:generate --force
```

### Configuration Scribe (`config/scribe.php`)

Le fichier est préconfiguré avec :
- **Titre** : *Plateforme Immobilière API*
- **Auth** : Bearer token Sanctum, activé par défaut sur toutes les routes
- **Groupes** : Authentification → Biens immobiliers → Images → Utilisateurs
- **Sortie** : `public/docs/` (HTML + OpenAPI YAML + collection Postman)
- **Try it out** : activé, testable directement dans le navigateur
