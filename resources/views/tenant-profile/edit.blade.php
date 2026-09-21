@extends('layouts.app')
@section('title','Profil BLK')
@section('content')

<div class="page-header">
  <div class="page-title">Profil BLK</div>
</div>

<form method="POST" action="{{ route('tenant-profile.update') }}" enctype="multipart/form-data">
  @csrf @method('PUT')

  <div class="card" style="margin-bottom:1rem">
    <div style="font-weight:700;font-size:.9rem;margin-bottom:1rem;color:var(--primary)">Identitas BLK</div>
    <div class="form-grid">
      <div class="form-group full">
        <label>Nama BLK *</label>
        <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required>
      </div>
      <div class="form-group">
        <label>Kode BLK</label>
        <input type="text" name="code" value="{{ old('code', $tenant->code) }}">
      </div>
      <div class="form-group">
        <label>Akreditasi</label>
        <input type="text" name="accreditation" value="{{ old('accreditation', $tenant->accreditation) }}" placeholder="mis. Terakreditasi A">
      </div>
      <div class="form-group">
        <label>Tahun Berdiri</label>
        <input type="number" name="established_year" value="{{ old('established_year', $tenant->established_year) }}" min="1900" max="{{ date('Y') + 1 }}">
      </div>
      <div class="form-group full">
        <label>Deskripsi / Sambutan <span style="font-size:.75rem;color:var(--text3)">(tampil di halaman profil publik)</span></label>
        <textarea name="description" rows="3">{{ old('description', $tenant->description) }}</textarea>
      </div>
      <div class="form-group">
        <label>Logo <span style="font-size:.75rem;color:var(--text3)">(maks 1MB)</span></label>
        @if($tenant->logoUrl())
        <div style="margin-bottom:.5rem"><img src="{{ $tenant->logoUrl() }}" style="height:48px;object-fit:contain;border-radius:4px;border:1px solid var(--border)"></div>
        @endif
        <input type="file" name="logo" accept="image/*">
      </div>
      <div class="form-group">
        <label>Gambar Sampul <span style="font-size:.75rem;color:var(--text3)">(maks 2MB, tampil di halaman publik)</span></label>
        @if($tenant->coverImageUrl())
        <div style="margin-bottom:.5rem"><img src="{{ $tenant->coverImageUrl() }}" style="height:60px;object-fit:cover;border-radius:4px;border:1px solid var(--border)"></div>
        @endif
        <input type="file" name="cover_image" accept="image/*">
      </div>
    </div>
  </div>

  <div class="card" style="margin-bottom:1rem">
    <div style="font-weight:700;font-size:.9rem;margin-bottom:1rem;color:var(--primary)">Alamat &amp; Kontak</div>
    <div class="form-grid">
      <div class="form-group full">
        <label>Alamat</label>
        <textarea name="address" rows="2">{{ old('address', $tenant->address) }}</textarea>
      </div>
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
        <input type="url" name="website" value="{{ old('website', $tenant->website) }}" placeholder="https://blk.id">
      </div>
    </div>
  </div>

  <div style="display:flex;gap:.5rem">
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="{{ route('public.tenant.show', $tenant->slug) }}" target="_blank" class="btn btn-outline">🌐 Lihat Halaman Publik</a>
  </div>
</form>
@endsection
