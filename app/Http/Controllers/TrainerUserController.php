<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// Provisioning akun login (role "trainer") untuk satu instruktur/trainer -- dipakai
// buat masuk ke portal instruktur lewat TrainerPortalController, read-only.
class TrainerUserController extends Controller
{
    public function create(Trainer $trainer)
    {
        return view('trainers.users.create', compact('trainer'));
    }

    public function store(Request $request, Trainer $trainer)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'active'   => 'boolean',
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['active'] = $request->boolean('active', true);
        $data['role'] = 'trainer';

        $user = new User($data);
        $user->tenant_id = $trainer->tenant_id;
        $user->save();

        $trainer->user_id = $user->id;
        $trainer->save();

        return redirect()->route('trainers.index')->with('success', 'Akun login untuk ' . $trainer->name . ' berhasil dibuat.');
    }
}
