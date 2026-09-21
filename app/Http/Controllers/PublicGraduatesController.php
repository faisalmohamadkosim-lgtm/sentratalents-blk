<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PublicGraduatesController extends Controller
{
    // Halaman ini terbuka juga untuk staf yang sedang login (tidak ada middleware
    // guest/auth), jadi setiap query ke model ber-BelongsToTenant WAJIB
    // withoutGlobalScope('tenant') -- kalau tidak, daftar lulusan lintas-BLK ini akan
    // diam-diam terfilter ke tenant_id milik user yang login.
    public function index(Request $request)
    {
        $tenantSlug = $request->tenant;
        $programId  = $request->program;
        $year       = $request->year;

        $graduates = Enrollment::withoutGlobalScope('tenant')
            ->with('participant', 'batch.program', 'tenant')
            ->graduated()
            ->when($tenantSlug, function ($q) use ($tenantSlug) {
                $q->whereHas('tenant', fn ($t) => $t->where('slug', $tenantSlug));
            })
            ->when($programId, function ($q) use ($programId) {
                $q->whereHas('batch', fn ($b) => $b->withoutGlobalScope('tenant')->where('program_id', $programId));
            })
            ->when($year, function ($q) use ($year) {
                $q->whereYear('graduation_date', $year);
            })
            ->orderByDesc('graduation_date')
            ->paginate(20)->withQueryString();

        $tenants = Tenant::where('active', true)->orderBy('name')->get();
        $programs = Program::withoutGlobalScope('tenant')->orderBy('name')->get();
        $years = Enrollment::withoutGlobalScope('tenant')->graduated()->selectRaw('YEAR(graduation_date) as y')
            ->whereNotNull('graduation_date')
            ->distinct()->orderByDesc('y')->pluck('y');

        return view('public.graduates', compact('graduates', 'tenants', 'programs', 'years', 'tenantSlug', 'programId', 'year'));
    }
}
