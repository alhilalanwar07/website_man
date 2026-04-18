<?php

use App\Support\SchoolHolidayWorkflow;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('school-holiday:check {--force : Paksa kirim konfirmasi walau sudah ada catatan}', function () {
    $result = app(SchoolHolidayWorkflow::class)->checkTomorrowAndNotifyAdmins((bool) $this->option('force'));

    $this->info((string) ($result['message'] ?? 'Proses selesai.'));

    if (! empty($result['events']) && is_array($result['events'])) {
        $this->line('Perayaan terdeteksi: ' . implode(', ', $result['events']));
    }
})->purpose('Deteksi tanggal merah besok dan minta konfirmasi libur ke Telegram admin');

Schedule::call(function (): void {
    app(SchoolHolidayWorkflow::class)->checkTomorrowAndNotifyAdmins();
})->dailyAt('05:30')->name('school-holiday-auto-check');
