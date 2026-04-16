<?php

namespace App\Livewire\Admin\PpdbV2;

use App\Models\PpdbPeriod;
use App\Models\ProgramKeahlian;
use App\Models\PpdbQuota;
use Livewire\Component;

class Pengaturan extends Component
{
    public $isRegistrationOpen = false;
    public $quotas = [];

    public function mount()
    {
        $period = PpdbPeriod::where('is_active', true)->first();
        if ($period) {
            $this->isRegistrationOpen = $period->status === 'published';
        }

        $programs = ProgramKeahlian::tampil()->get();
        foreach ($programs as $prog) {
            $quotaAmount = $period ? PpdbQuota::where('period_id', $period->id)->where('program_keahlian_id', $prog->id)->sum('kuota') : 0;
            $this->quotas[$prog->id] = $quotaAmount;
        }
    }

    public function updatedIsRegistrationOpen($value)
    {
        $period = PpdbPeriod::where('is_active', true)->first();
        if ($period) {
            // Update to exactly how Laravel tracks published status
            $period->update(['status' => $value ? 'published' : 'closed']);
            session()->flash('message', 'Formulir Berhasil Dinyalakan: ' . ($value ? 'DIBUKA' : 'DITUTUP'));
        } else {
            session()->flash('error', 'Tidak ada periode aktif.');
            $this->isRegistrationOpen = false;
        }
    }

    public function simpanKuota()
    {
        $period = PpdbPeriod::where('is_active', true)->first();
        if (!$period) return;

        foreach ($this->quotas as $programId => $jumlah) {
            $quota = PpdbQuota::where('period_id', $period->id)
                ->where('program_keahlian_id', $programId)
                ->first();
                
            if ($quota) {
                $quota->update(['kuota' => (int)$jumlah]);
            } else {
                PpdbQuota::create([
                    'period_id' => $period->id,
                    'program_keahlian_id' => $programId,
                    'track_id' => null, // Override any active tracking assumption
                    'kuota' => (int)$jumlah,
                    'status_aktif' => true,
                ]);
            }
        }
        
        session()->flash('message', 'Target kuota seluruh jurusan berhasil disimpan permanen.');
    }

    public function render()
    {
        $programs = ProgramKeahlian::tampil()->get();
        $period = PpdbPeriod::where('is_active', true)->first();
        return view('livewire.admin.ppdb-v2.pengaturan', [
            'programs' => $programs,
            'period' => $period
        ])->layout('components.layouts.admin', ['title' => 'Pengaturan PPDB']);
    }
}
