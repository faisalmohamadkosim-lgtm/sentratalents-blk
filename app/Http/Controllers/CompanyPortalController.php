<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;

// Portal read-only untuk akun perusahaan (role "perusahaan") -- hanya menampilkan peserta
// yang dikirim oleh perusahaan mereka sendiri (company_id milik user login).
class CompanyPortalController extends Controller
{
    public function index(Request $request)
    {
        $company = auth()->user()->company;
        abort_if(!$company, 403, 'Akun ini belum ditautkan ke perusahaan manapun.');

        $status = $request->status;
        $enrollments = Enrollment::with('participant', 'batch.program')
            ->where('company_id', $company->id)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)->withQueryString();

        return view('company-portal.index', compact('company', 'enrollments', 'status'));
    }
}
