<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;

// Portal read-only untuk akun instruktur/trainer (role "trainer") -- hanya menampilkan
// program yang mereka ampu (lead_trainer_id) beserta angkatan & peserta di dalamnya.
class TrainerPortalController extends Controller
{
    public function index()
    {
        $trainer = auth()->user()->trainer;
        abort_if(!$trainer, 403, 'Akun ini belum ditautkan ke profil trainer manapun.');

        $trainer->load(['leadPrograms' => fn ($q) => $q->with('batches')->orderBy('name')]);

        return view('trainer-portal.index', compact('trainer'));
    }

    public function batch(\App\Models\Batch $batch)
    {
        $trainer = auth()->user()->trainer;
        abort_if(!$trainer, 403, 'Akun ini belum ditautkan ke profil trainer manapun.');
        abort_unless($batch->program->lead_trainer_id === $trainer->id, 403);

        $enrollments = Enrollment::with('participant')
            ->where('batch_id', $batch->id)
            ->orderBy('status')
            ->get();

        return view('trainer-portal.batch', compact('trainer', 'batch', 'enrollments'));
    }
}
