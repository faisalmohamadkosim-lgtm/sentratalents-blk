<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $companies = Company::withCount('enrollments')
            ->when($search, fn ($q) => $q->where('name', 'like', "%$search%"))
            ->orderBy('name')
            ->paginate(20)->withQueryString();
        return view('companies.index', compact('companies', 'search'));
    }

    public function create()
    {
        return view('companies.form');
    }

    public function store(Request $request)
    {
        Company::create($this->validated($request));
        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function edit(Company $company)
    {
        return view('companies.form', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $company->update($this->validated($request));
        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil diperbarui.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Perusahaan dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'            => 'required|string|max:150',
            'contact_person'  => 'nullable|string|max:150',
            'phone'           => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:150',
            'address'         => 'nullable|string|max:255',
            'active'          => 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);
        return $data;
    }
}
