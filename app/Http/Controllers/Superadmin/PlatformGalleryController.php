<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\PlatformGalleryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlatformGalleryController extends Controller
{
    public function index()
    {
        $photos = PlatformGalleryPhoto::orderBy('sort_order')->orderByDesc('id')->paginate(20);
        return view('superadmin.gallery.index', compact('photos'));
    }

    public function create()
    {
        return view('superadmin.gallery.form');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->handlePhoto($request, $data);
        PlatformGalleryPhoto::create($data);
        return redirect()->route('superadmin.gallery.index')->with('success', 'Foto galeri berhasil ditambahkan.');
    }

    public function edit(PlatformGalleryPhoto $photo)
    {
        return view('superadmin.gallery.form', compact('photo'));
    }

    public function update(Request $request, PlatformGalleryPhoto $photo)
    {
        $data = $this->validated($request);
        if ($request->boolean('delete_photo')) {
            $this->deletePhoto($photo->photo);
            $data['photo'] = null;
        } else {
            $data = $this->handlePhoto($request, $data, $photo->photo);
        }
        $photo->update($data);
        return redirect()->route('superadmin.gallery.index')->with('success', 'Foto galeri berhasil diperbarui.');
    }

    public function destroy(PlatformGalleryPhoto $photo)
    {
        $this->deletePhoto($photo->photo);
        $photo->delete();
        return redirect()->route('superadmin.gallery.index')->with('success', 'Foto galeri dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'caption'    => 'nullable|string|max:150',
            'link_url'   => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'photo'      => 'nullable|image|max:2048',
            'active'     => 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);
        return $data;
    }

    private function handlePhoto(Request $request, array $data, ?string $existing = null): array
    {
        if ($request->hasFile('photo')) {
            if ($existing && !str_starts_with($existing, 'http')) $this->deletePhoto($existing);
            $data['photo'] = $request->file('photo')->store('platform-gallery', 'public');
        } else {
            unset($data['photo']);
        }
        return $data;
    }

    private function deletePhoto(?string $path): void
    {
        if ($path && !str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
