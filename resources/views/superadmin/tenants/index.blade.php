@extends('layouts.superadmin')
@section('title','Tenant BLK')
@section('content')

<div class="page-header">
  <div class="page-title">Tenant BLK</div>
  <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary">+ Daftarkan BLK</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem">
    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama BLK..." style="max-width:280px">
    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
    @if($search)<a href="{{ route('superadmin.tenants.index') }}" class="btn btn-outline btn-sm">Reset</a>@endif
  </form>

  <div style="overflow-x:auto">
  <table>
    <thead>
      <tr>
        <th>BLK</th>
        <th>Kode</th>
        <th>Kota</th>
        <th style="text-align:right">User</th>
        <th style="text-align:right">Peserta</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($tenants as $tenant)
      <tr>
        <td style="font-weight:600">{{ $tenant->name }}</td>
        <td style="font-family:monospace;font-size:.8rem">{{ $tenant->code ?? '—' }}</td>
        <td>{{ $tenant->city ?? '-' }}</td>
        <td style="text-align:right">{{ $tenant->users_count }}</td>
        <td style="text-align:right">{{ $tenant->participants_count }}</td>
        <td>
          @if($tenant->active)
            <span class="badge badge-green">Aktif</span>
          @else
            <span class="badge badge-gray">Nonaktif</span>
          @endif
        </td>
        <td><a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="btn btn-outline btn-sm">Kelola</a></td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:var(--text3);padding:2rem">Belum ada BLK terdaftar.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($tenants->hasPages())
  <div class="pagination">{!! $tenants->links('vendor.pagination.simple') !!}</div>
  @endif
  </div>
</div>
@endsection
