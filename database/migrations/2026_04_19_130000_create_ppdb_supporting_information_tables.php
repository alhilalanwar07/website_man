<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_contact_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('ppdb_periods')->cascadeOnDelete();
            $table->string('nama', 120);
            $table->string('jabatan', 120)->nullable();
            $table->string('nomor_telepon', 40)->nullable();
            $table->string('nomor_whatsapp', 40)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();

            $table->index(['period_id', 'is_active']);
        });

        Schema::create('ppdb_important_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('ppdb_periods')->cascadeOnDelete();
            $table->string('label', 150);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();

            $table->index(['period_id', 'is_active']);
        });

        Schema::create('ppdb_document_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('ppdb_periods')->cascadeOnDelete();
            $table->string('nama_berkas', 150);
            $table->string('keterangan', 255)->nullable();
            $table->boolean('wajib')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();

            $table->index(['period_id', 'is_active']);
        });

        Schema::create('ppdb_map_color_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('ppdb_periods')->cascadeOnDelete();
            $table->foreignId('program_keahlian_id')->constrained('program_keahlian')->cascadeOnDelete();
            $table->string('warna_map', 50);
            $table->string('keterangan', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();

            $table->unique(['period_id', 'program_keahlian_id'], 'ppdb_map_color_rules_unique');
            $table->index(['period_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_map_color_rules');
        Schema::dropIfExists('ppdb_document_requirements');
        Schema::dropIfExists('ppdb_important_dates');
        Schema::dropIfExists('ppdb_contact_persons');
    }
};
