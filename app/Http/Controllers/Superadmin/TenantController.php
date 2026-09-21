<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $tenants = Tenant::withCount(['users', 'participants', 'programs'])
            ->when($search, fn ($q) => $q->where('name', 'like', "%$search%"))
            ->orderBy('name')
            ->paginate(20)->withQueryString();
        return view('superadmin.tenants.index', compact('tenants', 'search'));
    }

    public function create()
    {
        return view('superadmin.tenants.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['active'] = $request->boolean('active', true);
        $data = $this->handleLogo($request, $data, null);

        $tenant = Tenant::create($data);

        return redirect()->route('superadmin.tenants.edit', $tenant)->with('status', 'BLK berhasil didaftarkan.');
    }

    public function edit(Tenant $tenant)
    {
        return view('superadmin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $this->validated($request);
        $data['active'] = $request->boolean('active', true);
        $data = $this->handleLogo($request, $data, $tenant);

        $tenant->update($data);

        return redirect()->route('superadmin.tenants.edit', $tenant)->with('status', 'Data BLK diperbarui.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'              => 'required|string|max:200',
            'code'              => 'nullable|string|max:30',
            'description'       => 'nullable|string',
            'address'           => 'nullable|string',
            'city'              => 'nullable|string|max:100',
            'province'          => 'nullable|string|max:100',
            'phone'             => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:150',
            'website'           => 'nullable|url|max:200',
            'established_year'  => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'accreditation'     => 'nullable|string|max:50',
            'logo'              => 'nullable|image|max:1024',
            'cover_image'       => 'nullable|image|max:2048',
        ]);
        unset($data['logo'], $data['cover_image']);
        return $data;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }

    private function handleLogo(Request $request, array $data, ?Tenant $tenant): array
    {
        if ($request->hasFile('logo')) {
            if ($tenant?->logo && Storage::disk('public')->exists($tenant->logo)) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $data['logo'] = $request->file('logo')->store('tenants', 'public');
        }
        if ($request->hasFile('cover_image')) {
            if ($tenant?->cover_image && Storage::disk('public')->exists($tenant->cover_image)) {
                Storage::disk('public')->delete($tenant->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('tenants', 'public');
        }
        return $data;
    }
}
