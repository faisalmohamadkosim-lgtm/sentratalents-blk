# Deploy produksi — blk.sentratalents.com (server 62.171.137.238)

Server ini sudah menjalankan Caddy dalam Docker (`vixiocall_caddy`, Caddyfile `/opt/vixiocall-single/Caddyfile`) yang memegang port 80/443 dan sertifikat HTTPS.
Aplikasi ini TIDAK membuka port host dan TIDAK memakai sertifikat self-signed di `docker/nginx/ssl/` (itu hanya untuk dev/kios LAN);
nginx-nya bergabung ke jaringan Docker `vixiocall_default` dengan alias `blk-web`, lalu Caddy meneruskan ke sana.
Rahasia (password DB, APP_KEY, token git) hanya diisi di server — jangan commit atau kirim lewat chat.
Server hanya menerima kode yang sudah di-commit dan di-push ke `main`.

**Akun default tidak ada di produksi.** Migrasi `users` membuat `ultraadmin@blk.test` dan `superadmin@blk.test` (password publik `Test@123`) dan seeder
(`DatabaseSeeder`, `LegacyDataSeeder`) membuat data/akun demo; semuanya sekarang dilewati bila `APP_ENV=production`.
Buat akun pertama dengan `php artisan app:bootstrap-ultraadmin` / `app:bootstrap-superadmin` (langkah 4). **Jangan jalankan `db:seed`.**

## 1. Ambil kode (deploy key read-only)
```bash
ssh-keygen -t ed25519 -C "deploy-blk" -f ~/.ssh/blk_deploy -N ""
cat ~/.ssh/blk_deploy.pub           # tempel di GitHub: repo sentratalents-blk → Settings → Deploy keys (tanpa write access)
printf 'Host github-blk\n  HostName github.com\n  User git\n  IdentityFile ~/.ssh/blk_deploy\n  IdentitiesOnly yes\n' >> ~/.ssh/config
ssh -T git@github-blk
git clone git@github-blk:faisalmohamadkosim-lgtm/sentratalents-blk.git /opt/blk
cd /opt/blk && cp .env.production.example .env
```

## 2. Isi .env
```bash
sed -i "s|^APP_KEY=.*|APP_KEY=base64:$(openssl rand -base64 32)|; s|^DB_PASSWORD=.*|DB_PASSWORD=$(openssl rand -hex 16)|; s|^DB_ROOT_PASSWORD=.*|DB_ROOT_PASSWORD=$(openssl rand -hex 16)|" .env
grep -c GANTI .env; grep -c '^APP_KEY=base64:' .env      # harus 0 dan 1
```
Password DB tersimpan di volume MySQL saat pertama kali jalan; jangan diubah di `.env` sesudahnya tanpa mengatur ulang volume.

## 3. Bangun, pasang dependensi, jalankan
Image tidak menjalankan `composer install` (vendor/ datang dari bind mount), jadi pasang sekali sebagai root lalu beri hak tulis ke user `www` (uid 1000) untuk storage:
```bash
docker compose -f docker-compose.prod.yml build
docker compose -f docker-compose.prod.yml run --rm --user root app composer install --no-dev --optimize-autoloader --no-interaction
chown -R 1000:1000 storage bootstrap/cache
docker compose -f docker-compose.prod.yml up -d
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec --user root app php artisan storage:link
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml ps
```
Tampilan tidak memakai Vite, jadi tidak perlu build aset Node. `FILESYSTEM_DISK=public` (foto/berkas) butuh `storage:link` di atas.

## 4. Akun pertama
Perintah bootstrap menerima password sebagai argumen; baca tanpa tampil di layar dan tanpa tersimpan di history (pilih password kuat, simpan di pengelola password, jangan kirim ke chat):
```bash
read -s -p "Password: " PW; echo
docker compose -f docker-compose.prod.yml exec app php artisan app:bootstrap-ultraadmin email@anda.com "Nama Anda" "$PW"
docker compose -f docker-compose.prod.yml exec app php artisan app:bootstrap-superadmin email@anda.com "Nama Anda" "$PW"   # bila perlu (email berbeda)
unset PW
```
Cek: `docker compose -f docker-compose.prod.yml exec app php artisan tinker --execute='echo \App\Models\User::count();'` → `0` sebelum membuat akun (tidak ada `*@blk.test`).

## 5. HTTPS (tambahkan situs ke Caddy yang sudah ada)
```bash
cp /opt/vixiocall-single/Caddyfile /opt/vixiocall-single/Caddyfile.bak-blk-$(date +%F)
printf '\nblk.sentratalents.com {\n    encode gzip\n    reverse_proxy blk-web:80\n}\n' >> /opt/vixiocall-single/Caddyfile
docker exec vixiocall_caddy caddy validate --config /etc/caddy/Caddyfile
docker exec vixiocall_caddy caddy reload --config /etc/caddy/Caddyfile
grep -n -A3 blk /opt/vixiocall-single/Caddyfile       # blok baru harus tampil
sleep 15; curl -4 -I https://blk.sentratalents.com/up      # HTTP/2 200
```
Gunakan `>>` (bukan editor yang mengganti file) agar bind-mount Caddyfile tetap valid. Tanpa blok ini Chrome menampilkan "site doesn't support a secure connection".

## 6. Setelah deploy
- Absensi QR/kios memerlukan HTTPS (kamera); sudah dipenuhi oleh Caddy. `PATIENT_QR_LAN_URL` dikosongkan di produksi.
- Update kode: `cd /opt/blk && git pull`, `run --rm --user root app composer install --no-dev --optimize-autoloader --no-interaction` bila composer berubah, `migrate --force`, `config:cache`, lalu `docker compose -f docker-compose.prod.yml restart app` (opcache tidak memeriksa timestamp → wajib restart).
- Backup DB: `docker compose -f docker-compose.prod.yml exec db sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" blk' > backup.sql`; berkas unggahan ada di volume `storage/app/public` (cadangkan juga).
- Cache/session memakai Redis (internal, tidak dibuka ke luar); `QUEUE_CONNECTION=sync` (tidak ada job antrean), `MAIL_MAILER=log`.
