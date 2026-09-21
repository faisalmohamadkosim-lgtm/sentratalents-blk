<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// Provisioning akun login (role "perusahaan") untuk satu perusahaan mitra -- perwakilan
// perusahaan memakai akun ini buat memantau status peserta yang mereka kirim lewat
// CompanyPortalController, read-only.
class CompanyUserController extends Controller
{
    public function create(Company $company)
    {
        return view('companies.users.create', compact('company'));
    }

    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'active'   => 'boolean',
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['active'] = $request->boolean('active', true);
        $data['role'] = 'perusahaan';

        $user = new User($data);
        $user->tenant_id = $company->tenant_id;
        $user->company_id = $company->id;
        $user->save();

        return redirect()->route('companies.index')->with('success', 'Akun login untuk ' . $company->name . ' berhasil dibuat.');
    }
}
