<?php

namespace App\Livewire\Admin\PpdbV2;

use App\Mail\PpdbRegistrationSubmittedMail;
use App\Models\PpdbApplication;
use App\Models\PpdbPeriod;
use App\Models\PpdbTrack;
use App\Models\ProgramKeahlian;
use App\Support\PpdbSecureDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL as SignedUrl;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class Pendaftar extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $periodFilter = '';
    public $trackFilter = '';
    public $programFilter = '';

    // Bulk selection
    public $selectedRows = [];
    public $selectAll = false;

    // Manual Add State
    public $showAddModal = false;
    public $formSiswa = [
        'nama_lengkap' => '',
        'nisn' => '',
        'asal_sekolah' => '',
        'nomor_hp' => '',
        'email' => '',
        'jenis_kelamin' => 'L',
        'tempat_lahir' => '',
        'tanggal_lahir' => '',
        'alamat_lengkap' => '',
        'track_id' => '',
        'pilihan_program_1_id' => '',
    ];
    public array $availableTracksForManual = [];
    public array $availableProgramsForManual = [];

    // Detail / Split View State
    public $selectedSiswaId = null;
    public ?array $selectedSecureDocument = null;

    public function mount(): void
    {
        $this->periodFilter = (string) ($this->getDefaultPeriodId() ?? '');
        $this->prepareManualOptions();
        $this->applyManualDefaults();
    }

    public function updatedShowAddModal($value): void
    {
        if (! $value) {
            return;
        }

        $this->prepareManualOptions();
        $this->applyManualDefaults();
    }

    public function getSelectedSiswaProperty()
    {
        if (! $this->selectedSiswaId) {
            return null;
        }

        return PpdbApplication::with(['documents', 'track', 'period', 'pilihanProgram1', 'pilihanProgram2', 'pilihanProgram3'])
            ->find($this->selectedSiswaId);
    }

    public function getSelectedFormulirDownloadUrlProperty(): ?string
    {
        if (! $this->selectedSiswaId) {
            return null;
        }

        return SignedUrl::temporarySignedRoute(
            'ppdb.form.download',
            now()->addDays(30),
            ['application' => $this->selectedSiswaId]
        );
    }

    public function selectSiswa($id)
    {
        $this->selectedSiswaId = (int) $id;
        $this->refreshSelectedSecureDocument();
    }

    public function closeDetail()
    {
        $this->selectedSiswaId = null;
        $this->selectedSecureDocument = null;
    }

    public function exportExcel()
    {
        session()->flash('message', 'Mengekspor data Pendaftar ke format Excel (.xlsx)...');
    }

    public function exportPdf()
    {
        session()->flash('message', 'Mengekspor data Pendaftar ke format PDF...');
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedPeriodFilter()
    {
        $this->trackFilter = '';
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedTrackFilter()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedProgramFilter()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->trackFilter = '';
        $this->programFilter = '';
        $this->periodFilter = (string) ($this->getDefaultPeriodId() ?? '');

        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedRows = $this->getFilteredPendaftarQuery()
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedRows = [];
        }
    }

    public function verifySelected()
    {
        if (empty($this->selectedRows)) return;
        
        PpdbApplication::whereIn('id', $this->selectedRows)->update([
            'status_berkas' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id() ?? 1, // fallback dummy auth
        ]);

        $this->selectedRows = [];
        $this->selectAll = false;
        session()->flash('message', 'Berkas pendaftar terpilih berhasil diverifikasi massal.');
    }

    public function verifySingle($id)
    {
        PpdbApplication::where('id', $id)->update([
            'status_berkas' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id() ?? 1,
        ]);
        session()->flash('message', 'Berkas berhasil disahkan.');
        
        // Refresh selectedSiswa
        $this->selectedSiswaId = (int) $id;
        $this->refreshSelectedSecureDocument();
    }

    public function rejectSingle($id)
    {
        PpdbApplication::where('id', $id)->update([
            'status_berkas' => 'revision',
            'verified_at' => now(),
            'verified_by' => auth()->id() ?? 1,
        ]);
        session()->flash('message', 'Berkas telah ditolak (diminta revisi).');
    }

    public function kirimJadwalSelected()
    {
        if (empty($this->selectedRows)) return;
        
        // Disini Logic memanggil facade antrian WhatsApp dsb.
        session()->flash('message', 'Sukses: Jadwal tes dikirim ke antrean broadcast bagi ' . count($this->selectedRows) . ' pendaftar.');
        
        $this->selectedRows = [];
        $this->selectAll = false;
    }

    public function saveSiswaManual()
    {
        $this->prepareManualOptions();

        $period = PpdbPeriod::where('is_active', true)->first();

        if (! $period) {
            session()->flash('error', 'Gagal: Periode PPDB saat ini sedang non-aktif/ditutup.');

            return;
        }

        $this->formSiswa['nomor_hp'] = $this->normalizePhoneNumber((string) ($this->formSiswa['nomor_hp'] ?? ''));
        $this->formSiswa['email'] = strtolower(trim((string) ($this->formSiswa['email'] ?? '')));

        $this->validate([
            'formSiswa.nama_lengkap' => 'required|string|max:255',
            'formSiswa.nisn' => 'nullable|string|max:20',
            'formSiswa.asal_sekolah' => 'required|string|max:255',
            'formSiswa.nomor_hp' => ['required', 'regex:/^62\d{8,13}$/', Rule::unique('ppdb_applications', 'nomor_hp')],
            'formSiswa.email' => ['required', 'email', 'max:255', Rule::unique('ppdb_applications', 'email')],
            'formSiswa.jenis_kelamin' => 'required|in:L,P',
            'formSiswa.tempat_lahir' => 'required|string|max:100',
            'formSiswa.tanggal_lahir' => 'required|date|before_or_equal:today',
            'formSiswa.alamat_lengkap' => 'required|string',
            'formSiswa.track_id' => ['required', Rule::exists('ppdb_tracks', 'id')->where('period_id', $period->id)],
            'formSiswa.pilihan_program_1_id' => 'required|exists:program_keahlian,id',
        ], [
            'formSiswa.nomor_hp.regex' => 'Nomor HP harus format aktif Indonesia (08... atau 62...).',
            'formSiswa.nomor_hp.unique' => 'Nomor HP sudah digunakan pendaftar lain.',
            'formSiswa.email.unique' => 'Email sudah digunakan pendaftar lain.',
        ]);

        $noPendaftaran = $this->generateRegistrationNumber($period);

        $application = PpdbApplication::create([
            'period_id' => $period->id,
            'track_id' => (int) $this->formSiswa['track_id'],
            'nomor_pendaftaran' => $noPendaftaran,
            'nama_lengkap' => $this->formSiswa['nama_lengkap'],
            'nisn' => trim((string) ($this->formSiswa['nisn'] ?? '')) ?: null,
            'jenis_kelamin' => $this->formSiswa['jenis_kelamin'],
            'tempat_lahir' => $this->formSiswa['tempat_lahir'],
            'tanggal_lahir' => $this->formSiswa['tanggal_lahir'],
            'alamat_lengkap' => $this->formSiswa['alamat_lengkap'],
            'asal_sekolah' => $this->formSiswa['asal_sekolah'],
            'nomor_hp' => $this->formSiswa['nomor_hp'],
            'email' => $this->formSiswa['email'],
            'pilihan_program_1_id' => (int) $this->formSiswa['pilihan_program_1_id'],
            'status_pendaftaran' => 'submitted',
            'status_berkas' => 'pending',
            'persetujuan_data_at' => now(),
            'submitted_at' => now(),
        ]);

        $emailSent = $this->sendManualRegistrationEmail($application);

        session()->flash(
            'message',
            'Pendaftar offline bernama ' . $this->formSiswa['nama_lengkap'] . ' berhasil terdaftar mandiri.'
                . ($emailSent ? ' Email konfirmasi dokumen aman juga berhasil dikirim.' : ' Email konfirmasi belum terkirim, silakan kirim ulang dari panel detail.')
        );
        
        // Tutup modal dan reset
        $this->resetFormSiswa();
        $this->showAddModal = false;

        $this->selectedSiswaId = $application->id;
        $this->refreshSelectedSecureDocument();
    }

    public function sendSecureDocumentEmailToSelected(): void
    {
        if (! $this->selectedSiswaId) {
            session()->flash('error', 'Pilih pendaftar terlebih dahulu.');

            return;
        }

        $application = PpdbApplication::with(['period', 'track', 'pilihanProgram1', 'pilihanProgram2', 'pilihanProgram3', 'achievements'])
            ->find($this->selectedSiswaId);

        if (! $application) {
            session()->flash('error', 'Data pendaftar tidak ditemukan.');

            return;
        }

        $email = strtolower(trim((string) $application->email));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            session()->flash('error', 'Email pendaftar tidak valid untuk pengiriman formulir aman.');

            return;
        }

        $throttleKey = sprintf('ppdb-admin:secure-doc-mail:%d:%d', $application->id, auth()->id() ?? 0);

        if (Cache::has($throttleKey)) {
            session()->flash('error', 'Kirim ulang terlalu cepat. Coba lagi dalam 1 menit.');

            return;
        }

        Cache::put($throttleKey, true, now()->addMinute());

        if (! $this->sendManualRegistrationEmail($application)) {
            Cache::forget($throttleKey);
            session()->flash('error', 'Gagal mengirim formulir aman ke email pendaftar.');

            return;
        }

        session()->flash('message', 'Formulir aman berhasil dikirim ulang ke ' . $email . '.');
    }

    protected function sendManualRegistrationEmail(PpdbApplication $application): bool
    {
        try {
            Mail::to($application->email)->send(
                new PpdbRegistrationSubmittedMail($application, $this->buildDownloadUrl($application))
            );

            return true;
        } catch (Throwable $exception) {
            Log::warning('Failed to send PPDB secure document email from admin.', [
                'application_id' => $application->id,
                'email' => $application->email,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    protected function buildDownloadUrl(PpdbApplication $application): string
    {
        return SignedUrl::temporarySignedRoute(
            'ppdb.form.download',
            now()->addDays(30),
            ['application' => $application->id]
        );
    }

    protected function refreshSelectedSecureDocument(): void
    {
        if (! $this->selectedSiswaId) {
            $this->selectedSecureDocument = null;

            return;
        }

        $application = PpdbApplication::find($this->selectedSiswaId);

        if (! $application) {
            $this->selectedSecureDocument = null;

            return;
        }

        $this->selectedSecureDocument = app(PpdbSecureDocument::class)
            ->createDocumentContext($application);
    }

    protected function prepareManualOptions(): void
    {
        $period = PpdbPeriod::query()->where('is_active', true)->first();

        $tracks = $period
            ? $period->tracks()->visible()->get(['id', 'nama_jalur'])
            : collect();

        $programs = ProgramKeahlian::query()
            ->tampil()
            ->orderBy('nama_jurusan')
            ->get(['id', 'nama_jurusan']);

        $this->availableTracksForManual = $tracks
            ->map(fn ($track) => ['id' => (string) $track->id, 'label' => $track->nama_jalur])
            ->all();

        $this->availableProgramsForManual = $programs
            ->map(fn ($program) => ['id' => (string) $program->id, 'label' => $program->nama_jurusan])
            ->all();
    }

    protected function applyManualDefaults(): void
    {
        if (($this->formSiswa['track_id'] ?? '') === '' && $this->availableTracksForManual !== []) {
            $this->formSiswa['track_id'] = $this->availableTracksForManual[0]['id'];
        }

        if (($this->formSiswa['pilihan_program_1_id'] ?? '') === '' && $this->availableProgramsForManual !== []) {
            $this->formSiswa['pilihan_program_1_id'] = $this->availableProgramsForManual[0]['id'];
        }
    }

    protected function resetFormSiswa(): void
    {
        $this->formSiswa = [
            'nama_lengkap' => '',
            'nisn' => '',
            'asal_sekolah' => '',
            'nomor_hp' => '',
            'email' => '',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => '',
            'tanggal_lahir' => '',
            'alamat_lengkap' => '',
            'track_id' => '',
            'pilihan_program_1_id' => '',
        ];

        $this->applyManualDefaults();
        $this->resetValidation();
    }

    protected function normalizePhoneNumber(string $rawPhone): string
    {
        $digits = preg_replace('/\D+/', '', trim($rawPhone));

        if (! is_string($digits) || $digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }

    protected function generateRegistrationNumber(PpdbPeriod $period): string
    {
        $prefix = sprintf('PPDB-%s-G%s-', $period->tahun_mulai ?? now()->format('Y'), $period->gelombang_ke ?? 1);
        $sequence = PpdbApplication::where('period_id', $period->id)->count() + 1;

        do {
            $candidate = $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while (PpdbApplication::where('nomor_pendaftaran', $candidate)->exists());

        return $candidate;
    }

    protected function getDefaultPeriodId(): ?int
    {
        $activeId = PpdbPeriod::query()->where('is_active', true)->value('id');

        return $activeId ? (int) $activeId : null;
    }

    protected function resolvePeriodFilterId(): ?int
    {
        if ($this->periodFilter === 'all') {
            return null;
        }

        if ($this->periodFilter !== '' && is_numeric($this->periodFilter)) {
            return (int) $this->periodFilter;
        }

        return $this->getDefaultPeriodId();
    }

    protected function applyCombinedStatusFilter(Builder $query): void
    {
        if ($this->statusFilter !== '') {
            $query->whereVerificationStatus($this->statusFilter);
        }
    }

    protected function getFilteredPendaftarQuery(): Builder
    {
        $query = PpdbApplication::query();
        $periodId = $this->resolvePeriodFilterId();

        if ($periodId) {
            $query->where('period_id', $periodId);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%')
                    ->orWhere('nomor_hp', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $this->applyCombinedStatusFilter($query);

        if ($this->trackFilter !== '' && is_numeric($this->trackFilter)) {
            $query->where('track_id', (int) $this->trackFilter);
        }

        if ($this->programFilter !== '' && is_numeric($this->programFilter)) {
            $query->where('pilihan_program_1_id', (int) $this->programFilter);
        }

        return $query;
    }

    protected function resetBulkSelection(): void
    {
        $this->selectedRows = [];
        $this->selectAll = false;
    }

    public function render()
    {
        $periodId = $this->resolvePeriodFilterId();
        $period = $periodId ? PpdbPeriod::query()->find($periodId) : null;

        $pendaftar = $this->getFilteredPendaftarQuery()
            ->latest()
            ->paginate(15);

        $periodFilterOptions = PpdbPeriod::query()
            ->orderForSelection()
            ->get(['id', 'nama_periode', 'tahun_ajaran', 'gelombang_label', 'gelombang_ke', 'is_active']);

        $trackFilterOptions = PpdbTrack::query()
            ->when($periodId, fn ($q) => $q->where('period_id', $periodId))
            ->orderBy('urutan')
            ->orderBy('nama_jalur')
            ->get(['id', 'nama_jalur', 'period_id']);

        $programFilterOptions = ProgramKeahlian::query()
            ->tampil()
            ->orderBy('nama_jurusan')
            ->get(['id', 'nama_jurusan']);

        return view('livewire.admin.ppdb-v2.pendaftar', [
            'pendaftar' => $pendaftar,
            'period' => $period,
            'periodFilterOptions' => $periodFilterOptions,
            'trackFilterOptions' => $trackFilterOptions,
            'programFilterOptions' => $programFilterOptions,
        ])->layout('components.layouts.admin', ['title' => 'Data Pendaftar PPDB']);
    }
}
