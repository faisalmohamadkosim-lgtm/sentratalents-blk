@extends('layouts.app')
@section('title', isset($batch) ? 'Edit Angkatan' : 'Tambah Angkatan')
@section('content')
@php $isEdit = isset($batch); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Angkatan' : 'Tambah Angkatan' }}</div>
  <a href="{{ route('batches.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <form method="POST" action="{{ $isEdit ? route('batches.update', $batch) : route('batches.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="form-group">
        <label>Program *</label>
        <select name="program_id" required>
          <option value="">-- Pilih Program --</option>
          @foreach($programs as $p)
            <option value="{{ $p->id }}" @selected(old('program_id', $batch->program_id ?? '') == $p->id)>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Kode Angkatan *</label>
        <input type="text" name="code" value="{{ old('code', $batch->code ?? '') }}" placeholder="mis. 2026-A" required>
      </div>
      <div class="form-group">
        <label>Tanggal Mulai</label>
        <input type="date" name="start_date" value="{{ old('start_date', ($batch->start_date ?? null)?->format('Y-m-d')) }}">
      </div>
      <div class="form-group">
        <label>Tanggal Selesai</label>
        <input type="date" name="end_date" value="{{ old('end_date', ($batch->end_date ?? null)?->format('Y-m-d')) }}">
      </div>
      <div class="form-group">
        <label>Kuota</label>
        <input type="number" name="quota" value="{{ old('quota', $batch->quota ?? '') }}" min="0">
      </div>
      <div class="form-group">
        <label>Status *</label>
        <select name="status" required>
          @foreach(\App\Models\Batch::STATUSES as $s)
            <option value="{{ $s }}" @selected(old('status', $batch->status ?? 'planned') === $s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Angkatan' }}</button>
      <a href="{{ route('batches.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
@endsection
