<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppdb_applications', function (Blueprint $table): void {
            $table->foreignId('pilihan_program_3_id')
                ->nullable()
                ->after('pilihan_program_2_id')
                ->constrained('program_keahlian')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ppdb_applications', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('pilihan_program_3_id');
        });
    }
};
