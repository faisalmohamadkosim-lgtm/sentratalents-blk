<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $companyId = $request->company_id;
        $participants = Participant::with('latestEnrollment.company')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")->orWhere('nik', 'like', "%$search%");
            })
            ->when($companyId === 'mandiri', function ($q) {
                $q->whereDoesntHave('enrollments', fn ($e) => $e->whereNotNull('company_id'));
            })
            ->when($companyId && $companyId !== 'mandiri', function ($q) use ($companyId) {
                $q->whereHas('enrollments', fn ($e) => $e->where('company_id', $companyId));
            })
            ->latest()
            ->paginate(20)->withQueryString();
        $companies = Company::orderBy('name')->get();
        return view('participants.index', compact('participants', 'search', 'companyId', 'companies'));
    }

    public function create()
    {
        return view('participants.form');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->handlePhoto($request, $data);
        Participant::create($data);
        return redirect()->route('participants.index')->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function show(Participant $participant)
    {
        $participant->load('enrollments.batch.program');
        return view('participants.show', compact('participant'));
    }

    public function edit(Participant $participant)
    {
        return view('participants.form', compact('participant'));
    }

    public function update(Request $request, Participant $participant)
    {
        $data = $this->validated($request, $participant->id);
        if ($request->boolean('delete_photo')) {
            $this->deletePhoto($participant->photo);
            $data['photo'] = null;
        } else {
            $data = $this->handlePhoto($request, $data, $participant->photo);
        }
        $participant->update($data);
        return redirect()->route('participants.show', $participant)->with('success', 'Data peserta diperbarui.');
    }

    public function destroy(Participant $participant)
    {
        $this->deletePhoto($participant->photo);
        $participant->delete();
        return redirect()->route('participants.index')->with('success', 'Peserta dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'       => 'required|string|max:150',
            'nik'        => 'nullable|string|max:30',
            'gender'     => 'nullable|in:L,P',
            'birth_date' => 'nullable|date',
            'address'    => 'nullable|string',
            'phone'      => 'nullable|string|max:30',
            'email'      => 'nullable|email|max:150',
            'education'  => 'nullable|string|max:50',
            'photo'      => 'nullable|image|max:2048',
        ]);
    }

    private function handlePhoto(Request $request, array $data, ?string $existing = null): array
    {
        if ($request->hasFile('photo')) {
            if ($existing) $this->deletePhoto($existing);
            $data['photo'] = $request->file('photo')->store('participants', 'public');
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
