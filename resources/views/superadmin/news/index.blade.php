@extends('layouts.superadmin')
@section('title','Berita Landing Page')
@section('content')

<div class="page-header">
  <div class="page-title">Berita Landing Page</div>
  <a href="{{ route('superadmin.news.create') }}" class="btn btn-primary">+ Tambah Berita</a>
</div>

<div class="card">
  <p class="text-muted" style="margin-bottom:1rem">Daftar berita yang tampil bisa digeser horizontal di halaman direktori publik ({{ url('/') }}). Urutan mengikuti kolom Urutan (kecil ke besar).</p>

  <div style="overflow-x:auto">
  <table>
    <thead><tr><th style="width:90px">Gambar</th><th>Judul</th><th>Tanggal</th><th>Tautan</th><th style="width:70px">Urutan</th><th>Status</th><th></th></tr></thead>
    <tbody>
      @forelse($news as $n)
      <tr>
        <td>
          @if($n->imageUrl())
            <img src="{{ $n->imageUrl() }}" style="width:72px;height:54px;object-fit:cover;border-radius:6px">
          @else
            <div style="width:72px;height:54px;border-radius:6px;background:var(--surface2);display:flex;align-items:center;justify-content:center;color:var(--text3)">📰</div>
          @endif
        </td>
        <td style="font-weight:600">{{ $n->title }}</td>
        <td style="font-size:.82rem">{{ $n->published_at?->format('d/m/Y') ?? '-' }}</td>
        <td style="font-size:.78rem;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
          @if($n->link_url)
            <a href="{{ $n->link_url }}" target="_blank" rel="noopener noreferrer">{{ $n->link_url }}</a>
          @else
            <span class="text-muted">Tanpa tautan</span>
          @endif
        </td>
        <td>{{ $n->sort_order }}</td>
        <td><span class="badge {{ $n->active ? 'badge-green' : 'badge-gray' }}">{{ $n->active ? 'Aktif' : 'Nonaktif' }}</span></td>
        <td style="white-space:nowrap">
          <a href="{{ route('superadmin.news.edit', $n) }}" class="btn btn-outline btn-sm">Edit</a>
          <form method="POST" action="{{ route('superadmin.news.destroy', $n) }}" style="display:inline" onsubmit="return confirm('Hapus berita ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:var(--text3);padding:2rem">Belum ada berita.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($news->hasPages())
  <div class="pagination">{!! $news->links('vendor.pagination.simple') !!}</div>
  @endif
  </div>
</div>
@endsection
