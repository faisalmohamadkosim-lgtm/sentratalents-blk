<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TenantUserController extends Controller
{
    public function create(Tenant $tenant)
    {
        return view('superadmin.tenants.users.create', compact('tenant'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'active'   => 'boolean',
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['active'] = $request->boolean('active', true);
        $data['role'] = 'admin';

        $user = new User($data);
        $user->tenant_id = $tenant->id;
        $user->save();

        return redirect()->route('superadmin.tenants.edit', $tenant)->with('status', 'Admin BLK berhasil ditambahkan.');
    }
}
