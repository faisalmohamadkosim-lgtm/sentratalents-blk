<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Company;
use App\Models\Enrollment;
use App\Models\Participant;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $status = $request->status;
        $enrollments = Enrollment::with('participant', 'batch.program', 'company')
            ->when($search, fn ($q) => $q->whereHas('participant', fn ($p) => $p->where('name', 'like', "%$search%")))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)->withQueryString();
        return view('enrollments.index', compact('enrollments', 'search', 'status'));
    }

    private function formData(): array
    {
        return [
            'participants' => Participant::orderBy('name')->get(),
            'batches'      => Batch::with('program')->orderBy('code')->get(),
            'companies'    => Company::where('active', true)->orderBy('name')->get(),
        ];
    }

    public function create()
    {
        return view('enrollments.form', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Enrollment::create($data);
        return redirect()->route('enrollments.index')->with('success', 'Pendaftaran berhasil ditambahkan.');
    }

    public function edit(Enrollment $enrollment)
    {
        return view('enrollments.form', array_merge($this->formData(), compact('enrollment')));
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $enrollment->update($this->validated($request));
        return redirect()->route('enrollments.index')->with('success', 'Pendaftaran berhasil diperbarui.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return redirect()->route('enrollments.index')->with('success', 'Pendaftaran dihapus.');
    }

    /**
     * Tandai peserta lulus -- status=graduated, graduation_date default hari ini
     * kecuali diisi manual, plus nilai akhir & nomor sertifikat opsional.
     */
    public function graduate(Request $request, Enrollment $enrollment)
    {
        $data = $request->validate([
            'graduation_date' => 'nullable|date',
            'final_score'     => 'nullable|numeric|min:0|max:100',
            'certificate_no'  => 'nullable|string|max:100',
        ]);
        $data['status'] = 'graduated';
        $data['graduation_date'] = $data['graduation_date'] ?? now()->toDateString();

        $enrollment->update($data);
        return back()->with('success', $enrollment->participant->name . ' berhasil ditandai lulus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'participant_id'     => 'required|exists:participants,id',
            'batch_id'           => 'required|exists:batches,id',
            'status'             => 'required|in:' . implode(',', Enrollment::STATUSES),
            'registration_type'  => 'required|in:' . implode(',', Enrollment::REGISTRATION_TYPES),
            'company_id'         => 'nullable|required_if:registration_type,perusahaan|exists:companies,id',
            'final_score'        => 'nullable|numeric|min:0|max:100',
            'graduation_date'    => 'nullable|date',
            'certificate_no'     => 'nullable|string|max:100',
            'notes'              => 'nullable|string',
        ]);
        if ($data['registration_type'] === 'mandiri') {
            $data['company_id'] = null;
        }
        return $data;
    }
}
