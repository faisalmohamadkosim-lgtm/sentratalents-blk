@extends('layouts.app')
@section('title', 'Generate Payroll')
@section('content')

<div class="page-header">
  <div class="page-title">Generate Payroll</div>
  <a href="{{ route('payroll.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <div class="text-muted" style="margin-bottom:1rem">
    Sistem akan menghitung gaji seluruh pegawai berstatus <strong>Aktif</strong> untuk periode yang dipilih,
    berdasarkan komponen gaji di Data Karyawan dan rekap absensi bulan tersebut (BPJS &amp; estimasi PPh21 dihitung otomatis).
  </div>
  <form method="POST" action="{{ route('payroll.store') }}">
    @csrf
    <div class="form-grid">
      <div class="form-group">
        <label>Bulan *</label>
        <select name="period_month" required>
          @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $namaBulan)
          <option value="{{ $i+1 }}" {{ (int) old('period_month', now()->month) === $i+1 ? 'selected' : '' }}>{{ $namaBulan }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Tahun *</label>
        <input type="number" name="period_year" value="{{ old('period_year', now()->year) }}" min="2020" max="2100" required>
      </div>
    </div>
    <button type="submit" class="btn btn-primary" onclick="return confirm('Generate payroll untuk periode ini? Pastikan absensi bulan tersebut sudah lengkap diinput.')">⚙ Generate Payroll</button>
  </form>
</div>
@endsection
