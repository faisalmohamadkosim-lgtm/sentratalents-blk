<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\PlatformNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlatformNewsController extends Controller
{
    public function index()
    {
        $news = PlatformNews::orderBy('sort_order')->orderByDesc('published_at')->paginate(20);
        return view('superadmin.news.index', compact('news'));
    }

    public function create()
    {
        return view('superadmin.news.form');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->handleImage($request, $data);
        PlatformNews::create($data);
        return redirect()->route('superadmin.news.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(PlatformNews $article)
    {
        return view('superadmin.news.form', compact('article'));
    }

    public function update(Request $request, PlatformNews $article)
    {
        $data = $this->validated($request);
        if ($request->boolean('delete_image')) {
            $this->deleteImage($article->image);
            $data['image'] = null;
        } else {
            $data = $this->handleImage($request, $data, $article->image);
        }
        $article->update($data);
        return redirect()->route('superadmin.news.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(PlatformNews $article)
    {
        $this->deleteImage($article->image);
        $article->delete();
        return redirect()->route('superadmin.news.index')->with('success', 'Berita dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'        => 'required|string|max:200',
            'excerpt'      => 'nullable|string|max:1000',
            'link_url'     => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'sort_order'   => 'nullable|integer|min:0',
            'image'        => 'nullable|image|max:2048',
            'active'       => 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);
        return $data;
    }

    private function handleImage(Request $request, array $data, ?string $existing = null): array
    {
        if ($request->hasFile('image')) {
            if ($existing && !str_starts_with($existing, 'http')) $this->deleteImage($existing);
            $data['image'] = $request->file('image')->store('platform-news', 'public');
        } else {
            unset($data['image']);
        }
        return $data;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && !str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
