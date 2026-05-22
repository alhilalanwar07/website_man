# Next Steps - MAN 2 Kolaka

Catatan ini dipakai sebagai panduan lanjutan untuk penyempurnaan rebranding di folder `web-man2kolaka`.

## Checklist Lanjutan
- Lakukan pengecekan ulang sisa teks "SMK/SMKN" dan "Jurusan" agar konsisten menjadi "MAN 2 Kolaka" dan "Peminatan".
- Periksa semua halaman PPDB (status, daftar ulang, kontak admin) dan pastikan label tampil sesuai terminologi "Peminatan".
- Review dokumen PDF/Email PPDB untuk memastikan kop surat, footer, dan label sudah sesuai branding.

## Data & Konten
- Update data `ProfilSekolah` (nama, alamat, email, nomor, sosial media, teks sambutan) sesuai MAN 2 Kolaka.
- Sesuaikan daftar peminatan di `ProgramKeahlian` dan konten deskripsi tiap peminatan.
- Review konten berita/agenda/galeri agar sesuai konteks madrasah.

## Aset & Branding
- Ganti logo dan favicon resmi MAN 2 Kolaka.
- Jika perlu, siapkan file logo yang dipakai PDF di `storage/app/public/` (mis. ganti `smk1kolaka.jpg`) dan sesuaikan path di template PDF.
- Perbarui warna/tema jika ada pedoman identitas visual baru.

## Uji Cepat
- Cek halaman: Beranda, Profil, Peminatan, Berita, Galeri, Agenda, PPDB.
- Cek PDF: formulir pendaftaran, pengumuman hasil, daftar ulang.
- Cek email PPDB (pendaftaran berhasil) dan halaman verifikasi dokumen.

Catatan: Hindari perubahan di folder `web-smk1kolaka`.
