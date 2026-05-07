<?php

namespace App\Livewire\Admin\PpdbV2;

use App\Models\PpdbApplication;
use App\Models\PpdbPeriod;
use App\Models\ProgramKeahlian;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Reports extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $programFilter = '';
    public $trackFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingProgramFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTrackFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->programFilter = '';
        $this->trackFilter = '';
        $this->resetPage();
    }

    public function exportAssessmentExcel()
    {
        $query = array_filter([
            'format' => 'xlsx',
            'type' => 'assessment',
            'search' => $this->search,
            'track_id' => $this->trackFilter,
            'program_id' => $this->programFilter,
            'status' => $this->statusFilter,
        ], fn ($value) => filled($value));

        return $this->redirect(route('admin.ppdb.export', $query), navigate: false);
    }

    public function exportApplicantDataExcel()
    {
        $query = array_filter([
            'format' => 'xlsx',
            'scope' => 'applicant-data',
            'search' => $this->search,
            'track_id' => $this->trackFilter,
            'program_id' => $this->programFilter,
        ], fn ($value) => filled($value));

        return $this->redirect(route('admin.ppdb.export', $query), navigate: false);
    }

    public function exportApplicantDataPdf()
    {
        $query = array_filter([
            'format' => 'pdf',
            'scope' => 're-registration',
            'search' => $this->search,
            'track_id' => $this->trackFilter,
            'program_id' => $this->programFilter,
        ], fn ($value) => filled($value));

        return $this->redirect(route('admin.ppdb.export', $query), navigate: false);
    }

    protected function getActivePeriod(): ?PpdbPeriod
    {
        return PpdbPeriod::query()->where('is_active', true)->first();
    }

    protected function getFilteredQuery(?PpdbPeriod $period): Builder
    {
        $query = PpdbApplication::query()
            ->with(['programDiterima', 'track', 'pilihanProgram1'])
            ->whereIn('hasil_seleksi', ['passed', 'lulus']);

        if ($period) {
            $query->where('period_id', $period->id);
        }

        if ($this->search !== '') {
            $query->where(function (Builder $q): void {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->programFilter !== '' && is_numeric($this->programFilter)) {
            $query->where('program_diterima_id', (int) $this->programFilter);
        }

        if ($this->trackFilter !== '' && is_numeric($this->trackFilter)) {
            $query->where('track_id', (int) $this->trackFilter);
        }

        if ($this->statusFilter !== '') {
            $query->where('hasil_seleksi', $this->statusFilter);
        }

        return $query;
    }

    public function render()
    {
        $period = $this->getActivePeriod();
        $query = $this->getFilteredQuery($period);

        $applicants = (clone $query)
            ->orderBy('nomor_pendaftaran')
            ->orderBy('nama_lengkap')
            ->paginate(15);

        $programFilterOptions = ProgramKeahlian::query()
            ->tampil()
            ->orderBy('nama_jurusan')
            ->get(['id', 'nama_jurusan']);

        $trackFilterOptions = \App\Models\PpdbTrack::query()
            ->orderBy('nama_jalur')
            ->get(['id', 'nama_jalur']);

        return view('livewire.admin.ppdb-v2.reports', [
            'applicants' => $applicants,
            'period' => $period,
            'programFilterOptions' => $programFilterOptions,
            'trackFilterOptions' => $trackFilterOptions,
            'countTotal' => (clone $query)->count(),
        ])->layout('components.layouts.admin', ['title' => 'Laporan PPDB']);
    }
}
