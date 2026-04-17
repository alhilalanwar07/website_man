<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->index(['status_publikasi', 'published_at'], 'berita_status_published_at_idx');
            $table->index(['kategori_id', 'status_publikasi', 'published_at'], 'berita_kategori_status_published_idx');
            $table->index('deleted_at', 'berita_deleted_at_idx');
        });

        Schema::table('agenda', function (Blueprint $table) {
            $table->index('waktu_mulai', 'agenda_waktu_mulai_idx');
        });

        Schema::table('pengumuman', function (Blueprint $table) {
            $table->index(['tanggal_mulai_tampil', 'tanggal_akhir_tampil'], 'pengumuman_tampil_window_idx');
        });

        Schema::table('pegawai', function (Blueprint $table) {
            $table->index(['status_aktif', 'nama_lengkap'], 'pegawai_status_nama_idx');
        });

        Schema::table('program_keahlian', function (Blueprint $table) {
            $table->index(['status_tampil', 'nama_jurusan'], 'program_keahlian_status_nama_idx');
        });

        Schema::table('tefa_produk', function (Blueprint $table) {
            $table->index('status_ketersediaan', 'tefa_produk_status_idx');
        });

        Schema::table('galeri_item', function (Blueprint $table) {
            $table->index(['tipe_file', 'created_at'], 'galeri_item_tipe_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('galeri_item', function (Blueprint $table) {
            $table->dropIndex('galeri_item_tipe_created_idx');
        });

        Schema::table('tefa_produk', function (Blueprint $table) {
            $table->dropIndex('tefa_produk_status_idx');
        });

        Schema::table('program_keahlian', function (Blueprint $table) {
            $table->dropIndex('program_keahlian_status_nama_idx');
        });

        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropIndex('pegawai_status_nama_idx');
        });

        Schema::table('pengumuman', function (Blueprint $table) {
            $table->dropIndex('pengumuman_tampil_window_idx');
        });

        Schema::table('agenda', function (Blueprint $table) {
            $table->dropIndex('agenda_waktu_mulai_idx');
        });

        Schema::table('berita', function (Blueprint $table) {
            $table->dropIndex('berita_status_published_at_idx');
            $table->dropIndex('berita_kategori_status_published_idx');
            $table->dropIndex('berita_deleted_at_idx');
        });
    }
};
