<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_properties'   => Property::count(),
            'published'          => Property::where('is_published', true)->count(),
            'disponible'         => Property::where('status', 'disponible')->count(),
            'vendu'              => Property::where('status', 'vendu')->count(),
            'location'           => Property::where('status', 'location')->count(),
            'total_agents'       => User::where('role', User::ROLE_AGENT)->count(),
            'total_users'        => User::count(),
            'trashed'            => Property::onlyTrashed()->count(),
        ];

        $byType = Property::selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $recent = Property::with('user')
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard', compact('stats', 'byType', 'recent'));
    }
}
