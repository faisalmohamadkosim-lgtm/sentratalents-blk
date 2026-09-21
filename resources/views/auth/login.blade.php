<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — {{ \App\Models\AppSetting::get('nama_aplikasi', 'BLK Connect') }}</title>
<style>
*,::before,::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,sans-serif;min-height:100vh;display:flex;align-items:stretch;background:#0f172a}

/* ── Sisi kiri: ilustrasi ── */
.login-bg{
  flex:1;position:relative;overflow:hidden;
  display:flex;align-items:flex-end;justify-content:center;
}
@media(max-width:720px){.login-bg{display:none}}
.login-bg svg{position:absolute;inset:0;width:100%;height:100%}
.overlay-text{
  position:relative;z-index:2;color:#fff;text-align:center;
  padding:2.5rem 2rem;text-shadow:0 2px 12px rgba(0,0,0,.6);
}
.overlay-text h2{font-size:1.75rem;font-weight:700;margin-bottom:.5rem}
.overlay-text p{font-size:.9rem;opacity:.85;max-width:340px;line-height:1.6;margin:0 auto}

/* ── Sisi kanan: form ── */
.login-panel{
  width:440px;flex-shrink:0;background:#fff;
  display:flex;align-items:center;justify-content:center;
  padding:2.5rem 2.25rem;
}
@media(max-width:720px){.login-panel{width:100%;min-height:100vh}}
.login-inner{width:100%;max-width:360px}

.login-logo{text-align:center;margin-bottom:2rem}
.logo-icon{
  width:64px;height:64px;margin:0 auto .875rem;
  background:linear-gradient(135deg,#0d6efd,#0284c7);
  border-radius:18px;display:flex;align-items:center;justify-content:center;
  box-shadow:0 6px 20px rgba(13,110,253,.35);
}
.login-title{font-size:1.5rem;font-weight:800;color:#0f172a;margin-bottom:.15rem}
.login-subtitle{font-size:.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;font-weight:600}
.login-sub{font-size:.83rem;color:#64748b;margin-top:.4rem}

.form-group{margin-bottom:1.1rem}
label{display:block;font-size:.78rem;font-weight:600;color:#374151;margin-bottom:.4rem}
.input-wrap{position:relative}
.input-wrap svg{position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none}
input{
  width:100%;padding:.65rem .875rem .65rem 2.4rem;
  border:1.5px solid #e2e8f0;border-radius:9px;
  font-size:.88rem;background:#f8fafc;color:#0f172a;
  transition:border-color .15s,box-shadow .15s,background .15s;
}
input:focus{
  outline:none;border-color:#0d6efd;
  box-shadow:0 0 0 3px rgba(13,110,253,.12);background:#fff;
}
input.is-invalid{border-color:#ef4444}

.error-msg{
  font-size:.75rem;color:#dc2626;margin-top:.35rem;
  display:flex;align-items:center;gap:.3rem;
}

.alert-error{
  background:#fee2e2;color:#991b1b;border-radius:9px;
  padding:.7rem 1rem;font-size:.83rem;margin-bottom:1.1rem;
  border-left:4px solid #ef4444;display:flex;align-items:flex-start;gap:.5rem;
}

.btn-login{
  width:100%;padding:.72rem;margin-top:.35rem;
  background:linear-gradient(135deg,#0d6efd,#0284c7);
  color:#fff;border:none;border-radius:9px;
  font-size:.95rem;font-weight:700;cursor:pointer;
  letter-spacing:.02em;transition:opacity .15s,transform .1s;
}
.btn-login:hover{opacity:.92;transform:translateY(-1px)}
.btn-login:active{transform:translateY(0)}

.forgot-link{
  display:block;text-align:right;margin-top:.625rem;
  font-size:.78rem;color:#64748b;text-decoration:none;
}
.forgot-link:hover{color:#0d6efd;text-decoration:underline}

hr{border:none;border-top:1px solid #f1f5f9;margin:1.5rem 0 1rem}
.footer-text{font-size:.72rem;color:#94a3b8;text-align:center;line-height:1.6}
</style>
</head>
<body>

{{-- Sisi Kiri: Ilustrasi Timur Tengah --}}
<div class="login-bg">
  <svg viewBox="0 0 900 700" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
    <defs>
      <linearGradient id="sky" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="#0a1628"/>
        <stop offset="45%" stop-color="#1a3a6b"/>
        <stop offset="75%" stop-color="#c2611f"/>
        <stop offset="100%" stop-color="#e8820c"/>
      </linearGradient>
      <linearGradient id="ground" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="#c9882a"/>
        <stop offset="100%" stop-color="#7a4e12"/>
      </linearGradient>
      <radialGradient id="moon" cx="50%" cy="50%" r="50%">
        <stop offset="0%" stop-color="#fffde7"/>
        <stop offset="100%" stop-color="#ffd54f"/>
      </radialGradient>
      <radialGradient id="glow" cx="50%" cy="50%" r="50%">
        <stop offset="0%" stop-color="#ff9800" stop-opacity=".35"/>
        <stop offset="100%" stop-color="#ff9800" stop-opacity="0"/>
      </radialGradient>
      <filter id="blur8"><feGaussianBlur stdDeviation="8"/></filter>
    </defs>

    <rect width="900" height="700" fill="url(#sky)"/>
    <ellipse cx="450" cy="480" rx="380" ry="120" fill="url(#glow)" filter="url(#blur8)"/>

    {{-- Bintang --}}
    @foreach([[80,40],[150,90],[240,30],[310,70],[390,20],[480,55],[560,35],[640,80],[720,25],[800,60],[850,100],[60,130],[200,110],[350,140],[500,100],[650,60],[760,120],[880,50],[130,60],[420,80],[700,45]] as $s)
    <circle cx="{{ $s[0] }}" cy="{{ $s[1] }}" r="1" fill="#fff" opacity=".8"/>
    @endforeach

    {{-- Bulan sabit --}}
    <circle cx="160" cy="110" r="38" fill="url(#moon)" opacity=".95"/>
    <circle cx="178" cy="100" r="32" fill="#0d2147"/>
    <polygon points="220,80 223,90 233,90 225,96 228,106 220,100 212,106 215,96 207,90 217,90" fill="#ffd54f" opacity=".9"/>

    {{-- Gurun --}}
    <path d="M0 500 Q225 460 450 490 Q675 520 900 480 L900 700 L0 700 Z" fill="url(#ground)"/>
    <path d="M0 520 Q180 490 360 510 Q540 530 720 505 Q810 495 900 510 L900 700 L0 700Z" fill="#a06820" opacity=".5"/>

    {{-- Masjid kiri --}}
    <ellipse cx="155" cy="360" rx="55" ry="58" fill="#1e3a5f"/>
    <ellipse cx="155" cy="362" rx="50" ry="52" fill="#254d7a"/>
    <rect x="62" y="290" width="20" height="160" rx="3" fill="#1e3a5f"/>
    <ellipse cx="72" cy="290" rx="10" ry="18" fill="#254d7a"/>
    <rect x="69" y="272" width="6" height="20" fill="#c9a227"/>
    <circle cx="72" cy="270" r="5" fill="#ffd54f"/>
    <rect x="232" y="290" width="20" height="160" rx="3" fill="#1e3a5f"/>
    <ellipse cx="242" cy="290" rx="10" ry="18" fill="#254d7a"/>
    <rect x="239" y="272" width="6" height="20" fill="#c9a227"/>
    <circle cx="242" cy="270" r="5" fill="#ffd54f"/>
    <rect x="80" y="400" width="150" height="80" rx="4" fill="#1e3a5f"/>
    <ellipse cx="110" cy="402" rx="20" ry="22" fill="#254d7a"/>
    <ellipse cx="200" cy="402" rx="20" ry="22" fill="#254d7a"/>
    <path d="M138 480 L138 440 Q155 425 172 440 L172 480 Z" fill="#0d2147"/>

    {{-- Masjid tengah --}}
    <ellipse cx="450" cy="295" rx="105" ry="95" fill="#2c4f82"/>
    <ellipse cx="450" cy="300" rx="95" ry="85" fill="#345e9a"/>
    <rect x="308" y="210" width="24" height="230" rx="4" fill="#2c4f82"/>
    <ellipse cx="320" cy="210" rx="12" ry="22" fill="#345e9a"/>
    <rect x="316" y="188" width="8" height="25" fill="#d4a827"/>
    <circle cx="320" cy="185" r="7" fill="#ffd54f"/>
    <rect x="568" y="210" width="24" height="230" rx="4" fill="#2c4f82"/>
    <ellipse cx="580" cy="210" rx="12" ry="22" fill="#345e9a"/>
    <rect x="576" y="188" width="8" height="25" fill="#d4a827"/>
    <circle cx="580" cy="185" r="7" fill="#ffd54f"/>
    <rect x="320" y="360" width="260" height="120" rx="5" fill="#2c4f82"/>
    <ellipse cx="360" cy="362" rx="28" ry="30" fill="#345e9a"/>
    <ellipse cx="540" cy="362" rx="28" ry="30" fill="#345e9a"/>
    <path d="M425 480 L425 432 Q450 412 475 432 L475 480 Z" fill="#0a1628"/>

    {{-- Masjid kanan --}}
    <ellipse cx="745" cy="370" rx="50" ry="52" fill="#1e3a5f"/>
    <ellipse cx="745" cy="372" rx="44" ry="46" fill="#254d7a"/>
    <rect x="658" y="305" width="18" height="150" rx="3" fill="#1e3a5f"/>
    <ellipse cx="667" cy="305" rx="9" ry="16" fill="#254d7a"/>
    <rect x="664" y="289" width="6" height="18" fill="#c9a227"/>
    <circle cx="667" cy="287" r="5" fill="#ffd54f"/>
    <rect x="819" y="305" width="18" height="150" rx="3" fill="#1e3a5f"/>
    <ellipse cx="828" cy="305" rx="9" ry="16" fill="#254d7a"/>
    <rect x="824" y="289" width="6" height="18" fill="#c9a227"/>
    <circle cx="828" cy="287" r="5" fill="#ffd54f"/>
    <rect x="672" y="406" width="146" height="74" rx="4" fill="#1e3a5f"/>
    <path d="M729 480 L729 446 Q745 432 761 446 L761 480 Z" fill="#0d2147"/>

    {{-- Pohon palem --}}
    <rect x="42" y="430" width="6" height="70" rx="2" fill="#6b3a10"/>
    <ellipse cx="45" cy="425" rx="22" ry="10" fill="#2d6a1f" transform="rotate(-20,45,425)"/>
    <ellipse cx="45" cy="425" rx="22" ry="10" fill="#2d6a1f" transform="rotate(20,45,425)"/>
    <rect x="867" y="440" width="6" height="60" rx="2" fill="#6b3a10"/>
    <ellipse cx="870" cy="435" rx="20" ry="9" fill="#2d6a1f" transform="rotate(-25,870,435)"/>
    <ellipse cx="870" cy="435" rx="20" ry="9" fill="#2d6a1f" transform="rotate(25,870,435)"/>

    {{-- Unta --}}
    <ellipse cx="88" cy="496" rx="22" ry="11" fill="#5a3008"/>
    <rect x="77" y="485" width="7" height="18" rx="3" fill="#5a3008"/>
    <rect x="97" y="485" width="7" height="18" rx="3" fill="#5a3008"/>
    <ellipse cx="78" cy="481" rx="10" ry="8" fill="#5a3008"/>
    <rect x="72" y="473" width="5" height="12" rx="2" fill="#5a3008"/>

    <rect x="0" y="550" width="900" height="150" fill="rgba(0,0,0,0.4)"/>
  </svg>

  <div class="overlay-text">
    <h2>{{ \App\Models\AppSetting::get('login_hero_title', 'Platform Balai Latihan Kerja') }}</h2>
    <p>{{ \App\Models\AppSetting::get('login_hero_text', 'Sistem informasi terpadu untuk BLK -- profil, program pelatihan, dan data lulusan.') }}</p>
  </div>
</div>

{{-- Sisi Kanan: Form Login --}}
<div class="login-panel">
  <div class="login-inner">

    <div class="login-logo">
      <div class="logo-icon">
        <svg width="32" height="32" fill="none" viewBox="0 0 24 24">
          <path d="M12 7v5m0 0v5m0-5H7m5 0h5" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="login-title">{{ \App\Models\AppSetting::get('nama_aplikasi', 'BLK Connect') }}</div>
      <div class="login-subtitle">Sistem Informasi BLK</div>
      <div class="login-sub">Masuk untuk melanjutkan</div>
    </div>

    @if($errors->has('email'))
    <div class="alert-error">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px">
        <circle cx="12" cy="12" r="10" stroke="#991b1b" stroke-width="2"/>
        <path d="M12 8v4m0 4h.01" stroke="#991b1b" stroke-width="2" stroke-linecap="round"/>
      </svg>
      {{ $errors->first('email') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
      @csrf

      <div class="form-group">
        <label for="email">Alamat Email</label>
        <div class="input-wrap">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="#94a3b8" stroke-width="1.8"/>
            <path d="M22 6l-10 7L2 6" stroke="#94a3b8" stroke-width="1.8"/>
          </svg>
          <input type="email" id="email" name="email"
                 value="{{ old('email') }}"
                 placeholder="nama@email.com"
                 class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                 required autofocus autocomplete="email">
        </div>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrap">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
            <rect x="3" y="11" width="18" height="11" rx="2" stroke="#94a3b8" stroke-width="1.8"/>
            <path d="M7 11V7a5 5 0 0110 0v4" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
          <input type="password" id="password" name="password"
                 placeholder="••••••••"
                 required autocomplete="current-password" style="padding-right:2.4rem">
          <button type="button" onclick="togglePw('password',this)" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:.9rem;color:#94a3b8;padding:.2rem">👁</button>
        </div>
      </div>

      <button type="submit" class="btn-login">Masuk →</button>
    </form>

    <a href="{{ route('password.forgot') }}" class="forgot-link">Lupa password?</a>

    <hr>
    <div class="footer-text">
      {{ \App\Models\AppSetting::get('nama_aplikasi', 'BLK Connect') }} &copy; {{ date('Y') }}<br>
      <span style="color:#cbd5e1">Login: admin@blkbandung.test / Test@123</span>
    </div>
  </div>
</div>

<script>
function togglePw(id, btn) {
  var el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
  btn.style.opacity = el.type === 'text' ? '1' : '.5';
}
</script>
</body>
</html>
