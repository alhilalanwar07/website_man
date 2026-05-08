<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profil_sekolah', function (Blueprint $table) {
            $table->string('akreditasi', 10)->nullable()->after('email_resmi');
            $table->string('nama_kepsek', 255)->nullable()->after('foto_kepsek');
        });
    }

    public function down(): void
    {
        Schema::table('profil_sekolah', function (Blueprint $table) {
            $table->dropColumn(['akreditasi', 'nama_kepsek']);
        });
    }
};
