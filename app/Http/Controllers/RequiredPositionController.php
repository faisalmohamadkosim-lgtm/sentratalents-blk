<?php

namespace App\Http\Controllers;

use App\Models\RequiredPosition;
use Illuminate\Http\Request;

class RequiredPositionController extends Controller
{
    public function index()
    {
        $positions = RequiredPosition::orderBy('position_name')->get();
        return view('required-positions.index', compact('positions'));
    }

    public function create()
    {
        return view('required-positions.form');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = auth()->id();
        RequiredPosition::create($data);
        return redirect()->route('required-positions.index')->with('success', 'Jabatan minimal ditambahkan.');
    }

    public function edit(RequiredPosition $requiredPosition)
    {
        return view('required-positions.form', ['position' => $requiredPosition]);
    }

    public function update(Request $request, RequiredPosition $requiredPosition)
    {
        $requiredPosition->update($this->validated($request));
        return redirect()->route('required-positions.index')->with('success', 'Jabatan minimal diperbarui.');
    }

    public function destroy(RequiredPosition $requiredPosition)
    {
        $requiredPosition->delete();
        return redirect()->route('required-positions.index')->with('success', 'Jabatan minimal dihapus.');
    }

    /** Muat contoh umum posisi minimal BLK -- hanya menambahkan yang belum ada (dicocokkan nama persis). */
    public function loadDefaults()
    {
        $existingNames = RequiredPosition::pluck('position_name')->map(fn ($n) => mb_strtolower($n))->all();
        $added = 0;
        foreach (RequiredPosition::defaultTemplate() as $tpl) {
            if (in_array(mb_strtolower($tpl['position_name']), $existingNames, true)) {
                continue;
            }
            RequiredPosition::create($tpl + ['created_by' => auth()->id()]);
            $added++;
        }
        return redirect()->route('required-positions.index')
            ->with('success', $added > 0 ? "$added contoh jabatan ditambahkan." : 'Semua contoh jabatan sudah ada di daftar.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'position_name' => 'required|string|max:150',
            'min_count' => 'required|integer|min:1|max:255',
            'notes' => 'nullable|string',
            'active' => 'sometimes|boolean',
        ]);
    }
}
