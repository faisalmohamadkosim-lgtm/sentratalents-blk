<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ \App\Models\AppSetting::get('nama_aplikasi', 'BLK Connect') }} — Direktori Balai Latihan Kerja</title>
<style>
*,::before,::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --primary:#0f766e;--primary-dark:#0d5c56;--primary-light:#ccfbf1;
  --accent:#f59e0b;
  --bg:#f8fafc;--surface:#fff;--surface2:#f1f5f9;
  --text1:#0f172a;--text2:#475569;--text3:#94a3b8;--border:#e2e8f0;--radius:12px;
}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text1);line-height:1.5}
a{color:inherit;text-decoration:none}
.container{max-width:1120px;margin:0 auto;padding:0 1.25rem}

/* topbar */
.topbar{background:var(--surface);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:50}
.topbar-inner{display:flex;align-items:center;justify-content:space-between;padding:.9rem 0}
.brand{display:flex;align-items:center;gap:.55rem;font-weight:800;font-size:1.15rem;color:var(--primary-dark)}
.brand small{font-size:.68rem;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.04em;margin-left:.15rem}
@media(max-width:640px){.brand small{display:none}}
.brand-icon{width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,var(--primary),#134e4a);display:flex;align-items:center;justify-content:center}
.nav-links{display:flex;align-items:center;gap:1.5rem;font-size:.88rem;font-weight:600}
.nav-links a:hover{color:var(--primary)}
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.1rem;border-radius:8px;font-size:.85rem;font-weight:700;cursor:pointer;border:1.5px solid transparent;transition:all .15s}
.btn-primary{background:var(--primary);color:#fff}
.btn-primary:hover{background:var(--primary-dark)}
.btn-outline{border-color:var(--border);color:var(--text1)}
.btn-outline:hover{border-color:var(--primary);color:var(--primary)}

/* hero */
.hero{background:linear-gradient(135deg,#0f766e 0%,#134e4a 100%);color:#fff;padding:3.5rem 0 3rem}
.hero h1{font-size:2.1rem;font-weight:800;margin-bottom:.6rem}
.hero p{font-size:1rem;opacity:.9;max-width:620px;margin-bottom:1.75rem}
.search-bar{background:var(--surface);border-radius:12px;padding:.5rem;display:flex;gap:.5rem;max-width:560px;box-shadow:0 10px 30px rgba(0,0,0,.15)}
.search-bar input{flex:1;border:none;padding:.6rem .9rem;font-size:.9rem;border-radius:8px;color:var(--text1)}
.search-bar input:focus{outline:none}
.search-bar button{background:var(--primary);color:#fff;border:none;border-radius:8px;padding:.6rem 1.3rem;font-weight:700;font-size:.85rem;cursor:pointer}

/* directory grid */
.section{padding:2.75rem 0}
.section-title{font-size:1.35rem;font-weight:800;margin-bottom:1.5rem}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem}
.tenant-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;transition:box-shadow .2s,transform .2s;display:flex;flex-direction:column}
.tenant-card:hover{box-shadow:0 12px 28px rgba(15,23,42,.09);transform:translateY(-2px)}
.tenant-cover{height:110px;background:linear-gradient(135deg,#0f766e,#134e4a);position:relative}
.tenant-cover img{width:100%;height:100%;object-fit:cover}
.tenant-logo{width:56px;height:56px;border-radius:10px;background:#fff;border:3px solid #fff;position:absolute;bottom:-24px;left:1rem;box-shadow:0 3px 10px rgba(0,0,0,.15);display:flex;align-items:center;justify-content:center;overflow:hidden;font-weight:800;color:var(--primary-dark);font-size:1.3rem}
.tenant-logo img{width:100%;height:100%;object-fit:contain}
.tenant-body{padding:2rem 1.1rem 1.1rem}
.tenant-name{font-weight:700;font-size:1.02rem;margin-bottom:.3rem}
.tenant-meta{font-size:.8rem;color:var(--text2);margin-bottom:.6rem;display:flex;align-items:center;gap:.3rem}
.tenant-tags{display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:1rem}
.tag{background:var(--primary-light);color:var(--primary-dark);font-size:.71rem;font-weight:700;padding:.2rem .55rem;border-radius:20px}
.empty{text-align:center;padding:3rem 1rem;color:var(--text3)}

/* news */
.news-scroll{display:flex;gap:1rem;overflow-x:auto;padding-bottom:.75rem;scroll-snap-type:x proximity;-webkit-overflow-scrolling:touch}
.news-scroll::-webkit-scrollbar{height:8px}
.news-scroll::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
.news-scroll::-webkit-scrollbar-track{background:transparent}
.news-card{flex:0 0 260px;scroll-snap-align:start;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);
  overflow:hidden;transition:box-shadow .2s,transform .2s;display:flex;flex-direction:column}
.news-card:hover{box-shadow:0 12px 28px rgba(15,23,42,.09);transform:translateY(-2px)}
.news-thumb{height:140px;background:var(--surface2);display:flex;align-items:center;justify-content:center;color:var(--text3);font-size:1.8rem}
.news-thumb img{width:100%;height:100%;object-fit:cover}
.news-body{padding:.9rem 1rem 1.1rem;display:flex;flex-direction:column;gap:.35rem;flex:1}
.news-date{font-size:.7rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.04em}
.news-title{font-weight:700;font-size:.92rem;line-height:1.35}
.news-excerpt{font-size:.78rem;color:var(--text2);line-height:1.5;
  display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.news-hint{margin-top:.5rem;text-align:right;font-size:.7rem;color:var(--text3)}
@media(max-width:640px){.news-card{flex-basis:220px}}

/* gallery */
.gallery-section{background:var(--surface2)}
.gallery-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:.9rem}
.gallery-item{position:relative;border-radius:var(--radius);overflow:hidden;aspect-ratio:4/3;background:var(--surface2);box-shadow:0 1px 3px rgba(15,23,42,.06)}
.gallery-item img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.gallery-item:hover img{transform:scale(1.06)}
.gallery-caption{position:absolute;left:0;right:0;bottom:0;padding:1.5rem .8rem .65rem;
  background:linear-gradient(to top,rgba(0,0,0,.75),transparent);color:#fff}
.gallery-caption .cap-text{font-size:.8rem;font-weight:600;line-height:1.3}
.gallery-caption .cap-tenant{font-size:.7rem;opacity:.85;margin-top:.15rem}
@media(max-width:900px){.gallery-grid{grid-template-columns:repeat(3,1fr)}}

/* pagination */
.pagination{display:flex;gap:.35rem;align-items:center;flex-wrap:wrap;margin-top:1.75rem;justify-content:center}
.pagination a,.pagination span{padding:.4rem .75rem;border-radius:6px;font-size:.8rem;border:1.5px solid var(--border);color:var(--text2)}
.pagination a:hover{border-color:var(--primary);color:var(--primary)}
.pagination a.active,.pagination span.active{background:var(--primary);color:#fff;border-color:var(--primary)}

footer{background:#0f172a;color:#94a3b8;padding:2rem 0;text-align:center;font-size:.8rem;margin-top:2rem}
footer a{color:#5eead4}

@media(max-width:640px){
  .nav-links{gap:.75rem;font-size:.8rem}
  .hero h1{font-size:1.6rem}
  .gallery-grid{grid-template-columns:repeat(2,1fr)}
}
</style>
</head>
<body>

<div class="topbar">
  <div class="container topbar-inner">
    <div class="brand">
      <div class="brand-icon">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M12 3l9 5-9 5-9-5 9-5z" stroke="#fff" stroke-width="1.8" stroke-linejoin="round"/><path d="M5 10.5V16c0 1.5 3 3 7 3s7-1.5 7-3v-5.5" stroke="#fff" stroke-width="1.8"/></svg>
      </div>
      {{ \App\Models\AppSetting::get('nama_aplikasi', 'BLK Connect') }}
      @if($tagline = \App\Models\AppSetting::get('tagline'))
        <small>{{ $tagline }}</small>
      @endif
    </div>
    <div class="nav-links">
      <a href="{{ route('home') }}">Direktori BLK</a>
      <a href="{{ route('public.graduates') }}">Lulusan</a>
      <a href="{{ route('login') }}" class="btn btn-outline">Login Staf</a>
    </div>
  </div>
</div>

<div class="hero">
  <div class="container">
    <h1>Direktori Balai Latihan Kerja</h1>
    <p>Temukan profil, program pelatihan, dan data lulusan dari BLK di seluruh Indonesia yang tergabung dalam platform ini.</p>
    <form method="GET" class="search-bar">
      <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama BLK, kota, atau provinsi...">
      <button type="submit">Cari</button>
    </form>
  </div>
</div>

<div class="section">
  <div class="container">
    <div class="section-title">{{ $tenants->total() }} BLK Terdaftar</div>

    @if($tenants->isEmpty())
      <div class="empty">Tidak ditemukan BLK yang sesuai pencarian.</div>
    @else
    <div class="grid">
      @foreach($tenants as $tenant)
      <a href="{{ route('public.tenant.show', $tenant->slug) }}" class="tenant-card">
        <div class="tenant-cover">
          @if($tenant->coverImageUrl())<img src="{{ $tenant->coverImageUrl() }}" alt=""> @endif
          <div class="tenant-logo">
            @if($tenant->logoUrl())<img src="{{ $tenant->logoUrl() }}" alt="">@else{{ strtoupper(substr($tenant->name,0,1)) }}@endif
          </div>
        </div>
        <div class="tenant-body">
          <div class="tenant-name">{{ $tenant->name }}</div>
          <div class="tenant-meta">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M12 22s7-7.5 7-13a7 7 0 10-14 0c0 5.5 7 13 7 13z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="9" r="2.3" stroke="currentColor" stroke-width="1.8"/></svg>
            {{ $tenant->city ?? 'Lokasi belum diisi' }}{{ $tenant->province ? ', '.$tenant->province : '' }}
          </div>
          <div class="tenant-tags">
            <span class="tag">{{ $tenant->programs_count }} Program</span>
            @if($tenant->accreditation)<span class="tag">{{ $tenant->accreditation }}</span>@endif
          </div>
        </div>
      </a>
      @endforeach
    </div>
    @if($tenants->hasPages())
    <div class="pagination">{!! $tenants->links('vendor.pagination.simple') !!}</div>
    @endif
    @endif
  </div>
</div>

@if($news->isNotEmpty())
<div class="section">
  <div class="container">
    <div class="section-title">Berita &amp; Pengumuman</div>
    <div class="news-scroll">
      @foreach($news as $article)
      @php $isExternal = $article->link_url && str_starts_with($article->link_url, 'http'); @endphp
      <{{ $article->link_url ? 'a' : 'div' }}
        @if($article->link_url) href="{{ $article->link_url }}" @if($isExternal) target="_blank" rel="noopener noreferrer" @endif @endif
        class="news-card">
        <div class="news-thumb">
          @if($article->imageUrl())<img src="{{ $article->imageUrl() }}" alt="{{ $article->title }}" loading="lazy">@else📰@endif
        </div>
        <div class="news-body">
          @if($article->published_at)<div class="news-date">{{ $article->published_at->translatedFormat('d M Y') }}</div>@endif
          <div class="news-title">{{ $article->title }}</div>
          @if($article->excerpt)<div class="news-excerpt">{{ $article->excerpt }}</div>@endif
          @if($article->link_url)<div class="news-hint">Baca selengkapnya →</div>@endif
        </div>
      </{{ $article->link_url ? 'a' : 'div' }}>
      @endforeach
    </div>
  </div>
</div>
@endif

@if($galleryPhotos->isNotEmpty())
<div class="section gallery-section">
  <div class="container">
    <div class="section-title">Galeri Kegiatan BLK</div>
    <div class="gallery-grid">
      @foreach($galleryPhotos as $photo)
      @php $isExternal = $photo->link_url && str_starts_with($photo->link_url, 'http'); @endphp
      <{{ $photo->link_url ? 'a' : 'div' }}
        @if($photo->link_url) href="{{ $photo->link_url }}" @if($isExternal) target="_blank" rel="noopener noreferrer" @endif @endif
        class="gallery-item">
        <img src="{{ $photo->photoUrl() }}" alt="{{ $photo->caption ?? 'Galeri BLK' }}" loading="lazy">
        @if($photo->caption)
        <div class="gallery-caption">
          <div class="cap-text">{{ $photo->caption }}</div>
        </div>
        @endif
      </{{ $photo->link_url ? 'a' : 'div' }}>
      @endforeach
    </div>
  </div>
</div>
@endif

@php $institusi = \App\Models\AppSetting::get('institusi'); @endphp
<footer>
  {{ \App\Models\AppSetting::get('nama_aplikasi', 'BLK Connect') }} &copy; {{ date('Y') }}@if($institusi) — Diselenggarakan oleh {{ $institusi }}@endif.
  <a href="{{ route('public.graduates') }}">Lihat daftar lulusan →</a>
</footer>

</body>
</html>
