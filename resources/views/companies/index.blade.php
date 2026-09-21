@extends('layouts.app')
@section('title','Perusahaan')
@section('content')

<div class="page-header">
  <div class="page-title">Perusahaan Mitra</div>
  <a href="{{ route('companies.create') }}" class="btn btn-primary">+ Tambah Perusahaan</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem">
    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama perusahaan..." style="max-width:280px">
    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
    @if($search)<a href="{{ route('companies.index') }}" class="btn btn-outline btn-sm">Reset</a>@endif
  </form>

  <table>
    <thead><tr><th>Nama</th><th>Kontak</th><th>Telepon</th><th>Peserta Dikirim</th><th>Status</th><th></th></tr></thead>
    <tbody>
      @forelse($companies as $c)
      <tr>
        <td style="font-weight:600">{{ $c->name }}</td>
        <td style="font-size:.82rem">{{ $c->contact_person ?? '-' }}</td>
        <td style="font-size:.82rem">{{ $c->phone ?? '-' }}</td>
        <td>{{ $c->enrollments_count }}</td>
        <td><span class="badge {{ $c->active ? 'badge-green' : 'badge-gray' }}">{{ $c->active ? 'Aktif' : 'Nonaktif' }}</span></td>
        <td style="white-space:nowrap">
          <a href="{{ route('companies.users.create', $c) }}" class="btn btn-outline btn-sm">+ Akun Login</a>
          <a href="{{ route('companies.edit', $c) }}" class="btn btn-outline btn-sm">Edit</a>
          <form method="POST" action="{{ route('companies.destroy', $c) }}" style="display:inline" onsubmit="return confirm('Hapus perusahaan ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;color:var(--text3);padding:2rem">Belum ada perusahaan mitra.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($companies->hasPages())
  <div class="pagination">{!! $companies->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
