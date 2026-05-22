<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\ProfilSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Berita;
use App\Models\Agenda;
use App\Models\Pengumuman;
use App\Models\Pegawai;
use App\Models\GaleriAlbum;
use App\Models\GaleriItem;
use Illuminate\Support\Facades\Cache;

#[Layout('components.layouts.app')]
#[Title('Beranda - MAN 2 Kolaka')]
class Home extends Component
{
    public function render()
    {
        $payload = Cache::remember('home:payload:v2', now()->addMinutes(5), function (): array {
            $profil = ProfilSekolah::first();

            $jurusans = ProgramKeahlian::tampil()
                ->select(['id', 'nama_jurusan', 'kode_jurusan', 'slug', 'gambar_cover', 'deskripsi_lengkap'])
                ->get();

            $recentBerita = Berita::published()
                ->with('kategori:id,nama_kategori')
                ->select(['id', 'judul', 'slug', 'gambar_thumbnail', 'konten_html', 'kategori_id', 'published_at'])
                ->latest('published_at')
                ->take(6)
                ->get();

            $upcomingAgenda = Agenda::upcoming()
                ->select(['id', 'nama_kegiatan', 'waktu_mulai', 'lokasi_pelaksanaan'])
                ->take(8)
                ->get();

            $pengumuman = Pengumuman::aktif()
                ->select(['id', 'judul_pengumuman', 'tanggal_akhir_tampil'])
                ->latest()
                ->take(5)
                ->get();

            $galeriItems = GaleriItem::where('tipe_file', 'foto')
                ->select(['id', 'file_path', 'caption'])
                ->latest()
                ->take(12)
                ->get();

            $pegawaiHighlight = Pegawai::aktif()
                ->select(['id', 'nama_lengkap', 'jabatan', 'foto_profil'])
                ->take(12)
                ->get();

            // Pre-compute counts to avoid extra queries
            $pegawaiCount = Pegawai::aktif()->count();
            $beritaCount = Berita::published()->count();

            $stats = [
                ['label' => 'Peminatan', 'value' => $jurusans->count(), 'icon' => 'academic', 'color' => 'from-blue-400 to-blue-600'],
                ['label' => 'Guru & Tendik', 'value' => $pegawaiCount, 'icon' => 'users', 'color' => 'from-indigo-400 to-indigo-600'],
                ['label' => 'Berita', 'value' => $beritaCount, 'icon' => 'newspaper', 'color' => 'from-purple-400 to-purple-600'],
                ['label' => 'Album Galeri', 'value' => GaleriAlbum::count(), 'icon' => 'camera', 'color' => 'from-cyan-400 to-cyan-600'],
                ['label' => 'Agenda Kegiatan', 'value' => Agenda::count(), 'icon' => 'calendar', 'color' => 'from-amber-400 to-amber-600'],
            ];

            return compact(
                'profil',
                'jurusans',
                'recentBerita',
                'upcomingAgenda',
                'pengumuman',
                'stats',
                'galeriItems',
                'pegawaiHighlight'
            );
        });

        return view('livewire.home', $payload);
    }
}
