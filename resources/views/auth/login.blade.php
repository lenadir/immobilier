<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Immobilier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 400px; background: #fff; border-radius: 1rem; padding: 2.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,.06); }
        .brand { color: #1e293b; font-weight: 700; font-size: 1.4rem; }
        .brand .dot { color: #3b82f6; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand"><i class="bi bi-buildings me-1"></i>Immobilier<span class="dot">.</span></div>
            <p class="text-muted small mt-1">Connectez-vous à votre espace</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold small">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="admin@immobilier.dz" autofocus required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Mot de passe</label>
                <input type="password" name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="••••••••" required>
            </div>
            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                <label class="form-check-label small" for="remember">Se souvenir de moi</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Connexion
            </button>
        </form>
        <p class="text-center text-muted small mt-4 mb-0">
            Pas encore de compte ?
            <a href="{{ route('register') }}" class="text-primary fw-semibold">Créer un compte</a>
        </p>
    </div>
</body>
</html>
