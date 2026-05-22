# Template Website Madrasah Aliyah

Starter kit website madrasah berbasis **Laravel 11 + Livewire 3**, siap pakai dan mudah dikustomisasi untuk Madrasah Aliyah Negeri (MAN) maupun swasta.

> Data MAN 2 Kolaka yang ada di project ini hanya digunakan sebagai **contoh/demo**. Ganti dengan data madrasah Anda melalui panel admin setelah instalasi.

---

## Fitur

### Frontend Publik
- Beranda dinamis (hero, statistik, sambutan kepsek, peminatan, galeri, agenda, berita)
- Halaman Profil Madrasah (visi misi, data madrasah, tenaga pendidik, peta lokasi)
- Halaman Peminatan dengan detail fasilitas dan prospek
- Berita & Artikel dengan kategori dan pencarian
- Galeri Foto per album
- Agenda Kegiatan

### PPDB / PMBM Online
- Formulir pendaftaran multi-step (7 langkah) dengan upload berkas
- Cek status pendaftaran & verifikasi
- Halaman daftar ulang dengan akses grup WhatsApp siswa baru
- Export formulir PDF

### Admin Panel
- Manajemen konten (berita, pengumuman, agenda, galeri)
- Profil madrasah & pengaturan
- Manajemen pegawai
- Manajemen peminatan
- Manajemen ekstrakurikuler

### PPDB Admin (V2)
- Dashboard ringkasan pendaftar
- Data pendaftar + verifikasi berkas + tambah offline
- Penentuan peminatan (bulk & individual)
- Verifikasi daftar ulang
- Broadcast pesan (WhatsApp/Email)
- Laporan & export (Excel, PDF, Dapodik)
- Pengaturan periode, jalur, kuota, persyaratan berkas

### Integrasi
- **Telegram Bot + NVIDIA AI** — kirim foto ke bot → AI buat berita otomatis
- **Queue Worker** — proses AI berjalan asynchronous

---

## Persyaratan

- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- Composer 2+

---

## Instalasi

```bash
# 1. Install dependencies
composer install
npm install

# 2. Konfigurasi environment
cp .env.example .env
php artisan key:generate

# 3. Buat database, jalankan migrasi + seeder demo
php artisan migrate --seed

# 4. Buat symlink storage
php artisan storage:link

# 5. Build aset frontend
npm run build
```

---

## Konfigurasi `.env`

```env
APP_NAME="Nama Madrasah Anda"
APP_URL=https://domain-anda.com

DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=

# Telegram Bot (opsional)
TELEGRAM_BOT_TOKEN=
TELEGRAM_WEBHOOK_SECRET=
TELEGRAM_ALLOWED_CHAT_IDS=
TELEGRAM_NEWS_AUTHOR_EMAIL=admin@madrasah.sch.id
TELEGRAM_NEWS_AUTO_PUBLISH=true

# NVIDIA AI — untuk fitur auto-berita via Telegram
NVIDIA_AI_API_KEY=
NVIDIA_AI_URL=https://integrate.api.nvidia.com/v1
NVIDIA_AI_MODEL=qwen/qwen3.5-397b-a17b

# Gunakan database untuk production
QUEUE_CONNECTION=database
```

---

## Akun Default (setelah seeder demo)

| Role | Email | Password |
|---|---|---|
| Admin | admin@man2kolaka.sch.id | password |
| Editor | editor@man2kolaka.sch.id | password |
| Admin PPDB | ppdb@man2kolaka.sch.id | password |

> Ganti email dan password segera setelah login pertama, lalu update data madrasah di menu **Profil Madrasah** pada admin panel.

---

## Kustomisasi untuk Madrasah Anda

Setelah instalasi, login ke admin panel dan update:

1. **Profil Madrasah** — nama, alamat, logo, foto kepsek, visi misi, koordinat peta
2. **Peminatan** — hapus data demo, tambah peminatan madrasah Anda (IPA/IPS/Bahasa/Keagamaan)
3. **Pegawai** — tambah data guru dan tenaga kependidikan
4. **Pengaturan PPDB** — buka periode, atur jalur, kuota, dan persyaratan berkas
5. **Berita & Konten** — hapus konten demo, mulai buat konten madrasah

---

## Integrasi Telegram + AI Berita

Bot Telegram menerima foto + judul, lalu NVIDIA AI membuat artikel berita secara otomatis.

### Daftarkan webhook

```bash
curl -X POST "https://api.telegram.org/bot<TOKEN>/setWebhook" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://domain-anda.com/telegram/webhook",
    "secret_token": "<WEBHOOK_SECRET>",
    "drop_pending_updates": true
  }'
```

### Cara pakai

- **Opsi 1** — Kirim foto dengan caption berisi judul berita.
- **Opsi 2** — Kirim foto dulu, lalu kirim judul teks berikutnya (maks. 15 menit).

Bot membalas judul hasil AI dan link berita setelah proses selesai.

---

## Queue Worker di Production

### Supervisor

```bash
sudo cp deploy/supervisor/web-man2kolaka-queue.conf /etc/supervisor/conf.d/
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl start web-man2kolaka-queue:*
```

### systemd

```bash
sudo cp deploy/systemd/web-man2kolaka-queue.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now web-man2kolaka-queue
```

### Shared Hosting (Cron Fallback)

Lihat [`deploy/hosting/queue-cron-fallback.txt`](deploy/hosting/queue-cron-fallback.txt).

---

## Stack Teknologi

- [Laravel 11](https://laravel.com)
- [Livewire 3](https://livewire.laravel.com)
- [Alpine.js](https://alpinejs.dev)
- [Tailwind CSS v4](https://tailwindcss.com)
- [NVIDIA AI API](https://build.nvidia.com)
