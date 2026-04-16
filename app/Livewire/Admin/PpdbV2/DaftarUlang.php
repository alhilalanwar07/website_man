<?php

namespace App\Livewire\Admin\PpdbV2;

use App\Models\PpdbApplication;
use App\Models\PpdbPeriod;
use Livewire\Component;

class DaftarUlang extends Component
{
    public $search = '';
    public $statusFilter = ''; // 'selesai' atau 'belum'

    public function toggleStatus($studentId)
    {
        $student = PpdbApplication::find($studentId);
        if ($student) {
            $isAlreadyVerified = $student->status_daftar_ulang === 'verified';
            
            $student->update([
                'status_daftar_ulang' => $isAlreadyVerified ? 'pending' : 'verified',
                'daftar_ulang_at' => $isAlreadyVerified ? null : now(),
                'verified_daftar_ulang_by' => $isAlreadyVerified ? null : auth()->id(),
                'verified_daftar_ulang_at' => $isAlreadyVerified ? null : now(),
            ]);

            session()->flash('message', 'Status daftar ulang atas nama ' . $student->nama_lengkap . ' '. ($isAlreadyVerified ? 'dibatalkan.' : 'telah diselesaikan.'));
        }
    }

    public function render()
    {
        $period = PpdbPeriod::where('is_active', true)->first();
        $query = PpdbApplication::query();

        if ($period) {
            $query->where('period_id', $period->id);
            // Syarat masuk daftar ulang: Siswa harus sudah dinyatakan Lulus dan Punya Jurusan
            $query->where('hasil_seleksi', 'lulus')->whereNotNull('program_diterima_id');
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter === 'selesai') {
            $query->where('status_daftar_ulang', 'verified');
        } elseif ($this->statusFilter === 'belum') {
            $query->where(function($q) {
                $q->whereNull('status_daftar_ulang')
                  ->orWhere('status_daftar_ulang', '!=', 'verified');
            });
        }

        $pendaftarLulus = $query->orderBy('nama_lengkap')->get();

        return view('livewire.admin.ppdb-v2.daftar-ulang', [
            'pendaftar' => $pendaftarLulus,
            'period' => $period,
            'countSelesai' => $period ? PpdbApplication::where('period_id', $period->id)->where('status_daftar_ulang', 'verified')->count() : 0,
            'countTotal' => $period ? PpdbApplication::where('period_id', $period->id)->where('hasil_seleksi', 'lulus')->count() : 0,
        ])->layout('components.layouts.admin', ['title' => 'Verifikasi Daftar Ulang']);
    }
}
