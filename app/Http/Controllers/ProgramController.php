<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $programs = Program::with('leadTrainer')
            ->when($search, fn ($q) => $q->where('name', 'like', "%$search%")->orWhere('category', 'like', "%$search%"))
            ->withCount('batches')
            ->orderBy('name')
            ->paginate(20)->withQueryString();
        return view('programs.index', compact('programs', 'search'));
    }

    private function trainers()
    {
        return Trainer::where('active', true)->orderBy('name')->get();
    }

    public function create()
    {
        $trainers = $this->trainers();
        return view('programs.form', compact('trainers'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['active'] = $request->boolean('active', true);
        $data = $this->handlePhoto($request, $data);
        $program = Program::create($data);
        return redirect()->route('programs.show', $program)->with('success', 'Program berhasil ditambahkan.');
    }

    public function show(Program $program)
    {
        $program->load('leadTrainer', 'stages', 'batches');
        return view('programs.show', compact('program'));
    }

    public function edit(Program $program)
    {
        $trainers = $this->trainers();
        return view('programs.form', compact('program', 'trainers'));
    }

    public function update(Request $request, Program $program)
    {
        $data = $this->validated($request);
        $data['active'] = $request->boolean('active', true);
        if ($request->boolean('delete_photo')) {
            $this->deletePhoto($program->photo);
            $data['photo'] = null;
        } else {
            $data = $this->handlePhoto($request, $data, $program->photo);
        }
        $program->update($data);
        return redirect()->route('programs.show', $program)->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program)
    {
        $this->deletePhoto($program->photo);
        $program->delete();
        return redirect()->route('programs.index')->with('success', 'Program dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'            => 'required|string|max:150',
            'lead_trainer_id' => 'nullable|exists:trainers,id',
            'category'        => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'duration_days'   => 'nullable|integer|min:0',
            'quota'           => 'nullable|integer|min:0',
            'photo'           => 'nullable|image|max:2048',
        ]);
    }

    private function handlePhoto(Request $request, array $data, ?string $existing = null): array
    {
        if ($request->hasFile('photo')) {
            if ($existing) $this->deletePhoto($existing);
            $data['photo'] = $request->file('photo')->store('programs', 'public');
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
