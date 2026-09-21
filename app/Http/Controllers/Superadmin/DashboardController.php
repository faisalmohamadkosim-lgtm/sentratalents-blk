<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Participant;
use App\Models\Tenant;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount(['participants', 'programs'])
            ->orderBy('name')
            ->get()
            ->map(function ($tenant) {
                return [
                    'tenant' => $tenant,
                    'graduates' => Enrollment::where('tenant_id', $tenant->id)->graduated()->count(),
                    'active_users' => User::where('tenant_id', $tenant->id)->where('active', true)->count(),
                ];
            });

        $totalParticipants = Participant::withoutGlobalScope('tenant')->count();

        $enrollmentsByStatus = Enrollment::withoutGlobalScope('tenant')
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('superadmin.dashboard', compact('tenants', 'totalParticipants', 'enrollmentsByStatus'));
    }
}
