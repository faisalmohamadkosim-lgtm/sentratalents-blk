@extends('layouts.app')
@section('title', $program->name)
@section('content')

<div class="page-header">
  <div class="page-title">{{ $program->name }}</div>
  <div style="display:flex;gap:.5rem">
    <a href="{{ route('programs.edit', $program) }}" class="btn btn-outline btn-sm">Edit</a>
    <a href="{{ route('programs.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
  </div>
</div>

<div class="card">
  <div class="form-grid">
    <div><span class="text-muted">Kategori</span><div style="margin-top:.25rem;font-weight:600">{{ $program->category ?? '-' }}</div></div>
    <div><span class="text-muted">Trainer Utama</span><div style="margin-top:.25rem">{{ $program->leadTrainer?->name ?? '-' }}</div></div>
    <div><span class="text-muted">Durasi</span><div style="margin-top:.25rem">{{ $program->duration_days ? $program->duration_days.' hari' : '-' }}</div></div>
    <div><span class="text-muted">Kuota per Angkatan</span><div style="margin-top:.25rem">{{ $program->quota ?? '-' }}</div></div>
    <div class="full"><span class="text-muted">Deskripsi</span><div style="margin-top:.25rem">{{ $program->description ?? '-' }}</div></div>
  </div>
</div>

{{-- Tahapan / kurikulum program --}}
<div class="card">
  <div class="card-title">Tahapan Pelatihan (Kurikulum)</div>

  @if($program->stages->isEmpty())
    <p style="color:var(--text3);font-size:.85rem;margin-bottom:1rem">Belum ada tahapan.</p>
  @else
  <table style="margin-bottom:1.25rem">
    <thead><tr><th style="width:50px">Urutan</th><th>Judul</th><th>Deskripsi</th><th style="width:90px">Durasi</th><th style="width:140px"></th></tr></thead>
    <tbody>
      @foreach($program->stages as $stage)
      <tr>
        <form method="POST" action="{{ route('programs.stages.update', [$program, $stage]) }}">
        @csrf @method('PUT')
        <td><input type="number" name="sort_order" value="{{ $stage->sort_order }}" min="0" style="width:70px"></td>
        <td><input type="text" name="title" value="{{ $stage->title }}" required></td>
        <td><input type="text" name="description" value="{{ $stage->description }}"></td>
        <td><input type="number" name="duration_days" value="{{ $stage->duration_days }}" min="0" style="width:80px"></td>
        <td style="white-space:nowrap">
          <button type="submit" class="btn btn-outline btn-sm">Simpan</button>
        </td>
        </form>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  <div style="border-top:1px solid var(--border);padding-top:1rem">
    <div style="font-weight:700;font-size:.85rem;margin-bottom:.6rem">+ Tambah Tahapan</div>
    <form method="POST" action="{{ route('programs.stages.store', $program) }}" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:flex-end">
      @csrf
      <div class="form-group" style="margin-bottom:0;min-width:160px">
        <label>Judul *</label>
        <input type="text" name="title" required>
      </div>
      <div class="form-group" style="margin-bottom:0;flex:1;min-width:200px">
        <label>Deskripsi</label>
        <input type="text" name="description">
      </div>
      <div class="form-group" style="margin-bottom:0;width:110px">
        <label>Durasi (hari)</label>
        <input type="number" name="duration_days" min="0">
      </div>
      <div class="form-group" style="margin-bottom:0;width:90px">
        <label>Urutan</label>
        <input type="number" name="sort_order" min="0">
      </div>
      <button type="submit" class="btn btn-primary">Tambah</button>
    </form>
  </div>
</div>

{{-- Daftar tahapan dengan tombol hapus (tabel terpisah supaya form update/hapus tidak bersarang) --}}
@if($program->stages->isNotEmpty())
<div class="card">
  <div class="card-title" style="font-size:.85rem">Hapus Tahapan</div>
  <table>
    <thead><tr><th>Judul</th><th style="width:100px"></th></tr></thead>
    <tbody>
      @foreach($program->stages as $stage)
      <tr>
        <td>{{ $stage->title }}</td>
        <td>
          <form method="POST" action="{{ route('programs.stages.destroy', [$program, $stage]) }}" onsubmit="return confirm('Hapus tahapan {{ $stage->title }}?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

<div class="card">
  <div class="card-title">Angkatan</div>
  @if($program->batches->isEmpty())
    <p style="color:var(--text3);font-size:.85rem">Belum ada angkatan untuk program ini.</p>
  @else
  <table>
    <thead><tr><th>Kode</th><th>Jadwal</th><th>Status</th><th style="text-align:right">Peserta</th><th></th></tr></thead>
    <tbody>
      @foreach($program->batches as $batch)
      <tr>
        <td style="font-weight:600">{{ $batch->code }}</td>
        <td style="font-size:.82rem">{{ $batch->start_date?->format('d/m/Y') ?? '-' }} — {{ $batch->end_date?->format('d/m/Y') ?? '-' }}</td>
        <td><span class="badge {{ match($batch->status) { 'ongoing' => 'badge-blue', 'completed' => 'badge-green', 'cancelled' => 'badge-red', default => 'badge-gray' } }}">{{ $batch->status }}</span></td>
        <td style="text-align:right">{{ $batch->participantCount() }}</td>
        <td><a href="{{ route('batches.show', $batch) }}" class="btn btn-outline btn-sm">Detail</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif
  <div style="margin-top:1rem"><a href="{{ route('batches.create') }}" class="btn btn-primary btn-sm">+ Tambah Angkatan</a></div>
</div>
@endsection
