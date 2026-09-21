<?php

namespace App\Http\Controllers;

use App\Models\PlatformGalleryPhoto;
use App\Models\PlatformNews;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PublicDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        // withoutGlobalScope('tenant') -- halaman ini terbuka juga untuk staf yang sedang
        // login (lihat routes/web.php), jadi global scope BelongsToTenant TIDAK boleh
        // membatasi hitungan program/gallery ke tenant_id milik user yang login; halaman
        // ini memang harus menampilkan data lintas-tenant apa adanya.
        $tenants = Tenant::withCount(['programs' => fn ($q) => $q->withoutGlobalScope('tenant')])
            ->where('active', true)
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('city', 'like', "%$search%")
                    ->orWhere('province', 'like', "%$search%");
            })
            ->orderBy('name')
            ->paginate(12)->withQueryString();

        // Galeri landing page utama -- platform-level, dikelola langsung oleh superadmin
        // (lihat Superadmin\PlatformGalleryController), termasuk tautan per foto.
        $galleryPhotos = PlatformGalleryPhoto::where('active', true)
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        // Berita landing page utama -- platform-level, dikelola langsung oleh superadmin
        // (lihat Superadmin\PlatformNewsController), tampil sebagai daftar geser horizontal.
        $news = PlatformNews::where('active', true)
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();

        return view('public.directory', compact('tenants', 'search', 'galleryPhotos', 'news'));
    }
}
