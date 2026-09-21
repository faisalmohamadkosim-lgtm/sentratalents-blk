<?php

namespace App\Http\Controllers;

use App\Models\TenantGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantGalleryController extends Controller
{
    public function index()
    {
        $photos = TenantGallery::orderBy('sort_order')->latest()->paginate(24);
        return view('gallery.index', compact('photos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'caption'    => 'nullable|string|max:200',
            'sort_order' => 'nullable|integer|min:0',
            'photo'      => 'required|image|max:2048',
        ]);
        $data['photo'] = $request->file('photo')->store('gallery', 'public');
        TenantGallery::create($data);
        return back()->with('success', 'Foto berhasil ditambahkan.');
    }

    public function destroy(TenantGallery $gallery)
    {
        if ($gallery->photo && Storage::disk('public')->exists($gallery->photo)) {
            Storage::disk('public')->delete($gallery->photo);
        }
        $gallery->delete();
        return back()->with('success', 'Foto dihapus.');
    }
}
