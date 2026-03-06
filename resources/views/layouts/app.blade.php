<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Immobilier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-w: 230px; }
        body { background: #f1f5f9; min-height: 100vh; }

        /* ── Sidebar ─────────────────────────────── */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: #0f172a;
            display: flex; flex-direction: column;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 1.25rem 1.25rem 1rem;
            color: #fff;
            font-size: 1.15rem; font-weight: 700;
            border-bottom: 1px solid #1e293b;
            display: flex; align-items: center; gap: .5rem;
        }
        .sidebar-brand .icon { color: #3b82f6; font-size: 1.4rem; }
        .sidebar nav { padding: .75rem .5rem; flex: 1; }
        .nav-item a {
            display: flex; align-items: center; gap: .55rem;
            padding: .55rem .9rem; border-radius: .5rem;
            color: #94a3b8; font-size: .875rem;
            text-decoration: none; transition: background .15s, color .15s;
        }
        .nav-item a:hover { background: #1e293b; color: #e2e8f0; }
        .nav-item a.active { background: #1d4ed8; color: #fff; }
        .nav-item a i { font-size: 1rem; width: 1.1rem; text-align: center; }
        .sidebar-section {
            font-size: .7rem; font-weight: 600; letter-spacing: .08em;
            color: #475569; padding: .75rem .9rem .3rem;
            text-transform: uppercase;
        }
        .sidebar-footer {
            padding: .75rem 1rem;
            border-top: 1px solid #1e293b;
            font-size: .8rem; color: #64748b;
        }
        .sidebar-footer .user-name { color: #e2e8f0; font-weight: 600; }

        /* ── Main ────────────────────────────────── */
        .main-wrapper { margin-left: var(--sidebar-w); min-height: 100vh; }
        .topbar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar .page-title { font-weight: 600; font-size: 1rem; color: #1e293b; }
        .content { padding: 1.5rem; }

        /* ── Cards ───────────────────────────────── */
        .stat-card {
            background: #fff; border-radius: .75rem;
            padding: 1.25rem; border: 1px solid #e2e8f0;
        }
        .stat-card .stat-icon {
            width: 2.75rem; height: 2.75rem; border-radius: .6rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; margin-bottom: .75rem;
        }
        .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; color: #1e293b; }
        .stat-card .stat-label { font-size: .8rem; color: #64748b; }

        /* ── Table ───────────────────────────────── */
        .card { border: 1px solid #e2e8f0; border-radius: .75rem; }
        .card-header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: .9rem 1.25rem; border-radius: .75rem .75rem 0 0 !important; }
        .table th { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #64748b; border-top: none; }
        .table td { vertical-align: middle; font-size: .875rem; }

        /* ── Badges ──────────────────────────────── */
        .badge-disponible { background: #dcfce7; color: #15803d; }
        .badge-vendu      { background: #fee2e2; color: #b91c1c; }
        .badge-location   { background: #dbeafe; color: #1d4ed8; }
        .badge-published  { background: #dcfce7; color: #15803d; }
        .badge-draft      { background: #f3f4f6; color: #6b7280; }
        .badge-admin  { background: #ede9fe; color: #7c3aed; }
        .badge-agent  { background: #dbeafe; color: #1d4ed8; }
        .badge-guest  { background: #f3f4f6; color: #6b7280; }

        /* ── Alerts ──────────────────────────────── */
        .flash { border-radius: .5rem; border: none; }
    </style>
</head>
<body>

{{-- ── Sidebar ────────────────────────────────────────────────────────────── --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-buildings icon"></i> Immobilier
    </div>

    <nav>
        <div class="sidebar-section">Principal</div>
        <ul class="list-unstyled mb-0">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>
        </ul>

        <div class="sidebar-section mt-2">Biens</div>
        <ul class="list-unstyled mb-0">
            <li class="nav-item">
                <a href="{{ route('properties.index') }}" class="{{ request()->routeIs('properties.*') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i> Biens immobiliers
                </a>
            </li>
            @if(auth()->user()->isAdmin() || auth()->user()->isAgent())
            <li class="nav-item">
                <a href="{{ route('properties.create') }}">
                    <i class="bi bi-plus-circle"></i> Ajouter un bien
                </a>
            </li>
            @endif
        </ul>

        @if(auth()->user()->isAdmin())
        <div class="sidebar-section mt-2">Administration</div>
        <ul class="list-unstyled mb-0">
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Utilisateurs
                </a>
            </li>
        </ul>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-name">{{ auth()->user()->name }}</div>
        <div class="text-capitalize">{{ auth()->user()->role }}</div>
    </div>
</aside>

{{-- ── Main content ─────────────────────────────────────────────────────────── --}}
<div class="main-wrapper">
    <div class="topbar">
        <span class="page-title">@yield('title', 'Dashboard')</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
            </button>
        </form>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success flash d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger flash d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
