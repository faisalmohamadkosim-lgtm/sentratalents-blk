<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Enrollment;
use App\Models\Participant;
use App\Models\Program;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isUltraadmin()) {
            return redirect()->route('ultraadmin.dashboard');
        }

        if ($user->isSuperadmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->isCompany()) {
            return redirect()->route('company-portal.index');
        }

        $stats = [
            'programs'      => Program::count(),
            'participants'  => Participant::count(),
            'active_batches'=> Batch::where('status', 'ongoing')->count(),
            'graduates'     => Enrollment::graduated()->count(),
        ];

        $recentEnrollments = Enrollment::with('participant', 'batch.program')
            ->latest()->limit(8)->get();

        $batchesByStatus = Batch::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard', compact('stats', 'recentEnrollments', 'batchesByStatus'));
    }
}
