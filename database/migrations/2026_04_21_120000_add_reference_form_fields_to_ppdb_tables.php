<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppdb_applications', function (Blueprint $table): void {
            $table->string('no_kk', 20)->nullable();
            $table->string('no_registrasi_akta_lahir', 100)->nullable();
            $table->string('kewarganegaraan', 10)->default('WNI');
            $table->string('negara_asal', 120)->nullable();
            $table->string('kebutuhan_khusus', 50)->nullable();
            $table->string('nama_dusun', 120)->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('lintang', 50)->nullable();
            $table->string('bujur', 50)->nullable();
            $table->string('tempat_tinggal', 50)->nullable();
            $table->string('moda_transportasi', 50)->nullable();
            $table->string('pekerjaan_warga_belajar', 50)->nullable();
            $table->boolean('punya_kip')->nullable();
            $table->boolean('menerima_kip')->nullable();
            $table->string('alasan_menolak_pip', 50)->nullable();
            $table->string('nomor_telepon_rumah', 30)->nullable();
            $table->string('nik_ayah', 20)->nullable();
            $table->string('kebutuhan_khusus_ayah', 50)->nullable();
            $table->string('nik_ibu', 20)->nullable();
            $table->string('kebutuhan_khusus_ibu', 50)->nullable();
            $table->unsignedSmallInteger('lingkar_kepala')->nullable();
            $table->string('jarak_tempat_tinggal_kategori', 30)->nullable();
            $table->decimal('jarak_tempat_tinggal_km', 6, 2)->nullable();
            $table->unsignedTinyInteger('waktu_tempuh_jam')->nullable();
            $table->unsignedTinyInteger('waktu_tempuh_menit')->nullable();
            $table->string('jenis_kesejahteraan', 50)->nullable();
            $table->string('nomor_kartu_kesejahteraan', 100)->nullable();
            $table->string('nama_di_kartu_kesejahteraan', 255)->nullable();
        });

        Schema::table('ppdb_achievements', function (Blueprint $table): void {
            $table->string('achievement_type', 100)->nullable();
            $table->unsignedSmallInteger('achievement_year')->nullable();
            $table->string('achievement_organizer', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ppdb_achievements', function (Blueprint $table): void {
            $table->dropColumn([
                'achievement_type',
                'achievement_year',
                'achievement_organizer',
            ]);
        });

        Schema::table('ppdb_applications', function (Blueprint $table): void {
            $table->dropColumn([
                'no_kk',
                'no_registrasi_akta_lahir',
                'kewarganegaraan',
                'negara_asal',
                'kebutuhan_khusus',
                'nama_dusun',
                'rt',
                'rw',
                'kode_pos',
                'lintang',
                'bujur',
                'tempat_tinggal',
                'moda_transportasi',
                'pekerjaan_warga_belajar',
                'punya_kip',
                'menerima_kip',
                'alasan_menolak_pip',
                'nomor_telepon_rumah',
                'nik_ayah',
                'kebutuhan_khusus_ayah',
                'nik_ibu',
                'kebutuhan_khusus_ibu',
                'lingkar_kepala',
                'jarak_tempat_tinggal_kategori',
                'jarak_tempat_tinggal_km',
                'waktu_tempuh_jam',
                'waktu_tempuh_menit',
                'jenis_kesejahteraan',
                'nomor_kartu_kesejahteraan',
                'nama_di_kartu_kesejahteraan',
            ]);
        });
    }
};
