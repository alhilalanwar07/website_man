<div x-data="adminConfirmModal()">
    <div class="sr-only" role="status" aria-live="polite" aria-atomic="true">
        <span wire:loading wire:target="search,previousPage,nextPage,gotoPage">Sedang memuat data pegawai.</span>
        <span wire:loading wire:target="create,edit,save">Sedang memproses formulir pegawai.</span>
    </div>

    <div wire:loading.flex wire:target="search,previousPage,nextPage,gotoPage" class="mb-4 items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 dark:border-blue-900/50 dark:bg-blue-900/20 dark:text-blue-300">
        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="10" class="opacity-25" stroke="currentColor" stroke-width="4"></circle>
            <path d="M4 12a8 8 0 0 1 8-8" class="opacity-75" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
        </svg>
        <span>Menyegarkan data pegawai...</span>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <input wire:model.live.debounce.300ms="search" wire:loading.attr="disabled" wire:target="search" type="text" placeholder="Cari pegawai..."
            class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white w-64 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
        <button wire:click="create" wire:loading.attr="disabled" wire:target="create" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition disabled:opacity-60">
            <span wire:loading.remove wire:target="create">+ Tambah Pegawai</span>
            <span wire:loading wire:target="create">Membuka...</span>
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div wire:loading.block wire:target="search,previousPage,nextPage,gotoPage" class="p-4">
            <x-admin.skeleton.table :columns="5" :rows="8" :key-prefix="'pegawai-table'" :show-actions="true" />
        </div>

        <div wire:loading.remove wire:target="search,previousPage,nextPage,gotoPage">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Nama</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">NIP</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Jabatan</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($pegawai as $item)
                        <tr wire:key="pegawai-row-{{ $item->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-4 py-3 flex items-center gap-3">
                                @if($item->foto_profil)
                                    <img src="{{ Storage::url($item->foto_profil) }}" class="w-8 h-8 rounded-lg object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xs font-bold">{{ strtoupper(substr($item->nama_lengkap, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <p class="font-medium text-slate-800 dark:text-white">{{ $item->nama_lengkap }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $item->bidang_tugas ?: 'Bidang belum diisi' }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $item->nip ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $item->jabatan ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $item->status_aktif ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300' }}">
                                    {{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-1">
                                <button wire:click="edit({{ $item->id }})" wire:loading.attr="disabled" wire:target="edit({{ $item->id }})" class="px-3 py-1 text-xs font-medium bg-yellow-50 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-800 transition disabled:opacity-60">
                                    <span wire:loading.remove wire:target="edit({{ $item->id }})">Edit</span>
                                    <span wire:loading wire:target="edit({{ $item->id }})">Memuat...</span>
                                </button>
                                <button x-on:click="openConfirm('delete', {{ $item->id }}, 'Hapus data pegawai ini?', 'Data pegawai akan dihapus permanen dari sistem.', 'Ya, Hapus', 'danger')" class="px-3 py-1 text-xs font-medium bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-100 dark:hover:bg-red-800 transition">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada data pegawai.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">{{ $pegawai->links() }}</div>
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="relative bg-white dark:bg-slate-900 rounded-xl shadow-xl w-full max-w-lg mx-4 p-6 border border-slate-200 dark:border-slate-800">
            <div wire:loading.flex wire:target="save" class="absolute inset-0 z-10 items-center justify-center rounded-xl bg-white/75 dark:bg-slate-900/75">
                <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">Menyimpan data pegawai...</div>
            </div>

            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">{{ $editId ? 'Edit Pegawai' : 'Tambah Pegawai' }}</h3>

            @if($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-900/20 dark:text-red-300">
                    <p class="font-semibold">Data belum bisa disimpan. Periksa kembali input berikut:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                        <input wire:model.blur="nama_lengkap" type="text" class="w-full px-3 py-2 rounded-lg border {{ $errors->has('nama_lengkap') ? 'border-red-500 focus:ring-red-500/20 focus:border-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-blue-500/20 focus:border-blue-500' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none">
                        @error('nama_lengkap') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">NIP</label>
                        <input wire:model.blur="nip" type="text" class="w-full px-3 py-2 rounded-lg border {{ $errors->has('nip') ? 'border-red-500 focus:ring-red-500/20 focus:border-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-blue-500/20 focus:border-blue-500' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none">
                        @error('nip') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jabatan</label>
                        <input wire:model.blur="jabatan" type="text" class="w-full px-3 py-2 rounded-lg border {{ $errors->has('jabatan') ? 'border-red-500 focus:ring-red-500/20 focus:border-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-blue-500/20 focus:border-blue-500' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none">
                        @error('jabatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Bidang Tugas</label>
                        <input wire:model.blur="bidang_tugas" type="text" class="w-full px-3 py-2 rounded-lg border {{ $errors->has('bidang_tugas') ? 'border-red-500 focus:ring-red-500/20 focus:border-red-500' : 'border-slate-200 dark:border-slate-700 focus:ring-blue-500/20 focus:border-blue-500' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none">
                        @error('bidang_tugas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model.live="status_aktif" type="checkbox" class="rounded border-slate-300 dark:border-slate-600 text-blue-600 dark:bg-slate-800">
                            <span class="text-sm text-slate-700 dark:text-slate-300">Aktif</span>
                        </label>
                        @error('status_aktif') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div
                        class="col-span-2 space-y-2"
                        x-data="{
                            clientPreviewUrl: null,
                            clientPreviewFailed: false,
                            setClientPreview(event) {
                                const file = event?.target?.files?.[0] ?? null;

                                this.clientPreviewFailed = false;

                                if (this.clientPreviewUrl) {
                                    URL.revokeObjectURL(this.clientPreviewUrl);
                                }

                                this.clientPreviewUrl = file ? URL.createObjectURL(file) : null;
                            },
                            markClientPreviewFailed() {
                                this.clientPreviewFailed = true;
                            }
                        }"
                    >
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Foto Profil</label>
                        <input wire:model="foto_profil" wire:loading.attr="disabled" wire:target="foto_profil" x-on:change="setClientPreview($event)" type="file" accept="image/*" class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 dark:file:bg-blue-900 file:text-blue-700 dark:file:text-blue-300">
                        <p class="text-xs text-slate-500 dark:text-slate-400">Format yang didukung: JPG, JPEG, PNG, WEBP. Maksimum 2MB.</p>

                        @php
                            $uploadedFotoPreviewUrl = $foto_profil ? $this->fotoProfilPreviewUrl() : null;
                            $uploadedFotoPreviewNotice = $foto_profil ? $this->fotoProfilPreviewNotice() : null;
                            $hasUploadedFotoServerPreview = (bool) $uploadedFotoPreviewUrl;
                        @endphp

                        @if($foto_profil)
                            <div x-cloak x-show="clientPreviewUrl && !clientPreviewFailed" class="flex items-center gap-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm dark:border-blue-900/50 dark:bg-blue-900/20">
                                <img :src="clientPreviewUrl" x-on:error="markClientPreviewFailed()" alt="Preview foto baru" class="h-14 w-14 rounded-lg object-cover">
                                <div>
                                    <p class="font-medium text-blue-700 dark:text-blue-300">Foto baru siap disimpan</p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400">Foto lama akan diganti setelah data disimpan.</p>
                                </div>
                            </div>

                            @if($uploadedFotoPreviewUrl)
                                <div x-cloak x-show="!clientPreviewUrl && !clientPreviewFailed" class="flex items-center gap-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm dark:border-blue-900/50 dark:bg-blue-900/20">
                                        <img src="{{ $uploadedFotoPreviewUrl }}" x-on:error="markClientPreviewFailed()" alt="Preview foto baru" class="h-14 w-14 rounded-lg object-cover">
                                    <div>
                                        <p class="font-medium text-blue-700 dark:text-blue-300">Foto baru siap disimpan</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400">Foto lama akan diganti setelah data disimpan.</p>
                                    </div>
                                </div>
                            @endif

                            <div x-cloak x-show="clientPreviewFailed || (!clientPreviewUrl && !@js($hasUploadedFotoServerPreview))" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm dark:border-amber-900/50 dark:bg-amber-900/20">
                                <p class="font-medium text-amber-700 dark:text-amber-300">Preview foto tidak tersedia</p>
                                <p class="text-xs text-amber-600 dark:text-amber-400">{{ $uploadedFotoPreviewNotice ?? 'Lanjutkan simpan untuk validasi format file.' }}</p>
                            </div>
                        @elseif($existing_foto && ! $remove_existing_foto)
                            <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800">
                                <img src="{{ Storage::url($existing_foto) }}" alt="Foto saat ini" class="h-14 w-14 rounded-lg object-cover">
                                <div>
                                    <p class="font-medium text-slate-700 dark:text-slate-300">Foto saat ini</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Unggah file baru bila ingin mengganti foto.</p>
                                </div>
                            </div>
                        @endif

                        @if($editId && $existing_foto)
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                <input wire:model.live="remove_existing_foto" type="checkbox" class="rounded border-slate-300 dark:border-slate-600 text-blue-600 dark:bg-slate-800">
                                Hapus foto saat ini ketika disimpan
                            </label>

                            @if($remove_existing_foto && ! $foto_profil)
                                <p class="text-xs text-amber-600 dark:text-amber-400">Foto saat ini akan dihapus saat data disimpan.</p>
                            @endif
                        @endif

                        @error('foto_profil') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeModal" wire:loading.attr="disabled" wire:target="save" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition disabled:opacity-60">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Simpan</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    @endif
    <x-admin.confirm-modal />
</div>
