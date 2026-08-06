# Panduan Deploy Docker — mybas (Laravel 7)

Dokumentasi ini untuk programmer maupun AI agent (Claude, Cursor, dll.) yang mengerjakan/deploy aplikasi ini dengan Docker.

---

## 1. Arsitektur

```
┌──────────────────────────────────────────────────────────┐
│ Host (Linux)                                             │
│  - MySQL eksternal di 127.0.0.1:3306 (DB: mybas, pme)   │
│  - Port 8080 (nginx)                                     │
│                                                          │
│  Docker Compose (network: mybas_mybas)                   │
│  ┌──────────────┐   ┌──────────────┐   ┌──────────────┐  │
│  │ app          │   │ nginx        │   │ queue        │  │
│  │ php:7.4-fpm  │◄──│ 1.25-alpine  │   │ 2x queue:work│  │
│  │ ext: pdo_mysql│   │ gzip + cache│   │ database     │  │
│  │ mbstring, gd,│   │ port 8080    │   │              │  │
│  │ zip, dll.    │   └──────────────┘   └──────────────┘  │
│  └──────────────┘                                        │
└──────────────────────────────────────────────────────────┘
```

- **TIDAK ada service MySQL di Docker** — aplikasi memakai DB eksternal di host.
- Container mengakses DB host via `host.docker.internal` (`extra_hosts: host-gateway`).
- `app` dan `queue` memakai image yang sama (`mybas-app:latest`), dibangun dari `Dockerfile`.

## 2. Struktur File Terkait Docker

| File | Fungsi |
|---|---|
| `Dockerfile` | Image PHP 7.4-FPM + composer install (`--no-dev`) |
| `docker-compose.yml` | Service `app`, `nginx`, `queue` |
| `docker/nginx/default.conf` | Nginx: Laravel + gzip + cache aset statis |
| `docker/php-fpm/www.conf` | Pool FPM: `pm=dynamic`, min 10 / max 50 worker |
| `docker/php/opcache.ini` | OPcache production: 512MB, 20000 file, `validate_timestamps=0` |
| `.dockerignore` | Mengecualikan vendor/node_modules/.git/storage dari build |

## 3. Prasyarat

1. Docker + Docker Compose v2 terinstall (`docker compose version`).
2. MySQL di host berjalan dan **listen di `0.0.0.0:3306`** (bukan hanya `127.0.0.1`), DB `mybas` ada.
   - Cek: `ss -tlnp | grep 3306` → harus muncul `*:3306`.
3. Port **8080** kosong. (Port 80 dipakai container lain `cbt-nginx`/`nginx_proxy` — jangan dipakai.)
4. `.env` lengkap (APP_KEY terisi, DB_HOST bisa tetap `127.0.0.1` — di-override compose).

## 4. Deploy Pertama Kali

> **PENTING (masalah DNS):** Host memakai systemd-resolved (stub `127.0.0.53`) sehingga `docker compose up --build` gagal dengan *"Temporary failure resolving 'deb.debian.org'"*. Solusinya: build manual dengan `--network=host`.

```bash
# 1. Build image (WAJIB pakai --network=host, ~2-5 menit)
docker build --network=host -t mybas-app:latest .

# 2. Jalankan stack (memakai image yang sudah dibangun)
docker compose up -d

# 3. Verifikasi
docker compose ps            # 3 container: app, nginx, queue — semua "Up"
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/login   # → 200
```

Setelah ini, aplikasi bisa diakses di **http://localhost:8080**.

## 5. Konfigurasi Penting

### 5.1 Koneksi DB eksternal
- `.env` di host TIDAK diubah (DB_HOST tetap `127.0.0.1` untuk penggunaan non-Docker).
- `docker-compose.yml` meng-override via `environment`:
  ```yaml
  environment:
    - DB_HOST=host.docker.internal
    - PME_HOST=host.docker.internal
  extra_hosts:
    - "host.docker.internal:host-gateway"
  ```
- `PME2_HOST=192.168.154.81` tidak perlu diubah (IP LAN langsung terjangkau dari container).

### 5.2 User / Permission storage
- Service `app` & `queue` berjalan sebagai `user: "1000:1000"` (uid user host) agar bisa menulis ke bind mount `./storage` dan `./public` yang dimiliki uid 1000 di host.
- Jangan hapus baris `user:` ini — tanpa itu muncul *"storage/logs/laravel.log could not be opened: Permission denied"* (HTTP 500).

### 5.3 Nginx (`docker/nginx/default.conf`)
- gzip level 5 untuk css/js/json/xml/svg/font.
- Aset statis (`css|js|png|jpg|svg|woff2|...`) di-cache 30 hari: `Cache-Control: public, immutable`.
- `client_max_body_size 100M` untuk upload.

### 5.4 PHP-FPM (`docker/php-fpm/www.conf`, mount ke `zz-mybas.conf`)
- `pm = dynamic`, `pm.start_servers = 10`, `pm.min_spare_servers = 10`, `pm.max_children = 50`, `pm.max_spare_servers = 30`, `pm.max_requests = 500`.

### 5.5 Queue worker (service `queue`)
- Menjalankan **2 worker** `php artisan queue:work database --tries=3 --timeout=90 --sleep=1` dalam satu container via `sh -c ... & wait`.
- Perlu tabel `jobs` di DB (migration `2023_11_22_083702_create_jobs_table.php`, sudah ada).
- Berguna karena aplikasi banyak memakai `ShouldQueue` (Mail, Telegram, HR jobs).

### 5.6 OPcache (`docker/php/opcache.ini`, baked ke image)
- `opcache.enable=1`, `memory_consumption=512`, `max_accelerated_files=20000`, `fast_shutdown=1`.
- **`validate_timestamps=0`** — perubahan kode TIDAK otomatis terlihat (harus rebuild image, sama seperti aturan bake di §7). Jangan set ini ke 1 di production.
- Benchmark nyata di lingkungan ini: `/login` dari **23.9 → 224.9 req/s** (~9.4x) dengan `ab -n 500 -c 20`.
- Karena code baked ke image, cache tidak perlu di-flush saat deploy — konten cache terhapus otomatis saat container baru di-recreate.

## 6. Operasional Sehari-hari

```bash
# Mulai / hentikan / restart
docker compose up -d
docker compose down            # stop + hapus container (data storage tetap aman di host)
docker compose restart         # restart semua

# Lihat log
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f queue

# Jalankan artisan (di dalam container app)
docker compose exec app php artisan list
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:list

# Hapus failed jobs lama (setelah debug)
docker compose exec app php artisan queue:flush

# Cek jumlah worker queue yang hidup
docker compose exec queue sh -c 'for p in $(ls /proc | grep -E "^[0-9]+$"); do cat /proc/$p/cmdline 2>/dev/null | tr "\0" " " | grep -q "queue:work" && echo "PID $p: live"; done'
```

## 7. Deploy Perubahan Kode

> Kode sumber (kecuali `public/` dan `storage/`) di-*bake* ke image — perubahan di `app/`, `routes/`, `resources/`, `composer.json` **WAJIB rebuild image**.

```bash
# 1. Rebuild image (WAJIB --network=host)
docker build --network=host -t mybas-app:latest .

# 2. Recreate hanya app + queue (nginx tidak berubah)
docker compose up -d --force-recreate app queue
# atau recreate semua
docker compose up -d --force-recreate

# 3. Verifikasi
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/login
```

### Perubahan yang TIDAK butuh rebuild (bind mount)
- `public/` (aset statis, upload) — langsung terlihat.
- `storage/` — langsung terlihat.
- `docker/nginx/default.conf` — butuh recreate nginx:
  ```bash
  docker compose up -d --force-recreate nginx
  ```
- `docker/php-fpm/www.conf` — butuh recreate app:
  ```bash
  docker compose up -d --force-recreate app
  ```

## 8. Troubleshooting (berdasarkan insiden nyata)

### 8.1 Build gagal: "Temporary failure resolving 'deb.debian.org'"
- **Penyebab:** systemd-resolved host (nameserver `127.0.0.53`) tidak bisa dipakai resolver Docker.
- **Solusi:** selalu build dengan `docker build --network=host -t mybas-app:latest .`, jangan `docker compose up --build`.

### 8.2 HTTP 500: "storage/logs/laravel.log could not be opened: Permission denied"
- **Penyebab:** php-fpm jalan sebagai `www-data` (uid 33) tapi bind mount `storage/` milik uid 1000.
- **Solusi:** pastikan `user: "1000:1000"` ada di service `app` & `queue`; cek `id -u` host bila bukan 1000.

### 8.3 Login gagal "CSRF token mismatch" (419)
- **Penyebab klasik:** output/karakter tak terlihat sebelum `<?php` di file PHP yang di-load saat request → header (termasuk `Set-Cookie`) tidak terkirim → session tidak terbentuk.
- **Insiden nyata:** `routes/kedatangan-lauk.php` punya newline di baris 1 (file dimulai `\n<?php`). Di PHP-FPM (output_buffering=Off) newline itu jadi output; di XAMPP (buffering 4096) bug tersembunyi.
- **Pengecekan:**
  ```bash
  for f in routes/*.php app/**/*.php; do head -c 1 "$f" | od -An -c | tr -d ' \n'; echo " ← $f"; done
  ```
  Semua harus dimulai `<`. Perbaiki file bermasalah lalu **rebuild image** (routes tidak di-bind-mount).
- **Verifikasi cookie:** `curl -s -D - -o /dev/null http://localhost:8080/login | grep -i set-cookie` → harus ada `laravel_session`.

### 8.4 Header respons aplikasi hilang (Set-Cookie dll. tidak keluar)
- **Cek cepat:** buat file `public/dbg.php` berisi `var_dump(headers_sent($f,$l));` — jika `true` di path tertentu, ada output liar sebelum `<?php` di file itu. Hapus dbg.php setelah selesai.

### 8.5 Port 80 bentrok
- `cbt-nginx` dan `nginx_proxy` (container lain) sama-sama restarting karena rebutan port 80. Setup ini memakai port 8080 jadi tidak terpengaruh. Jangan map port 80 tanpa menghentikan keduanya.

### 8.6 Tinker/psysh error: "Writing to directory /.config/psysh is not allowed"
- **Solusi:** jalankan dengan `docker exec -e HOME=/tmp mybas-app php artisan tinker ...`.

### 8.7 Job queue gagal "Call to a member function bindTo() on null (SerializableClosure)"
- Hanya terjadi pada closure job yang di-dispatch dari tinker (`eval` code) — bukan bug aplikasi. Job class asli tidak terpengaruh. Bersihkan dengan `queue:flush`.

## 9. Catatan Khusus untuk AI Agent

1. **Jangan rebuild dengan `docker compose up --build`** — selalu `docker build --network=host -t mybas-app:latest .` dulu, baru `docker compose up -d`.
2. **Jangan ubah `.env` host untuk keperluan Docker** — override ada di `docker-compose.yml` (`DB_HOST`, `PME_HOST`).
3. **Perubahan kode di `app/`, `routes/`, `resources/`, `composer.*` harus rebuild image.** Bind mount hanya `storage/` dan `public/`.
4. **Hindari menulis file PHP debug di `public/`** — kalau terpaksa, HAPUS setelah selesai.
5. **Gunakan image yang sama untuk debug worker:** `docker exec mybas-queue php artisan queue:monitor` (perlu tabel `jobs`).
6. **Verifikasi sebelum selesai:** (a) `curl` halaman login 200, (b) `set-cookie` ada di header, (c) `docker compose ps` semua Up, (d) `jobs pending = 0` setelah test dispatch.
