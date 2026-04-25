<?php

namespace App\Livewire\Admin\PpdbV2;

use App\Models\PpdbApplication;
use App\Models\PpdbPeriod;
use App\Models\ProgramKeahlian;
use App\Support\PpdbNipdAllocator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class DaftarUlang extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $programFilter = '';
    public array $selectedIds = [];
    public bool $selectPage = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingProgramFilter(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatedSelectPage(bool $value): void
    {
        if ($value) {
            $pageItems = $this->getFilteredQuery($this->getActivePeriod())
                ->where('status_daftar_ulang', 'submitted')
                ->orderByRaw("CASE WHEN status_daftar_ulang = 'submitted' THEN 0 WHEN status_daftar_ulang = 'rejected' THEN 1 WHEN status_daftar_ulang = 'pending' THEN 2 WHEN status_daftar_ulang = 'not_available' THEN 3 WHEN status_daftar_ulang = 'verified' THEN 4 ELSE 5 END")
                ->orderByDesc('daftar_ulang_at')
                ->orderBy('nama_lengkap')
                ->paginate(15, ['*'], 'page', $this->getPage())
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all();

            $this->selectedIds = $pageItems;

            return;
        }

        $this->selectedIds = [];
    }

    public function toggleStatus($studentId)
    {
        $student = PpdbApplication::find($studentId);

        if (! $student) {
            return;
        }

        $isAlreadyVerified = $student->status_daftar_ulang === 'verified';
        $hadNipd = (bool) $student->nipd;

        $student->update([
            'status_daftar_ulang' => $isAlreadyVerified ? 'pending' : 'verified',
            'daftar_ulang_at' => $isAlreadyVerified ? null : now(),
            'verified_daftar_ulang_by' => $isAlreadyVerified ? null : auth()->id(),
            'verified_daftar_ulang_at' => $isAlreadyVerified ? null : now(),
        ]);

        $nipdMessage = '';

        if (! $isAlreadyVerified) {
            $assignedNipd = app(PpdbNipdAllocator::class)->assignIfEligible($student);

            if (! $hadNipd && $assignedNipd) {
                $nipdMessage = ' NIPD ' . $assignedNipd . ' ditetapkan otomatis.';
            }
        }

        session()->flash('message', 'Status daftar ulang atas nama ' . $student->nama_lengkap . ' ' . ($isAlreadyVerified ? 'dibatalkan.' : 'telah diselesaikan.') . $nipdMessage);
    }

    public function bulkVerifySelected(): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        $selectedCount = count($this->selectedIds);

        $students = PpdbApplication::query()
            ->whereIn('id', $this->selectedIds)
            ->where('status_daftar_ulang', 'submitted')
            ->get();

        $processedCount = 0;

        foreach ($students as $student) {
            if ($student->status_daftar_ulang === 'verified') {
                continue;
            }

            $student->update([
                'status_daftar_ulang' => 'verified',
                'daftar_ulang_at' => $student->daftar_ulang_at ?? now(),
                'verified_daftar_ulang_by' => auth()->id(),
                'verified_daftar_ulang_at' => now(),
            ]);

            app(PpdbNipdAllocator::class)->assignIfEligible($student);
            $processedCount++;
        }

        $this->resetSelection();

        $skippedCount = $selectedCount - $processedCount;
        $message = $processedCount . ' siswa berhasil diverifikasi massal pada daftar ulang.';

        if ($skippedCount > 0) {
            $message .= ' ' . $skippedCount . ' siswa dilewati karena statusnya bukan Menunggu Verifikasi.';
        }

        session()->flash('message', $message);
    }

    protected function getActivePeriod(): ?PpdbPeriod
    {
        return PpdbPeriod::query()->where('is_active', true)->first();
    }

    protected function resetSelection(): void
    {
        $this->selectedIds = [];
        $this->selectPage = false;
    }

    protected function getFilteredQuery(?PpdbPeriod $period): Builder
    {
        $query = PpdbApplication::query()
            ->with(['programDiterima', 'track'])
            ->whereIn('hasil_seleksi', ['passed', 'lulus'])
            ->whereNotNull('program_diterima_id');

        if ($period) {
            $query->where('period_id', $period->id);
        }

        if ($this->search !== '') {
            $query->where(function (Builder $q): void {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%')
                    ->orWhere('nipd', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->programFilter !== '' && is_numeric($this->programFilter)) {
            $query->where('program_diterima_id', (int) $this->programFilter);
        }

        if ($this->statusFilter === 'submitted') {
            $query->where('status_daftar_ulang', 'submitted');
        } elseif ($this->statusFilter === 'verified') {
            $query->where('status_daftar_ulang', 'verified');
        } elseif ($this->statusFilter === 'pending') {
            $query->where(function (Builder $q): void {
                $q->whereNull('status_daftar_ulang')
                    ->orWhereIn('status_daftar_ulang', ['not_available', 'pending']);
            });
        } elseif ($this->statusFilter === 'rejected') {
            $query->where('status_daftar_ulang', 'rejected');
        } elseif ($this->statusFilter === 'unfinished') {
            $query->where(function (Builder $q): void {
                $q->whereNull('status_daftar_ulang')
                    ->orWhereIn('status_daftar_ulang', ['not_available', 'pending', 'submitted', 'rejected']);
            });
        }

        return $query;
    }

    public function render()
    {
        $period = $this->getActivePeriod();
        $query = $this->getFilteredQuery($period);
        $pendaftarLulus = (clone $query)
            ->orderByRaw("CASE WHEN status_daftar_ulang = 'submitted' THEN 0 WHEN status_daftar_ulang = 'rejected' THEN 1 WHEN status_daftar_ulang = 'pending' THEN 2 WHEN status_daftar_ulang = 'not_available' THEN 3 WHEN status_daftar_ulang = 'verified' THEN 4 ELSE 5 END")
            ->orderByDesc('daftar_ulang_at')
            ->orderBy('nama_lengkap')
            ->paginate(15);

        $programFilterOptions = ProgramKeahlian::query()
            ->tampil()
            ->orderBy('nama_jurusan')
            ->get(['id', 'nama_jurusan']);

        $countBaseQuery = PpdbApplication::query()
            ->when($period, fn (Builder $q) => $q->where('period_id', $period->id))
            ->whereIn('hasil_seleksi', ['passed', 'lulus'])
            ->whereNotNull('program_diterima_id');

        return view('livewire.admin.ppdb-v2.daftar-ulang', [
            'pendaftar' => $pendaftarLulus,
            'period' => $period,
            'programFilterOptions' => $programFilterOptions,
            'countSelesai' => (clone $countBaseQuery)->where('status_daftar_ulang', 'verified')->count(),
            'countSubmitted' => (clone $countBaseQuery)->where('status_daftar_ulang', 'submitted')->count(),
            'countPending' => (clone $countBaseQuery)->whereIn('status_daftar_ulang', ['pending', 'not_available'])->count(),
            'countTotal' => (clone $countBaseQuery)->count(),
        ])->layout('components.layouts.admin', ['title' => 'Verifikasi Daftar Ulang']);
    }
}
