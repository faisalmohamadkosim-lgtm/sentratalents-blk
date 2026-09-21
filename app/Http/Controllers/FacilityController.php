<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $facilities = Facility::when($search, fn ($q) => $q->where('name', 'like', "%$search%"))
            ->orderBy('sort_order')->orderBy('name')
            ->paginate(20)->withQueryString();
        return view('facilities.index', compact('facilities', 'search'));
    }

    public function create()
    {
        return view('facilities.form');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->handlePhoto($request, $data);
        Facility::create($data);
        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(Facility $facility)
    {
        return view('facilities.form', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $data = $this->validated($request);
        if ($request->boolean('delete_photo')) {
            $this->deletePhoto($facility->photo);
            $data['photo'] = null;
        } else {
            $data = $this->handlePhoto($request, $data, $facility->photo);
        }
        $facility->update($data);
        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Facility $facility)
    {
        $this->deletePhoto($facility->photo);
        $facility->delete();
        return redirect()->route('facilities.index')->with('success', 'Fasilitas dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer|min:0',
            'photo'       => 'nullable|image|max:2048',
        ]);
    }

    private function handlePhoto(Request $request, array $data, ?string $existing = null): array
    {
        if ($request->hasFile('photo')) {
            if ($existing) $this->deletePhoto($existing);
            $data['photo'] = $request->file('photo')->store('facilities', 'public');
        } else {
            unset($data['photo']);
        }
        return $data;
    }

    private function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
