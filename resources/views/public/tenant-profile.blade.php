<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $tenant->name }} — BLK Connect</title>
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
.brand{display:flex;align-items:center;gap:.55rem;font-weight:800;font-size:1.1rem;color:var(--primary-dark)}
.nav-links{display:flex;align-items:center;gap:1.25rem;font-size:.85rem;font-weight:600}
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;border-radius:8px;font-size:.82rem;font-weight:700;cursor:pointer;border:1.5px solid var(--border)}
.btn:hover{border-color:var(--primary);color:var(--primary)}

.cover{height:220px;background:linear-gradient(135deg,#0f766e,#134e4a);position:relative;overflow:hidden}
.cover img{width:100%;height:100%;object-fit:cover;opacity:.9}
.profile-header{display:flex;align-items:flex-end;gap:1.25rem;margin-top:-56px;position:relative;z-index:2;padding-bottom:1.5rem;flex-wrap:wrap}
.profile-logo{width:100px;height:100px;border-radius:16px;background:#fff;border:4px solid #fff;box-shadow:0 8px 24px rgba(0,0,0,.15);display:flex;align-items:center;justify-content:center;overflow:hidden;font-weight:800;color:var(--primary-dark);font-size:2.2rem;flex-shrink:0}
.profile-logo img{width:100%;height:100%;object-fit:contain}
.profile-info{padding-bottom:.35rem}
.profile-name{font-size:1.55rem;font-weight:800;color:#fff;text-shadow:0 1px 8px rgba(0,0,0,.45)}
.profile-meta{font-size:.85rem;color:var(--text2);margin-top:.35rem;display:flex;gap:1rem;flex-wrap:wrap}
.profile-meta span{display:flex;align-items:center;gap:.3rem}

.tabs{display:flex;gap:.25rem;border-bottom:2px solid var(--border);margin-bottom:1.75rem;flex-wrap:wrap}
.tab-link{padding:.6rem 1rem;font-size:.85rem;font-weight:700;color:var(--text2);border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer}
.tab-link.active,.tab-link:hover{color:var(--primary);border-color:var(--primary)}

.section{padding:2rem 0}
.section-title{font-size:1.15rem;font-weight:800;margin-bottom:1.25rem}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:1.1rem}
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.card-photo{height:130px;background:var(--surface2);display:flex;align-items:center;justify-content:center;color:var(--text3);font-size:2rem}
.card-photo img{width:100%;height:100%;object-fit:cover}
.card-body{padding:.9rem 1rem}
.card-title{font-weight:700;font-size:.95rem;margin-bottom:.25rem}
.card-sub{font-size:.78rem;color:var(--primary-dark);font-weight:600;margin-bottom:.4rem}
.card-desc{font-size:.8rem;color:var(--text2);line-height:1.5}

.trainer-card{display:flex;gap:.9rem;padding:1rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}
.trainer-photo{width:64px;height:64px;border-radius:50%;object-fit:cover;background:var(--surface2);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--text3)}

.program-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.1rem;margin-bottom:1rem}
.program-head{display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap}
.program-name{font-weight:800;font-size:1.02rem}
.program-tags{display:flex;gap:.4rem;flex-wrap:wrap;margin-top:.4rem}
.tag{background:var(--primary-light);color:var(--primary-dark);font-size:.71rem;font-weight:700;padding:.2rem .55rem;border-radius:20px}
.stage-list{margin-top:.9rem;border-top:1px dashed var(--border);padding-top:.75rem}
.stage-item{display:flex;gap:.6rem;font-size:.82rem;padding:.3rem 0;align-items:baseline}
.stage-num{width:22px;height:22px;border-radius:50%;background:var(--primary-light);color:var(--primary-dark);font-size:.7rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0}

.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.75rem}
.gallery-grid img{width:100%;height:130px;object-fit:cover;border-radius:8px}

.cta-graduates{background:linear-gradient(135deg,#0f766e,#134e4a);color:#fff;border-radius:var(--radius);padding:1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap}
.cta-graduates .btn{background:#fff;color:var(--primary-dark);border:none}

.empty{color:var(--text3);font-size:.85rem}
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

<div class="cover">
  @if($tenant->coverImageUrl())<img src="{{ $tenant->coverImageUrl() }}" alt="">@endif
</div>

<div class="container">
  <div class="profile-header">
    <div class="profile-logo">
      @if($tenant->logoUrl())<img src="{{ $tenant->logoUrl() }}" alt="">@else{{ strtoupper(substr($tenant->name,0,1)) }}@endif
    </div>
    <div class="profile-info">
      <div class="profile-name">{{ $tenant->name }}</div>
      <div class="profile-meta">
        @if($tenant->city)<span>📍 {{ $tenant->city }}{{ $tenant->province ? ', '.$tenant->province : '' }}</span>@endif
        @if($tenant->accreditation)<span>🏆 {{ $tenant->accreditation }}</span>@endif
        @if($tenant->established_year)<span>📅 Berdiri {{ $tenant->established_year }}</span>@endif
        @if($tenant->phone)<span>📞 {{ $tenant->phone }}</span>@endif
      </div>
    </div>
  </div>

  @if($tenant->description)
  <div class="section" style="padding-top:0;padding-bottom:1.5rem">
    <p style="font-size:.92rem;color:var(--text2);max-width:760px">{{ $tenant->description }}</p>
  </div>
  @endif

  {{-- Fasilitas --}}
  <div class="section" style="padding-top:0">
    <div class="section-title">Fasilitas</div>
    @if($tenant->facilities->isEmpty())
      <p class="empty">Belum ada data fasilitas.</p>
    @else
    <div class="grid">
      @foreach($tenant->facilities as $f)
      <div class="card">
        <div class="card-photo">
          @if($f->photoUrl())<img src="{{ $f->photoUrl() }}" alt="">@else🏭@endif
        </div>
        <div class="card-body">
          <div class="card-title">{{ $f->name }}</div>
          @if($f->description)<div class="card-desc">{{ $f->description }}</div>@endif
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>

  {{-- Trainer --}}
  <div class="section" style="padding-top:0">
    <div class="section-title">Trainer / Instruktur</div>
    @if($tenant->trainers->isEmpty())
      <p class="empty">Belum ada data trainer.</p>
    @else
    <div class="grid">
      @foreach($tenant->trainers as $t)
      <div class="trainer-card">
        @if($t->photoUrl())
          <img src="{{ $t->photoUrl() }}" class="trainer-photo" alt="">
        @else
          <div class="trainer-photo">🧑‍🏫</div>
        @endif
        <div>
          <div class="card-title">{{ $t->name }}</div>
          @if($t->specialization)<div class="card-sub">{{ $t->specialization }}</div>@endif
          @if($t->bio)<div class="card-desc">{{ Str::limit($t->bio, 90) }}</div>@endif
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>

  {{-- Struktur Organisasi --}}
  <div class="section" style="padding-top:0">
    <div class="section-title">Struktur Organisasi</div>
    @if($tenant->orgStructureMembers->isEmpty())
      <p class="empty">Belum ada data struktur organisasi.</p>
    @else
    <div class="grid">
      @foreach($tenant->orgStructureMembers as $m)
      <div class="trainer-card">
        @if($m->photoUrl())
          <img src="{{ $m->photoUrl() }}" class="trainer-photo" alt="">
        @else
          <div class="trainer-photo">🗂️</div>
        @endif
        <div>
          <div class="card-title">{{ $m->name }}</div>
          <div class="card-sub">{{ $m->position }}</div>
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>

  {{-- Program --}}
  <div class="section" style="padding-top:0">
    <div class="section-title">Program Pelatihan</div>
    @if($tenant->programs->isEmpty())
      <p class="empty">Belum ada program pelatihan.</p>
    @else
      @foreach($tenant->programs as $p)
      <div class="program-card">
        <div class="program-head">
          <div>
            <div class="program-name">{{ $p->name }}</div>
            <div class="program-tags">
              @if($p->category)<span class="tag">{{ $p->category }}</span>@endif
              @if($p->duration_days)<span class="tag">{{ $p->duration_days }} hari</span>@endif
              @if($p->quota)<span class="tag">Kuota {{ $p->quota }}</span>@endif
              @if($p->leadTrainer)<span class="tag">Pengampu: {{ $p->leadTrainer->name }}</span>@endif
            </div>
          </div>
        </div>
        @if($p->description)<p class="card-desc" style="margin-top:.6rem">{{ $p->description }}</p>@endif
        @if($p->stages->isNotEmpty())
        <div class="stage-list">
          @foreach($p->stages as $stage)
          <div class="stage-item">
            <div class="stage-num">{{ $loop->iteration }}</div>
            <div>
              <strong>{{ $stage->title }}</strong>
              @if($stage->duration_days) — {{ $stage->duration_days }} hari @endif
              @if($stage->description)<div style="color:var(--text3)">{{ $stage->description }}</div>@endif
            </div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
      @endforeach
    @endif
  </div>

  {{-- Galeri --}}
  @if($tenant->galleries->isNotEmpty())
  <div class="section" style="padding-top:0">
    <div class="section-title">Galeri</div>
    <div class="gallery-grid">
      @foreach($tenant->galleries as $g)
      <img src="{{ $g->photoUrl() }}" alt="{{ $g->caption }}" title="{{ $g->caption }}">
      @endforeach
    </div>
  </div>
  @endif

  {{-- Lulusan --}}
  <div class="section">
    <div class="cta-graduates">
      <div>
        <div style="font-weight:800;font-size:1.1rem">{{ $graduatesCount }} Lulusan dari {{ $tenant->name }}</div>
        <div style="font-size:.85rem;opacity:.9">Lihat daftar lengkap alumni yang telah lulus pelatihan.</div>
      </div>
      <a href="{{ route('public.graduates', ['tenant' => $tenant->slug]) }}" class="btn">Lihat Daftar Lulusan →</a>
    </div>
  </div>

</div>

<footer>
  BLK Connect &copy; {{ date('Y') }} — <a href="{{ route('home') }}">← Kembali ke Direktori BLK</a>
</footer>

</body>
</html>
