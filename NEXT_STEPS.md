# Checklist Pengembangan Lanjutan

## ✅ Sudah Selesai
- Logo & favicon dari database (ProfilSekolah)
- Semua warna blue/indigo/purple → emerald/teal (tema MAN)
- Semua teks "Jurusan" → "Peminatan", "Sekolah" → "Madrasah"
- TEFA → Ekstrakurikuler (model, route, view, seeder)
- Seeder peminatan MAN (IPA/IPS/Bahasa/Keagamaan)
- Kop surat PDF diambil dari database ProfilSekolah
- Fix semua error wire:key & @foreach(range) di skeleton
- Header navbar breakpoint tablet (md → xl)
- Navbar: hapus link # (Zona Integritas, Layanan), Guru & Tendik → route profil
- Email template sudah bersih (MAN 2 Kolaka)
- README diperbarui sebagai template madrasah
- .gitignore siap GitHub

## 📋 Perlu Diisi via Admin Panel
- [ ] Profil Madrasah — nama kepsek, foto kepsek, koordinat peta, NPSN
- [ ] Peminatan — hapus data demo, tambah IPA/IPS/Bahasa/Keagamaan dengan deskripsi & foto
- [ ] Pegawai — tambah data guru dan tendik asli
- [ ] Berita/Agenda/Galeri — hapus konten demo, isi konten nyata
- [ ] Pengaturan PPDB — buka periode, atur jalur & kuota sesuai madrasah

## 🔵 Opsional / Pengembangan Lanjutan
- Halaman Zona Integritas (buat halaman baru jika diperlukan)
- Menu Layanan (Portal e-RDM, e-Learning, Simpatika) — tambahkan URL eksternal jika sudah ada
- Integrasi WhatsApp Gateway untuk broadcast PPDB
- Konfigurasi Telegram Bot untuk auto-berita AI
- Setup queue worker di production (Supervisor/systemd)
