@extends('layouts.app')
@section('title','Data Peserta')
@section('content')

<div class="page-header">
  <div class="page-title">Data Peserta</div>
  <a href="{{ route('participants.create') }}" class="btn btn-primary">+ Tambah Peserta</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem;flex-wrap:wrap">
    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama / NIK..." style="max-width:280px">
    <select name="company_id" style="max-width:220px">
      <option value="">Semua Perusahaan</option>
      <option value="mandiri" {{ $companyId === 'mandiri' ? 'selected' : '' }}>Mandiri (tanpa perusahaan)</option>
      @foreach($companies as $c)
        <option value="{{ $c->id }}" {{ (string) $companyId === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
    @if($search || $companyId)<a href="{{ route('participants.index') }}" class="btn btn-outline btn-sm">Reset</a>@endif
  </form>

  <table>
    <thead><tr><th>#</th><th>Nama</th><th>NIK</th><th>L/P</th><th>Pendidikan</th><th>No. HP</th><th>Perusahaan</th><th></th></tr></thead>
    <tbody>
      @forelse($participants as $p)
      <tr>
        <td style="color:var(--text3)">{{ $p->id }}</td>
        <td><a href="{{ route('participants.show', $p) }}" style="font-weight:600">{{ $p->name }}</a></td>
        <td>{{ $p->nik ?? '-' }}</td>
        <td>{{ $p->gender ?? '-' }}</td>
        <td style="font-size:.82rem">{{ $p->education ?? '-' }}</td>
        <td style="font-size:.82rem">{{ $p->phone ?? '-' }}</td>
        <td style="font-size:.82rem">
          @if($p->latestEnrollment?->company)
            <span class="badge badge-blue">{{ $p->latestEnrollment->company->name }}</span>
          @else
            <span class="badge badge-gray">Mandiri</span>
          @endif
        </td>
        <td style="white-space:nowrap">
          <a href="{{ route('participants.edit', $p) }}" class="btn btn-outline btn-sm">Edit</a>
          <form method="POST" action="{{ route('participants.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Hapus peserta ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" style="text-align:center;color:var(--text3);padding:2rem">Belum ada data peserta.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($participants->hasPages())
  <div class="pagination">{!! $participants->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
