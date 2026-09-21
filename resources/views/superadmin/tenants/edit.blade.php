@extends('layouts.superadmin')
@section('title','Kelola BLK')
@section('content')

<div class="page-header">
  <div class="page-title">{{ $tenant->name }}</div>
  <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card" style="max-width:640px">
  <div class="card-title">Profil BLK</div>
  <form method="POST" action="{{ route('superadmin.tenants.update', $tenant) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="form-group full">
      <label>Nama BLK *</label>
      <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required>
    </div>
    <div class="form-group full">
      <label>Deskripsi / Sambutan</label>
      <textarea name="description" rows="3">{{ old('description', $tenant->description) }}</textarea>
    </div>
    <div class="form-group full">
      <label>Alamat</label>
      <textarea name="address">{{ old('address', $tenant->address) }}</textarea>
    </div>
    <div class="form-grid">
      <div class="form-group">
        <label>Kota</label>
        <input type="text" name="city" value="{{ old('city', $tenant->city) }}">
      </div>
      <div class="form-group">
        <label>Provinsi</label>
        <input type="text" name="province" value="{{ old('province', $tenant->province) }}">
      </div>
      <div class="form-group">
        <label>Telepon</label>
        <input type="tel" name="phone" value="{{ old('phone', $tenant->phone) }}">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $tenant->email) }}">
      </div>
      <div class="form-group">
        <label>Website</label>
        <input type="url" name="website" value="{{ old('website', $tenant->website) }}">
      </div>
      <div class="form-group">
        <label>Kode BLK</label>
        <input type="text" name="code" value="{{ old('code', $tenant->code) }}">
      </div>
      <div class="form-group">
        <label>Tahun Berdiri</label>
        <input type="number" name="established_year" value="{{ old('established_year', $tenant->established_year) }}" min="1900" max="{{ date('Y') + 1 }}">
      </div>
      <div class="form-group">
        <label>Akreditasi</label>
        <input type="text" name="accreditation" value="{{ old('accreditation', $tenant->accreditation) }}">
      </div>
    </div>
    <div class="form-group full">
      <label>Logo</label>
      @if($tenant->logoUrl())
        <div style="margin-bottom:.5rem"><img src="{{ $tenant->logoUrl() }}" style="height:48px;object-fit:contain;border-radius:4px;border:1px solid var(--border)"></div>
      @endif
      <input type="file" name="logo" accept="image/*">
    </div>
    <div class="form-group full">
      <label>Gambar Sampul</label>
      @if($tenant->coverImageUrl())
        <div style="margin-bottom:.5rem"><img src="{{ $tenant->coverImageUrl() }}" style="height:80px;object-fit:cover;border-radius:4px;border:1px solid var(--border)"></div>
      @endif
      <input type="file" name="cover_image" accept="image/*">
    </div>
    <div class="form-group" style="display:flex;align-items:center;gap:.5rem">
      <input type="hidden" name="active" value="0">
      <input type="checkbox" id="active" name="active" value="1" style="width:auto" @checked(old('active', $tenant->active)) >
      <label for="active" style="margin:0">Aktif</label>
    </div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
  </form>
</div>

<div class="card" style="max-width:640px">
  <div class="card-title">Halaman Publik</div>
  <a href="{{ route('public.tenant.show', $tenant->slug) }}" target="_blank" class="btn btn-outline btn-sm">🌐 Lihat Halaman Publik →</a>
</div>

<div class="card" style="max-width:640px">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <div>
      <div class="card-title" style="margin-bottom:.25rem">Admin BLK Ini</div>
      <p class="text-muted">Superadmin hanya berwenang membuat admin pertama BLK. Pengelolaan user lain dilakukan oleh admin BLK sendiri.</p>
    </div>
    <a href="{{ route('superadmin.tenants.users.create', $tenant) }}" class="btn btn-primary btn-sm" style="flex-shrink:0">+ Tambah Admin</a>
  </div>
</div>
@endsection
