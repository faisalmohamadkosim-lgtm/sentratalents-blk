<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantProfileController extends Controller
{
    public function edit()
    {
        $tenant = auth()->user()->tenant;
        abort_unless($tenant, 404);
        return view('tenant-profile.edit', compact('tenant'));
    }

    public function update(Request $request)
    {
        $tenant = auth()->user()->tenant;
        abort_unless($tenant, 404);

        $data = $request->validate([
            'name'              => 'required|string|max:200',
            'code'              => 'nullable|string|max:30|unique:tenants,code,' . $tenant->id,
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

        if ($request->hasFile('logo')) {
            if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $data['logo'] = $request->file('logo')->store('tenants', 'public');
        }
        if ($request->hasFile('cover_image')) {
            if ($tenant->cover_image && Storage::disk('public')->exists($tenant->cover_image)) {
                Storage::disk('public')->delete($tenant->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('tenants', 'public');
        }

        $tenant->update($data);
        return redirect()->route('tenant-profile.edit')->with('success', 'Profil BLK berhasil diperbarui.');
    }
}
