@extends('layouts.app')
@section('title','Pengguna')
@section('content')

<div class="page-header">
  <div class="page-title">Pengguna</div>
  <a href="{{ route('users.create') }}" class="btn btn-primary">+ Tambah Pengguna</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem">
    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama / email..." style="max-width:260px">
    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
    @if($search)<a href="{{ route('users.index') }}" class="btn btn-outline btn-sm">Reset</a>@endif
  </form>

  <table>
    <thead>
      <tr><th>#</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
      @forelse($users as $u)
      <tr>
        <td style="color:var(--text3)">{{ $u->id }}</td>
        <td>{{ $u->name }}</td>
        <td style="color:var(--text2)">{{ $u->email }}</td>
        <td><span class="badge badge-blue">{{ $u->role }}</span></td>
        <td>
          <form method="POST" action="{{ route('users.toggle', $u) }}" style="display:inline">
            @csrf
            <button type="submit" class="badge {{ $u->active ? 'badge-green' : 'badge-red' }}" style="cursor:pointer;border:none">
              {{ $u->active ? 'Aktif' : 'Nonaktif' }}
            </button>
          </form>
        </td>
        <td style="white-space:nowrap">
          <a href="{{ route('users.edit', $u) }}" class="btn btn-outline btn-sm">Edit</a>
          @if($u->id !== auth()->id())
          <form method="POST" action="{{ route('users.destroy', $u) }}" style="display:inline" onsubmit="return confirm('Hapus pengguna ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
          </form>
          @endif
        </td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;color:var(--text3);padding:2rem">Tidak ada data.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($users->hasPages())
  <div class="pagination">{!! $users->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
