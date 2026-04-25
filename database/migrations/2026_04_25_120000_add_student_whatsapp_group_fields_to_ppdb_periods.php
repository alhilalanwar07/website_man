<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppdb_periods', function (Blueprint $table): void {
            $table->string('student_whatsapp_group_label', 120)->nullable()->after('catatan_pengumuman');
            $table->string('student_whatsapp_group_url', 500)->nullable()->after('student_whatsapp_group_label');
        });
    }

    public function down(): void
    {
        Schema::table('ppdb_periods', function (Blueprint $table): void {
            $table->dropColumn([
                'student_whatsapp_group_label',
                'student_whatsapp_group_url',
            ]);
        });
    }
};
