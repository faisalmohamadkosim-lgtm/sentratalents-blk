<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramStage;
use Illuminate\Http\Request;

class ProgramStageController extends Controller
{
    public function store(Request $request, Program $program)
    {
        $data = $this->validated($request);
        $data['program_id'] = $program->id;
        $data['sort_order'] = $data['sort_order'] ?? ($program->stages()->max('sort_order') + 1);
        ProgramStage::create($data);
        return back()->with('success', 'Tahapan program berhasil ditambahkan.');
    }

    public function update(Request $request, Program $program, ProgramStage $stage)
    {
        abort_unless($stage->program_id === $program->id, 404);
        $stage->update($this->validated($request));
        return back()->with('success', 'Tahapan program berhasil diperbarui.');
    }

    public function destroy(Program $program, ProgramStage $stage)
    {
        abort_unless($stage->program_id === $program->id, 404);
        $stage->delete();
        return back()->with('success', 'Tahapan program dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'         => 'required|string|max:150',
            'description'   => 'nullable|string',
            'duration_days' => 'nullable|integer|min:0',
            'sort_order'    => 'nullable|integer|min:0',
        ]);
    }
}
