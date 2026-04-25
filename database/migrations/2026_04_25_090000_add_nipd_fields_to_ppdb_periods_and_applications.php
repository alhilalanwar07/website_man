<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppdb_periods', function (Blueprint $table): void {
            $table->unsignedBigInteger('nipd_last_number')
                ->default(0)
                ->after('catatan_pengumuman');
        });

        Schema::table('ppdb_applications', function (Blueprint $table): void {
            $table->unsignedBigInteger('nipd')
                ->nullable()
                ->after('nomor_pendaftaran');

            $table->unique(['period_id', 'nipd'], 'ppdb_applications_period_nipd_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::table('ppdb_applications', function (Blueprint $table): void {
            $table->dropUnique('ppdb_applications_period_nipd_unique_idx');
            $table->dropColumn('nipd');
        });

        Schema::table('ppdb_periods', function (Blueprint $table): void {
            $table->dropColumn('nipd_last_number');
        });
    }
};
