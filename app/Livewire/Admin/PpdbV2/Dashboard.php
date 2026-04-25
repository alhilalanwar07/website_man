<?php

namespace App\Livewire\Admin\PpdbV2;

use App\Models\PpdbApplication;
use App\Models\PpdbPeriod;
use App\Models\ProgramKeahlian;
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
        $sudahDiwawancara = (clone $baseQuery)->whereVerificationStatus('approved')->count();
        $selesaiJurusan = (clone $baseQuery)->whereNotNull('program_diterima_id')->count();
        $tuntasDaftarUlang = (clone $baseQuery)->where('status_daftar_ulang', 'verified')->count();
        
        $pendaftarTerbaru = (clone $baseQuery)->latest()->take(5)->get();

        $programDistribusi = ProgramKeahlian::tampil()->get()->map(function ($program) use ($period, $baseQuery) {
            $kuota = 0;
            if ($period) {
                $kuotaObj = $program->ppdbQuotas()->where('period_id', $period->id)->first();
                if ($kuotaObj) {
                    $kuota = $kuotaObj->quota;
                }
            }

            $diterima = (clone $baseQuery)->where('program_diterima_id', $program->id)->count();
            $pilihanSatu = (clone $baseQuery)->where('pilihan_program_1_id', $program->id)->count();

            return [
                'nama' => $program->nama_jurusan,
                'singkatan' => $program->kode_jurusan,
                'kuota' => $kuota,
                'diterima' => $diterima,
                'pilihan_satu' => $pilihanSatu,
                'persentase_terisi' => $kuota > 0 ? min(100, round(($diterima / $kuota) * 100)) : 0,
            ];
        })->sortByDesc('diterima')->values();

        return view('livewire.admin.ppdb-v2.dashboard', [
            'totalPendaftar' => $totalPendaftar,
            'sudahDiwawancara' => $sudahDiwawancara,
            'selesaiJurusan' => $selesaiJurusan,
            'tuntasDaftarUlang' => $tuntasDaftarUlang,
            'pendaftarTerbaru' => $pendaftarTerbaru,
            'programDistribusi' => $programDistribusi,
        ])->layout('components.layouts.admin', ['title' => 'Dashboard PPDB']);
    }
}
