<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Superadmin') — BLK Connect</title>
<style>
*,::before,::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --primary:#0d6efd;--primary-dark:#0a58ca;--primary-light:#dbeafe;
  --success:#16a34a;--warning:#d97706;--danger:#dc2626;--info:#0284c7;
  --bg:#f1f5f9;--surface:#fff;--surface2:#f8fafc;
  --text1:#0f172a;--text2:#475569;--text3:#94a3b8;
  --border:#e2e8f0;--radius:10px;--sidebar-w:230px;
}
@media(prefers-color-scheme:dark){:root{
  --bg:#0f172a;--surface:#1e293b;--surface2:#263347;
  --text1:#f1f5f9;--text2:#94a3b8;--text3:#475569;--border:#334155;
}}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text1);min-height:100vh;display:flex;flex-direction:column}
a{color:var(--primary);text-decoration:none}
.topbar{height:56px;background:#0a2342;color:#fff;display:flex;align-items:center;
  padding:0 1.25rem;gap:1rem;position:sticky;top:0;z-index:100;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,.25)}
.tb-brand{font-weight:800;font-size:1.05rem;display:flex;align-items:center;gap:.5rem}
.tb-brand small{font-size:.68rem;font-weight:400;opacity:.6;letter-spacing:.06em;text-transform:uppercase}
.tb-user{margin-left:auto;display:flex;align-items:center;gap:.75rem;font-size:.82rem}
.tb-avatar{width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.2);
  display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0}
.tb-link{color:rgba(255,255,255,.7);font-size:.78rem;padding:.25rem .5rem;border-radius:5px;transition:background .12s}
.tb-link:hover{background:rgba(255,255,255,.12);color:#fff;text-decoration:none}
.app-body{display:flex;flex:1;min-height:0}
.sidebar{width:var(--sidebar-w);background:var(--surface);border-right:1px solid var(--border);
  padding:.75rem .625rem;overflow-y:auto;flex-shrink:0;display:flex;flex-direction:column;gap:.1rem}
.main{flex:1;overflow-y:auto;padding:1.5rem}
.nav-section{font-size:.64rem;font-weight:700;color:var(--text3);text-transform:uppercase;
  letter-spacing:.09em;padding:.625rem .625rem .25rem;margin-top:.375rem}
.nav-item{display:flex;align-items:center;gap:.5rem;padding:.48rem .75rem;border-radius:7px;
  font-size:.82rem;color:var(--text2);transition:background .12s,color .12s;white-space:nowrap}
.nav-item:hover{background:var(--primary-light);color:var(--primary);text-decoration:none}
.nav-item.active{background:var(--primary-light);color:var(--primary);font-weight:600}
.card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);padding:1.25rem;margin-bottom:1.25rem}
.card-title{font-size:.95rem;font-weight:700;margin-bottom:1rem}
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem}
.page-title{font-size:1.2rem;font-weight:800}
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:1rem;margin-bottom:1.25rem}
.stat-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);padding:1.1rem 1.25rem;position:relative;overflow:hidden}
.stat-label{font-size:.7rem;font-weight:600;color:var(--text2);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem}
.stat-value{font-size:1.8rem;font-weight:800;line-height:1}
.stat-sub{font-size:.72rem;color:var(--text3);margin-top:.3rem}
table{width:100%;border-collapse:collapse;font-size:.83rem}
th{text-align:left;padding:.55rem .75rem;font-size:.7rem;font-weight:700;color:var(--text2);
   text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);background:var(--surface2);white-space:nowrap}
td{padding:.6rem .75rem;border-bottom:1px solid var(--border);vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:var(--surface2)}
.badge{display:inline-flex;align-items:center;padding:.18rem .6rem;border-radius:20px;font-size:.71rem;font-weight:600;white-space:nowrap}
.badge-green{background:#dcfce7;color:#15803d}
.badge-gray{background:#f1f5f9;color:#475569}
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .9rem;border-radius:7px;
  font-size:.82rem;font-weight:600;cursor:pointer;border:1.5px solid transparent;transition:all .15s;text-decoration:none;white-space:nowrap}
.btn:hover{text-decoration:none;filter:brightness(.93)}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
.btn-outline{background:transparent;color:var(--text1);border-color:var(--border)}
.btn-outline:hover{border-color:var(--primary);color:var(--primary)}
.btn-sm{padding:.3rem .65rem;font-size:.75rem}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.form-group{margin-bottom:1rem}
.form-group.full{grid-column:1/-1}
label{display:block;font-size:.78rem;font-weight:600;color:var(--text2);margin-bottom:.35rem}
input[type=text],input[type=email],input[type=password],input[type=date],
input[type=number],input[type=url],input[type=tel],select,textarea{
  width:100%;padding:.55rem .8rem;border:1.5px solid var(--border);border-radius:7px;
  font-size:.85rem;background:var(--surface);color:var(--text1);transition:border-color .15s,box-shadow .15s}
input:focus,select:focus,textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(13,110,253,.1)}
.text-muted{color:var(--text3);font-size:.78rem}
.flash{padding:.65rem 1rem;border-radius:8px;font-size:.83rem;margin-bottom:1rem;font-weight:500}
.flash-success{background:#dcfce7;color:#166534;border-left:4px solid var(--success)}
.flash-error{background:#fee2e2;color:#991b1b;border-left:4px solid var(--danger)}
@media(max-width:768px){.sidebar{display:none}.form-grid{grid-template-columns:1fr}.main{padding:1rem}}
</style>
</head>
<body>

<div class="topbar">
  <div class="tb-brand">
    <svg width="22" height="22" fill="none" viewBox="0 0 24 24">
      <path d="M12 7v5m0 0v5m0-5H7m5 0h5" stroke="#fff" stroke-width="2.3" stroke-linecap="round"/>
    </svg>
    BLK Connect <small>Superadmin</small>
  </div>
  <div class="tb-user">
    <a href="{{ route('superadmin.profile.edit') }}" class="tb-avatar" style="text-decoration:none">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</a>
    <div style="line-height:1.3">
      <a href="{{ route('superadmin.profile.edit') }}" style="font-weight:600;font-size:.85rem;color:#fff;text-decoration:none">{{ auth()->user()->name }}</a>
      <div style="opacity:.6;font-size:.7rem">Superadmin</div>
    </div>
    <form method="POST" action="{{ route('logout') }}" style="display:inline">
      @csrf
      <button type="submit" class="tb-link" style="background:none;border:none;cursor:pointer">Keluar</button>
    </form>
  </div>
</div>

<div class="app-body">

<div class="sidebar">
  <div class="nav-section">Menu Utama</div>
  <a href="{{ route('superadmin.dashboard') }}" class="nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">📊 Ringkasan</a>
  <a href="{{ route('superadmin.tenants.index') }}" class="nav-item {{ request()->routeIs('superadmin.tenants.*') ? 'active' : '' }}">🏫 Tenant BLK</a>
  <a href="{{ route('superadmin.app-profile.edit') }}" class="nav-item {{ request()->routeIs('superadmin.app-profile.*') ? 'active' : '' }}">🏷️ Profil Aplikasi</a>
  <a href="{{ route('superadmin.gallery.index') }}" class="nav-item {{ request()->routeIs('superadmin.gallery.*') ? 'active' : '' }}">🖼️ Galeri Landing Page</a>
  <a href="{{ route('superadmin.news.index') }}" class="nav-item {{ request()->routeIs('superadmin.news.*') ? 'active' : '' }}">📰 Berita Landing Page</a>
</div>

<div class="main">
  @if(session('success'))
    <div class="flash flash-success">✅ {{ session('success') }}</div>
  @endif
  @if(session('status'))
    <div class="flash flash-success">✅ {{ session('status') }}</div>
  @endif
  @if(session('error'))
    <div class="flash flash-error">❌ {{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="flash flash-error">{{ $errors->first() }}</div>
  @endif

  @yield('content')

  <div style="margin-top:2rem;text-align:center;font-size:.72rem;color:var(--text3)">
    BLK Connect &copy; {{ date('Y') }}
  </div>
</div>

</div>{{-- app-body --}}
@stack('scripts')
</body>
</html>
