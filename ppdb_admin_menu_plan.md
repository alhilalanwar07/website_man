# Rencana Alur & Menu Admin PPDB (Versi Simple & Realistis)

Rancangan ini disesuaikan dengan kendala di lapangan: **guru/staf kesulitan dengan sistem yang terlalu rumit**. Sistem ini dirancang sebagai "Asisten Pendata dan Pengirim Pesan" dengan fitur manipulasi **banyak data sekaligus (bulk actions)** seperti menggunakan Excel, agar staf tidak lelah bekerja. Penentuan kelulusan bersumber dari tes manual/wawancara di lapangan.

## 1. 📊 Dashboard Utama
Halaman super bersih untuk melihat ringkasan data.
* **Widget Metrik:** Total pendaftar, Sudah Wawancara, Belum Penentuan Jurusan, Selesai Daftar Ulang.
* **Aksi Cepat:** Tombol besar "Tulis Pengumuman / Kirim Broadcast Pesan".

## 2. 👥 Pendaftaran & Verifikasi (Menu: Data Pendaftar)
Tabel pendaftar utama yang cara kerjanya mirip Excel (sangat mudah digunakan staf).
* **Tabel Real-time:** Memuat pendaftar baru dari website secara otomatis.
* **Input Manual Pendaftar:** Tombol **"Tambah Siswa (Offline/Susulan)"** jika ada siswa mendaftar lewat admin tanpa mengisi form website.
* **Kirim Jadwal Tes:** Fitur memilih banyak siswa (centang) > klik "Kirim Jadwal Wawancara/Tes" (via WA/Email).
* **Verifikasi Cepat:** Cek list kelengkapan berkas langsung dari tabel (tidak perlu banyak berpindah halaman).

## 3. 🎯 Penentuan Jurusan (Bulk & Fleksibel)
Karena hasil wawancara dilakukan manual, halaman ini khusus bagi admin untuk menginput *"Siswa A masuk jurusan B"*.
* **Tabel Bebas Pilih Jurusan:** Menampilkan siswa, 3 jurusan pilihannya (hanya sebagai referensi), lalu sebuah **Dropdown Penentuan Akhir** yang memuat *seluruh jurusan* (admin bebas memasukkan siswa ke jurusan apa saja walau di luar 3 pilihannya).
* **Input Massal:** Admin bisa men-set jurusan dari tabel ini dengan sangat cepat (seperti edit Excel) lalu menyimpan semuanya sekaligus.
* **Otomatisasi Notifikasi Kelulusan:** Setelah jurusan disimpan, sistem akan otomatis mengirim Broadcast WhatsApp/Email kepada siswa bertuliskan: *"Selamat Anda diterima di Jurusan [X]. Berikut jadwal dan cara Daftar Ulang Anda..."*

## 4. 📝 Daftar Ulang
Menu khusus untuk menangani siswa yang sudah mendapat jurusan dan datang/konfirmasi mendaftar ulang.
* **Checklist Mudah:** Centang bagi siswa yang sudah lunas bayar/kumpul berkas fisik. 
* **Cetak Bukti:** tombol print PDF bukti selesai daftar ulang secara cepat.

## 5. 📢 Broadcast & Pengumuman (Fitur Baru)
Pusat komunikasi antara sekolah dan siswa.
* **Kirim Pesan Massal (WA / Email):** Mengirim pengingat jadwal, informasi berkas kurang, atau informasi seragam langsung ke nomor/email siswa dalam satu klik (bisa difilter misal: "Kirim pesan hanya ke siswa yang belum daftar ulang").
* **Pengumuman Website:** Tempat menulis berita atau pop-up pengumuman jadwal di halaman depan (Sisi Pendaftar/Siswa).

## 6. ⚙️ Pengaturan PPDB
Pengaturan teknis yang jarang diubah.
* Buka/Tutup Gelombang Pendaftaran.
* Format Teks Pesan WhatsApp/Email untuk kelulusan dan pendaftaran.
* Manajemen Kuota tiap jurusan.

---
### Solusi Atas Kendala Staf:
1. **Lebih "Dumb-Proof" (Anti Ribet):** Tidak ada alur melingkar, semua fokus di klik baris tabel -> edit/set status -> simpan -> broadcast ke anak.
2. **Fleksibel Mengatasi Aturan:** Jurusan tidak dikunci mati oleh sistem (override bebas oleh panitia berdasarkan tes manual). Dan panitia bebas menambah murid yang telat/offline.
3. **Menghemat Waktu:** Mengurangi beban sekolah karena pengumuman dikirim otomatis ke WA/Email tanpa harus diprint manual.
