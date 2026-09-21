@extends('layouts.app')
@section('title', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('content')
@php $isEdit = isset($user); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna' }}</div>
  <a href="{{ route('users.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <form method="POST" action="{{ $isEdit ? route('users.update', $user) : route('users.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="form-group">
        <label>Nama *</label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required>
      </div>
      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
      </div>
      <div class="form-group">
        <label>Password {{ $isEdit ? '(kosongkan jika tidak diubah)' : '*' }}</label>
        <div style="position:relative">
          <input type="password" name="password" id="u_pw" {{ $isEdit ? '' : 'required' }} minlength="8" style="padding-right:2.5rem">
          <button type="button" onclick="togglePw('u_pw',this)" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:.95rem;color:var(--text3)">👁</button>
        </div>
      </div>
      <div class="form-group">
        <label>Konfirmasi Password</label>
        <div style="position:relative">
          <input type="password" name="password_confirmation" id="u_pw_conf" style="padding-right:2.5rem">
          <button type="button" onclick="togglePw('u_pw_conf',this)" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:.95rem;color:var(--text3)">👁</button>
        </div>
      </div>
      <div class="form-group">
        <label>Role *</label>
        <select name="role" required>
          @foreach($roles as $r)
            <option value="{{ $r->name }}" @selected(old('role', $user->role ?? '') === $r->name)>
              {{ $r->label ?: ucfirst($r->name) }}
              @if($r->description) — <span style="color:var(--text3)">{{ Str::limit($r->description,40) }}</span>@endif
            </option>
          @endforeach
        </select>
      </div>
      <div class="form-group" style="display:flex;align-items:center;gap:.5rem;padding-top:1.5rem">
        <input type="hidden" name="active" value="0">
        <input type="checkbox" id="active" name="active" value="1" style="width:auto" @checked(old('active', $user->active ?? 1))>
        <label for="active" style="margin:0;font-size:.85rem">Aktif</label>
      </div>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan' : 'Tambah' }}</button>
      <a href="{{ route('users.index') }}" class="btn btn-outline">Batal</a>
    </div>
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
