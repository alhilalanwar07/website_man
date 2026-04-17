<?php

namespace App\Livewire\Admin\PpdbV2;

use App\Models\PpdbApplication;
use App\Models\PpdbPeriod;
use Livewire\Component;
use Livewire\WithPagination;

class Pendaftar extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    // Bulk selection
    public $selectedRows = [];
    public $selectAll = false;

    // Manual Add State
    public $showAddModal = false;
    public $formSiswa = [
        'nama_lengkap' => '',
        'nisn' => '',
        'asal_sekolah' => '',
        'nomor_hp' => ''
    ];

    // Detail / Split View State
    public $selectedSiswaId = null;

    public function getSelectedSiswaProperty()
    {
        if (!$this->selectedSiswaId) return null;
        return \App\Models\PpdbApplication::with('documents')->find($this->selectedSiswaId);
    }

    public function selectSiswa($id)
    {
        $this->selectedSiswaId = $id;
    }

    public function closeDetail()
    {
        $this->selectedSiswaId = null;
    }

    public function exportExcel()
    {
        session()->flash('message', 'Mengekspor data Pendaftar ke format Excel (.xlsx)...');
    }

    public function exportPdf()
    {
        session()->flash('message', 'Mengekspor data Pendaftar ke format PDF...');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $period = PpdbPeriod::where('is_active', true)->first();
            if ($period) {
                // Select only IDs of current page if possible, or all
                $this->selectedRows = PpdbApplication::where('period_id', $period->id)
                    ->when($this->search, function ($q) {
                        $q->where(function ($subQ) {
                            $subQ->where('nama_lengkap', 'like', '%' . $this->search . '%')
                                 ->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%')
                                 ->orWhere('nisn', 'like', '%' . $this->search . '%');
                        });
                    })
                    ->pluck('id')->map(fn($id) => (string) $id)->toArray();
            }
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
        $this->selectedSiswaId = $id; 
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
        $this->validate([
            'formSiswa.nama_lengkap' => 'required|string|max:255',
            'formSiswa.nisn' => 'nullable|string|max:20',
            'formSiswa.asal_sekolah' => 'nullable|string|max:255',
        ]);

        $period = PpdbPeriod::where('is_active', true)->first();
        if (!$period) {
            session()->flash('error', 'Gagal: Periode PPDB saat ini sedang non-aktif/ditutup.');
            return;
        }

        // Auto generete nomor pendaftaran sederhana
        $no_pendaftaran = 'PPDB-' . date('ym') . '-' . rand(1000, 9999);

        PpdbApplication::create([
            'period_id' => $period->id,
            'nomor_pendaftaran' => $no_pendaftaran,
            'nama_lengkap' => $this->formSiswa['nama_lengkap'],
            'nisn' => $this->formSiswa['nisn'],
            'asal_sekolah' => $this->formSiswa['asal_sekolah'],
            'nomor_hp' => $this->formSiswa['nomor_hp'] ?? null,
            'status_pendaftaran' => 'submitted',
            'status_berkas' => 'pending',
            'submitted_at' => now(),
        ]);

        session()->flash('message', 'Pendaftar offline bernama ' . $this->formSiswa['nama_lengkap'] . ' berhasil terdaftar mandiri.');
        
        // Tutup modal dan reset
        $this->reset(['formSiswa', 'showAddModal']);
    }

    public function render()
    {
        $period = PpdbPeriod::where('is_active', true)->first();
        $query = PpdbApplication::query();

        if ($period) {
            $query->where('period_id', $period->id);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%')
                  ->orWhere('nisn', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status_berkas', $this->statusFilter);
        }

        $pendaftar = $query->latest()->paginate(15);

        return view('livewire.admin.ppdb-v2.pendaftar', [
            'pendaftar' => $pendaftar,
            'period' => $period
        ])->layout('components.layouts.admin', ['title' => 'Data Pendaftar PPDB']);
    }
}
