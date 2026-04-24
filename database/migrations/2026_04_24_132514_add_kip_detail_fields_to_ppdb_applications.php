<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppdb_applications', function (Blueprint $table): void {
            $table->string('nomor_kip', 100)->nullable()->after('punya_kip');
            $table->string('nama_di_kip', 255)->nullable()->after('nomor_kip');
        });
    }

    public function down(): void
    {
        Schema::table('ppdb_applications', function (Blueprint $table): void {
            $table->dropColumn(['nomor_kip', 'nama_di_kip']);
        });
    }
};
