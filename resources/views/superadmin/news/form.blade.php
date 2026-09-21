@extends('layouts.superadmin')
@section('title', isset($article) ? 'Edit Berita' : 'Tambah Berita')
@section('content')
@php $isEdit = isset($article); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Berita' : 'Tambah Berita' }}</div>
  <a href="{{ route('superadmin.news.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card" style="max-width:560px">
  <form method="POST" action="{{ $isEdit ? route('superadmin.news.update', $article) : route('superadmin.news.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-group">
      <label>Judul *</label>
      <input type="text" name="title" value="{{ old('title', $article->title ?? '') }}" required>
    </div>

    <div class="form-group">
      <label>Ringkasan</label>
      <textarea name="excerpt" rows="3">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
    </div>

    <div class="form-group">
      <label>Gambar {{ $isEdit ? '' : '' }}</label>
      @if($isEdit && $article->imageUrl())
      <div style="margin-bottom:.5rem;display:flex;align-items:center;gap:.75rem">
        <img src="{{ $article->imageUrl() }}" style="height:56px;width:80px;object-fit:cover;border-radius:6px">
        <label style="display:flex;align-items:center;gap:.3rem;font-size:.8rem;cursor:pointer">
          <input type="checkbox" name="delete_image" value="1"> Hapus gambar
        </label>
      </div>
      @endif
      <input type="file" name="image" accept="image/*">
      <div class="text-muted" style="margin-top:.3rem">Rasio disarankan 4:3, maks 2MB.</div>
    </div>

    <div class="form-group">
      <label>Tanggal Terbit</label>
      <input type="date" name="published_at" value="{{ old('published_at', ($article->published_at ?? null)?->format('Y-m-d')) }}">
    </div>

    <div class="form-group">
      <label>Tautan (opsional)</label>
      <input type="text" name="link_url" value="{{ old('link_url', $article->link_url ?? '') }}" placeholder="mis. /blk/blk-bandung atau https://...">
      <div class="text-muted" style="margin-top:.3rem">Kosongkan supaya berita tidak bisa diklik. Bisa path relatif atau URL lengkap.</div>
    </div>

    <div class="form-group">
      <label>Urutan</label>
      <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $article->sort_order ?? 0) }}" style="max-width:120px">
    </div>

    <div class="form-group" style="display:flex;align-items:center;gap:.5rem">
      <input type="hidden" name="active" value="0">
      <input type="checkbox" id="active" name="active" value="1" style="width:auto" @checked(old('active', $article->active ?? true))>
      <label for="active" style="margin:0">Aktif (tampil di landing page)</label>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Berita' }}</button>
      <a href="{{ route('superadmin.news.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
@endsection
