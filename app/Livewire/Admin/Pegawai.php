<?php

namespace App\Livewire\Admin;

use App\Models\Pegawai as PegawaiModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Pegawai')]
class Pegawai extends Component
{
    use WithPagination, WithFileUploads;

    public bool $showModal = false;
    #[Locked]
    public ?int $editId = null;
    public string $search = '';

    public string $nip = '';
    public string $nama_lengkap = '';
    public string $jabatan = '';
    public string $bidang_tugas = '';
    public bool $status_aktif = true;
    public $foto_profil;
    public ?string $existing_foto = null;
    public bool $remove_existing_foto = false;

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $pegawai = PegawaiModel::findOrFail($id);
        $this->editId = $pegawai->id;
        $this->nip = $pegawai->nip ?? '';
        $this->nama_lengkap = $pegawai->nama_lengkap;
        $this->jabatan = $pegawai->jabatan ?? '';
        $this->bidang_tugas = $pegawai->bidang_tugas ?? '';
        $this->status_aktif = $pegawai->status_aktif;
        $this->existing_foto = $pegawai->foto_profil;
        $this->foto_profil = null;
        $this->remove_existing_foto = false;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());
        $isEditing = $this->editId !== null;

        $data = [
            'nip' => filled($validated['nip']) ? trim($validated['nip']) : null,
            'nama_lengkap' => trim($validated['nama_lengkap']),
            'jabatan' => filled($validated['jabatan']) ? trim($validated['jabatan']) : null,
            'bidang_tugas' => filled($validated['bidang_tugas']) ? trim($validated['bidang_tugas']) : null,
            'status_aktif' => (bool) $validated['status_aktif'],
        ];

        $pegawai = $isEditing
            ? PegawaiModel::findOrFail($this->editId)
            : new PegawaiModel();

        $oldFotoPath = $pegawai->foto_profil;
        $newFotoPath = null;
        $removeCurrentPhoto = $isEditing && $this->remove_existing_foto && ! $this->foto_profil;

        if ($this->foto_profil) {
            $newFotoPath = $this->foto_profil->store('pegawai', 'public');
            $data['foto_profil'] = $newFotoPath;
        } elseif ($removeCurrentPhoto) {
            $data['foto_profil'] = null;
        }

        try {
            $pegawai->fill($data);
            $pegawai->save();
        } catch (\Throwable $exception) {
            if ($newFotoPath) {
                Storage::disk('public')->delete($newFotoPath);
            }

            throw $exception;
        }

        if ($oldFotoPath) {
            $shouldDeleteOldPhoto = ($newFotoPath && $oldFotoPath !== $newFotoPath) || $removeCurrentPhoto;

            if ($shouldDeleteOldPhoto) {
                Storage::disk('public')->delete($oldFotoPath);
            }
        }

        $this->closeModal();
        $this->dispatch('toast', type: 'success', message: $isEditing ? 'Data pegawai diperbarui.' : 'Pegawai berhasil ditambahkan.');
    }

    public function delete(int $id): void
    {
        $pegawai = PegawaiModel::findOrFail($id);

        if ($pegawai->foto_profil) {
            Storage::disk('public')->delete($pegawai->foto_profil);
        }

        $pegawai->delete();
        $this->dispatch('toast', type: 'success', message: 'Pegawai berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function updatedFotoProfil(): void
    {
        if ($this->foto_profil) {
            $this->remove_existing_foto = false;
        }
    }

    protected function rules(): array
    {
        $nipUniqueRule = Rule::unique('pegawai', 'nip');

        if ($this->editId !== null) {
            $nipUniqueRule->ignore($this->editId);
        }

        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nip' => [
                'nullable',
                'string',
                'max:30',
                $nipUniqueRule,
            ],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'bidang_tugas' => ['nullable', 'string', 'max:255'],
            'status_aktif' => ['required', 'boolean'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_existing_foto' => ['boolean'],
        ];
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->nip = '';
        $this->nama_lengkap = '';
        $this->jabatan = '';
        $this->bidang_tugas = '';
        $this->status_aktif = true;
        $this->foto_profil = null;
        $this->existing_foto = null;
        $this->remove_existing_foto = false;
        $this->resetValidation();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $pegawai = PegawaiModel::when($this->search, fn($q) => $q->where('nama_lengkap', 'like', "%{$this->search}%")->orWhere('nip', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.pegawai', compact('pegawai'));
    }
}
