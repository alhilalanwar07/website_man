<?php

namespace App\Livewire\Admin\PpdbV2;

use App\Models\PpdbContactPerson;
use App\Models\PpdbDocumentRequirement;
use App\Models\PpdbImportantDate;
use App\Models\PpdbMapColorRule;
use App\Models\PpdbPeriod;
use App\Models\PpdbQuota;
use App\Models\PpdbTrack;
use App\Models\ProgramKeahlian;
use App\Models\ProfilSekolah;
use App\Support\PpdbPeriodResolver;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Throwable;

class Pengaturan extends Component
{
    protected const TAB_OPTIONS = [
        'periode',
        'jalur',
        'kuota',
        'kontak',
        'tanggal',
        'persyaratan',
        'warna-map',
    ];

    protected const MAP_COLOR_PALETTE = [
        'Merah',
        'Kuning',
        'Hijau',
        'Biru',
        'Oranye',
        'Ungu',
        'Cokelat',
        'Pink',
        'Abu-abu',
        'Putih',
    ];

    #[Url(as: 'periode')]
    public string $period = '';

    #[Url(as: 'tab')]
    public string $tab = 'periode';

    public ?int $managementPeriodId = null;
    public array $periodForm = [];
    public array $trackSettings = [];
    public array $quotaSettings = [];
    public array $contactPersonSettings = [];
    public array $importantDateSettings = [];
    public array $documentRequirementSettings = [];
    public array $mapColorSettings = [];
    public array $newPeriodForm = [];

    public bool $showActionModal = false;
    public string $pendingAction = '';
    public string $actionModalTitle = '';
    public string $actionModalMessage = '';
    public string $actionModalConfirmLabel = '';
    public string $actionModalTone = 'info';

    public function mount(): void
    {
        $this->ensureTabIsValid();

        $selectedPeriod = $this->resolveSelectedPeriod();

        if ($selectedPeriod && $this->period === '') {
            $this->period = (string) $selectedPeriod->id;
        }

        $this->syncManagementForms($selectedPeriod);
        $this->resetNewPeriodForm($selectedPeriod);
    }

    public function updatedPeriod(): void
    {
        $selectedPeriod = $this->resolveSelectedPeriod();

        $this->syncManagementForms($selectedPeriod);
        $this->resetNewPeriodForm($selectedPeriod);
    }

    public function updatedTab(): void
    {
        $this->ensureTabIsValid();
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, self::TAB_OPTIONS, true)) {
            $this->tab = $tab;
        }
    }

    public function openActionModal(string $action): void
    {
        $config = $this->resolveActionModalConfig($action);

        if (! $config) {
            $this->dispatch('toast', type: 'error', message: 'Aksi tidak dikenali. Silakan muat ulang halaman.');
            return;
        }

        $this->pendingAction = $action;
        $this->actionModalTitle = $config['title'];
        $this->actionModalMessage = $config['message'];
        $this->actionModalConfirmLabel = $config['confirm_label'];
        $this->actionModalTone = $config['tone'];
        $this->showActionModal = true;
    }

    public function closeActionModal(): void
    {
        $this->showActionModal = false;
        $this->pendingAction = '';
        $this->actionModalTitle = '';
        $this->actionModalMessage = '';
        $this->actionModalConfirmLabel = '';
        $this->actionModalTone = 'info';
    }

    public function confirmAction(): void
    {
        if ($this->pendingAction === '') {
            $this->dispatch('toast', type: 'error', message: 'Belum ada aksi yang dipilih.');
            return;
        }

        $action = $this->pendingAction;
        $this->closeActionModal();

        try {
            switch ($action) {
                case 'activate-period':
                    $this->activateSelectedPeriod();
                    break;
                case 'archive-period':
                    $this->archiveSelectedPeriod();
                    break;
                case 'delete-period':
                    $this->deleteSelectedPeriod();
                    break;
                case 'create-period':
                    $this->createPeriod();
                    break;
                case 'save-period-settings':
                    $this->savePeriodSettings();
                    break;
                case 'save-track-settings':
                    $this->saveTrackSettings();
                    break;
                case 'save-quota-settings':
                    $this->saveQuotaSettings();
                    break;
                case 'save-contact-settings':
                    $this->saveContactPersonSettings();
                    break;
                case 'save-important-date-settings':
                    $this->saveImportantDateSettings();
                    break;
                case 'save-document-requirement-settings':
                    $this->saveDocumentRequirementSettings();
                    break;
                case 'save-map-color-settings':
                    $this->saveMapColorSettings();
                    break;
                default:
                    $this->dispatch('toast', type: 'error', message: 'Aksi tidak dikenali. Silakan muat ulang halaman.');
                    break;
            }
        } catch (ValidationException) {
            $this->dispatch('toast', type: 'error', message: 'Validasi gagal. Periksa kolom bertanda merah lalu coba lagi.');
        } catch (Throwable $exception) {
            report($exception);
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan sistem saat memproses aksi. Silakan coba lagi.');
        }
    }

    public function savePeriodSettings(): void
    {
        if (! $this->managementPeriodId) {
            $this->dispatch('toast', type: 'error', message: 'Belum ada periode PPDB yang bisa diperbarui.');
            return;
        }

        $validated = $this->validate([
            'periodForm.nama_periode' => 'required|string|max:255',
            'periodForm.tahun_ajaran' => 'required|string|max:50',
            'periodForm.tahun_mulai' => 'required|integer|min:2020|max:2100',
            'periodForm.tahun_selesai' => 'required|integer|gte:periodForm.tahun_mulai',
            'periodForm.gelombang_ke' => 'required|integer|min:1|max:20',
            'periodForm.gelombang_label' => 'required|string|max:100',
            'periodForm.tanggal_mulai_pendaftaran' => 'required|date',
            'periodForm.tanggal_selesai_pendaftaran' => 'required|date|after_or_equal:periodForm.tanggal_mulai_pendaftaran',
            'periodForm.tanggal_pengumuman' => 'nullable|date|after_or_equal:periodForm.tanggal_selesai_pendaftaran',
            'periodForm.tanggal_mulai_daftar_ulang' => 'nullable|date|after_or_equal:periodForm.tanggal_pengumuman',
            'periodForm.tanggal_selesai_daftar_ulang' => 'nullable|date|after_or_equal:periodForm.tanggal_mulai_daftar_ulang',
            'periodForm.status' => 'required|in:draft,published,closed,archived',
            'periodForm.is_active' => 'boolean',
            'periodForm.status_pengumuman' => 'required|in:draft,published',
            'periodForm.catatan_pengumuman' => 'nullable|string',
            'periodForm.deskripsi' => 'nullable|string',
        ]);

        $status = $validated['periodForm']['status'];
        $statusPengumuman = $validated['periodForm']['status_pengumuman'];
        $tanggalPengumuman = $validated['periodForm']['tanggal_pengumuman'] ?? null;
        $tanggalMulaiDaftarUlang = $validated['periodForm']['tanggal_mulai_daftar_ulang'] ?? null;
        $tanggalSelesaiDaftarUlang = $validated['periodForm']['tanggal_selesai_daftar_ulang'] ?? null;

        if ($statusPengumuman === 'published' && ! $tanggalPengumuman) {
            $this->addError('periodForm.tanggal_pengumuman', 'Tanggal pengumuman wajib diisi saat status pengumuman dipublikasikan.');
            $this->dispatch('toast', type: 'error', message: 'Tanggal pengumuman wajib diisi saat status pengumuman dipublikasikan.');
            return;
        }

        if ($statusPengumuman === 'published' && (! $tanggalMulaiDaftarUlang || ! $tanggalSelesaiDaftarUlang)) {
            $this->addError('periodForm.tanggal_mulai_daftar_ulang', 'Tanggal daftar ulang wajib diisi saat pengumuman dipublikasikan.');
            $this->dispatch('toast', type: 'error', message: 'Tanggal daftar ulang wajib diisi saat pengumuman dipublikasikan.');
            return;
        }

        $period = PpdbPeriod::findOrFail($this->managementPeriodId);
        $payload = $validated['periodForm'];
        $payload['is_active'] = (bool) ($payload['is_active'] ?? false);

        if ($payload['is_active'] && $status === 'draft') {
            $this->addError('periodForm.status', 'Periode draft tidak dapat dijadikan aktif default. Ubah status terlebih dahulu.');
            $this->dispatch('toast', type: 'error', message: 'Periode draft tidak dapat dijadikan aktif default. Ubah status terlebih dahulu.');
            return;
        }

        $payload['hasil_diumumkan_at'] = $payload['status_pengumuman'] === 'published'
            ? ($period->hasil_diumumkan_at ?? now())
            : null;

        if ($payload['is_active']) {
            PpdbPeriod::query()->whereKeyNot($period->id)->update(['is_active' => false]);
        }

        $period->update($payload);

        $this->refreshSelectedPeriod();
        $this->dispatch('toast', type: 'success', message: 'Pengaturan periode PPDB berhasil diperbarui.');
    }

    public function saveTrackSettings(): void
    {
        if (! $this->managementPeriodId) {
            $this->dispatch('toast', type: 'error', message: 'Belum ada periode aktif untuk jalur PPDB.');
            return;
        }

        $this->validate([
            'trackSettings.*.status_tampil' => 'boolean',
            'trackSettings.*.requires_verification' => 'boolean',
            'trackSettings.*.urutan' => 'required|integer|min:1|max:99',
        ]);

        $tracks = PpdbTrack::where('period_id', $this->managementPeriodId)->get();

        foreach ($tracks as $track) {
            if (! isset($this->trackSettings[$track->id])) {
                continue;
            }

            $track->update([
                'status_tampil' => (bool) ($this->trackSettings[$track->id]['status_tampil'] ?? false),
                'requires_verification' => (bool) ($this->trackSettings[$track->id]['requires_verification'] ?? false),
                'urutan' => (int) ($this->trackSettings[$track->id]['urutan'] ?? $track->urutan),
            ]);
        }

        $this->refreshSelectedPeriod();
        $this->dispatch('toast', type: 'success', message: 'Pengaturan jalur PPDB berhasil diperbarui.');
    }

    public function saveQuotaSettings(): void
    {
        if (! $this->managementPeriodId) {
            $this->dispatch('toast', type: 'error', message: 'Belum ada periode aktif untuk kuota PPDB.');
            return;
        }

        $this->validate([
            'quotaSettings.*.kuota' => 'required|integer|min:0|max:500',
            'quotaSettings.*.status_aktif' => 'boolean',
        ]);

        $quotas = PpdbQuota::where('period_id', $this->managementPeriodId)->get();

        foreach ($quotas as $quota) {
            if (! isset($this->quotaSettings[$quota->id])) {
                continue;
            }

            $quota->update([
                'kuota' => (int) ($this->quotaSettings[$quota->id]['kuota'] ?? $quota->kuota),
                'status_aktif' => (bool) ($this->quotaSettings[$quota->id]['status_aktif'] ?? false),
            ]);
        }

        $this->refreshSelectedPeriod();
        $this->dispatch('toast', type: 'success', message: 'Kuota PPDB berhasil diperbarui.');
    }

    public function saveContactPersonSettings(): void
    {
        if (! $this->managementPeriodId) {
            $this->dispatch('toast', type: 'error', message: 'Belum ada periode aktif untuk kontak person.');
            return;
        }

        $validated = $this->validate([
            'contactPersonSettings' => 'required|array|min:1',
            'contactPersonSettings.*.id' => 'nullable|integer',
            'contactPersonSettings.*.nama' => 'required|string|max:120',
            'contactPersonSettings.*.jabatan' => 'nullable|string|max:120',
            'contactPersonSettings.*.nomor_telepon' => 'nullable|string|max:40',
            'contactPersonSettings.*.nomor_whatsapp' => 'nullable|string|max:40',
            'contactPersonSettings.*.email' => 'nullable|email|max:255',
            'contactPersonSettings.*.is_primary' => 'boolean',
            'contactPersonSettings.*.is_active' => 'boolean',
        ]);

        $rows = collect($validated['contactPersonSettings'])
            ->values()
            ->map(function (array $row): array {
                return [
                    'id' => isset($row['id']) && $row['id'] ? (int) $row['id'] : null,
                    'nama' => trim((string) $row['nama']),
                    'jabatan' => trim((string) ($row['jabatan'] ?? '')) ?: null,
                    'nomor_telepon' => trim((string) ($row['nomor_telepon'] ?? '')) ?: null,
                    'nomor_whatsapp' => $this->normalizeWhatsapp((string) ($row['nomor_whatsapp'] ?? '')),
                    'email' => trim((string) ($row['email'] ?? '')) ?: null,
                    'is_primary' => (bool) ($row['is_primary'] ?? false),
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ];
            });

        foreach ($rows as $index => $row) {
            if (! $row['nomor_telepon'] && ! $row['nomor_whatsapp'] && ! $row['email']) {
                $this->addError('contactPersonSettings.' . $index . '.nomor_telepon', 'Isi minimal telepon, WhatsApp, atau email.');
                $this->dispatch('toast', type: 'error', message: 'Setiap kontak person harus memiliki minimal satu kanal kontak.');
                return;
            }
        }

        $primaryIndex = $rows->search(fn (array $row) => $row['is_primary'] === true);

        if ($primaryIndex === false) {
            $primaryIndex = 0;
        }

        $rows = $rows
            ->values()
            ->map(fn (array $row, int $index): array => array_merge($row, ['is_primary' => $index === $primaryIndex]))
            ->values();

        $period = PpdbPeriod::findOrFail($this->managementPeriodId);
        $existingIds = $rows->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();

        if ($existingIds === []) {
            $period->contactPersons()->delete();
        } else {
            $period->contactPersons()->whereNotIn('id', $existingIds)->delete();
        }

        foreach ($rows as $index => $row) {
            $contact = $row['id']
                ? $period->contactPersons()->whereKey($row['id'])->first()
                : null;

            if (! $contact) {
                $contact = new PpdbContactPerson();
            }

            $contact->fill([
                'period_id' => $period->id,
                'nama' => $row['nama'],
                'jabatan' => $row['jabatan'],
                'nomor_telepon' => $row['nomor_telepon'],
                'nomor_whatsapp' => $row['nomor_whatsapp'],
                'email' => $row['email'],
                'is_primary' => $row['is_primary'],
                'is_active' => $row['is_active'],
                'urutan' => $index + 1,
            ]);
            $contact->save();
        }

        $this->refreshSelectedPeriod();
        $this->dispatch('toast', type: 'success', message: 'Kontak person PPDB berhasil diperbarui.');
    }

    public function saveImportantDateSettings(): void
    {
        if (! $this->managementPeriodId) {
            $this->dispatch('toast', type: 'error', message: 'Belum ada periode aktif untuk tanggal penting.');
            return;
        }

        $validated = $this->validate([
            'importantDateSettings' => 'required|array|min:1',
            'importantDateSettings.*.id' => 'nullable|integer',
            'importantDateSettings.*.label' => 'required|string|max:150',
            'importantDateSettings.*.tanggal_mulai' => 'required|date',
            'importantDateSettings.*.tanggal_selesai' => 'nullable|date',
            'importantDateSettings.*.keterangan' => 'nullable|string|max:255',
            'importantDateSettings.*.is_active' => 'boolean',
        ]);

        $rows = collect($validated['importantDateSettings'])
            ->values()
            ->map(function (array $row): array {
                return [
                    'id' => isset($row['id']) && $row['id'] ? (int) $row['id'] : null,
                    'label' => trim((string) $row['label']),
                    'tanggal_mulai' => (string) $row['tanggal_mulai'],
                    'tanggal_selesai' => $row['tanggal_selesai'] ? (string) $row['tanggal_selesai'] : null,
                    'keterangan' => trim((string) ($row['keterangan'] ?? '')) ?: null,
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ];
            });

        foreach ($rows as $index => $row) {
            if ($row['tanggal_selesai'] && $row['tanggal_selesai'] < $row['tanggal_mulai']) {
                $this->addError('importantDateSettings.' . $index . '.tanggal_selesai', 'Tanggal selesai harus sama atau setelah tanggal mulai.');
                $this->dispatch('toast', type: 'error', message: 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
                return;
            }
        }

        $period = PpdbPeriod::findOrFail($this->managementPeriodId);
        $existingIds = $rows->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();

        if ($existingIds === []) {
            $period->importantDates()->delete();
        } else {
            $period->importantDates()->whereNotIn('id', $existingIds)->delete();
        }

        foreach ($rows as $index => $row) {
            $item = $row['id']
                ? $period->importantDates()->whereKey($row['id'])->first()
                : null;

            if (! $item) {
                $item = new PpdbImportantDate();
            }

            $item->fill([
                'period_id' => $period->id,
                'label' => $row['label'],
                'tanggal_mulai' => $row['tanggal_mulai'],
                'tanggal_selesai' => $row['tanggal_selesai'],
                'keterangan' => $row['keterangan'],
                'is_active' => $row['is_active'],
                'urutan' => $index + 1,
            ]);
            $item->save();
        }

        $this->refreshSelectedPeriod();
        $this->dispatch('toast', type: 'success', message: 'Tanggal penting PPDB berhasil diperbarui.');
    }

    public function saveDocumentRequirementSettings(): void
    {
        if (! $this->managementPeriodId) {
            $this->dispatch('toast', type: 'error', message: 'Belum ada periode aktif untuk persyaratan berkas.');
            return;
        }

        $validated = $this->validate([
            'documentRequirementSettings' => 'required|array|min:1',
            'documentRequirementSettings.*.id' => 'nullable|integer',
            'documentRequirementSettings.*.nama_berkas' => 'required|string|max:150',
            'documentRequirementSettings.*.keterangan' => 'nullable|string|max:255',
            'documentRequirementSettings.*.wajib' => 'boolean',
            'documentRequirementSettings.*.is_active' => 'boolean',
        ]);

        $rows = collect($validated['documentRequirementSettings'])
            ->values()
            ->map(function (array $row): array {
                return [
                    'id' => isset($row['id']) && $row['id'] ? (int) $row['id'] : null,
                    'nama_berkas' => trim((string) $row['nama_berkas']),
                    'keterangan' => trim((string) ($row['keterangan'] ?? '')) ?: null,
                    'wajib' => (bool) ($row['wajib'] ?? true),
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ];
            });

        $period = PpdbPeriod::findOrFail($this->managementPeriodId);
        $existingIds = $rows->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();

        if ($existingIds === []) {
            $period->documentRequirements()->delete();
        } else {
            $period->documentRequirements()->whereNotIn('id', $existingIds)->delete();
        }

        foreach ($rows as $index => $row) {
            $item = $row['id']
                ? $period->documentRequirements()->whereKey($row['id'])->first()
                : null;

            if (! $item) {
                $item = new PpdbDocumentRequirement();
            }

            $item->fill([
                'period_id' => $period->id,
                'nama_berkas' => $row['nama_berkas'],
                'keterangan' => $row['keterangan'],
                'wajib' => $row['wajib'],
                'is_active' => $row['is_active'],
                'urutan' => $index + 1,
            ]);
            $item->save();
        }

        $this->refreshSelectedPeriod();
        $this->dispatch('toast', type: 'success', message: 'Persyaratan berkas PPDB berhasil diperbarui.');
    }

    public function saveMapColorSettings(): void
    {
        if (! $this->managementPeriodId) {
            $this->dispatch('toast', type: 'error', message: 'Belum ada periode aktif untuk warna map jurusan.');
            return;
        }

        $validated = $this->validate([
            'mapColorSettings' => 'required|array|min:1',
            'mapColorSettings.*.program_keahlian_id' => ['required', 'integer', Rule::exists('program_keahlian', 'id')],
            'mapColorSettings.*.warna_map' => 'required|string|max:50',
            'mapColorSettings.*.keterangan' => 'nullable|string|max:255',
            'mapColorSettings.*.is_active' => 'boolean',
        ]);

        $rows = collect($validated['mapColorSettings'])
            ->values()
            ->map(function (array $row): array {
                return [
                    'program_keahlian_id' => (int) $row['program_keahlian_id'],
                    'warna_map' => trim((string) $row['warna_map']),
                    'keterangan' => trim((string) ($row['keterangan'] ?? '')) ?: null,
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ];
            });

        if ($rows->pluck('program_keahlian_id')->duplicates()->isNotEmpty()) {
            $this->dispatch('toast', type: 'error', message: 'Terdapat duplikasi program keahlian pada aturan warna map.');
            return;
        }

        $period = PpdbPeriod::findOrFail($this->managementPeriodId);
        $programIds = $rows->pluck('program_keahlian_id')->all();

        $period->mapColorRules()->whereNotIn('program_keahlian_id', $programIds)->delete();

        foreach ($rows as $index => $row) {
            $rule = $period->mapColorRules()
                ->where('program_keahlian_id', $row['program_keahlian_id'])
                ->first();

            if (! $rule) {
                $rule = new PpdbMapColorRule();
            }

            $rule->fill([
                'period_id' => $period->id,
                'program_keahlian_id' => $row['program_keahlian_id'],
                'warna_map' => $row['warna_map'],
                'keterangan' => $row['keterangan'],
                'is_active' => $row['is_active'],
                'urutan' => $index + 1,
            ]);
            $rule->save();
        }

        $this->refreshSelectedPeriod();
        $this->dispatch('toast', type: 'success', message: 'Pengaturan warna map jurusan berhasil diperbarui.');
    }

    public function addContactPersonRow(): void
    {
        $this->contactPersonSettings[] = $this->defaultContactPersonRow(false);
    }

    public function setPrimaryContactPerson(int $index): void
    {
        foreach ($this->contactPersonSettings as $rowIndex => $row) {
            $this->contactPersonSettings[$rowIndex]['is_primary'] = $rowIndex === $index;
        }
    }

    public function removeContactPersonRow(int $index): void
    {
        if (count($this->contactPersonSettings) <= 1) {
            $this->dispatch('toast', type: 'error', message: 'Minimal harus ada satu kontak person.');
            return;
        }

        unset($this->contactPersonSettings[$index]);
        $this->contactPersonSettings = array_values($this->contactPersonSettings);

        $hasPrimary = collect($this->contactPersonSettings)->contains(fn (array $row) => (bool) ($row['is_primary'] ?? false));

        if (! $hasPrimary && isset($this->contactPersonSettings[0])) {
            $this->contactPersonSettings[0]['is_primary'] = true;
        }
    }

    public function addImportantDateRow(): void
    {
        $this->importantDateSettings[] = [
            'id' => null,
            'label' => '',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => null,
            'keterangan' => '',
            'is_active' => true,
        ];
    }

    public function removeImportantDateRow(int $index): void
    {
        if (count($this->importantDateSettings) <= 1) {
            $this->dispatch('toast', type: 'error', message: 'Minimal harus ada satu tanggal penting.');
            return;
        }

        unset($this->importantDateSettings[$index]);
        $this->importantDateSettings = array_values($this->importantDateSettings);
    }

    public function addDocumentRequirementRow(): void
    {
        $this->documentRequirementSettings[] = [
            'id' => null,
            'nama_berkas' => '',
            'keterangan' => '',
            'wajib' => true,
            'is_active' => true,
        ];
    }

    public function removeDocumentRequirementRow(int $index): void
    {
        if (count($this->documentRequirementSettings) <= 1) {
            $this->dispatch('toast', type: 'error', message: 'Minimal harus ada satu persyaratan berkas.');
            return;
        }

        unset($this->documentRequirementSettings[$index]);
        $this->documentRequirementSettings = array_values($this->documentRequirementSettings);
    }

    public function createPeriod(): void
    {
        $validated = $this->validate([
            'newPeriodForm.nama_periode' => 'required|string|max:255',
            'newPeriodForm.tahun_ajaran' => 'required|string|max:50',
            'newPeriodForm.tahun_mulai' => 'required|integer|min:2020|max:2100',
            'newPeriodForm.tahun_selesai' => 'required|integer|gte:newPeriodForm.tahun_mulai',
            'newPeriodForm.gelombang_ke' => 'required|integer|min:1|max:20',
            'newPeriodForm.gelombang_label' => 'required|string|max:100',
            'newPeriodForm.tanggal_mulai_pendaftaran' => 'required|date',
            'newPeriodForm.tanggal_selesai_pendaftaran' => 'required|date|after_or_equal:newPeriodForm.tanggal_mulai_pendaftaran',
            'newPeriodForm.tanggal_pengumuman' => 'nullable|date|after_or_equal:newPeriodForm.tanggal_selesai_pendaftaran',
            'newPeriodForm.tanggal_mulai_daftar_ulang' => 'nullable|date|after_or_equal:newPeriodForm.tanggal_pengumuman',
            'newPeriodForm.tanggal_selesai_daftar_ulang' => 'nullable|date|after_or_equal:newPeriodForm.tanggal_mulai_daftar_ulang',
            'newPeriodForm.status' => 'required|in:draft,published,closed,archived',
            'newPeriodForm.is_active' => 'boolean',
            'newPeriodForm.clone_template' => 'boolean',
            'newPeriodForm.deskripsi' => 'nullable|string',
        ]);

        if (($validated['newPeriodForm']['is_active'] ?? false) && $validated['newPeriodForm']['status'] === 'draft') {
            $this->addError('newPeriodForm.status', 'Periode draft tidak dapat dijadikan aktif default saat dibuat.');
            $this->dispatch('toast', type: 'error', message: 'Periode draft tidak dapat dijadikan aktif default saat dibuat.');
            return;
        }

        $payload = $validated['newPeriodForm'];
        $payload['is_active'] = (bool) ($payload['is_active'] ?? false);
        $payload['status_pengumuman'] = 'draft';
        $payload['hasil_diumumkan_at'] = null;
        $payload['catatan_pengumuman'] = null;

        if ($payload['is_active']) {
            PpdbPeriod::query()->update(['is_active' => false]);
        }

        $newPeriod = PpdbPeriod::create($payload);

        if (($validated['newPeriodForm']['clone_template'] ?? false) && $this->managementPeriodId) {
            $templatePeriod = PpdbPeriod::with([
                'tracks',
                'quotas',
                'contactPersons',
                'importantDates',
                'documentRequirements',
                'mapColorRules',
            ])->find($this->managementPeriodId);

            if ($templatePeriod) {
                $trackMap = [];

                foreach ($templatePeriod->tracks as $track) {
                    $replica = $track->replicate();
                    $replica->period_id = $newPeriod->id;
                    $replica->save();

                    $trackMap[$track->id] = $replica->id;
                }

                foreach ($templatePeriod->quotas as $quota) {
                    $replica = $quota->replicate();
                    $replica->period_id = $newPeriod->id;
                    $replica->track_id = $quota->track_id ? ($trackMap[$quota->track_id] ?? null) : null;
                    $replica->kuota_terisi = 0;
                    $replica->save();
                }

                foreach ($templatePeriod->contactPersons as $contactPerson) {
                    $replica = $contactPerson->replicate();
                    $replica->period_id = $newPeriod->id;
                    $replica->save();
                }

                foreach ($templatePeriod->importantDates as $importantDate) {
                    $replica = $importantDate->replicate();
                    $replica->period_id = $newPeriod->id;
                    $replica->save();
                }

                foreach ($templatePeriod->documentRequirements as $requirement) {
                    $replica = $requirement->replicate();
                    $replica->period_id = $newPeriod->id;
                    $replica->save();
                }

                foreach ($templatePeriod->mapColorRules as $rule) {
                    $replica = $rule->replicate();
                    $replica->period_id = $newPeriod->id;
                    $replica->save();
                }
            }
        }

        $this->period = (string) $newPeriod->id;
        $this->syncManagementForms($newPeriod->fresh($this->periodRelations()));
        $this->resetNewPeriodForm($newPeriod);

        $this->dispatch('toast', type: 'success', message: 'Periode PPDB baru berhasil dibuat.');
    }

    public function activateSelectedPeriod(): void
    {
        if (! $this->managementPeriodId) {
            $this->dispatch('toast', type: 'error', message: 'Belum ada periode yang dipilih untuk diaktifkan.');
            return;
        }

        $period = PpdbPeriod::findOrFail($this->managementPeriodId);

        if ($period->status === 'draft') {
            $this->dispatch('toast', type: 'error', message: 'Periode draft tidak dapat dijadikan aktif default.');
            return;
        }

        PpdbPeriod::query()->update(['is_active' => false]);
        $period->update(['is_active' => true]);

        $this->refreshSelectedPeriod();

        $this->dispatch('toast', type: 'success', message: 'Periode terpilih sekarang menjadi periode aktif default.');
    }

    public function archiveSelectedPeriod(): void
    {
        if (! $this->managementPeriodId) {
            $this->dispatch('toast', type: 'error', message: 'Belum ada periode yang dipilih untuk diarsipkan.');
            return;
        }

        $period = PpdbPeriod::findOrFail($this->managementPeriodId);

        if ($period->status === 'archived' && ! $period->is_active) {
            $this->dispatch('toast', type: 'info', message: 'Periode ini sudah diarsipkan dan disembunyikan dari frontend.');
            return;
        }

        if ($period->is_active) {
            $replacement = PpdbPeriod::query()
                ->whereKeyNot($period->id)
                ->whereIn('status', ['published', 'closed'])
                ->orderForSelection()
                ->first();

            if ($replacement) {
                PpdbPeriod::query()->whereKeyNot($replacement->id)->update(['is_active' => false]);
                $replacement->update(['is_active' => true]);
            }
        }

        $period->update([
            'status' => 'archived',
            'is_active' => false,
        ]);

        $this->refreshSelectedPeriod();

        $this->dispatch('toast', type: 'success', message: 'Periode berhasil diarsipkan dan tidak ditampilkan di frontend.');
    }

    public function deleteSelectedPeriod(): void
    {
        if (! $this->managementPeriodId) {
            $this->dispatch('toast', type: 'error', message: 'Belum ada periode yang dipilih untuk dihapus.');
            return;
        }

        $period = PpdbPeriod::withCount('applications')->findOrFail($this->managementPeriodId);

        if ($period->is_active) {
            $this->dispatch('toast', type: 'error', message: 'Periode aktif tidak dapat dihapus. Aktifkan periode lain terlebih dahulu.');
            return;
        }

        if ($period->applications_count > 0) {
            $this->dispatch('toast', type: 'error', message: 'Periode yang sudah memiliki pendaftar tidak bisa dihapus. Gunakan arsipkan untuk menyembunyikan dari frontend.');
            return;
        }

        $deletedLabel = $period->full_label;
        $period->delete();

        $nextPeriod = $this->resolveSelectedPeriod();
        $this->period = $nextPeriod ? (string) $nextPeriod->id : '';
        $this->syncManagementForms($nextPeriod);
        $this->resetNewPeriodForm($nextPeriod);

        $this->dispatch('toast', type: 'success', message: 'Periode ' . $deletedLabel . ' berhasil dihapus permanen.');
    }

    public function render()
    {
        $activePeriod = $this->resolveSelectedPeriod();
        $availablePeriods = app(PpdbPeriodResolver::class)->adminOptions();
        $selectedPeriodId = $activePeriod?->id;

        $quotaOverview = $activePeriod
            ? $activePeriod->quotas->where('status_aktif', true)->values()
            : collect();

        $tabOptions = $this->buildTabOptions($activePeriod);

        return view('livewire.admin.ppdb-v2.pengaturan', compact(
            'activePeriod',
            'quotaOverview',
            'availablePeriods',
            'selectedPeriodId',
            'tabOptions'
        ))->layout('components.layouts.admin', ['title' => 'Pengaturan PPDB']);
    }

    protected function ensureTabIsValid(): void
    {
        if (! in_array($this->tab, self::TAB_OPTIONS, true)) {
            $this->tab = 'periode';
        }
    }

    protected function resolveSelectedPeriod(): ?PpdbPeriod
    {
        $resolver = app(PpdbPeriodResolver::class);

        return $resolver->resolveAdmin(
            $resolver->resolveInput($this->period),
            $this->periodRelations()
        );
    }

    protected function refreshSelectedPeriod(): void
    {
        $period = $this->managementPeriodId
            ? PpdbPeriod::with($this->periodRelations())->find($this->managementPeriodId)
            : $this->resolveSelectedPeriod();

        if ($period) {
            $this->period = (string) $period->id;
        }

        $this->syncManagementForms($period);
    }

    protected function periodRelations(): array
    {
        return [
            'tracks',
            'quotas.track',
            'quotas.programKeahlian',
            'contactPersons',
            'importantDates',
            'documentRequirements',
            'mapColorRules.programKeahlian',
        ];
    }

    protected function syncManagementForms(?PpdbPeriod $period): void
    {
        if (! $period) {
            $this->managementPeriodId = null;
            $this->periodForm = [];
            $this->trackSettings = [];
            $this->quotaSettings = [];
            $this->contactPersonSettings = [];
            $this->importantDateSettings = [];
            $this->documentRequirementSettings = [];
            $this->mapColorSettings = [];

            return;
        }

        $this->managementPeriodId = $period->id;
        $this->periodForm = [
            'nama_periode' => $period->nama_periode,
            'tahun_ajaran' => $period->tahun_ajaran,
            'tahun_mulai' => $period->tahun_mulai,
            'tahun_selesai' => $period->tahun_selesai,
            'gelombang_ke' => $period->gelombang_ke,
            'gelombang_label' => $period->gelombang_label,
            'tanggal_mulai_pendaftaran' => $period->tanggal_mulai_pendaftaran?->format('Y-m-d') ?? '',
            'tanggal_selesai_pendaftaran' => $period->tanggal_selesai_pendaftaran?->format('Y-m-d') ?? '',
            'tanggal_pengumuman' => $period->tanggal_pengumuman?->format('Y-m-d') ?? '',
            'tanggal_mulai_daftar_ulang' => $period->tanggal_mulai_daftar_ulang?->format('Y-m-d') ?? '',
            'tanggal_selesai_daftar_ulang' => $period->tanggal_selesai_daftar_ulang?->format('Y-m-d') ?? '',
            'status' => $period->status,
            'is_active' => (bool) $period->is_active,
            'status_pengumuman' => $period->status_pengumuman ?? 'draft',
            'catatan_pengumuman' => $period->catatan_pengumuman ?? '',
            'deskripsi' => $period->deskripsi ?? '',
        ];

        $this->trackSettings = $period->tracks
            ->mapWithKeys(fn (PpdbTrack $track) => [
                $track->id => [
                    'status_tampil' => (bool) $track->status_tampil,
                    'requires_verification' => (bool) $track->requires_verification,
                    'urutan' => (int) $track->urutan,
                ],
            ])
            ->toArray();

        $this->quotaSettings = $period->quotas
            ->mapWithKeys(fn (PpdbQuota $quota) => [
                $quota->id => [
                    'kuota' => (int) $quota->kuota,
                    'status_aktif' => (bool) $quota->status_aktif,
                ],
            ])
            ->toArray();

        $this->contactPersonSettings = $period->contactPersons
            ->sortBy('urutan')
            ->values()
            ->map(fn (PpdbContactPerson $contact) => [
                'id' => $contact->id,
                'nama' => $contact->nama,
                'jabatan' => $contact->jabatan ?? '',
                'nomor_telepon' => $contact->nomor_telepon ?? '',
                'nomor_whatsapp' => $contact->nomor_whatsapp ?? '',
                'email' => $contact->email ?? '',
                'is_primary' => (bool) $contact->is_primary,
                'is_active' => (bool) $contact->is_active,
            ])
            ->toArray();

        if ($this->contactPersonSettings === []) {
            $this->contactPersonSettings = [$this->defaultContactPersonRow(true)];
        }

        $this->importantDateSettings = $period->importantDates
            ->sortBy('urutan')
            ->values()
            ->map(fn (PpdbImportantDate $item) => [
                'id' => $item->id,
                'label' => $item->label,
                'tanggal_mulai' => $item->tanggal_mulai?->format('Y-m-d') ?? '',
                'tanggal_selesai' => $item->tanggal_selesai?->format('Y-m-d') ?? '',
                'keterangan' => $item->keterangan ?? '',
                'is_active' => (bool) $item->is_active,
            ])
            ->toArray();

        if ($this->importantDateSettings === []) {
            $this->importantDateSettings = $this->fallbackImportantDateSettings($period);
        }

        $this->documentRequirementSettings = $period->documentRequirements
            ->sortBy('urutan')
            ->values()
            ->map(fn (PpdbDocumentRequirement $item) => [
                'id' => $item->id,
                'nama_berkas' => $item->nama_berkas,
                'keterangan' => $item->keterangan ?? '',
                'wajib' => (bool) $item->wajib,
                'is_active' => (bool) $item->is_active,
            ])
            ->toArray();

        if ($this->documentRequirementSettings === []) {
            $this->documentRequirementSettings = $this->fallbackDocumentRequirementSettings();
        }

        $rulesByProgram = $period->mapColorRules->keyBy('program_keahlian_id');
        $programs = ProgramKeahlian::query()->tampil()->orderBy('nama_jurusan')->get(['id', 'nama_jurusan']);

        if ($programs->isEmpty()) {
            $programs = ProgramKeahlian::query()->orderBy('nama_jurusan')->get(['id', 'nama_jurusan']);
        }

        $this->mapColorSettings = $programs
            ->values()
            ->map(function (ProgramKeahlian $program, int $index) use ($rulesByProgram): array {
                /** @var PpdbMapColorRule|null $rule */
                $rule = $rulesByProgram->get($program->id);

                return [
                    'program_keahlian_id' => $program->id,
                    'nama_jurusan' => $program->nama_jurusan,
                    'warna_map' => $rule?->warna_map ?? $this->defaultMapColor($index),
                    'keterangan' => $rule?->keterangan ?? '',
                    'is_active' => $rule ? (bool) $rule->is_active : true,
                ];
            })
            ->toArray();
    }

    protected function resetNewPeriodForm(?PpdbPeriod $period = null): void
    {
        $tahunMulai = $period?->tahun_mulai ?? (int) now()->format('Y');
        $tahunSelesai = $period?->tahun_selesai ?? ($tahunMulai + 1);
        $gelombangKe = $period?->gelombang_ke ?? 1;

        $this->newPeriodForm = [
            'nama_periode' => 'PPDB ' . ($period?->gelombang_label ?? 'Gelombang 1') . ' ' . $tahunMulai . '/' . $tahunSelesai,
            'tahun_ajaran' => $tahunMulai . '/' . $tahunSelesai,
            'tahun_mulai' => $tahunMulai,
            'tahun_selesai' => $tahunSelesai,
            'gelombang_ke' => $gelombangKe,
            'gelombang_label' => $period?->gelombang_label ?? 'Gelombang 1',
            'tanggal_mulai_pendaftaran' => $period?->tanggal_mulai_pendaftaran?->format('Y-m-d') ?? now()->toDateString(),
            'tanggal_selesai_pendaftaran' => $period?->tanggal_selesai_pendaftaran?->format('Y-m-d') ?? now()->addMonth()->toDateString(),
            'tanggal_pengumuman' => $period?->tanggal_pengumuman?->format('Y-m-d') ?? now()->addMonths(2)->toDateString(),
            'tanggal_mulai_daftar_ulang' => $period?->tanggal_mulai_daftar_ulang?->format('Y-m-d') ?? now()->addMonths(2)->addDay()->toDateString(),
            'tanggal_selesai_daftar_ulang' => $period?->tanggal_selesai_daftar_ulang?->format('Y-m-d') ?? now()->addMonths(2)->addWeek()->toDateString(),
            'status' => 'draft',
            'is_active' => false,
            'clone_template' => true,
            'deskripsi' => $period?->deskripsi ?? '',
        ];
    }

    protected function resolveActionModalConfig(string $action): ?array
    {
        return match ($action) {
            'activate-period' => [
                'title' => 'Jadikan Periode Aktif Default?',
                'message' => 'Periode terpilih akan menjadi konteks default admin dan frontend ketika parameter periode tidak dikirim.',
                'confirm_label' => 'Ya, Jadikan Aktif',
                'tone' => 'primary',
            ],
            'archive-period' => [
                'title' => 'Arsipkan Periode Ini?',
                'message' => 'Periode akan disembunyikan dari frontend. Data pendaftar tetap aman dan bisa ditinjau di admin.',
                'confirm_label' => 'Ya, Arsipkan',
                'tone' => 'warning',
            ],
            'delete-period' => [
                'title' => 'Hapus Periode Secara Permanen?',
                'message' => 'Aksi ini tidak bisa dibatalkan. Penghapusan hanya berhasil jika periode tidak aktif dan belum memiliki pendaftar.',
                'confirm_label' => 'Ya, Hapus Permanen',
                'tone' => 'danger',
            ],
            'create-period' => [
                'title' => 'Buat Periode Baru?',
                'message' => 'Sistem akan membuat periode baru sesuai data formulir yang sudah diisi.',
                'confirm_label' => 'Ya, Buat Periode',
                'tone' => 'primary',
            ],
            'save-period-settings' => [
                'title' => 'Simpan Pengaturan Periode?',
                'message' => 'Perubahan jadwal, status, dan pengumuman akan langsung mempengaruhi perilaku portal frontend.',
                'confirm_label' => 'Ya, Simpan Periode',
                'tone' => 'primary',
            ],
            'save-track-settings' => [
                'title' => 'Simpan Pengaturan Jalur?',
                'message' => 'Perubahan status tampil, verifikasi, dan urutan jalur akan langsung diterapkan pada portal frontend.',
                'confirm_label' => 'Ya, Simpan Jalur',
                'tone' => 'primary',
            ],
            'save-quota-settings' => [
                'title' => 'Simpan Pengaturan Kuota?',
                'message' => 'Kuota dan status aktif kombinasi jalur-jurusan akan diperbarui sesuai input saat ini.',
                'confirm_label' => 'Ya, Simpan Kuota',
                'tone' => 'primary',
            ],
            'save-contact-settings' => [
                'title' => 'Simpan Kontak Person?',
                'message' => 'Daftar kontak person ini dipakai pada lampiran informasi resmi dan komunikasi bantuan PPDB.',
                'confirm_label' => 'Ya, Simpan Kontak',
                'tone' => 'primary',
            ],
            'save-important-date-settings' => [
                'title' => 'Simpan Tanggal Penting?',
                'message' => 'Tanggal penting akan dipakai sebagai referensi resmi jadwal pada dokumen dan informasi publik PPDB.',
                'confirm_label' => 'Ya, Simpan Tanggal',
                'tone' => 'primary',
            ],
            'save-document-requirement-settings' => [
                'title' => 'Simpan Persyaratan Berkas?',
                'message' => 'Daftar persyaratan berkas akan ditampilkan sebagai panduan resmi saat verifikasi dan daftar ulang.',
                'confirm_label' => 'Ya, Simpan Persyaratan',
                'tone' => 'primary',
            ],
            'save-map-color-settings' => [
                'title' => 'Simpan Warna Map Jurusan?',
                'message' => 'Aturan warna map per jurusan akan dipakai sebagai acuan resmi yang ditampilkan pada lampiran formulir.',
                'confirm_label' => 'Ya, Simpan Warna Map',
                'tone' => 'primary',
            ],
            default => null,
        };
    }

    protected function buildTabOptions(?PpdbPeriod $period): array
    {
        return [
            [
                'key' => 'periode',
                'label' => 'Periode',
                'description' => 'Master periode dan publikasi',
                'count' => null,
            ],
            [
                'key' => 'jalur',
                'label' => 'Jalur',
                'description' => 'Urutan dan visibilitas jalur',
                'count' => $period?->tracks?->count() ?? 0,
            ],
            [
                'key' => 'kuota',
                'label' => 'Kuota',
                'description' => 'Kuota aktif per jurusan',
                'count' => $period?->quotas?->count() ?? 0,
            ],
            [
                'key' => 'kontak',
                'label' => 'Kontak Person',
                'description' => 'PIC bantuan PPDB',
                'count' => count($this->contactPersonSettings),
            ],
            [
                'key' => 'tanggal',
                'label' => 'Tanggal Penting',
                'description' => 'Timeline resmi per periode',
                'count' => count($this->importantDateSettings),
            ],
            [
                'key' => 'persyaratan',
                'label' => 'Persyaratan Berkas',
                'description' => 'Dokumen yang wajib dikumpulkan',
                'count' => count($this->documentRequirementSettings),
            ],
            [
                'key' => 'warna-map',
                'label' => 'Warna Map Jurusan',
                'description' => 'Aturan map per program',
                'count' => count($this->mapColorSettings),
            ],
        ];
    }

    protected function defaultMapColor(int $index): string
    {
        return self::MAP_COLOR_PALETTE[$index % count(self::MAP_COLOR_PALETTE)];
    }

    protected function defaultContactPersonRow(bool $isPrimary = false): array
    {
        $schoolPhone = trim((string) (ProfilSekolah::query()->value('nomor_telepon') ?? ''));
        $adminEmail = trim((string) config('services.ppdb_contact.admin_email', ''));
        $adminWhatsapp = $this->normalizeWhatsapp((string) config('services.ppdb_contact.admin_whatsapp', ''));

        return [
            'id' => null,
            'nama' => 'Panitia PPDB',
            'jabatan' => 'Helpdesk PPDB',
            'nomor_telepon' => $schoolPhone,
            'nomor_whatsapp' => $adminWhatsapp ?? '',
            'email' => $adminEmail,
            'is_primary' => $isPrimary,
            'is_active' => true,
        ];
    }

    protected function fallbackImportantDateSettings(PpdbPeriod $period): array
    {
        return [
            [
                'id' => null,
                'label' => 'Pendaftaran dibuka',
                'tanggal_mulai' => $period->tanggal_mulai_pendaftaran?->format('Y-m-d') ?? now()->toDateString(),
                'tanggal_selesai' => $period->tanggal_selesai_pendaftaran?->format('Y-m-d') ?? null,
                'keterangan' => '',
                'is_active' => true,
            ],
            [
                'id' => null,
                'label' => 'Pengumuman hasil',
                'tanggal_mulai' => $period->tanggal_pengumuman?->format('Y-m-d') ?? now()->addWeek()->toDateString(),
                'tanggal_selesai' => null,
                'keterangan' => '',
                'is_active' => true,
            ],
            [
                'id' => null,
                'label' => 'Daftar ulang',
                'tanggal_mulai' => $period->tanggal_mulai_daftar_ulang?->format('Y-m-d') ?? now()->addWeeks(2)->toDateString(),
                'tanggal_selesai' => $period->tanggal_selesai_daftar_ulang?->format('Y-m-d') ?? null,
                'keterangan' => '',
                'is_active' => true,
            ],
        ];
    }

    protected function fallbackDocumentRequirementSettings(): array
    {
        return [
            [
                'id' => null,
                'nama_berkas' => 'Formulir pendaftaran cetak asli yang sudah ditandatangani',
                'keterangan' => '',
                'wajib' => true,
                'is_active' => true,
            ],
            [
                'id' => null,
                'nama_berkas' => 'Fotokopi Kartu Keluarga',
                'keterangan' => '1 lembar',
                'wajib' => true,
                'is_active' => true,
            ],
            [
                'id' => null,
                'nama_berkas' => 'Fotokopi Akta Kelahiran',
                'keterangan' => '1 lembar',
                'wajib' => true,
                'is_active' => true,
            ],
            [
                'id' => null,
                'nama_berkas' => 'Fotokopi rapor semester 1 s.d. 5',
                'keterangan' => 'Legalisir bila diperlukan',
                'wajib' => true,
                'is_active' => true,
            ],
            [
                'id' => null,
                'nama_berkas' => 'Fotokopi SKL atau Ijazah',
                'keterangan' => 'Sesuai ketersediaan saat daftar ulang',
                'wajib' => true,
                'is_active' => true,
            ],
            [
                'id' => null,
                'nama_berkas' => 'Pas foto 3x4 latar biru',
                'keterangan' => '2 lembar',
                'wajib' => true,
                'is_active' => true,
            ],
        ];
    }

    protected function normalizeWhatsapp(string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', $value) ?: '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }
}
