<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reset Password — BLK Connect</title>
<style>
*,::before,::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,sans-serif;background:linear-gradient(135deg,#0d6efd 0%,#0284c7 100%);
  min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
.card{background:#fff;border-radius:16px;padding:2.5rem;width:100%;max-width:400px;
  box-shadow:0 20px 60px rgba(0,0,0,.25)}
.logo{text-align:center;margin-bottom:1.75rem}
.logo-icon{width:64px;height:64px;background:#0d6efd;border-radius:16px;
  display:inline-flex;align-items:center;justify-content:center;margin-bottom:.75rem}
.title{font-size:1.3rem;font-weight:700;color:#1e293b;margin-bottom:.25rem}
.sub{font-size:.85rem;color:#64748b}
.form-group{margin-bottom:1rem}
label{display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.375rem}
.pw-wrap{position:relative}
input{width:100%;padding:.6rem .875rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.9rem}
input:focus{outline:none;border-color:#0d6efd;box-shadow:0 0 0 3px rgba(13,110,253,.15)}
.pw-toggle{position:absolute;right:.75rem;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;color:#94a3b8;font-size:.85rem;padding:.2rem}
.strength{height:4px;border-radius:2px;margin-top:.375rem;transition:width .3s,background .3s;width:0}
.btn{width:100%;padding:.7rem;background:#0d6efd;color:#fff;border:none;border-radius:8px;
  font-size:.95rem;font-weight:600;cursor:pointer;margin-top:1rem}
.btn-outline{background:#fff;color:#0d6efd;border:1.5px solid #0d6efd;margin-top:.625rem;display:block;
  text-align:center;border-radius:8px;padding:.6rem;font-size:.875rem;text-decoration:none;font-weight:600}
.alert{border-radius:8px;padding:.7rem .875rem;font-size:.85rem;margin-bottom:1rem;line-height:1.5}
.alert-error{background:#fee2e2;color:#991b1b;border-left:4px solid #ef4444}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-icon">
      <svg width="32" height="32" fill="none" viewBox="0 0 24 24">
        <path d="M12 1a5 5 0 00-5 5v3H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2v-9a2 2 0 00-2-2h-2V6a5 5 0 00-5-5zm0 2a3 3 0 013 3v3H9V6a3 3 0 013-3zm0 9a2 2 0 110 4 2 2 0 010-4z" fill="#fff"/>
      </svg>
    </div>
    <div class="title">Reset Password</div>
    <div class="sub">Buat password baru untuk <strong>{{ $email }}</strong></div>
  </div>

  @if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
    <a href="{{ route('password.forgot') }}" class="btn-outline">Minta Tautan Baru</a>
  @else
  <form method="POST" action="{{ route('password.reset') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <div class="form-group">
      <label>Password Baru</label>
      <div class="pw-wrap">
        <input type="password" name="password" id="pw1" placeholder="Min. 6 karakter"
               required oninput="checkStrength(this.value)" autofocus>
        <button type="button" class="pw-toggle" onclick="togglePw('pw1')">👁</button>
      </div>
      <div class="strength" id="strengthBar"></div>
      <div style="font-size:.71rem;color:#94a3b8;margin-top:.25rem" id="strengthLabel"></div>
    </div>
    <div class="form-group">
      <label>Konfirmasi Password</label>
      <div class="pw-wrap">
        <input type="password" name="password_confirmation" id="pw2" placeholder="Ulangi password baru" required>
        <button type="button" class="pw-toggle" onclick="togglePw('pw2')">👁</button>
      </div>
    </div>
    <button class="btn" type="submit">Simpan Password Baru</button>
  </form>
  <a href="{{ route('login') }}" class="btn-outline">← Kembali ke Login</a>
  @endif

  <div style="font-size:.75rem;color:#94a3b8;text-align:center;margin-top:1.25rem">BLK Connect</div>
</div>

<script>
function togglePw(id) { var el=document.getElementById(id); el.type=el.type==='password'?'text':'password'; }
function checkStrength(val) {
  var bar=document.getElementById('strengthBar'), label=document.getElementById('strengthLabel');
  var score=0;
  if(val.length>=6)score++;if(val.length>=10)score++;
  if(/[A-Z]/.test(val))score++;if(/[0-9]/.test(val))score++;if(/[^A-Za-z0-9]/.test(val))score++;
  var colors=['#ef4444','#f97316','#eab308','#22c55e','#16a34a'];
  var labels=['Sangat lemah','Lemah','Cukup','Kuat','Sangat kuat'];
  var pct=[20,40,60,80,100];
  var i=Math.min(score-1,4);
  bar.style.width=(val.length?pct[i<0?0:i]:0)+'%';
  bar.style.background=val.length?(colors[i<0?0:i]):'transparent';
  label.textContent=val.length?(labels[i<0?0:i]):'';
  label.style.color=val.length?(colors[i<0?0:i]):'';
}
</script>
</body>
</html>
