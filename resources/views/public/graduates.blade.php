<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Daftar Lulusan — BLK Connect</title>
<style>
*,::before,::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --primary:#0f766e;--primary-dark:#0d5c56;--primary-light:#ccfbf1;
  --bg:#f8fafc;--surface:#fff;--surface2:#f1f5f9;
  --text1:#0f172a;--text2:#475569;--text3:#94a3b8;--border:#e2e8f0;--radius:12px;
}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text1);line-height:1.5}
a{color:inherit;text-decoration:none}
.container{max-width:1080px;margin:0 auto;padding:0 1.25rem}

.topbar{background:var(--surface);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:50}
.topbar-inner{display:flex;align-items:center;justify-content:space-between;padding:.9rem 0}
.brand{font-weight:800;font-size:1.1rem;color:var(--primary-dark)}
.nav-links{display:flex;align-items:center;gap:1.25rem;font-size:.85rem;font-weight:600}
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;border-radius:8px;font-size:.82rem;font-weight:700;cursor:pointer;border:1.5px solid var(--border)}
.btn:hover{border-color:var(--primary);color:var(--primary)}

.hero{background:linear-gradient(135deg,#0f766e 0%,#134e4a 100%);color:#fff;padding:2.5rem 0 2rem}
.hero h1{font-size:1.7rem;font-weight:800;margin-bottom:.4rem}
.hero p{font-size:.92rem;opacity:.9}

.section{padding:2rem 0}
.filters{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1rem;display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem}
.filters select,.filters input{padding:.5rem .75rem;border:1.5px solid var(--border);border-radius:8px;font-size:.85rem;min-width:160px}
.filters button{background:var(--primary);color:#fff;border:none;border-radius:8px;padding:.5rem 1.1rem;font-weight:700;font-size:.85rem;cursor:pointer}
.filters .reset{background:transparent;color:var(--text2);border:1.5px solid var(--border)}

table{width:100%;border-collapse:collapse;background:var(--surface);border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);font-size:.85rem}
th{text-align:left;padding:.7rem 1rem;background:var(--surface2);font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text2);font-weight:700}
td{padding:.7rem 1rem;border-top:1px solid var(--border)}
tr:hover td{background:var(--surface2)}
.badge{display:inline-flex;padding:.15rem .55rem;border-radius:20px;font-size:.7rem;font-weight:700;background:var(--primary-light);color:var(--primary-dark)}
.empty{text-align:center;padding:3rem;color:var(--text3)}

.pagination{display:flex;gap:.35rem;align-items:center;flex-wrap:wrap;margin-top:1.5rem;justify-content:center}
.pagination a,.pagination span{padding:.4rem .75rem;border-radius:6px;font-size:.8rem;border:1.5px solid var(--border);color:var(--text2)}
.pagination a:hover{border-color:var(--primary);color:var(--primary)}
.pagination a.active,.pagination span.active{background:var(--primary);color:#fff;border-color:var(--primary)}

footer{background:#0f172a;color:#94a3b8;padding:2rem 0;text-align:center;font-size:.8rem;margin-top:2rem}
footer a{color:#5eead4}
</style>
</head>
<body>

<div class="topbar">
  <div class="container topbar-inner">
    <div class="brand">BLK Connect</div>
    <div class="nav-links">
      <a href="{{ route('home') }}">Direktori BLK</a>
      <a href="{{ route('public.graduates') }}">Lulusan</a>
      <a href="{{ route('login') }}" class="btn">Login Staf</a>
    </div>
  </div>
</div>

<div class="hero">
  <div class="container">
    <h1>Daftar Lulusan</h1>
    <p>Alumni yang telah menyelesaikan pelatihan di BLK seluruh Indonesia.</p>
  </div>
</div>

<div class="section">
  <div class="container">
    <form method="GET" class="filters">
      <select name="tenant">
        <option value="">Semua BLK</option>
        @foreach($tenants as $t)
          <option value="{{ $t->slug }}" {{ $tenantSlug === $t->slug ? 'selected' : '' }}>{{ $t->name }}</option>
        @endforeach
      </select>
      <select name="program">
        <option value="">Semua Program</option>
        @foreach($programs as $p)
          <option value="{{ $p->id }}" {{ (string) $programId === (string) $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
        @endforeach
      </select>
      <select name="year">
        <option value="">Semua Tahun</option>
        @foreach($years as $y)
          <option value="{{ $y }}" {{ (string) $year === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
      </select>
      <button type="submit">Terapkan Filter</button>
      @if($tenantSlug || $programId || $year)
      <a href="{{ route('public.graduates') }}" class="btn reset">Reset</a>
      @endif
    </form>

    @if($graduates->isEmpty())
      <div class="empty">Belum ada data lulusan yang sesuai filter.</div>
    @else
    <div style="overflow-x:auto">
    <table>
      <thead><tr><th>Nama</th><th>BLK</th><th>Program</th><th>Angkatan</th><th>Tgl Lulus</th><th>No. Sertifikat</th></tr></thead>
      <tbody>
        @foreach($graduates as $g)
        <tr>
          <td style="font-weight:700">{{ $g->participant->name ?? '-' }}</td>
          <td><a href="{{ route('public.tenant.show', $g->tenant->slug) }}">{{ $g->tenant->name ?? '-' }}</a></td>
          <td>{{ $g->batch->program->name ?? '-' }}</td>
          <td><span class="badge">{{ $g->batch->code ?? '-' }}</span></td>
          <td>{{ $g->graduation_date?->format('d/m/Y') ?? '-' }}</td>
          <td style="font-family:monospace;font-size:.78rem">{{ $g->certificate_no ?? '-' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    @if($graduates->hasPages())
    <div class="pagination">{!! $graduates->links('vendor.pagination.simple') !!}</div>
    @endif
    @endif
  </div>
</div>

<footer>
  BLK Connect &copy; {{ date('Y') }} — <a href="{{ route('home') }}">← Kembali ke Direktori BLK</a>
</footer>

</body>
</html>
