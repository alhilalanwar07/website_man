# Website Resmi MAN 2 Kolaka

Website madrasah berbasis Laravel + Livewire untuk **Madrasah Aliyah Negeri 2 Kolaka**, Sulawesi Tenggara.

---

## Fitur Utama

- **Frontend publik** — Beranda, Profil Madrasah, Peminatan, Berita, Galeri, Agenda
- **PPDB Online (PMBM)** — Formulir pendaftaran multi-step, cek status, daftar ulang
- **Admin Panel** — Kelola konten, pegawai, ekstrakurikuler, pengaturan PPDB
- **PPDB V2** — Dashboard, data pendaftar, penentuan peminatan, daftar ulang, broadcast, laporan
- **Integrasi Telegram + AI** — Bot Telegram kirim foto → AI buat berita otomatis via NVIDIA API
- **Export PDF & Excel** — Formulir pendaftaran, assessment, dapodik, re-registrasi

---

## Persyaratan

- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- Composer 2+

---

## Instalasi

```bash
# 1. Clone & install dependencies
composer install
npm install

# 2. Salin dan isi konfigurasi
cp .env.example .env
php artisan key:generate

# 3. Buat database, lalu jalankan migrasi + seeder
php artisan migrate --seed

# 4. Buat symlink storage
php artisan storage:link

# 5. Build aset frontend
npm run build
```

---

## Konfigurasi `.env` Penting

```env
APP_NAME="MAN 2 Kolaka"
APP_URL=https://domain-anda.com

DB_DATABASE=man2klk
DB_USERNAME=root
DB_PASSWORD=

# Telegram Bot (opsional)
TELEGRAM_BOT_TOKEN=
TELEGRAM_WEBHOOK_SECRET=
TELEGRAM_ALLOWED_CHAT_IDS=
TELEGRAM_NEWS_AUTHOR_EMAIL=admin@man2kolaka.sch.id
TELEGRAM_NEWS_AUTO_PUBLISH=true

# NVIDIA AI (untuk auto-berita Telegram)
NVIDIA_AI_API_KEY=
NVIDIA_AI_URL=https://integrate.api.nvidia.com/v1
NVIDIA_AI_MODEL=qwen/qwen3.5-397b-a17b

# Queue (gunakan database untuk production)
QUEUE_CONNECTION=database
```

---

## Integrasi Telegram + AI Berita

Bot Telegram menerima foto + judul, lalu server membuat berita dengan NVIDIA AI dan mengirim link berita kembali ke chat.

### Daftarkan webhook

```bash
curl -X POST "https://api.telegram.org/bot<TELEGRAM_BOT_TOKEN>/setWebhook" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://domain-anda.com/telegram/webhook",
    "secret_token": "<TELEGRAM_WEBHOOK_SECRET>",
    "drop_pending_updates": true
  }'
```

### Format kirim dari Telegram

- **Opsi 1** — Kirim foto dengan caption berisi judul berita.
- **Opsi 2** — Kirim foto dulu, lalu kirim judul teks berikutnya (maks. 15 menit).

Bot akan membalas judul hasil AI dan link berita setelah proses selesai.

### Jalankan Queue Worker

```bash
php artisan queue:work --queue=default --tries=3
```

---

## Queue Worker di Production

### Opsi A: Supervisor

```bash
# Salin config
sudo cp deploy/supervisor/web-man2kolaka-queue.conf /etc/supervisor/conf.d/

# Aktifkan
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start web-man2kolaka-queue:*
```

### Opsi B: systemd

```bash
sudo cp deploy/systemd/web-man2kolaka-queue.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable web-man2kolaka-queue
sudo systemctl start web-man2kolaka-queue
```

### Opsi C: Shared Hosting (Cron Fallback)

Lihat contoh di [`deploy/hosting/queue-cron-fallback.txt`](deploy/hosting/queue-cron-fallback.txt).

1. Set `QUEUE_CONNECTION=database` di `.env`.
2. Buat Cron Job per menit untuk `queue:work --stop-when-empty`.
3. Pantau log di `storage/logs/queue-worker.log`.

---

## Akun Default (setelah seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@man2kolaka.sch.id | password |
| Editor | editor@man2kolaka.sch.id | password |
| Admin PPDB | ppdb@man2kolaka.sch.id | password |

> **Ganti password segera setelah login pertama kali di production.**

---

## Struktur Direktori Penting

```
app/
  Livewire/
    Admin/          # Komponen admin panel
    Admin/PpdbV2/   # Modul PPDB V2
    Frontend/       # Halaman publik
resources/views/
  livewire/
    admin/          # Blade admin
    frontend/       # Blade frontend
  pdf/              # Template PDF export
  emails/           # Template email
database/
  seeders/          # DatabaseSeeder.php
  migrations/
storage/app/public/ # Upload file (logo, foto, berkas PPDB)
```

---

## Lisensi

Proyek ini dikembangkan khusus untuk **MAN 2 Kolaka**. Tidak untuk didistribusikan ulang tanpa izin.
