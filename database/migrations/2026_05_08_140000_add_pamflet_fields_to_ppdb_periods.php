<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppdb_periods', function (Blueprint $table) {
            $table->string('pamflet_desktop')->nullable()->after('student_whatsapp_group_url');
            $table->string('pamflet_mobile')->nullable()->after('pamflet_desktop');
        });
    }

    public function down(): void
    {
        Schema::table('ppdb_periods', function (Blueprint $table) {
            $table->dropColumn(['pamflet_desktop', 'pamflet_mobile']);
        });
    }
};
