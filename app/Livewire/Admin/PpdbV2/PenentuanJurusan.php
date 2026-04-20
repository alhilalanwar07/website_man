<?php

namespace App\Livewire\Admin\PpdbV2;

use App\Models\PpdbApplication;
use App\Models\PpdbPeriod;
use App\Models\ProgramKeahlian;
use Livewire\Component;

class PenentuanJurusan extends Component
{
    public $search = '';
    public $statusFilter = 'belum_ditentukan';
    public $pilihanFilter = '';
    
    // Bulk selection
    public $selectedRows = [];
    public $selectAll = false;

    public $assignedMajors = [];
    public $majors = [];

    public function mount()
    {
        $this->majors = ProgramKeahlian::all();
        
        $period = PpdbPeriod::where('is_active', true)->first();
        if ($period) {
            // Load existing assignments
            $students = PpdbApplication::where('period_id', $period->id)
                            ->whereNotNull('program_diterima_id')
                            ->get();
            foreach($students as $s) {
                $this->assignedMajors[$s->id] = (string) $s->program_diterima_id;
            }
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $period = PpdbPeriod::where('is_active', true)->first();
            if ($period) {
                // Kumpulkan ID sesuai filter & pencarian saat ini
                $query = PpdbApplication::where('period_id', $period->id)->whereVerificationStatus('approved');
                
                if ($this->search) {
                    $query->where(function($q) {
                        $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                          ->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%')
                          ->orWhere('nisn', 'like', '%' . $this->search . '%');
                    });
                }
                if ($this->statusFilter === 'belum_ditentukan') {
                    $query->whereNull('program_diterima_id');
                } elseif ($this->statusFilter === 'sudah_ditentukan') {
                    $query->whereNotNull('program_diterima_id');
                }
                if ($this->pilihanFilter) {
                    $query->where(function($q) {
                        $q->where('pilihan_program_1_id', $this->pilihanFilter)
                          ->orWhere('pilihan_program_2_id', $this->pilihanFilter);
                    });
                }

                $this->selectedRows = $query->pluck('id')->map(fn($id) => (string) $id)->toArray();
            }
        } else {
            $this->selectedRows = [];
        }
    }

    public function updatedAssignedMajors($value, $key)
    {
        $studentId = $key;
        $majorId = $value ?: null;
        
        PpdbApplication::where('id', $studentId)->update([
            'program_diterima_id' => $majorId,
            'hasil_seleksi' => $majorId ? 'passed' : 'pending',
        ]);

        // Opsional: Dispatch broadcast event untuk WA/Email gateway disini nanti
        // event(new KelulusanDitetapkan($studentId, $majorId));

        session()->flash('message', 'Jurusan berhasil ditetapkan secara instan.');
    }

    public function setBatchTo($majorId)
    {
        if (empty($this->selectedRows)) {
            session()->flash('message', 'Pilih minimal satu siswa terlebih dahulu.');
            return;
        }

        $targetStudents = PpdbApplication::whereIn('id', $this->selectedRows)->get();

        foreach($targetStudents as $s) {
            $this->assignedMajors[$s->id] = (string) $majorId;
            // Ignore IDE warning; $s is an Eloquent Model
            /** @var PpdbApplication $s */
            $s->update([
                'program_diterima_id' => $majorId,
                'hasil_seleksi' => 'passed'
            ]);
        }
        
        session()->flash('message', 'Sukses menetapkan ' . count($this->selectedRows) . ' siswa terpilih secara masif.');
        $this->selectedRows = [];
        $this->selectAll = false;
    }

    public function render()
    {
        $period = PpdbPeriod::where('is_active', true)->first();
        $query = PpdbApplication::query();

        if ($period) {
            $query->where('period_id', $period->id);
            // Standar operasi: hanya yang sudah lulus verifikasi berkas yang bisa diberi jurusan akhir
            $query->whereVerificationStatus('approved');
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%')
                  ->orWhere('nisn', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter === 'belum_ditentukan') {
            $query->whereNull('program_diterima_id');
        } elseif ($this->statusFilter === 'sudah_ditentukan') {
            $query->whereNotNull('program_diterima_id');
        }

        if ($this->pilihanFilter) {
            $query->where(function($q) {
                $q->where('pilihan_program_1_id', $this->pilihanFilter)
                  ->orWhere('pilihan_program_2_id', $this->pilihanFilter);
            });
        }

        $pendaftar = $query->orderBy('nama_lengkap')->get();

        return view('livewire.admin.ppdb-v2.penentuan-jurusan', [
            'pendaftar' => $pendaftar,
            'period' => $period,
            'majors' => $this->majors
        ])->layout('components.layouts.admin', ['title' => 'Penentuan Jurusan']);
    }
}
