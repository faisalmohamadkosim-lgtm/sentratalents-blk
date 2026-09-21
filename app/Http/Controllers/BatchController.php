<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Program;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $status = $request->status;
        $batches = Batch::with('program')
            ->when($search, fn ($q) => $q->where('code', 'like', "%$search%")->orWhereHas('program', fn ($p) => $p->where('name', 'like', "%$search%")))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->withCount('enrollments')
            ->latest('start_date')
            ->paginate(20)->withQueryString();
        return view('batches.index', compact('batches', 'search', 'status'));
    }

    private function programs()
    {
        return Program::where('active', true)->orderBy('name')->get();
    }

    public function create()
    {
        $programs = $this->programs();
        return view('batches.form', compact('programs'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Batch::create($data);
        return redirect()->route('batches.index')->with('success', 'Angkatan berhasil ditambahkan.');
    }

    public function show(Batch $batch)
    {
        $batch->load('program', 'enrollments.participant');
        return view('batches.show', compact('batch'));
    }

    public function edit(Batch $batch)
    {
        $programs = $this->programs();
        return view('batches.form', compact('batch', 'programs'));
    }

    public function update(Request $request, Batch $batch)
    {
        $batch->update($this->validated($request));
        return redirect()->route('batches.show', $batch)->with('success', 'Angkatan berhasil diperbarui.');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();
        return redirect()->route('batches.index')->with('success', 'Angkatan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'program_id' => 'required|exists:programs,id',
            'code'       => 'required|string|max:50',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
            'quota'      => 'nullable|integer|min:0',
            'status'     => 'required|in:' . implode(',', Batch::STATUSES),
        ]);
    }
}
