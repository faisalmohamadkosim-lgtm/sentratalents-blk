{{-- Kartu kamera scan QR absensi + JS-nya -- dipakai baik oleh halaman scan yang butuh
     login (attendance-scan/show.blade.php) maupun kiosk publik tanpa login
     (public/attendance-scan.blade.php). Butuh var $handleUrl (endpoint POST hasil scan). --}}
<style>
/* Kamera depan (facingMode "user") di-mirror horizontal supaya terasa alami seperti
   cermin -- kamera belakang (facingMode "environment") TIDAK di-mirror, lihat
   applyMirrorIfFrontCamera() di bawah. */
#reader video.is-mirrored{transform:scaleX(-1)}
</style>

<div class="card" style="max-width:480px">
  <div id="reader" style="width:100%"></div>
  <p id="scanHint" style="text-align:center;color:var(--text3);font-size:.85rem;margin-top:.75rem">Arahkan kamera ke QR absensi karyawan/trainer.</p>

  <div style="text-align:center;margin-top:.5rem">
    <button type="button" id="switchCameraBtn" class="btn btn-outline btn-sm">🔄 Ganti Kamera</button>
  </div>

  <div id="scanResult" style="display:none;margin-top:1rem;padding:1rem;border-radius:10px"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function(){
  var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
  var ENDPOINT = @json($handleUrl);
  var AUTO_RESCAN_SECONDS = 3;

  var resultBox = document.getElementById('scanResult');
  var hint = document.getElementById('scanHint');
  var busy = false;
  var resumeTimer = null;

  var html5QrCode = new Html5Qrcode('reader');
  var switchBtn = document.getElementById('switchCameraBtn');
  var currentFacingMode = 'user'; // default kamera depan
  var switching = false;

  function scanConfigFor(facingMode) {
    return { fps: 10, qrbox: 240, videoConstraints: { facingMode: { ideal: facingMode } } };
  }

  function showResult(ok, html) {
    resultBox.style.display = 'block';
    resultBox.style.background = ok ? '#dcfce7' : '#fee2e2';
    resultBox.style.color = ok ? '#166534' : '#991b1b';
    resultBox.innerHTML = html;
  }

  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, function(c) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
  }

  // Kamera belakang (environment) TIDAK di-mirror -- itu yang biasa dipakai memindai QR
  // orang lain. Kamera depan (user) -- atau webcam laptop yang tidak melaporkan facingMode
  // sama sekali, biasanya berarti depan juga -- di-mirror supaya terasa seperti cermin,
  // bukan terbalik membingungkan.
  function applyMirrorIfFrontCamera() {
    var videoEl = document.querySelector('#reader video');
    if (!videoEl || !videoEl.srcObject) return;
    var tracks = videoEl.srcObject.getVideoTracks ? videoEl.srcObject.getVideoTracks() : [];
    var settings = (tracks[0] && tracks[0].getSettings) ? tracks[0].getSettings() : {};
    videoEl.classList.toggle('is-mirrored', settings.facingMode !== 'environment');
  }

  async function onScanSuccess(decodedText) {
    if (busy) return;
    busy = true;
    hint.textContent = 'Memproses...';

    try {
      var res = await fetch(ENDPOINT, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ token: decodedText }),
      });
      var data = await res.json();
      if (res.ok && data.success) {
        // Foto ditampilkan supaya petugas bisa mencocokkan wajah pemegang HP dengan foto
        // karyawan -- backstop manusia untuk kasus akun dibagi ke orang lain.
        var photoHtml = data.employee_photo_url
          ? '<img src="' + escapeHtml(data.employee_photo_url) + '" style="width:64px;height:64px;object-fit:cover;border-radius:50%;float:left;margin-right:.75rem">'
          : '<div style="width:64px;height:64px;border-radius:50%;background:rgba(0,0,0,.08);float:left;margin-right:.75rem;display:flex;align-items:center;justify-content:center;font-size:1.5rem">🧑</div>';
        showResult(true, '<div style="overflow:hidden">' + photoHtml
          + '<strong>' + escapeHtml(data.employee_name) + '</strong>'
          + (data.employee_position ? '<br><span style="font-size:.82rem;opacity:.85">' + escapeHtml(data.employee_position) + '</span>' : '')
          + '<br>Absen ' + escapeHtml(data.clock_type_label) + ' tercatat jam <strong>' + escapeHtml(data.time) + '</strong>.'
          + '</div>');
      } else {
        showResult(false, data.message || 'QR tidak valid.');
      }
    } catch (e) {
      showResult(false, 'Gagal menghubungi server, coba lagi.');
    }

    hint.textContent = 'Siap memindai QR berikutnya dalam ' + AUTO_RESCAN_SECONDS + ' detik...';
    resumeTimer = setTimeout(function () {
      busy = false;
      hint.textContent = 'Arahkan kamera ke QR absensi karyawan/trainer.';
    }, AUTO_RESCAN_SECONDS * 1000);
  }

  function startScanner(facingMode) {
    return html5QrCode.start({ facingMode: facingMode }, scanConfigFor(facingMode), onScanSuccess).then(function () {
      // Ditunda sedikit -- elemen <video> & srcObject-nya baru terpasang tak lama setelah
      // start() resolve, bukan langsung di frame yang sama.
      setTimeout(applyMirrorIfFrontCamera, 300);
    });
  }

  switchBtn.addEventListener('click', function () {
    if (switching) return;
    switching = true;
    switchBtn.disabled = true;
    var nextFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';

    html5QrCode.stop().then(function () {
      return startScanner(nextFacingMode);
    }).then(function () {
      currentFacingMode = nextFacingMode;
      hint.textContent = 'Arahkan kamera ke QR absensi karyawan/trainer.';
    }).catch(function (err) {
      hint.textContent = 'Gagal ganti kamera: ' + err;
    }).finally(function () {
      switching = false;
      switchBtn.disabled = false;
    });
  });

  startScanner(currentFacingMode).catch(function (err) {
    hint.textContent = 'Tidak bisa mengakses kamera: ' + err;
  });
})();
</script>
