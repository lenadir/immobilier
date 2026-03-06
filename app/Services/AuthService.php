<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    // ─── Inscription ─────────────────────────────────────────────────────────

    /**
     * Crée un compte et retourne le token API.
     */
    public function register(array $data): array
    {
        $user = $this->userRepository->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],      // hash géré par le cast du modèle
            'role'     => $data['role'] ?? User::ROLE_GUEST,
            'phone'    => $data['phone'] ?? null,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return compact('user', 'token');
    }

    // ─── Connexion ───────────────────────────────────────────────────────────

    /**
     * Vérifie les credentials et retourne le token API.
     *
     * @throws AuthenticationException
     */
    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new AuthenticationException('Identifiants invalides.');
        }

        if (!$user->is_active) {
            throw new AuthenticationException('Ce compte est désactivé.');
        }

        // Révoque les anciens tokens pour n'avoir qu'un seul token actif
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return compact('user', 'token');
    }

    // ─── Déconnexion ─────────────────────────────────────────────────────────

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
