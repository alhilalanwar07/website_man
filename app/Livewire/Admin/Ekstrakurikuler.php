<?php

namespace App\Livewire\Admin;

use App\Models\Ekstrakurikuler as EkskulModel;
use App\Models\EkstrakurikulerKategori;
use App\Models\ProgramKeahlian;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Ekstrakurikuler')]
class Ekstrakurikuler extends Component
{
    use WithPagination, WithFileUploads;

    public string $tab = 'ekskul';
    public bool $showModal = false;
    public ?int $editId = null;
    public string $search = '';

    // Ekskul fields
    public $program_keahlian_id = '';
    public $kategori_id = '';
    public string $nama_produk_jasa = '';
    public string $deskripsi = '';
    public string $harga_estimasi = '';
    public string $status_ketersediaan = 'tersedia';
    public $gambar_utama;
    public ?string $existing_gambar = null;

    // Kategori fields
    public string $nama_kategori = '';
    public bool $showKategoriModal = false;
    public ?int $editKategoriId = null;

    // Ekskul CRUD
    public function createEkskul(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editEkskul(int $id): void
    {
        $p = EkskulModel::findOrFail($id);
        $this->editId = $p->id;
        $this->program_keahlian_id = $p->program_keahlian_id;
        $this->kategori_id = $p->kategori_id;
        $this->nama_produk_jasa = $p->nama_produk_jasa;
        $this->deskripsi = $p->deskripsi ?? '';
        $this->harga_estimasi = $p->harga_estimasi ?? '';
        $this->status_ketersediaan = $p->status_ketersediaan;
        $this->existing_gambar = $p->gambar_utama;
        $this->showModal = true;
    }

    public function saveEkskul(): void
    {
        $this->validate([
            'program_keahlian_id' => 'required|exists:program_keahlian,id',
            'kategori_id'         => 'required|exists:tefa_kategori,id',
            'nama_produk_jasa'    => 'required|string|max:255',
            'harga_estimasi'      => 'nullable|numeric|min:0',
            'gambar_utama'        => 'nullable|image|max:2048',
        ]);

        $data = [
            'program_keahlian_id' => $this->program_keahlian_id,
            'kategori_id'         => $this->kategori_id,
            'nama_produk_jasa'    => $this->nama_produk_jasa,
            'slug'                => Str::slug($this->nama_produk_jasa),
            'deskripsi'           => $this->deskripsi,
            'harga_estimasi'      => $this->harga_estimasi ?: null,
            'status_ketersediaan' => $this->status_ketersediaan,
        ];

        if ($this->gambar_utama) {
            $data['gambar_utama'] = $this->gambar_utama->store('ekstrakurikuler', 'public');
        }

        EkskulModel::updateOrCreate(['id' => $this->editId], $data);
        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: 'Ekstrakurikuler berhasil disimpan.');
    }

    public function deleteEkskul(int $id): void
    {
        EkskulModel::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Ekstrakurikuler berhasil dihapus.');
    }

    // Kategori CRUD
    public function createKategori(): void
    {
        $this->nama_kategori = '';
        $this->editKategoriId = null;
        $this->showKategoriModal = true;
    }

    public function editKategori(int $id): void
    {
        $k = EkstrakurikulerKategori::findOrFail($id);
        $this->editKategoriId = $k->id;
        $this->nama_kategori = $k->nama_kategori;
        $this->showKategoriModal = true;
    }

    public function saveKategori(): void
    {
        $this->validate(['nama_kategori' => 'required|string|max:255']);
        EkstrakurikulerKategori::updateOrCreate(['id' => $this->editKategoriId], [
            'nama_kategori' => $this->nama_kategori,
            'slug'          => Str::slug($this->nama_kategori),
        ]);
        $this->showKategoriModal = false;
        $this->dispatch('toast', type: 'success', message: 'Kategori berhasil disimpan.');
    }

    public function deleteKategori(int $id): void
    {
        EkstrakurikulerKategori::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Kategori berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->program_keahlian_id = '';
        $this->kategori_id = '';
        $this->nama_produk_jasa = '';
        $this->deskripsi = '';
        $this->harga_estimasi = '';
        $this->status_ketersediaan = 'tersedia';
        $this->gambar_utama = null;
        $this->existing_gambar = null;
        $this->resetValidation();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.ekstrakurikuler', [
            'ekskulList' => EkskulModel::with(['programKeahlian', 'kategori'])
                ->when($this->search, fn ($q) => $q->where('nama_produk_jasa', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate(10),
            'kategoriList'  => EkstrakurikulerKategori::withCount('ekskul')->get(),
            'programList'   => ProgramKeahlian::all(),
        ]);
    }
}
