# Panduan Deploy Laravel Stockz ke Render dengan Docker

Dokumen ini berisi panduan lengkap untuk men-deploy aplikasi **Stockz** (Laravel 12 + Vite) ke platform cloud **Render** menggunakan Docker.

---

## 📁 Struktur File Docker yang Telah Dibuat

- **[`Dockerfile`](file:///c:/rustaman/Stockz/Dockerfile)**: Multi-stage build (Node.js 22 untuk build asset Vite + PHP 8.2-FPM Alpine & Nginx).
- **[`docker/nginx.conf.template`](file:///c:/rustaman/Stockz/docker/nginx.conf.template)**: Konfigurasi Nginx produksi untuk Laravel yang otomatis mendukung port dinamis `$PORT` dari Render.
- **[`docker/supervisord.conf`](file:///c:/rustaman/Stockz/docker/supervisord.conf)**: Menjalankan Nginx dan PHP-FPM bersamaan dalam 1 container.
- **[`docker/php.ini`](file:///c:/rustaman/Stockz/docker/php.ini)**: Optimasi OPcache, batas upload file 64MB, dan memory limit 256MB.
- **[`docker/entrypoint.sh`](file:///c:/rustaman/Stockz/docker/entrypoint.sh)**: Startup script otomatis (substitusi PORT Nginx, storage symlink, migrasi otomatis, optimasi cache Laravel).
- **[`render.yaml`](file:///c:/rustaman/Stockz/render.yaml)**: Blueprint Render untuk deploy 1-klik.
- **[`docker-compose.yml`](file:///c:/rustaman/Stockz/docker-compose.yml)**: Untuk pengujian di lokal sebelum deploy.

---

## 🚀 Cara 1: Deploy Menggunakan Render Blueprint (`render.yaml`) (Paling Mudah)

1. **Push kode Anda ke GitHub / GitLab**.
2. Buka dashboard [Render](https://dashboard.render.com/).
3. Klik **New +** > pilih **Blueprint**.
4. Hubungkan repository GitHub Anda.
5. Render akan otomatis mendeteksi file [`render.yaml`](file:///c:/rustaman/Stockz/render.yaml) dan membuat Web Service.
6. Pada bagian Environment Variables, isi nilai `APP_KEY` dan `APP_URL`.
7. Klik **Apply**.

---

## 🛠️ Cara 2: Deploy Manual Sebagai Web Service Docker di Render

1. **Push kode Anda ke GitHub / GitLab**.
2. Di dashboard Render, klik tombol **New +** > pilih **Web Service**.
3. Pilih repository project Anda.
4. Isi konfigurasi berikut:
   - **Name**: `stockz-app` (atau nama pilihan Anda)
   - **Region**: Singapore (atau terdekat)
   - **Branch**: `main` / `master`
   - **Runtime**: **Docker**
   - **Dockerfile Path**: `./Dockerfile`
   - **Instance Type**: Free (atau Starter)
   - **Health Check Path**: `/up`
5. Buka tab **Environment Variables** dan tambahkan variabel berikut:

| Key | Value | Keterangan |
|---|---|---|
| `APP_NAME` | `Stockz` | Nama aplikasi |
| `APP_ENV` | `production` | Environment Laravel |
| `APP_DEBUG` | `false` | Matikan debug mode di produksi |
| `APP_KEY` | `base64:...` | Ambil dari file `.env` lokal atau jalankan `php artisan key:generate --show` |
| `APP_URL` | `https://nama-app-anda.onrender.com` | URL yang diberikan Render |
| `AUTO_MIGRATE` | `true` | Otomatis jalankan migrasi tabel saat startup |
| `AUTO_SEED` | `true` (opsional) | Set `true` pada deploy pertama jika ingin generate data awal seeder |
| `DB_CONNECTION` | `sqlite` atau `pgsql` | Jenis database yang digunakan |
| `SESSION_DRIVER` | `cookie` | Aman untuk multi-instance / free tier |
| `CACHE_STORE` | `file` | Cache driver |
| `QUEUE_CONNECTION` | `sync` | Queue driver |

6. Klik **Create Web Service**.

---

## 🗄️ Opsi Database di Render

### Opsi A: SQLite (Default & Paling Sederhana)
Container sudah dikonfigurasi untuk otomatis membuat file `database.sqlite` jika belum ada.
> ⚠️ **Catatan SQLite di Free Tier**: Pada Render Free Tier (tanpa persistent disk), file SQLite akan reset jika container di-restart. Untuk penyimpanan permanen, gunakan PostgreSQL.

### Opsi B: PostgreSQL di Render (Direkomendasikan untuk Produksi)
1. Di dashboard Render, klik **New +** > pilih **PostgreSQL**.
2. Setelah database aktif, salin koneksinya dan tambahkan variabel ini ke Web Service Anda:
   - `DB_CONNECTION` = `pgsql`
   - `DB_HOST` = *(Host dari database Render)*
   - `DB_PORT` = `5432`
   - `DB_DATABASE` = *(Nama database)*
   - `DB_USERNAME` = *(User database)*
   - `DB_PASSWORD` = *(Password database)*

---

## 🧪 Menguji Docker di Komputer Lokal

Jika Anda memiliki Docker terpasang di komputer dan ingin mencoba container sebelum deploy:

```bash
docker compose up --build
```

Lalu buka browser di `http://localhost:8000`.
Untuk menghentikan:
```bash
docker compose down
```

---

## ❓ FAQ & Troubleshooting

- **Q: Mengapa Nginx gagal binding port di Render?**
  **A**: Render mengalokasikan port dinamis via environment variable `$PORT`. Script [`docker/entrypoint.sh`](file:///c:/rustaman/Stockz/docker/entrypoint.sh) sudah otomatis mengonfigurasi Nginx untuk mendengarkan port `${PORT}` tersebut.
- **Q: Bagaimana jika CSS / Javascript tidak muncul?**
  **A**: Dockerfile sudah melakukan multi-stage build dengan Node.js untuk mengeksekusi `npm run build` sehingga semua aset Vite di `public/build` sudah ter-bundle di dalam image.
- **Q: Apakah endpoint `/up` aman untuk health check?**
  **A**: Ya, Laravel 12 secara bawaan memiliki rute `/up` yang mengembalikan HTTP 200 jika aplikasi siap menerima request.
