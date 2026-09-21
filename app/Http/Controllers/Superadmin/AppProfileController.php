<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppProfileController extends Controller
{
    private const KEYS = [
        'kode_aplikasi',
        'nama_aplikasi',
        'tagline',
        'institusi',
        'login_hero_title',
        'login_hero_text',
    ];

    public function edit()
    {
        $settings = collect(self::KEYS)->mapWithKeys(fn (string $key) => [
            $key => AppSetting::get($key),
        ]);

        return view('superadmin.app-profile', ['settings' => $settings]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'kode_aplikasi' => ['nullable', 'string', 'max:50'],
            'nama_aplikasi' => ['nullable', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:150'],
            'institusi' => ['nullable', 'string', 'max:255'],
            'login_hero_title' => ['nullable', 'string', 'max:255'],
            'login_hero_text' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($data as $key => $value) {
            AppSetting::set($key, $value);
        }

        return redirect()->route('superadmin.app-profile.edit')->with('success', 'Profil aplikasi berhasil disimpan.');
    }
}
