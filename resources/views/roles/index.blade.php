@extends('layouts.app')
@section('title','Manajemen Role')
@section('content')

<div class="page-header">
  <div class="page-title">Manajemen Role</div>
  <a href="{{ route('roles.create') }}" class="btn btn-primary">+ Tambah Role</a>
</div>

<div class="card" style="margin-bottom:1rem">
  <div style="overflow-x:auto">
    <table>
      <thead><tr>
        <th>Nama Role</th>
        <th>Label</th>
        <th>Deskripsi</th>
        <th style="text-align:center">Admin</th>
        <th style="text-align:center">Pengguna</th>
        <th>Status</th>
        <th style="width:120px"></th>
      </tr></thead>
      <tbody>
      @foreach($roles as $role)
      <tr>
        <td style="font-weight:700;font-family:monospace;font-size:.82rem">{{ $role->name }}</td>
        <td style="font-weight:600">{{ $role->label ?: '—' }}</td>
        <td style="font-size:.78rem;color:var(--text2);max-width:260px">{{ Str::limit($role->description ?? '', 80) ?: '—' }}</td>
        <td style="text-align:center">{!! $role->can_admin ? '<span style="color:var(--success)">✓</span>' : '<span style="color:var(--border)">—</span>' !!}</td>
        <td style="text-align:center">
          @if($role->user_count > 0)
          <span style="background:var(--primary);color:#fff;border-radius:12px;padding:.1rem .6rem;font-size:.72rem;font-weight:700">{{ $role->user_count }}</span>
          @else
          <span style="color:var(--border)">0</span>
          @endif
        </td>
        <td>
          <form method="POST" action="{{ route('roles.toggle', $role->id) }}" style="display:inline">
            @csrf
            <button type="submit" class="badge {{ $role->active ? 'badge-green' : 'badge-red' }}" style="border:none;cursor:pointer">
              {{ $role->active ? 'Aktif' : 'Nonaktif' }}
            </button>
          </form>
        </td>
        <td>
          <div style="display:flex;gap:.4rem">
            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-outline btn-sm">Edit</a>
            @if(!in_array($role->name, ['superadmin','ultraadmin','admin','owner','staff']))
            <form method="POST" action="{{ route('roles.destroy', $role->id) }}" onsubmit="return confirm('Hapus role ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm">Hapus</button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="card" style="font-size:.8rem;color:var(--text2);padding:.875rem 1rem">
  <strong>Keterangan akses:</strong>
  Admin BLK/Owner = mengelola profil dan data BLK sepenuhnya &nbsp;|&nbsp; Staff = input data harian (peserta, pendaftaran angkatan, dll)
</div>
@endsection
