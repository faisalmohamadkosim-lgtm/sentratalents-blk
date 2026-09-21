@extends('layouts.app')
@section('title', isset($enrollment) ? 'Edit Pendaftaran' : 'Tambah Pendaftaran')
@section('content')
@php $isEdit = isset($enrollment); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Pendaftaran' : 'Tambah Pendaftaran' }}</div>
  <a href="{{ route('enrollments.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <form method="POST" action="{{ $isEdit ? route('enrollments.update', $enrollment) : route('enrollments.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="form-group">
        <label>Peserta *</label>
        <select name="participant_id" required>
          <option value="">-- Pilih Peserta --</option>
          @foreach($participants as $p)
            <option value="{{ $p->id }}" @selected(old('participant_id', $enrollment->participant_id ?? request('participant_id', '')) == $p->id)>{{ $p->name }} @if($p->nik) ({{ $p->nik }}) @endif</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Angkatan *</label>
        <select name="batch_id" required>
          <option value="">-- Pilih Angkatan --</option>
          @foreach($batches as $b)
            <option value="{{ $b->id }}" @selected(old('batch_id', $enrollment->batch_id ?? request('batch_id', '')) == $b->id)>{{ $b->program->name ?? '-' }} — {{ $b->code }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Status *</label>
        <select name="status" required>
          @foreach(\App\Models\Enrollment::STATUSES as $s)
            <option value="{{ $s }}" @selected(old('status', $enrollment->status ?? 'registered') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Jenis Pendaftaran *</label>
        <select name="registration_type" id="regType" required onchange="toggleCompanyField()">
          @php $regType = old('registration_type', $enrollment->registration_type ?? 'mandiri'); @endphp
          <option value="mandiri" @selected($regType === 'mandiri')>Mandiri</option>
          <option value="perusahaan" @selected($regType === 'perusahaan')>Dikirim Perusahaan</option>
        </select>
      </div>
      <div class="form-group" id="companyField" style="{{ $regType === 'perusahaan' ? '' : 'display:none' }}">
        <label>Perusahaan Pengirim *</label>
        <select name="company_id">
          <option value="">-- Pilih Perusahaan --</option>
          @foreach($companies as $c)
            <option value="{{ $c->id }}" @selected(old('company_id', $enrollment->company_id ?? '') == $c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
        @if($companies->isEmpty())
        <div class="text-muted" style="margin-top:.3rem">Belum ada perusahaan mitra. <a href="{{ route('companies.create') }}">Tambah perusahaan</a> dulu.</div>
        @endif
      </div>
      <div class="form-group">
        <label>Nilai Akhir</label>
        <input type="number" name="final_score" step="0.01" min="0" max="100" value="{{ old('final_score', $enrollment->final_score ?? '') }}">
      </div>
      <div class="form-group">
        <label>Tanggal Lulus</label>
        <input type="date" name="graduation_date" value="{{ old('graduation_date', ($enrollment->graduation_date ?? null)?->format('Y-m-d')) }}">
      </div>
      <div class="form-group">
        <label>No. Sertifikat</label>
        <input type="text" name="certificate_no" value="{{ old('certificate_no', $enrollment->certificate_no ?? '') }}">
      </div>
      <div class="form-group full">
        <label>Catatan</label>
        <textarea name="notes">{{ old('notes', $enrollment->notes ?? '') }}</textarea>
      </div>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Pendaftaran' }}</button>
      <a href="{{ route('enrollments.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
function toggleCompanyField() {
  var type = document.getElementById('regType').value;
  var field = document.getElementById('companyField');
  var select = field.querySelector('select[name=company_id]');
  if (type === 'perusahaan') {
    field.style.display = '';
    select.required = true;
  } else {
    field.style.display = 'none';
    select.required = false;
    select.value = '';
  }
}
document.addEventListener('DOMContentLoaded', toggleCompanyField);
</script>
@endpush
@endsection
