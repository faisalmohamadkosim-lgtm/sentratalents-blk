<?php

namespace App\Http\Controllers\Ultraadmin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSuperadmins = User::where('role', 'superadmin')->count();
        $totalUltraadmins = User::where('role', 'ultraadmin')->count();

        return view('ultraadmin.dashboard', compact('totalSuperadmins', 'totalUltraadmins'));
    }
}
