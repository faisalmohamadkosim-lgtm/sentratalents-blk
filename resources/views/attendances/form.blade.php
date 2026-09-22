@extends('layouts.app')
@section('title', isset($attendance) ? 'Edit Absensi' : 'Tambah Absensi')
@section('content')
@php $isEdit = isset($attendance); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Absensi' : 'Tambah Absensi' }}</div>
  <a href="{{ route('attendances.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <form method="POST" action="{{ $isEdit ? route('attendances.update', $attendance) : route('attendances.store') }}">
    @csrf @if($isEdit) @method('PUT') @endif
    <div class="form-grid">
      <div class="form-group">
        <label>Pegawai *</label>
        <select name="employee_id" required {{ $isEdit ? 'disabled' : '' }}>
          @foreach($employees as $emp)
          <option value="{{ $emp->id }}" {{ old('employee_id', $attendance->employee_id ?? '') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
          @endforeach
        </select>
        @if($isEdit)<input type="hidden" name="employee_id" value="{{ $attendance->employee_id }}">@endif
      </div>
      <div class="form-group">
        <label>Tanggal *</label>
        <input type="date" name="date" value="{{ old('date', $isEdit ? $attendance->date->toDateString() : '') }}" required>
      </div>
      <div class="form-group">
        <label>Status *</label>
        <select name="status" required>
          @foreach(\App\Models\Attendance::statusOptions() as $val => $lbl)
          <option value="{{ $val }}" {{ old('status', $attendance->status ?? 'hadir') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Lembur (jam)</label>
        <input type="number" name="overtime_hours" value="{{ old('overtime_hours', $attendance->overtime_hours ?? 0) }}" min="0" step="0.5" max="24">
      </div>
      <div class="form-group">
        <label>Jam Masuk</label>
        <input type="time" name="clock_in" value="{{ old('clock_in', $isEdit ? $attendance->clock_in?->format('H:i') : '') }}">
      </div>
      <div class="form-group">
        <label>Jam Pulang</label>
        <input type="time" name="clock_out" value="{{ old('clock_out', $isEdit ? $attendance->clock_out?->format('H:i') : '') }}">
      </div>
      <div class="form-group full">
        <label>Catatan</label>
        <textarea name="notes">{{ old('notes', $attendance->notes ?? '') }}</textarea>
      </div>
    </div>
    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan' : 'Tambah' }}</button>
      <a href="{{ route('attendances.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
@endsection
