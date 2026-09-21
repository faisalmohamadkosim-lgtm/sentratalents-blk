<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Lupa Password — BLK Connect</title>
<style>
*,::before,::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,sans-serif;background:linear-gradient(135deg,#0d6efd 0%,#0284c7 100%);
  min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
.card{background:#fff;border-radius:16px;padding:2.5rem;width:100%;max-width:430px;
  box-shadow:0 20px 60px rgba(0,0,0,.25)}
.logo{text-align:center;margin-bottom:1.75rem}
.logo-icon{width:64px;height:64px;background:#0d6efd;border-radius:16px;
  display:inline-flex;align-items:center;justify-content:center;margin-bottom:.75rem}
.title{font-size:1.3rem;font-weight:700;color:#1e293b;margin-bottom:.25rem}
.sub{font-size:.85rem;color:#64748b}
.form-group{margin-bottom:1rem}
label{display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.375rem}
input{width:100%;padding:.6rem .875rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.9rem;transition:border-color .15s}
input:focus{outline:none;border-color:#0d6efd;box-shadow:0 0 0 3px rgba(13,110,253,.15)}
.btn{width:100%;padding:.7rem;background:#0d6efd;color:#fff;border:none;border-radius:8px;
  font-size:.95rem;font-weight:600;cursor:pointer;transition:background .15s;margin-top:.25rem}
.btn:hover{background:#0b5ed7}
.btn-outline{background:#fff;color:#0d6efd;border:1.5px solid #0d6efd;margin-top:.625rem;display:block;
  text-align:center;border-radius:8px;padding:.6rem;font-size:.875rem;text-decoration:none;font-weight:600}
.btn-outline:hover{background:#f0f4ff}
.alert{border-radius:8px;padding:.7rem .875rem;font-size:.85rem;margin-bottom:1rem;line-height:1.5}
.alert-error{background:#fee2e2;color:#991b1b;border-left:4px solid #ef4444}
.alert-info{background:#dbeafe;color:#1e40af;border-left:4px solid #3b82f6}
.reset-box{margin-top:1rem;background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:8px;padding:.875rem}
.reset-label{font-size:.72rem;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.375rem}
.reset-link{font-size:.78rem;color:#0d6efd;word-break:break-all;display:block;margin-bottom:.625rem}
.copy-btn{background:#0d6efd;color:#fff;border:none;border-radius:6px;padding:.3rem .75rem;font-size:.75rem;font-weight:600;cursor:pointer}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-icon">
      <svg width="32" height="32" fill="none" viewBox="0 0 24 24">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="#fff"/>
      </svg>
    </div>
    <div class="title">Lupa Password</div>
    <div class="sub">Masukkan email akun Anda</div>
  </div>

  @if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
  @endif

  @if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
    <a href="{{ route('login') }}" class="btn-outline">← Kembali ke Login</a>
  @elseif(session('reset_link'))
    <div class="alert alert-info" style="margin-bottom:.5rem">
      Tautan reset untuk <strong>{{ session('reset_email') }}</strong> berhasil dibuat.
    </div>
    <div class="reset-box">
      <div class="reset-label">Tautan Reset Password (berlaku 1 jam)</div>
      <a href="{{ session('reset_link') }}" class="reset-link">{{ session('reset_link') }}</a>
      <button class="copy-btn" onclick="copyLink()">Salin Tautan</button>
      <div style="font-size:.72rem;color:#15803d;margin-top:.5rem">
        ℹ️ Kirim tautan ini ke pengguna secara manual (SMTP belum dikonfigurasi).
      </div>
    </div>
    <a href="{{ route('login') }}" class="btn-outline" style="margin-top:1rem">← Kembali ke Login</a>
    <script>
    function copyLink() {
      navigator.clipboard.writeText('{{ session('reset_link') }}').then(function() {
        alert('Tautan disalin!');
      });
    }
    </script>
  @else
    <form method="POST" action="{{ route('password.send') }}">
      @csrf
      <div class="form-group">
        <label>Alamat Email</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
      </div>
      <button class="btn" type="submit">Kirim Tautan Reset</button>
    </form>
    <a href="{{ route('login') }}" class="btn-outline">← Kembali ke Login</a>
  @endif

  <div style="font-size:.75rem;color:#94a3b8;text-align:center;margin-top:1.25rem">BLK Connect</div>
</div>
</body>
</html>
