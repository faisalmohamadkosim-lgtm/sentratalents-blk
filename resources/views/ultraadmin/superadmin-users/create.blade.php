@extends('layouts.ultraadmin')
@section('title', 'Tambah Superadmin')
@section('content')
<div class="page-header">
    <div class="page-title">Tambah Superadmin</div>
</div>

<div class="card" style="max-width:32rem;">
    <form method="POST" action="{{ route('ultraadmin.superadmin-users.store') }}">
        @csrf
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <div style="position:relative">
                <input type="password" name="password" id="su_pw" required style="padding-right:2.5rem">
                <button type="button" onclick="togglePw('su_pw',this)" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:.95rem;color:var(--text3)">👁</button>
            </div>
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
