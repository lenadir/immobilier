<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(Request $request)
    {
        $this->adminOnly();

        $query = User::withCount('properties')->latest();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('users.index', [
            'users'   => $users,
            'filters' => $request->only('search', 'role'),
            'roles'   => [User::ROLE_ADMIN, User::ROLE_AGENT, User::ROLE_GUEST],
        ]);
    }

    public function show(User $user)
    {
        $this->adminOnly();

        $user->loadCount('properties');
        $recentProperties = $user->properties()->latest()->limit(5)->get();

        return view('users.show', compact('user', 'recentProperties'));
    }

    public function edit(User $user)
    {
        $this->adminOnly();

        return view('users.edit', [
            'user'  => $user,
            'roles' => [User::ROLE_ADMIN, User::ROLE_AGENT, User::ROLE_GUEST],
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->adminOnly();

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'role'      => ['required', 'in:' . implode(',', [User::ROLE_ADMIN, User::ROLE_AGENT, User::ROLE_GUEST])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->userService->update($user, $data, Auth::user());

        return redirect()
            ->route('users.show', $user)
            ->with('success', "Utilisateur « {$user->name} » mis à jour.");
    }

    public function destroy(User $user)
    {
        $this->adminOnly();

        $name = $user->name;
        $this->userService->delete($user, Auth::user());

        return redirect()
            ->route('users.index')
            ->with('success', "Utilisateur « {$name} » supprimé.");
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    private function adminOnly(): void
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Accès réservé aux administrateurs.');
        }
    }
}
