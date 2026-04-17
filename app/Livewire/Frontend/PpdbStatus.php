<?php

namespace App\Livewire\Frontend;

use App\Models\PpdbApplication;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Cek Status PPDB - SMK Negeri 1 Kolaka')]
class PpdbStatus extends Component
{
    public string $nomor_pendaftaran = '';
    public string $tanggal_lahir = '';
    public ?PpdbApplication $result = null;
    public bool $searched = false;

    public function search(): void
    {
        $keyword = $this->normalizeKeyword();

        $this->validate([
            'nomor_pendaftaran' => 'required|string',
            'tanggal_lahir' => 'required|date',
        ]);

        $this->searched = true;
        $this->result = PpdbApplication::with(['period', 'track', 'pilihanProgram1', 'pilihanProgram2', 'programDiterima', 'documents'])
            ->where(function ($query) use ($keyword) {
                $query->where('nomor_pendaftaran', $keyword)
                    ->orWhere('nisn', $keyword);
            })
            ->whereDate('tanggal_lahir', $this->tanggal_lahir)
            ->first();
    }

    public function resetSearch(): void
    {
        $this->reset(['nomor_pendaftaran', 'tanggal_lahir', 'result', 'searched']);
        $this->resetValidation();
    }

    protected function normalizeKeyword(): string
    {
        $keyword = trim($this->nomor_pendaftaran);
        $this->nomor_pendaftaran = $keyword;

        return $keyword;
    }

    public function render()
    {
        return view('livewire.frontend.ppdb-status');
    }
}
