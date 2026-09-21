<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Support\Facades\App;

class PublicTenantController extends Controller
{
    /**
     * Profil publik satu BLK. Halaman ini terbuka juga untuk staf yang sedang login
     * (lihat routes/web.php -- tidak dipasang middleware guest/auth), jadi auth()->user()
     * BISA terisi di sini. Global scope tenant di BelongsToTenant akan otomatis membatasi
     * query ke tenant_id milik user yang login kalau tidak di-bypass -- setiap relasi/
     * query tenant-scoped di bawah ini WAJIB withoutGlobalScope('tenant') supaya profil
     * BLK manapun tetap tampil benar terlepas dari siapa yang sedang login.
     */
    public function show(string $slug)
    {
        $tenant = Tenant::where('slug', $slug)->where('active', true)->firstOrFail();

        $tenant->load([
            'facilities' => fn ($q) => $q->withoutGlobalScope('tenant')->orderBy('sort_order'),
            'trainers' => fn ($q) => $q->withoutGlobalScope('tenant')->where('active', true)->orderBy('name'),
            'orgStructureMembers' => fn ($q) => $q->withoutGlobalScope('tenant'),
            'programs' => fn ($q) => $q->withoutGlobalScope('tenant')->where('active', true)
                ->with(['leadTrainer' => fn ($lt) => $lt->withoutGlobalScope('tenant'), 'stages'])
                ->orderBy('name'),
            'galleries' => fn ($q) => $q->withoutGlobalScope('tenant'),
        ]);

        $graduatesCount = \App\Models\Enrollment::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)->graduated()->count();

        return view('public.tenant-profile', compact('tenant', 'graduatesCount'));
    }
}
