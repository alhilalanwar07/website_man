<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppdb_applications', function (Blueprint $table): void {
            $table->string('alamat_sekolah')->nullable();
            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->unsignedTinyInteger('jumlah_saudara')->nullable();
            $table->string('rt_rw', 30)->nullable();
            $table->string('kelurahan', 120)->nullable();
            $table->string('kecamatan', 120)->nullable();
            $table->unsignedSmallInteger('tinggi_badan')->nullable();
            $table->unsignedSmallInteger('berat_badan')->nullable();
            $table->string('gol_darah', 10)->nullable();
            $table->string('ukuran_seragam', 120)->nullable();

            $table->string('tempat_tanggal_lahir_ayah')->nullable();
            $table->string('pendidikan_terakhir_ayah')->nullable();
            $table->string('penghasilan_ayah')->nullable();
            $table->text('alamat_ayah')->nullable();
            $table->string('kelurahan_ayah', 120)->nullable();
            $table->string('kecamatan_ayah', 120)->nullable();
            $table->string('nomor_hp_ayah', 30)->nullable();

            $table->string('tempat_tanggal_lahir_ibu')->nullable();
            $table->string('pendidikan_terakhir_ibu')->nullable();
            $table->string('penghasilan_ibu')->nullable();
            $table->text('alamat_ibu')->nullable();
            $table->string('kelurahan_ibu', 120)->nullable();
            $table->string('kecamatan_ibu', 120)->nullable();
            $table->string('nomor_hp_ibu', 30)->nullable();

            $table->timestamp('persetujuan_data_at')->nullable();

            $table->unique('email', 'ppdb_applications_email_unique_idx');
            $table->unique('nomor_hp', 'ppdb_applications_phone_unique_idx');
        });

        Schema::create('ppdb_achievements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('application_id')->constrained('ppdb_applications')->cascadeOnDelete();
            $table->string('achievement_name');
            $table->string('achievement_rank', 100);
            $table->string('achievement_level', 100);
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['application_id', 'sort_order'], 'ppdb_achievements_app_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_achievements');

        Schema::table('ppdb_applications', function (Blueprint $table): void {
            $table->dropUnique('ppdb_applications_email_unique_idx');
            $table->dropUnique('ppdb_applications_phone_unique_idx');

            $table->dropColumn([
                'alamat_sekolah',
                'anak_ke',
                'jumlah_saudara',
                'rt_rw',
                'kelurahan',
                'kecamatan',
                'tinggi_badan',
                'berat_badan',
                'gol_darah',
                'ukuran_seragam',
                'tempat_tanggal_lahir_ayah',
                'pendidikan_terakhir_ayah',
                'penghasilan_ayah',
                'alamat_ayah',
                'kelurahan_ayah',
                'kecamatan_ayah',
                'nomor_hp_ayah',
                'tempat_tanggal_lahir_ibu',
                'pendidikan_terakhir_ibu',
                'penghasilan_ibu',
                'alamat_ibu',
                'kelurahan_ibu',
                'kecamatan_ibu',
                'nomor_hp_ibu',
                'persetujuan_data_at',
            ]);
        });
    }
};
