<?php

namespace App\Livewire\Admin\PpdbV2;

use App\Models\PpdbApplication;
use App\Models\PpdbPeriod;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $period = PpdbPeriod::where('is_active', true)->first();
        
        $baseQuery = PpdbApplication::query();
        if ($period) {
            $baseQuery->where('period_id', $period->id);
        }

        $totalPendaftar = (clone $baseQuery)->count();
        $sudahDiwawancara = (clone $baseQuery)->where('status_berkas', 'verified')->count();
        $selesaiJurusan = (clone $baseQuery)->whereNotNull('program_diterima_id')->count();
        $tuntasDaftarUlang = (clone $baseQuery)->where('status_daftar_ulang', 'verified')->count();
        
        $pendaftarTerbaru = (clone $baseQuery)->latest()->take(5)->get();

        return view('livewire.admin.ppdb-v2.dashboard', [
            'totalPendaftar' => $totalPendaftar,
            'sudahDiwawancara' => $sudahDiwawancara,
            'selesaiJurusan' => $selesaiJurusan,
            'tuntasDaftarUlang' => $tuntasDaftarUlang,
            'pendaftarTerbaru' => $pendaftarTerbaru,
        ])->layout('components.layouts.admin', ['title' => 'Dashboard PPDB']);
    }
}
