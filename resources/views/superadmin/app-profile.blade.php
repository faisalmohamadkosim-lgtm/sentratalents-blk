@extends('layouts.superadmin')
@section('title','Profil Aplikasi')
@section('content')

<div class="page-header">
  <div class="page-title">Profil Aplikasi</div>
  <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline btn-sm">← Dashboard</a>
</div>

<div class="card" style="max-width:520px">
  <div class="card-title">Identitas Aplikasi</div>
  <p class="text-muted" style="margin-bottom:1rem">Kode & nama aplikasi, serta teks yang tampil di halaman login.</p>
  <form method="POST" action="{{ route('superadmin.app-profile.update') }}">
    @csrf
    @method('put')

    <div class="form-group">
      <label>Kode Aplikasi</label>
      <input type="text" name="kode_aplikasi" value="{{ old('kode_aplikasi', $settings['kode_aplikasi']) }}">
    </div>

    <div class="form-group">
      <label>Brand (Nama Aplikasi)</label>
      <input type="text" name="nama_aplikasi" value="{{ old('nama_aplikasi', $settings['nama_aplikasi'] ?? 'BLK Connect') }}">
      <div class="text-muted" style="margin-top:.3rem">Ditampilkan di judul halaman landing, badge, dan halaman login.</div>
    </div>

    <div class="form-group">
      <label>Tagline Platform</label>
      <input type="text" name="tagline" value="{{ old('tagline', $settings['tagline'] ?? '') }}" placeholder="mis. Platform Pelatihan Kerja Terpadu">
      <div class="text-muted" style="margin-top:.3rem">Teks singkat pendamping brand, ditampilkan di sebelah nama aplikasi di halaman direktori publik.</div>
    </div>

    <div class="form-group">
      <label>Institusi/Perusahaan</label>
      <input type="text" name="institusi" value="{{ old('institusi', $settings['institusi'] ?? '') }}">
      <div class="text-muted" style="margin-top:.3rem">Nama institusi/perusahaan pengelola, ditampilkan di footer halaman direktori publik.</div>
    </div>

    <div class="form-group">
      <label>Judul Hero Login</label>
      <input type="text" name="login_hero_title" value="{{ old('login_hero_title', $settings['login_hero_title'] ?? 'Platform Balai Latihan Kerja') }}">
    </div>

    <div class="form-group">
      <label>Deskripsi Hero Login</label>
      <textarea name="login_hero_text" rows="3">{{ old('login_hero_text', $settings['login_hero_text'] ?? 'Sistem informasi terpadu untuk BLK -- profil, program pelatihan, dan data lulusan.') }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
    @if (session('success'))
      <span style="color:#16a34a;font-size:.83rem;margin-left:.5rem">{{ session('success') }}</span>
    @endif
  </form>
</div>
@endsection
