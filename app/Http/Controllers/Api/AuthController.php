<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Authentification
 *
 * Gestion de l'inscription, connexion et déconnexion via Laravel Sanctum.
 * Les routes d'inscription et de connexion sont publiques (sans token).
 */
class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    // ─── POST /api/auth/register ──────────────────────────────────────────────

    /**
     * Inscription
     *
     * Crée un nouveau compte utilisateur et retourne un token Sanctum.
     *
     * @bodyParam name string required Nom complet. Example: Karim Benamara
     * @bodyParam email string required Adresse email unique. Example: karim@example.com
     * @bodyParam password string required Mot de passe (min 8 car., majuscule + chiffre). Example: Secret@123
     * @bodyParam password_confirmation string required Confirmation du mot de passe. Example: Secret@123
     * @bodyParam role string Rôle demandé : agent ou guest. Défaut: guest. Example: agent
     * @bodyParam phone string Numéro de téléphone. Example: +213 555 123456
     *
     * @response 201 {
     *   "message": "Inscription réussie.",
     *   "user": { "id": 5, "name": "Karim Benamara", "email": "karim@example.com", "role": "agent" },
     *   "token": "5|aBcDeFgHiJkLmNoPqRsTuVwXyZ..."
     * }
     * @response 422 { "message": "Les données fournies sont invalides.", "errors": { "email": ["Cette adresse email est déjà utilisée."] } }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'Inscription réussie.',
            'user'    => new UserResource($result['user']),
            'token'   => $result['token'],
        ], 201);
    }

    // ─── POST /api/auth/login ─────────────────────────────────────────────

    /**
     * Connexion
     *
     * Authentifie l'utilisateur et retourne un token Sanctum Bearer.
     * L'ancien token est révoqué à chaque connexion.
     *
     * @bodyParam email string required Adresse email. Example: admin@immobilier.dz
     * @bodyParam password string required Mot de passe. Example: Admin@12345
     *
     * @response 200 {
     *   "message": "Connexion réussie.",
     *   "user": { "id": 1, "name": "Administrateur", "email": "admin@immobilier.dz", "role": "admin" },
     *   "token": "1|xYzAbCdEfGhIjKlMnOpQrStUvWx..."
     * }
     * @response 401 { "message": "Identifiants invalides." }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->authService->login(
            $validated['email'],
            $validated['password'],
        );

        return response()->json([
            'message' => 'Connexion réussie.',
            'user'    => new UserResource($result['user']),
            'token'   => $result['token'],
        ]);
    }

    // ─── POST /api/auth/logout ────────────────────────────────────────────

    /**
     * Déconnexion
     *
     * Révoque le token courant de l'utilisateur connecté.
     *
     * @authenticated
     * @response 200 { "message": "Déconnexion réussie." }
     * @response 401 { "message": "Non authentifié." }
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    // ─── GET /api/auth/me ───────────────────────────────────────────────

    /**
     * Profil connecté
     *
     * Retourne le profil de l'utilisateur actuellement connecté.
     *
     * @authenticated
     * @response 200 {
     *   "id": 1, "name": "Administrateur", "email": "admin@immobilier.dz",
     *   "role": "admin", "phone": null, "is_active": true
     * }
     * @response 401 { "message": "Non authentifié." }
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }
}
