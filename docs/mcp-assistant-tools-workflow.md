# Playbook MCP Assistant Tools

Dokumen ini berisi cara pakai praktis untuk tiga tools MCP di project ini:
- TeamCreate
- Context7
- Sniper

Server MCP yang dipakai:
- handle local: assistant-tools
- route web: /mcp/assistant-tools

## 1. Kapan Dipakai

Gunakan urutan ini untuk hampir semua pekerjaan fitur atau perbaikan:
1. TeamCreate saat mulai task
2. Context7 saat riset referensi
3. Sniper sebelum merge atau deploy

## 2. Quick Start di MCP Inspector

1. Transport Type: STDIO
2. Command: C:/laragon/bin/php/php-8.3.26-Win32-vs16-x64/php.exe
3. Arguments: C:/laragon/www/web-smk1kolaka/artisan mcp:start assistant-tools
4. Klik Connect
5. Buka tab Tools dan klik List Tools

## 3. Template TeamCreate

Tujuan: membangun rencana eksekusi kerja yang jelas.

Payload contoh:
~~~json
{
  "goal": "Selesaikan perbaikan validasi PPDB tahap publik",
  "context": "Livewire frontend PPDB multi-step dan pesan validasi bahasa Indonesia",
  "agents": ["explore-codebase", "implementation", "validator"],
  "constraints": [
    "jangan ubah alur bisnis pendaftaran",
    "jaga kompatibilitas test existing"
  ],
  "deliverables": [
    "patch final",
    "hasil test",
    "catatan risiko"
  ]
}
~~~

Cara pakai output TeamCreate:
1. Jadikan output sebagai checklist urutan kerja
2. Jadikan Expected Deliverables sebagai acceptance criteria task

## 4. Template Context7

Tujuan: menyusun query riset dokumentasi yang terarah.

Payload contoh:
~~~json
{
  "topic": "form validation and custom messages",
  "framework": "Laravel",
  "version": "12.x",
  "objectives": [
    "Livewire integration",
    "localized validation attributes",
    "feature test strategy"
  ]
}
~~~

Cara pakai output Context7:
1. Ambil 3 sampai 5 query teratas
2. Pakai query itu untuk cari referensi implementasi sebelum coding

## 5. Template Sniper

Tujuan: validasi terfokus untuk area paling berisiko.

Payload contoh:
~~~json
{
  "objective": "Audit perubahan validasi PPDB agar tidak regresi",
  "scope": "lang/id/validation.php dan komponen Livewire form PPDB",
  "risks": [
    "label atribut tidak sinkron",
    "rule wajib tidak terpanggil",
    "pesan validasi campur bahasa"
  ],
  "test_command": "php artisan test --filter=PpdbPublicRoutesTest"
}
~~~

Cara pakai output Sniper:
1. Jalankan Critical Checks sebagai QA checklist
2. Jalankan Suggested Test Command
3. Catat hasil sebagai bukti sebelum merge

## 6. Alur Kerja Harian yang Direkomendasikan

1. Buat plan dengan TeamCreate
2. Riset cepat dengan Context7
3. Implementasi perubahan di codebase
4. Gunakan Sniper sebagai gate sebelum commit atau PR

## 7. Setelah Semua Berhasil, Bisa Dipakai Untuk Apa

1. Planning sprint atau task breakdown
2. Standar review perubahan berisiko tinggi
3. Checklist release kecil tanpa melewatkan test penting
4. Onboarding developer baru dengan pola kerja yang konsisten

## 8. Troubleshooting Singkat

1. Jika Connect gagal, pastikan path php dan artisan benar
2. Jika tab Prompts kosong, itu normal karena server ini hanya mendaftarkan tools
3. Jika tools tidak muncul, klik Restart di Inspector lalu List Tools ulang
