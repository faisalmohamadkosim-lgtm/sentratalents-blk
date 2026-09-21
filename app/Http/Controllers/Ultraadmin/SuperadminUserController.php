<?php

namespace App\Http\Controllers\Ultraadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperadminUserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['superadmin', 'ultraadmin'])->latest()->get();

        return view('ultraadmin.superadmin-users.index', compact('users'));
    }

    public function create()
    {
        return view('ultraadmin.superadmin-users.create');
    }

    /**
     * Ultraadmin hanya berwenang membuat akun superadmin baru — role sengaja
     * dipaksa di sini, bukan dipercaya dari request.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'superadmin';
        $data['active'] = true;

        $user = new User($data);
        $user->tenant_id = null;
        $user->save();

        return redirect()->route('ultraadmin.superadmin-users.index')->with('status', 'Akun superadmin berhasil ditambahkan.');
    }
}
