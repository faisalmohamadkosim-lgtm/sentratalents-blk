<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $trainers = Trainer::when($search, fn ($q) => $q->where('name', 'like', "%$search%")->orWhere('specialization', 'like', "%$search%"))
            ->orderBy('name')
            ->paginate(20)->withQueryString();
        return view('trainers.index', compact('trainers', 'search'));
    }

    public function create()
    {
        return view('trainers.form');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['active'] = $request->boolean('active', true);
        $data = $this->handlePhoto($request, $data);
        Trainer::create($data);
        return redirect()->route('trainers.index')->with('success', 'Trainer berhasil ditambahkan.');
    }

    public function edit(Trainer $trainer)
    {
        return view('trainers.form', compact('trainer'));
    }

    public function update(Request $request, Trainer $trainer)
    {
        $data = $this->validated($request);
        $data['active'] = $request->boolean('active', true);
        if ($request->boolean('delete_photo')) {
            $this->deletePhoto($trainer->photo);
            $data['photo'] = null;
        } else {
            $data = $this->handlePhoto($request, $data, $trainer->photo);
        }
        $trainer->update($data);
        return redirect()->route('trainers.index')->with('success', 'Trainer berhasil diperbarui.');
    }

    public function destroy(Trainer $trainer)
    {
        $this->deletePhoto($trainer->photo);
        $trainer->delete();
        return redirect()->route('trainers.index')->with('success', 'Trainer dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'           => 'required|string|max:150',
            'specialization' => 'nullable|string|max:150',
            'bio'            => 'nullable|string',
            'email'          => 'nullable|email|max:150',
            'phone'          => 'nullable|string|max:30',
            'photo'          => 'nullable|image|max:2048',
        ]);
    }

    private function handlePhoto(Request $request, array $data, ?string $existing = null): array
    {
        if ($request->hasFile('photo')) {
            if ($existing) $this->deletePhoto($existing);
            $data['photo'] = $request->file('photo')->store('trainers', 'public');
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
