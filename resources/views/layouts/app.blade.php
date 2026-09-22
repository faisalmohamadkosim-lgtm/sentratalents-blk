<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — BLK Connect</title>
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

/* topbar */
.topbar{height:56px;background:#0a2342;color:#fff;display:flex;align-items:center;
  padding:0 1.25rem;gap:1rem;position:sticky;top:0;z-index:100;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,.25)}
.tb-brand{font-weight:800;font-size:1.05rem;display:flex;align-items:center;gap:.5rem}
.tb-brand small{font-size:.68rem;font-weight:400;opacity:.6;letter-spacing:.06em;text-transform:uppercase}
.tb-user{margin-left:auto;display:flex;align-items:center;gap:.75rem;font-size:.82rem}
.tb-avatar{width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.2);
  display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0}
.tb-link{color:rgba(255,255,255,.7);font-size:.78rem;padding:.25rem .5rem;border-radius:5px;transition:background .12s}
.tb-link:hover{background:rgba(255,255,255,.12);color:#fff;text-decoration:none}
.tb-menu-btn{display:none;background:none;border:none;color:#fff;font-size:1.3rem;cursor:pointer;padding:.2rem .4rem;line-height:1}

/* layout */
.app-body{display:flex;flex:1;min-height:0}
.sidebar{width:var(--sidebar-w);background:var(--surface);border-right:1px solid var(--border);
  padding:.75rem .625rem;overflow-y:auto;flex-shrink:0;display:flex;flex-direction:column;gap:.1rem}
.main{flex:1;overflow-y:auto;padding:1.5rem}
.sidebar-backdrop{display:none}

/* nav */
.nav-section{font-size:.64rem;font-weight:700;color:var(--text3);text-transform:uppercase;
  letter-spacing:.09em;padding:.625rem .625rem .25rem;margin-top:.375rem}
.nav-item{display:flex;align-items:center;gap:.5rem;padding:.48rem .75rem;border-radius:7px;
  font-size:.82rem;color:var(--text2);transition:background .12s,color .12s;white-space:nowrap}
.nav-item:hover{background:var(--primary-light);color:var(--primary);text-decoration:none}
.nav-item.active{background:var(--primary-light);color:var(--primary);font-weight:600}
.nav-badge{margin-left:auto;background:var(--danger);color:#fff;border-radius:10px;
  padding:.08rem .45rem;font-size:.65rem;font-weight:700;min-width:18px;text-align:center}

/* cards */
.card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);padding:1.25rem;margin-bottom:1.25rem}
.card-title{font-size:.95rem;font-weight:700;margin-bottom:1rem}

/* page header */
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem}
.page-title{font-size:1.2rem;font-weight:800}

/* stat grid */
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:1rem;margin-bottom:1.25rem}
.stat-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);padding:1.1rem 1.25rem;position:relative;overflow:hidden}
.stat-label{font-size:.7rem;font-weight:600;color:var(--text2);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem}
.stat-value{font-size:1.8rem;font-weight:800;line-height:1}
.stat-sub{font-size:.72rem;color:var(--text3);margin-top:.3rem}
.stat-icon{position:absolute;right:1rem;top:50%;transform:translateY(-50%);font-size:2rem;opacity:.12}

/* table */
table{width:100%;border-collapse:collapse;font-size:.83rem}
th{text-align:left;padding:.55rem .75rem;font-size:.7rem;font-weight:700;color:var(--text2);
   text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid var(--border);background:var(--surface2);white-space:nowrap}
td{padding:.6rem .75rem;border-bottom:1px solid var(--border);vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:var(--surface2)}

/* badge */
.badge{display:inline-flex;align-items:center;padding:.18rem .6rem;border-radius:20px;font-size:.71rem;font-weight:600;white-space:nowrap}
.badge-green{background:#dcfce7;color:#15803d}
.badge-red{background:#fee2e2;color:#991b1b}
.badge-blue{background:#dbeafe;color:#1d4ed8}
.badge-yellow{background:#fef9c3;color:#854d0e}
.badge-gray{background:#f1f5f9;color:#475569}
.badge-purple{background:#ede9fe;color:#6d28d9}
.badge-orange{background:#ffedd5;color:#9a3412}

/* buttons */
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .9rem;border-radius:7px;
  font-size:.82rem;font-weight:600;cursor:pointer;border:1.5px solid transparent;transition:all .15s;text-decoration:none;white-space:nowrap}
.btn:hover{text-decoration:none;filter:brightness(.93)}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
.btn-danger{background:var(--danger);color:#fff;border-color:var(--danger)}
.btn-success{background:var(--success);color:#fff;border-color:var(--success)}
.btn-warning{background:var(--warning);color:#fff;border-color:var(--warning)}
.btn-outline{background:transparent;color:var(--text1);border-color:var(--border)}
.btn-outline:hover{border-color:var(--primary);color:var(--primary)}
.btn-sm{padding:.3rem .65rem;font-size:.75rem}

/* forms */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.form-group{margin-bottom:1rem}
.form-group.full{grid-column:1/-1}
label{display:block;font-size:.78rem;font-weight:600;color:var(--text2);margin-bottom:.35rem}
input[type=text],input[type=email],input[type=password],input[type=date],
input[type=number],input[type=url],input[type=tel],select,textarea{
  width:100%;padding:.55rem .8rem;border:1.5px solid var(--border);border-radius:7px;
  font-size:.85rem;background:var(--surface);color:var(--text1);transition:border-color .15s,box-shadow .15s}
input:focus,select:focus,textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(13,110,253,.1)}
textarea{resize:vertical;min-height:80px}
.text-muted{color:var(--text3);font-size:.78rem}

/* flash */
.flash{padding:.65rem 1rem;border-radius:8px;font-size:.83rem;margin-bottom:1rem;font-weight:500}
.flash-success{background:#dcfce7;color:#166534;border-left:4px solid var(--success)}
.flash-error{background:#fee2e2;color:#991b1b;border-left:4px solid var(--danger)}
.flash-info{background:#dbeafe;color:#1e40af;border-left:4px solid var(--info)}

/* pagination */
.pagination{display:flex;gap:.35rem;align-items:center;flex-wrap:wrap;margin-top:1rem;padding-top:.75rem;border-top:1px solid var(--border)}
.pagination a,.page-info{padding:.35rem .65rem;border-radius:6px;font-size:.78rem;border:1.5px solid var(--border);color:var(--text2)}
.pagination a:hover{border-color:var(--primary);color:var(--primary);text-decoration:none}
.pagination a.active{background:var(--primary);color:#fff;border-color:var(--primary)}

/* misc */
hr.divider{border:none;border-top:1px solid var(--border);margin:1.25rem 0}
.text-danger{color:var(--danger)}
.text-success{color:var(--success)}
.text-warning{color:var(--warning)}

@media(max-width:768px){
  .tb-menu-btn{display:block}
  .sidebar{
    position:fixed;top:56px;left:0;bottom:0;z-index:160;
    transform:translateX(-100%);transition:transform .2s ease;
    box-shadow:2px 0 16px rgba(0,0,0,.2);
  }
  .sidebar.is-open{transform:translateX(0)}
  .sidebar-backdrop.is-open{display:block;position:fixed;inset:56px 0 0 0;background:rgba(0,0,0,.4);z-index:150}
  .form-grid{grid-template-columns:1fr}
  .main{padding:1rem}
}
</style>
@stack('styles')
</head>
<body>

{{-- Topbar --}}
<div class="topbar">
  <button type="button" class="tb-menu-btn" id="tbMenuBtn" aria-label="Buka menu">☰</button>
  <div class="tb-brand">
    @if (auth()->user()->tenant?->logoUrl())
      <img src="{{ auth()->user()->tenant->logoUrl() }}" alt="Logo" style="width:22px;height:22px;object-fit:contain;border-radius:4px">
    @else
      <svg width="22" height="22" fill="none" viewBox="0 0 24 24">
        <path d="M12 7v5m0 0v5m0-5H7m5 0h5" stroke="#fff" stroke-width="2.3" stroke-linecap="round"/>
      </svg>
    @endif
    {{ auth()->user()->tenant?->name ?? 'BLK Connect' }} <small>Platform BLK</small>
  </div>
  <div class="tb-user">
    <div class="tb-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
    <div style="line-height:1.3">
      <div style="font-weight:600;font-size:.85rem">{{ auth()->user()->name }}</div>
      <div style="opacity:.6;font-size:.7rem;text-transform:capitalize">{{ auth()->user()->role }}</div>
    </div>
    <a href="{{ route('profile.password') }}" class="tb-link">Ganti Password</a>
    <form method="POST" action="{{ route('logout') }}" style="display:inline">
      @csrf
      <button type="submit" class="tb-link" style="background:none;border:none;cursor:pointer">Keluar</button>
    </form>
  </div>
</div>

<div class="app-body">

{{-- Sidebar --}}
<div class="sidebar" id="sidebarEl">
  @php $role = auth()->user()->role; $tenant = auth()->user()->tenant; @endphp

  <div class="nav-section">Menu Utama</div>
  <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 Dashboard</a>

  @if(in_array($role, ['admin','staff','owner']))
  <div class="nav-section">Profil BLK</div>
  <a href="{{ route('facilities.index') }}" class="nav-item {{ request()->routeIs('facilities.*') ? 'active' : '' }}">🏭 Fasilitas</a>
  <a href="{{ route('trainers.index') }}" class="nav-item {{ request()->routeIs('trainers.*') ? 'active' : '' }}">🧑‍🏫 Trainer/Instruktur</a>
  <a href="{{ route('org-structure.index') }}" class="nav-item {{ request()->routeIs('org-structure.*') ? 'active' : '' }}">🗂️ Struktur Organisasi</a>
  <a href="{{ route('gallery.index') }}" class="nav-item {{ request()->routeIs('gallery.*') ? 'active' : '' }}">🖼️ Galeri</a>

  <div class="nav-section">Pelatihan</div>
  <a href="{{ route('programs.index') }}" class="nav-item {{ request()->routeIs('programs.*') ? 'active' : '' }}">📚 Program</a>
  <a href="{{ route('batches.index') }}" class="nav-item {{ request()->routeIs('batches.*') ? 'active' : '' }}">📅 Angkatan</a>

  <div class="nav-section">Peserta</div>
  <a href="{{ route('participants.index') }}" class="nav-item {{ request()->routeIs('participants.*') ? 'active' : '' }}">👤 Data Peserta</a>
  <a href="{{ route('enrollments.index') }}" class="nav-item {{ request()->routeIs('enrollments.*') ? 'active' : '' }}">📝 Pendaftaran/Enrollment</a>
  <a href="{{ route('companies.index') }}" class="nav-item {{ request()->routeIs('companies.*') ? 'active' : '' }}">🏢 Perusahaan Mitra</a>
  @endif

  @if($role === 'perusahaan')
  <div class="nav-section">Portal Perusahaan</div>
  <a href="{{ route('company-portal.index') }}" class="nav-item {{ request()->routeIs('company-portal.*') ? 'active' : '' }}">📋 Peserta Kami</a>
  @endif

  @if($role === 'trainer')
  <div class="nav-section">Portal Trainer</div>
  <a href="{{ route('trainer-portal.index') }}" class="nav-item {{ request()->routeIs('trainer-portal.*') ? 'active' : '' }}">📚 Program Saya</a>
  <a href="{{ route('attendances.mine') }}" class="nav-item {{ request()->routeIs('attendances.mine') ? 'active' : '' }}">🕒 Absensi Saya</a>
  @endif

  @if(in_array($role, ['admin','owner','staff']))
  <div class="nav-section">SDM</div>
  <a href="{{ route('attendances.mine') }}" class="nav-item {{ request()->routeIs('attendances.mine') ? 'active' : '' }}">🕒 Absensi Saya</a>
  <a href="{{ route('payroll.mine') }}" class="nav-item {{ request()->routeIs('payroll.mine') ? 'active' : '' }}">💰 Slip Gaji Saya</a>
  @if(in_array($role, ['admin','owner']))
  <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">🧑‍💼 Karyawan</a>
  <a href="{{ route('attendances.index') }}" class="nav-item {{ request()->routeIs('attendances.index') || request()->routeIs('attendances.create') || request()->routeIs('attendances.edit') ? 'active' : '' }}">📆 Absensi Pegawai</a>
  <a href="{{ route('payroll.index') }}" class="nav-item {{ request()->routeIs('payroll.index') || request()->routeIs('payroll.create') || request()->routeIs('payroll.show') ? 'active' : '' }}">💵 Payroll</a>
  <a href="{{ route('required-positions.index') }}" class="nav-item {{ request()->routeIs('required-positions.*') ? 'active' : '' }}">🗒️ Jabatan Minimal</a>
  <a href="{{ route('attendance-scan.show') }}" class="nav-item {{ request()->routeIs('attendance-scan.*') ? 'active' : '' }}">📷 Scan QR Absensi</a>
  @endif
  @endif

  @if($role === 'security')
  <div class="nav-section">Portal Security</div>
  <a href="{{ route('attendance-scan.show') }}" class="nav-item {{ request()->routeIs('attendance-scan.*') ? 'active' : '' }}">📷 Scan QR Absensi</a>
  @endif

  @if(in_array($role, ['admin','owner']))
  <div class="nav-section">Administrasi</div>
  <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">👥 Pengguna</a>
  @if($role === 'admin')
  <a href="{{ route('roles.index') }}" class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">🔑 Role</a>
  @endif
  <a href="{{ route('tenant-profile.edit') }}" class="nav-item {{ request()->routeIs('tenant-profile.*') ? 'active' : '' }}">⚙️ Profil BLK</a>
  @endif

  @if(in_array($role, ['admin','staff','owner']) && $tenant)
  <div class="nav-section">Lainnya</div>
  <a href="{{ route('public.tenant.show', $tenant->slug) }}" target="_blank" class="nav-item">🌐 Lihat Halaman Publik</a>
  @endif

  @if($role === 'superadmin')
  <div class="nav-section">Superadmin</div>
  <a href="{{ route('superadmin.dashboard') }}" class="nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
  <a href="{{ route('superadmin.tenants.index') }}" class="nav-item {{ request()->routeIs('superadmin.tenants.*') ? 'active' : '' }}">🏫 Kelola Tenant BLK</a>
  <a href="{{ route('superadmin.profile.edit') }}" class="nav-item {{ request()->routeIs('superadmin.profile.*') ? 'active' : '' }}">👤 Profil</a>
  <a href="{{ route('superadmin.app-profile.edit') }}" class="nav-item {{ request()->routeIs('superadmin.app-profile.*') ? 'active' : '' }}">🏷️ App Profile</a>
  @endif

  @if($role === 'ultraadmin')
  <div class="nav-section">Ultraadmin</div>
  <a href="{{ route('ultraadmin.dashboard') }}" class="nav-item {{ request()->routeIs('ultraadmin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
  <a href="{{ route('ultraadmin.superadmin-users.index') }}" class="nav-item {{ request()->routeIs('ultraadmin.superadmin-users.*') ? 'active' : '' }}">👤 Kelola Superadmin</a>
  @endif
</div>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

{{-- Main content --}}
<div class="main">
  @if(session('success'))
    <div class="flash flash-success">✅ {{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="flash flash-error">❌ {{ session('error') }}</div>
  @endif
  @if(session('info'))
    <div class="flash flash-info">ℹ️ {{ session('info') }}</div>
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
<script>
(function () {
  var btn = document.getElementById('tbMenuBtn');
  var sidebar = document.getElementById('sidebarEl');
  var backdrop = document.getElementById('sidebarBackdrop');
  if (!btn || !sidebar || !backdrop) return;
  function closeMenu() { sidebar.classList.remove('is-open'); backdrop.classList.remove('is-open'); }
  btn.addEventListener('click', function () {
    sidebar.classList.toggle('is-open');
    backdrop.classList.toggle('is-open');
  });
  backdrop.addEventListener('click', closeMenu);
  sidebar.querySelectorAll('.nav-item').forEach(function (a) { a.addEventListener('click', closeMenu); });
})();
</script>
@stack('scripts')
</body>
</html>
