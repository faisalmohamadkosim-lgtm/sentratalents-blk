@extends('layouts.superadmin')
@section('title','Galeri Landing Page')
@section('content')

<div class="page-header">
  <div class="page-title">Galeri Landing Page</div>
  <a href="{{ route('superadmin.gallery.create') }}" class="btn btn-primary">+ Tambah Foto</a>
</div>

<div class="card">
  <p class="text-muted" style="margin-bottom:1rem">Foto yang tampil di bagian "Galeri Kegiatan BLK" pada halaman direktori publik ({{ url('/') }}). Urutan mengikuti kolom Urutan (kecil ke besar).</p>

  <div style="overflow-x:auto">
  <table>
    <thead><tr><th style="width:90px">Foto</th><th>Caption</th><th>Tautan</th><th style="width:70px">Urutan</th><th>Status</th><th></th></tr></thead>
    <tbody>
      @forelse($photos as $p)
      <tr>
        <td>
          <img src="{{ $p->photoUrl() }}" style="width:72px;height:54px;object-fit:cover;border-radius:6px">
        </td>
        <td style="font-weight:600">{{ $p->caption ?? '-' }}</td>
        <td style="font-size:.78rem;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
          @if($p->link_url)
            <a href="{{ $p->link_url }}" target="_blank" rel="noopener noreferrer">{{ $p->link_url }}</a>
          @else
            <span class="text-muted">Tanpa tautan</span>
          @endif
        </td>
        <td>{{ $p->sort_order }}</td>
        <td><span class="badge {{ $p->active ? 'badge-green' : 'badge-gray' }}">{{ $p->active ? 'Aktif' : 'Nonaktif' }}</span></td>
        <td style="white-space:nowrap">
          <a href="{{ route('superadmin.gallery.edit', $p) }}" class="btn btn-outline btn-sm">Edit</a>
          <form method="POST" action="{{ route('superadmin.gallery.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Hapus foto ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;color:var(--text3);padding:2rem">Belum ada foto galeri.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($photos->hasPages())
  <div class="pagination">{!! $photos->links('vendor.pagination.simple') !!}</div>
  @endif
  </div>
</div>
@endsection
