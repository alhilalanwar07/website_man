<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\PpdbExportController;
use App\Http\Controllers\Frontend\PpdbRegistrationDocumentController;
use App\Http\Controllers\TelegramWebhookController;
use App\Livewire\Home;
use App\Livewire\Auth\Login;
use App\Livewire\Frontend\Profil;
use App\Livewire\Frontend\JurusanIndex;
use App\Livewire\Frontend\JurusanDetail;
use App\Livewire\Frontend\BeritaIndex;
use App\Livewire\Frontend\BeritaDetail;
use App\Livewire\Frontend\GaleriPage;
use App\Livewire\Frontend\AgendaIndex;
use App\Livewire\Frontend\PpdbFormPage;
use App\Livewire\Frontend\PpdbPage;
use App\Livewire\Frontend\PpdbDaftarUlang;
use App\Livewire\Frontend\PpdbStatus;
use App\Livewire\Frontend\PpdbContactAdmin;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ProfilSekolah;
use App\Livewire\Admin\Pegawai;
use App\Livewire\Admin\ProgramKeahlian;
use App\Livewire\Admin\Tefa;
use App\Livewire\Admin\Berita;
use App\Livewire\Admin\BeritaEditor;
use App\Livewire\Admin\Pengumuman;
use App\Livewire\Admin\Agenda;
use App\Livewire\Admin\Galeri;
use App\Livewire\Admin\HariLibur;
use App\Livewire\Admin\Ppdb;
use App\Livewire\Admin\PpdbAnalytics;
use App\Livewire\Admin\PpdbApplicants;
use App\Livewire\Admin\PpdbReRegistration;
use App\Livewire\Admin\PpdbSettings;
use App\Livewire\Admin\PpdbTestScoring;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\PpdbV2\Dashboard as PpdbDashboardV2;
use App\Livewire\Admin\PpdbV2\Pendaftar as PpdbPendaftarV2;
use App\Livewire\Admin\PpdbV2\PenentuanJurusan as PpdbPenentuanJurusanV2;
use App\Livewire\Admin\PpdbV2\DaftarUlang as PpdbDaftarUlangV2;
use App\Livewire\Admin\PpdbV2\Broadcast as PpdbBroadcastV2;
use App\Livewire\Admin\PpdbV2\Pengaturan as PpdbPengaturanV2;
Route::post('/telegram/webhook', TelegramWebhookController::class)
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('telegram.webhook');
    
// Frontend
Route::get('/', Home::class)->name('home');
Route::get('/profil', Profil::class)->name('profil');
Route::get('/jurusan', JurusanIndex::class)->name('jurusan.index');
Route::get('/jurusan/{slug}', JurusanDetail::class)->name('jurusan.show');
Route::get('/berita', BeritaIndex::class)->name('berita.index');
Route::get('/berita/{slug}', BeritaDetail::class)->name('berita.show');
Route::get('/galeri', GaleriPage::class)->name('galeri');
Route::get('/agenda', AgendaIndex::class)->name('agenda.index');
Route::get('/ppdb', PpdbPage::class)->name('ppdb.index');
Route::get('/ppdb/formulir', PpdbFormPage::class)->name('ppdb.form');
Route::get('/ppdb/formulir/{application}/unduh', [PpdbRegistrationDocumentController::class, 'download'])
    ->middleware('signed')
    ->name('ppdb.form.download');
Route::get('/ppdb/verifikasi-dokumen/{application}', [PpdbRegistrationDocumentController::class, 'verify'])
    ->name('ppdb.document.verify');
Route::get('/ppdb/status', PpdbStatus::class)->name('ppdb.status');
Route::get('/ppdb/daftar-ulang', PpdbDaftarUlang::class)->name('ppdb.daftar-ulang');
Route::get('/ppdb/hubungi-admin', PpdbContactAdmin::class)->name('ppdb.contact');

// Auth
Route::get('/login', Login::class)->middleware('guest')->name('login');
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/login');
})->middleware('auth')->name('logout');

// Admin
// OLD ROUTES PPDB - DISABLED (Untuk dibuat ulang versi Simple)
/*
Route::middleware(['auth', 'role:admin,ppdb-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/ppdb', Ppdb::class)->name('ppdb');
    Route::get('/ppdb/export', PpdbExportController::class)->name('ppdb.export');
    Route::get('/ppdb/pendaftar', PpdbApplicants::class)->name('ppdb.applicants');
    Route::get('/ppdb/penilaian-tes', PpdbTestScoring::class)->name('ppdb.tests');
    Route::get('/ppdb/verifikasi-daftar-ulang', PpdbReRegistration::class)->name('ppdb.re-registration');
    Route::get('/ppdb/pengaturan', PpdbSettings::class)->name('ppdb.settings');
});
*/

// ROUTES PPDB V2 (Simple UI)
Route::middleware(['auth', 'role:admin,ppdb-admin'])->prefix('admin/ppdb')->name('admin.ppdb.')->group(function () {
    Route::get('/', PpdbDashboardV2::class)->name('dashboard');
    Route::get('/pendaftar', PpdbPendaftarV2::class)->name('pendaftar');
    Route::get('/penentuan-jurusan', PpdbPenentuanJurusanV2::class)->name('penentuan-jurusan');
    Route::get('/daftar-ulang', PpdbDaftarUlangV2::class)->name('daftar-ulang');
    Route::get('/broadcast', PpdbBroadcastV2::class)->name('broadcast');
    Route::get('/pengaturan', PpdbPengaturanV2::class)->name('pengaturan');
});

// Legacy aliases to keep existing menus/tests compatible during PPDB V2 rollout.
Route::middleware(['auth', 'role:admin,ppdb-admin'])->prefix('admin/ppdb')->group(function () {
    Route::get('/', PpdbDashboardV2::class)->name('admin.ppdb');
    Route::get('/dashboard', PpdbDashboardV2::class)->name('admin.ppdb.dashboard');
    Route::get('/export', PpdbExportController::class)->name('admin.ppdb.export');
    Route::get('/pendaftar-lanjutan', PpdbApplicants::class)->name('admin.ppdb.applicants');
    Route::get('/penilaian-tes', PpdbTestScoring::class)->name('admin.ppdb.tests');
    Route::get('/verifikasi-daftar-ulang', PpdbReRegistration::class)->name('admin.ppdb.re-registration');
    Route::get('/pengaturan-lanjutan', PpdbSettings::class)->name('admin.ppdb.settings');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/ppdb/analisa', PpdbAnalytics::class)->name('ppdb.analytics');
    Route::get('/profil-sekolah', ProfilSekolah::class)->name('profil-sekolah');
    Route::get('/pegawai', Pegawai::class)->name('pegawai');
    Route::get('/program-keahlian', ProgramKeahlian::class)->name('program-keahlian');
    Route::get('/tefa', Tefa::class)->name('tefa');
    Route::get('/berita', Berita::class)->name('berita');
    Route::get('/berita/tambah', BeritaEditor::class)->name('berita.create');
    Route::get('/berita/{berita}/edit', BeritaEditor::class)->name('berita.edit');
    Route::get('/pengumuman', Pengumuman::class)->name('pengumuman');
    Route::get('/agenda', Agenda::class)->name('agenda');
    Route::get('/galeri', Galeri::class)->name('galeri');
    Route::get('/hari-libur', HariLibur::class)->name('hari-libur');
    Route::get('/settings', Settings::class)->name('settings');

    

});

// ═══════════════════════════════════════════════════════════════
// HOSTING UTILITY ROUTE (untuk shared hosting tanpa SSH)
// Akses: /run/{command}?token=smk1kolaka2026
// Contoh: /run/migrate, /run/cache-clear, /run/optimize
// ═══════════════════════════════════════════════════════════════
Route::get('/run/{command}', function (Request $request, string $command) {
    if ($request->query('token') !== 'smk1kolaka2026') {
        abort(403, 'Token tidak valid.');
    }

    $commands = [
        'migrate'        => fn () => Artisan::call('migrate', ['--force' => true]),
        'migrate-path'   => fn () => Artisan::call('migrate', [
            '--path' => 'database/migrations/' . request()->query('path') . '.php',
            '--force' => true,
        ]),
        'migrate-status' => fn () => Artisan::call('migrate:status'),
        'storage-link'   => fn () => Artisan::call('storage:link'),
        'optimize'       => fn () => Artisan::call('optimize'),
        'optimize-clear' => fn () => Artisan::call('optimize:clear'),
        'cache-clear'    => function () {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
        },
        'seed' => fn () => Artisan::call('db:seed', [
            '--class' => request()->query('class', 'DatabaseSeeder'),
            '--force' => true,
        ]),
    ];

    if (! isset($commands[$command])) {
        return '<pre style="font-family:monospace;padding:20px;">❌ Command tidak dikenali: ' . e($command) . PHP_EOL
             . 'Tersedia: ' . implode(', ', array_keys($commands)) . '</pre>';
    }

    $commands[$command]();

    return '<pre style="font-family:monospace;padding:20px;">✅ ' . strtoupper($command) . PHP_EOL . Artisan::output() . '</pre>';
});
