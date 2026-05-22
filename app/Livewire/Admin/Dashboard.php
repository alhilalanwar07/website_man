<?php

namespace App\Livewire\Admin;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\GaleriAlbum;
use App\Models\Pegawai;
use App\Models\Pengumuman;
use App\Models\ProgramKeahlian;
use App\Models\Ekstrakurikuler;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $payload = Cache::remember('admin:dashboard:payload:v1', now()->addMinutes(2), function (): array {
            return [
                'stats' => [
                    ['label' => 'Pegawai', 'value' => Pegawai::count(), 'color' => 'blue'],
                    ['label' => 'Program Keahlian', 'value' => ProgramKeahlian::count(), 'color' => 'indigo'],
                    ['label' => 'Ekstrakurikuler', 'value' => Ekstrakurikuler::count(), 'color' => 'emerald'],
                    ['label' => 'Berita', 'value' => Berita::count(), 'color' => 'orange'],
                    ['label' => 'Pengumuman', 'value' => Pengumuman::count(), 'color' => 'pink'],
                    ['label' => 'Agenda', 'value' => Agenda::count(), 'color' => 'cyan'],
                    ['label' => 'Album Galeri', 'value' => GaleriAlbum::count(), 'color' => 'purple'],
                ],
                'recentBerita' => Berita::with('user')->latest()->take(5)->get(),
                'upcomingAgenda' => Agenda::where('waktu_mulai', '>=', now())->orderBy('waktu_mulai')->take(5)->get(),
            ];
        });

        return view('livewire.admin.dashboard', $payload);
    }
}
