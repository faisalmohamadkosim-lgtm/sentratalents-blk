@extends('layouts.app')
@section('title','Akun Login Perusahaan')
@section('content')

<div class="page-header">
  <div class="page-title">Akun Login — {{ $company->name }}</div>
  <a href="{{ route('companies.edit', $company) }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card" style="max-width:480px">
  <form method="POST" action="{{ route('companies.users.store', $company) }}">
    @csrf
    <div class="form-group">
      <label>Nama *</label>
      <input type="text" name="name" value="{{ old('name') }}" required autofocus>
    </div>
    <div class="form-group">
      <label>Email *</label>
      <input type="email" name="email" value="{{ old('email') }}" required>
    </div>
    <div class="form-group">
      <label>Password *</label>
      <div style="position:relative">
        <input type="password" name="password" id="cu_pw" required minlength="8" style="padding-right:2.5rem">
        <button type="button" onclick="togglePw('cu_pw',this)" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:.95rem;color:var(--text3)">👁</button>
      </div>
    </div>
    <div class="form-group" style="display:flex;align-items:center;gap:.5rem">
      <input type="checkbox" name="active" value="1" checked style="width:auto">
      <label style="margin:0">Aktif</label>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
  </form>
</div>

@push('scripts')
<script>
function togglePw(id, btn) {
  var el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
  btn.style.opacity = el.type === 'text' ? '1' : '.5';
}
</script>
@endpush
@endsection
