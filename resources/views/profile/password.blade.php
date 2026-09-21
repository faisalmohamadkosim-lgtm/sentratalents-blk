@extends('layouts.app')
@section('title','Ganti Password')
@section('content')

<div class="page-header">
  <div class="page-title">Ganti Password</div>
  <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm">← Dashboard</a>
</div>

<div class="card" style="max-width:440px">
  <form method="POST" action="{{ route('profile.password.update') }}" id="pwForm">
    @csrf

    <div class="form-group">
      <label>Password Saat Ini *</label>
      <div style="position:relative">
        <input type="password" name="current_password" id="cur_pw" required style="padding-right:2.5rem">
        <button type="button" onclick="togglePw('cur_pw',this)" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:.95rem;color:var(--text3)">👁</button>
      </div>
    </div>

    <div class="form-group">
      <label>Password Baru *</label>
      <div style="position:relative">
        <input type="password" name="password" id="new_pw" minlength="8" required style="padding-right:2.5rem" oninput="checkStrength(this.value);checkMatch()">
        <button type="button" onclick="togglePw('new_pw',this)" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:.95rem;color:var(--text3)">👁</button>
      </div>
      {{-- Strength bar --}}
      <div style="margin-top:.4rem">
        <div style="height:5px;border-radius:3px;background:var(--border);overflow:hidden">
          <div id="strBar" style="height:100%;width:0;border-radius:3px;transition:width .25s,background .25s"></div>
        </div>
        <div id="strLabel" style="font-size:.72rem;color:var(--text3);margin-top:.2rem"></div>
      </div>
    </div>

    <div class="form-group">
      <label>Konfirmasi Password Baru *</label>
      <div style="position:relative">
        <input type="password" name="password_confirmation" id="conf_pw" required style="padding-right:2.5rem" oninput="checkMatch()">
        <button type="button" onclick="togglePw('conf_pw',this)" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:.95rem;color:var(--text3)">👁</button>
      </div>
      <div id="matchMsg" style="font-size:.75rem;margin-top:.3rem"></div>
    </div>

    <button type="submit" id="submitBtn" class="btn btn-primary">Ubah Password</button>
  </form>
</div>

@push('scripts')
<script>
function togglePw(id, btn) {
  var el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
  btn.style.opacity = el.type === 'text' ? '1' : '.5';
}

var STR_LEVELS = [
  { min:0,  color:'#ef4444', label:'Sangat lemah', pct:10 },
  { min:20, color:'#f97316', label:'Lemah',         pct:30 },
  { min:40, color:'#eab308', label:'Cukup',         pct:55 },
  { min:60, color:'#22c55e', label:'Kuat',          pct:78 },
  { min:80, color:'#15803d', label:'Sangat kuat',   pct:100 },
];

function passwordScore(pw) {
  if (!pw) return 0;
  var s = 0;
  if (pw.length >= 8)  s += 20;
  if (pw.length >= 12) s += 20;
  if (/[A-Z]/.test(pw))       s += 15;
  if (/[a-z]/.test(pw))       s += 15;
  if (/[0-9]/.test(pw))       s += 15;
  if (/[^A-Za-z0-9]/.test(pw)) s += 15;
  return Math.min(s, 100);
}

function checkStrength(pw) {
  var score = passwordScore(pw);
  var level = STR_LEVELS[0];
  STR_LEVELS.forEach(function(l){ if (score >= l.min) level = l; });
  var bar   = document.getElementById('strBar');
  var label = document.getElementById('strLabel');
  if (pw) {
    bar.style.width = level.pct + '%';
    bar.style.background = level.color;
    label.textContent = level.label;
    label.style.color  = level.color;
  } else {
    bar.style.width = '0';
    label.textContent = '';
  }
}

function checkMatch() {
  var np   = document.getElementById('new_pw').value;
  var cp   = document.getElementById('conf_pw').value;
  var msg  = document.getElementById('matchMsg');
  var btn  = document.getElementById('submitBtn');
  if (!cp) { msg.textContent = ''; btn.disabled = false; return; }
  if (np === cp) {
    msg.textContent = '✓ Password cocok';
    msg.style.color = '#22c55e';
    btn.disabled = false;
  } else {
    msg.textContent = '✗ Password tidak cocok';
    msg.style.color = '#ef4444';
    btn.disabled = true;
  }
}
</script>
@endpush
@endsection
