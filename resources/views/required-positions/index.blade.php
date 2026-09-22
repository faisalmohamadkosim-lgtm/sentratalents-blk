@extends('layouts.app')
@section('title', 'Jabatan Minimal')
@section('content')

<div class="page-header">
  <div class="page-title">Jabatan Minimal BLK</div>
  <div style="display:flex;gap:.5rem">
    <form method="POST" action="{{ route('required-positions.load-defaults') }}">
      @csrf
      <button type="submit" class="btn btn-outline">📋 Muat Contoh Standar</button>
    </form>
    <a href="{{ route('required-positions.create') }}" class="btn btn-primary">+ Tambah Jabatan</a>
  </div>
</div>

<div class="card">
  <div class="text-muted">
    Daftar posisi minimum yang sebaiknya dimiliki BLK ini, dicek otomatis dari data Karyawan &amp; Instruktur/Trainer aktif (pencocokan nama jabatan, tidak sempurna -- periksa manual kalau statusnya meragukan).
    "Muat Contoh Standar" mengisi contoh umum posisi minimal BLK -- <strong>bukan nasihat hukum/perizinan resmi</strong>, sesuaikan dengan jenis, skala, dan regulasi yang berlaku untuk BLK Anda.
  </div>
</div>

<div class="card">
  <table>
    <thead><tr><th>Jabatan</th><th style="text-align:center">Min.</th><th style="text-align:center">Terpenuhi</th><th>Status</th><th>Catatan</th><th></th></tr></thead>
    <tbody>
      @forelse($positions as $p)
      <tr>
        <td>{{ $p->position_name }} @if(! $p->active)<span class="badge badge-gray" style="margin-left:.3rem">Non-aktif</span>@endif</td>
        <td style="text-align:center">{{ $p->min_count }}</td>
        <td style="text-align:center">{{ $p->fulfilledCount() }}</td>
        <td>
          @if($p->isFulfilled())
          <span class="badge badge-green">✔ Terpenuhi</span>
          @else
          <span class="badge badge-red">✘ Kurang</span>
          @endif
        </td>
        <td class="text-muted" style="font-size:.8rem">{{ $p->notes ?? '-' }}</td>
        <td style="white-space:nowrap">
          <a href="{{ route('required-positions.edit', $p) }}" class="btn btn-outline btn-sm">Edit</a>
          <form method="POST" action="{{ route('required-positions.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Hapus jabatan ini dari daftar?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;color:var(--text3);padding:1.5rem">Belum ada daftar jabatan minimal. Klik "Muat Contoh Standar" atau "+ Tambah Jabatan".</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
