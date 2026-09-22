<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $users = User::where('tenant_id', auth()->user()->tenant_id)
            ->whereNotIn('role', ['perusahaan', 'trainer'])
            ->when($search, fn ($q) => $q->where('name', 'like', "%$search%")->orWhere('email', 'like', "%$search%"))
            ->latest()->paginate(20)->withQueryString();
        return view('users.index', compact('users', 'search'));
    }

    // Role "perusahaan"/"trainer" sengaja tidak muncul di sini -- akun perusahaan wajib
    // ditautkan ke satu company_id (lewat CompanyUserController, menu Perusahaan Mitra),
    // dan akun trainer wajib ditautkan ke satu profil Trainer (lewat TrainerUserController,
    // menu Trainer/Instruktur).
    private const EXCLUDED_ROLES = ['superadmin', 'ultraadmin', 'perusahaan', 'trainer'];

    private function formData(): array
    {
        return [
            'roles' => DB::table('roles')->where('active', 1)->whereNotIn('name', self::EXCLUDED_ROLES)->orderBy('name')->get(),
        ];
    }

    public function create()
    {
        return view('users.form', $this->formData());
    }

    public function store(Request $request)
    {
        $validRoles = DB::table('roles')->where('active', 1)->whereNotIn('name', self::EXCLUDED_ROLES)->pluck('name')->implode(',');
        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:' . $validRoles,
            'active'   => 'boolean',
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['active'] = $request->boolean('active', true);

        $user = new User($data);
        $user->tenant_id = auth()->user()->tenant_id;
        $user->save();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        abort_unless($user->tenant_id === auth()->user()->tenant_id && !in_array($user->role, ['perusahaan', 'trainer']), 404);
        return view('users.form', array_merge($this->formData(), compact('user')));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->tenant_id === auth()->user()->tenant_id && !in_array($user->role, ['perusahaan', 'trainer']), 404);
        $validRoles = DB::table('roles')->where('active', 1)->whereNotIn('name', self::EXCLUDED_ROLES)->pluck('name')->implode(',');
        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role'     => 'required|in:' . $validRoles,
        ]);
        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $data['active'] = $request->boolean('active');
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Data pengguna diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->tenant_id === auth()->user()->tenant_id, 404);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna dihapus.');
    }

    public function toggleActive(User $user)
    {
        abort_unless($user->tenant_id === auth()->user()->tenant_id, 404);
        $user->update(['active' => !$user->active]);
        return back()->with('success', $user->active ? 'Pengguna diaktifkan.' : 'Pengguna dinonaktifkan.');
    }
}
