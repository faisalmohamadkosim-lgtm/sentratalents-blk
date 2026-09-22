<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    const SYSTEM_ROLES = ['superadmin', 'ultraadmin', 'admin', 'owner', 'staff', 'perusahaan', 'trainer', 'security'];

    public function index()
    {
        $roles = DB::table('roles')
            ->leftJoin(DB::raw('(SELECT role, COUNT(*) as user_count FROM users GROUP BY role) uc'), 'roles.name', '=', 'uc.role')
            ->select('roles.*', DB::raw('COALESCE(uc.user_count, 0) as user_count'))
            ->orderBy('roles.id')
            ->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name|alpha_dash',
        ]);
        DB::table('roles')->insert([
            'name'        => strtolower($request->name),
            'label'       => $request->label,
            'description' => $request->description,
            'can_admin'   => $request->boolean('can_admin'),
            'active'      => $request->boolean('active', true),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $role = DB::table('roles')->find($id);
        abort_if(!$role, 404);
        return view('roles.form', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $role = DB::table('roles')->find($id);
        abort_if(!$role, 404);
        $request->validate([
            'name' => 'required|string|max:50|alpha_dash|unique:roles,name,' . $id,
        ]);
        DB::table('roles')->where('id', $id)->update([
            'name'        => in_array($role->name, self::SYSTEM_ROLES) ? $role->name : strtolower($request->name),
            'label'       => $request->label,
            'description' => $request->description,
            'can_admin'   => $request->boolean('can_admin'),
            'active'      => $request->boolean('active'),
            'updated_at'  => now(),
        ]);
        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $role = DB::table('roles')->find($id);
        abort_if(!$role, 404);
        if (in_array($role->name, self::SYSTEM_ROLES)) {
            return back()->with('error', 'Role bawaan sistem tidak dapat dihapus.');
        }
        if (DB::table('users')->where('role', $role->name)->count()) {
            return back()->with('error', 'Role tidak dapat dihapus karena masih digunakan pengguna.');
        }
        DB::table('roles')->delete($id);
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }

    public function toggle($id)
    {
        $role = DB::table('roles')->find($id);
        abort_if(!$role || in_array($role->name, ['admin']), 403);
        DB::table('roles')->where('id', $id)->update(['active' => !$role->active, 'updated_at' => now()]);
        return back()->with('success', 'Status role diperbarui.');
    }
}
