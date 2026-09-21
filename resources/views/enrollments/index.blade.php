@extends('layouts.app')
@section('title','Pendaftaran/Enrollment')
@section('content')

<div class="page-header">
  <div class="page-title">Pendaftaran / Enrollment</div>
  <a href="{{ route('enrollments.create') }}" class="btn btn-primary">+ Tambah Pendaftaran</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem;flex-wrap:wrap">
    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama peserta..." style="max-width:240px">
    <select name="status" style="max-width:180px">
      <option value="">Semua Status</option>
      @foreach(\App\Models\Enrollment::STATUSES as $s)
        <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
    @if($search || $status)<a href="{{ route('enrollments.index') }}" class="btn btn-outline btn-sm">Reset</a>@endif
  </form>

  <table>
    <thead><tr><th>Peserta</th><th>Program</th><th>Angkatan</th><th>Perusahaan</th><th>Status</th><th>Tgl Lulus</th><th></th></tr></thead>
    <tbody>
      @forelse($enrollments as $e)
      <tr>
        <td><a href="{{ route('participants.show', $e->participant) }}" style="font-weight:600">{{ $e->participant->name ?? '-' }}</a></td>
        <td style="font-size:.82rem">{{ $e->batch->program->name ?? '-' }}</td>
        <td style="font-size:.82rem">{{ $e->batch->code ?? '-' }}</td>
        <td style="font-size:.82rem">
          @if($e->company)
            <span class="badge badge-blue">{{ $e->company->name }}</span>
          @else
            <span class="badge badge-gray">Mandiri</span>
          @endif
        </td>
        <td><span class="badge {{ $e->statusBadge() }}">{{ $e->status }}</span></td>
        <td style="font-size:.82rem">{{ $e->graduation_date?->format('d/m/Y') ?? '-' }}</td>
        <td style="white-space:nowrap">
          @if($e->status !== 'graduated')
          <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('grad-modal-{{ $e->id }}').style.display='flex'">Tandai Lulus</button>
          @endif
          <a href="{{ route('enrollments.edit', $e) }}" class="btn btn-outline btn-sm">Edit</a>
          <form method="POST" action="{{ route('enrollments.destroy', $e) }}" style="display:inline" onsubmit="return confirm('Hapus pendaftaran ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
          </form>

          {{-- Modal kecil "Tandai Lulus" -- nilai akhir & no sertifikat opsional --}}
          <div id="grad-modal-{{ $e->id }}" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center">
            <div class="card" style="max-width:380px;width:90%;margin:0">
              <div class="card-title">Tandai Lulus — {{ $e->participant->name ?? '' }}</div>
              <form method="POST" action="{{ route('enrollments.graduate', $e) }}">
                @csrf
                <div class="form-group">
                  <label>Tanggal Lulus</label>
                  <input type="date" name="graduation_date" value="{{ now()->toDateString() }}">
                </div>
                <div class="form-group">
                  <label>Nilai Akhir</label>
                  <input type="number" name="final_score" step="0.01" min="0" max="100">
                </div>
                <div class="form-group">
                  <label>No. Sertifikat</label>
                  <input type="text" name="certificate_no">
                </div>
                <div style="display:flex;gap:.5rem">
                  <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                  <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('grad-modal-{{ $e->id }}').style.display='none'">Batal</button>
                </div>
              </form>
            </div>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:var(--text3);padding:2rem">Belum ada pendaftaran.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($enrollments->hasPages())
  <div class="pagination">{!! $enrollments->links('vendor.pagination.simple') !!}</div>
  @endif
</div>
@endsection
