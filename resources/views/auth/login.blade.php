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

{{-- Sisi Kiri: Ilustrasi Pendidikan & Pelatihan --}}
<div class="login-bg">
  <svg viewBox="0 0 900 700" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
    <defs>
      <linearGradient id="sky" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="#0a1e3d"/>
        <stop offset="55%" stop-color="#154a78"/>
        <stop offset="100%" stop-color="#1c7293"/>
      </linearGradient>
      <linearGradient id="ground" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="#0f3a5f"/>
        <stop offset="100%" stop-color="#0a2342"/>
      </linearGradient>
      <radialGradient id="glow" cx="50%" cy="50%" r="50%">
        <stop offset="0%" stop-color="#ffd54f" stop-opacity=".3"/>
        <stop offset="100%" stop-color="#ffd54f" stop-opacity="0"/>
      </radialGradient>
      <filter id="blur10"><feGaussianBlur stdDeviation="10"/></filter>
    </defs>

    <rect width="900" height="700" fill="url(#sky)"/>

    {{-- Partikel -- nuansa jaringan/terhubung --}}
    @foreach([[80,40],[150,90],[240,30],[310,70],[390,20],[480,55],[560,35],[640,80],[720,25],[800,60],[850,100],[60,130],[200,110],[350,140],[500,100],[650,60],[760,120],[880,50],[130,60],[420,80],[700,45]] as $p)
    <circle cx="{{ $p[0] }}" cy="{{ $p[1] }}" r="1.6" fill="#bfe3ff" opacity=".55"/>
    @endforeach

    <ellipse cx="470" cy="440" rx="270" ry="160" fill="url(#glow)" filter="url(#blur10)"/>

    {{-- Panggung --}}
    <path d="M0 560 Q225 525 450 545 Q675 565 900 530 L900 700 L0 700 Z" fill="url(#ground)"/>
    <path d="M0 580 Q220 555 440 570 Q660 585 900 560 L900 700 L0 700Z" fill="#0d2c4d" opacity=".55"/>

    {{-- Roda gigi + kunci pas (pelatihan vokasi/teknis) --}}
    <g transform="translate(235,470)">
      <circle r="62" fill="#1c7293"/>
      <rect x="-8" y="-78" width="16" height="24" rx="3" fill="#1c7293"/>
      <rect x="-8" y="-78" width="16" height="24" rx="3" fill="#1c7293" transform="rotate(45)"/>
      <rect x="-8" y="-78" width="16" height="24" rx="3" fill="#1c7293" transform="rotate(90)"/>
      <rect x="-8" y="-78" width="16" height="24" rx="3" fill="#1c7293" transform="rotate(135)"/>
      <rect x="-8" y="-78" width="16" height="24" rx="3" fill="#1c7293" transform="rotate(180)"/>
      <rect x="-8" y="-78" width="16" height="24" rx="3" fill="#1c7293" transform="rotate(225)"/>
      <rect x="-8" y="-78" width="16" height="24" rx="3" fill="#1c7293" transform="rotate(270)"/>
      <rect x="-8" y="-78" width="16" height="24" rx="3" fill="#1c7293" transform="rotate(315)"/>
      <circle r="42" fill="#1c7293"/>
      <circle r="24" fill="#0a2342"/>
      <circle r="8" fill="#2c8fb5"/>
      <g transform="rotate(-35)">
        <rect x="-70" y="-8" width="140" height="16" rx="8" fill="#f4f7fb"/>
        <path d="M-92,-18 Q-104,0 -92,18 L-70,9 L-70,-9 Z" fill="#f4f7fb"/>
        <circle cx="88" cy="0" r="16" fill="#f4f7fb"/>
        <circle cx="88" cy="0" r="7" fill="#1c7293"/>
      </g>
    </g>

    {{-- Buku terbuka + topi wisuda (pendidikan) --}}
    <g transform="translate(470,520)">
      <path d="M0,-70 Q-90,-85 -155,-55 L-155,-10 Q-90,-30 0,-15 Z" fill="#fdf6e3"/>
      <path d="M0,-70 Q90,-85 155,-55 L155,-10 Q90,-30 0,-15 Z" fill="#fdf6e3"/>
      <line x1="-130" y1="-47" x2="-42" y2="-37" stroke="#d9c9a3" stroke-width="3" stroke-linecap="round"/>
      <line x1="-124" y1="-32" x2="-38" y2="-24" stroke="#d9c9a3" stroke-width="3" stroke-linecap="round"/>
      <line x1="42" y1="-37" x2="130" y2="-47" stroke="#d9c9a3" stroke-width="3" stroke-linecap="round"/>
      <line x1="38" y1="-24" x2="124" y2="-32" stroke="#d9c9a3" stroke-width="3" stroke-linecap="round"/>
      <rect x="-4" y="-78" width="8" height="68" rx="3" fill="#e8a33d"/>

      <polygon points="0,-150 78,-122 0,-96 -78,-122" fill="#16243b"/>
      <rect x="-30" y="-122" width="60" height="22" rx="4" fill="#0f1b2e"/>
      <circle cx="0" cy="-150" r="6" fill="#ffd54f"/>
      <path d="M0,-150 Q55,-140 60,-100" stroke="#ffd54f" stroke-width="3" fill="none" stroke-linecap="round"/>
      <circle cx="60" cy="-100" r="7" fill="#ffd54f"/>
    </g>

    {{-- Bola lampu (ide/inovasi) --}}
    <g transform="translate(650,290)">
      <line x1="0" y1="-52" x2="0" y2="-68" stroke="#ffd54f" stroke-width="4" stroke-linecap="round"/>
      <line x1="34" y1="-30" x2="46" y2="-42" stroke="#ffd54f" stroke-width="4" stroke-linecap="round"/>
      <line x1="-34" y1="-30" x2="-46" y2="-42" stroke="#ffd54f" stroke-width="4" stroke-linecap="round"/>
      <line x1="44" y1="4" x2="60" y2="4" stroke="#ffd54f" stroke-width="4" stroke-linecap="round" opacity=".8"/>
      <line x1="-44" y1="4" x2="-60" y2="4" stroke="#ffd54f" stroke-width="4" stroke-linecap="round" opacity=".8"/>
      <circle r="34" fill="#ffd54f"/>
      <rect x="-12" y="28" width="24" height="14" rx="3" fill="#c9a227"/>
      <line x1="-8" y1="42" x2="8" y2="42" stroke="#8a6d1a" stroke-width="3" stroke-linecap="round"/>
      <line x1="-8" y1="48" x2="8" y2="48" stroke="#8a6d1a" stroke-width="3" stroke-linecap="round"/>
    </g>

    {{-- Tumpukan buku + pensil (belajar) --}}
    <g transform="translate(710,500)">
      <rect x="-55" y="10" width="110" height="20" rx="4" fill="#1c7293"/>
      <rect x="-48" y="-12" width="96" height="20" rx="4" fill="#e8a33d"/>
      <rect x="-40" y="-32" width="80" height="18" rx="4" fill="#f4f7fb"/>
      <g transform="translate(48,-30) rotate(25)">
        <rect x="-6" y="-95" width="12" height="70" rx="3" fill="#ffd54f"/>
        <polygon points="-6,-95 6,-95 0,-112" fill="#f4c56b"/>
        <polygon points="-2,-108 2,-108 0,-116" fill="#5a4326"/>
        <rect x="-6" y="-28" width="12" height="10" fill="#e0836b"/>
      </g>
    </g>

    {{-- Kilau pencapaian di sekitar topi --}}
    @foreach([[380,260],[570,235],[430,185],[540,320]] as $sp)
    <g opacity=".85">
      <line x1="{{ $sp[0]-6 }}" y1="{{ $sp[1] }}" x2="{{ $sp[0]+6 }}" y2="{{ $sp[1] }}" stroke="#ffd54f" stroke-width="2" stroke-linecap="round"/>
      <line x1="{{ $sp[0] }}" y1="{{ $sp[1]-6 }}" x2="{{ $sp[0] }}" y2="{{ $sp[1]+6 }}" stroke="#ffd54f" stroke-width="2" stroke-linecap="round"/>
    </g>
    @endforeach

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
