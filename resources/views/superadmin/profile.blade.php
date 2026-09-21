@extends('layouts.superadmin')
@section('title','Profil Saya')
@section('content')

<div class="page-header">
  <div class="page-title">Profil Saya</div>
  <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline btn-sm">← Dashboard</a>
</div>

<div class="card" style="max-width:440px">
  <div class="card-title">Nama</div>
  <form method="POST" action="{{ route('superadmin.profile.name.update') }}">
    @csrf
    <div class="form-group">
      <label>Nama *</label>
      <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
      @error('name')<div style="color:#dc2626;font-size:.78rem;margin-top:.3rem">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary">Simpan Nama</button>
  </form>
</div>

<div class="card" style="max-width:440px">
  <div class="card-title">Ganti Password</div>
  <form method="POST" action="{{ route('superadmin.profile.password.update') }}">
    @csrf
    <div class="form-group">
      <label>Password Saat Ini *</label>
      <div style="position:relative">
        <input type="password" name="current_password" id="sa_cur_pw" required style="padding-right:2.5rem">
        <button type="button" onclick="togglePw('sa_cur_pw',this)" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:.95rem;color:var(--text3)">👁</button>
      </div>
      @error('current_password')<div style="color:#dc2626;font-size:.78rem;margin-top:.3rem">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <label>Password Baru *</label>
      <div style="position:relative">
        <input type="password" name="password" id="sa_new_pw" minlength="8" required style="padding-right:2.5rem">
        <button type="button" onclick="togglePw('sa_new_pw',this)" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:.95rem;color:var(--text3)">👁</button>
      </div>
    </div>
    <div class="form-group">
      <label>Konfirmasi Password Baru *</label>
      <div style="position:relative">
        <input type="password" name="password_confirmation" id="sa_conf_pw" required style="padding-right:2.5rem">
        <button type="button" onclick="togglePw('sa_conf_pw',this)" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:.95rem;color:var(--text3)">👁</button>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Ubah Password</button>
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
