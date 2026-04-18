<div x-data="adminConfirmModal()">
    <div class="sr-only" role="status" aria-live="polite" aria-atomic="true">
        <span wire:loading wire:target="search,previousPage,nextPage,gotoPage">Sedang memuat daftar album galeri.</span>
        <span wire:loading wire:target="openAlbum,backToAlbums">Sedang memuat data album galeri.</span>
        <span wire:loading wire:target="createAlbum,editAlbum,saveAlbum">Sedang memproses form album galeri.</span>
        <span wire:loading wire:target="addItem,saveItem,deleteItem">Sedang memproses item galeri.</span>
    </div>

    <div wire:loading.flex wire:target="search,previousPage,nextPage,gotoPage,openAlbum,backToAlbums" class="mb-4 items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 dark:border-blue-900/50 dark:bg-blue-900/20 dark:text-blue-300">
        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="10" class="opacity-25" stroke="currentColor" stroke-width="4"></circle>
            <path d="M4 12a8 8 0 0 1 8-8" class="opacity-75" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
        </svg>
        <span>Menyegarkan data galeri...</span>
    </div>

    @if(!$activeAlbum)
    {{-- Album List --}}
    <div class="flex items-center justify-between mb-6">
        <input wire:model.live.debounce.300ms="search" wire:loading.attr="disabled" wire:target="search" type="text" placeholder="Cari album..." class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white w-64 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        <button wire:click="createAlbum" wire:loading.attr="disabled" wire:target="createAlbum" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition disabled:opacity-60">
            <span wire:loading.remove wire:target="createAlbum">+ Tambah Album</span>
            <span wire:loading wire:target="createAlbum">Membuka...</span>
        </button>
    </div>

    <div wire:loading.block wire:target="search,previousPage,nextPage,gotoPage,openAlbum,backToAlbums" class="space-y-4">
        <x-admin.skeleton.card-grid :cards="6" columns="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3" :key-prefix="'galeri-album-list'" />
    </div>

    <div wire:loading.remove wire:target="search,previousPage,nextPage,gotoPage,openAlbum,backToAlbums">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($albums as $album)
            <div wire:key="album-card-{{ $album->id }}" class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white">{{ $album->judul_album }}</h4>
                        <p class="text-xs text-slate-400 mt-1">{{ $album->items_count }} item &middot; {{ $album->tanggal_kegiatan?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div class="flex gap-1">
                        <button wire:click="editAlbum({{ $album->id }})" wire:loading.attr="disabled" wire:target="editAlbum({{ $album->id }})" class="p-1 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900 rounded transition text-xs disabled:opacity-60">
                            <span wire:loading.remove wire:target="editAlbum({{ $album->id }})">Edit</span>
                            <span wire:loading wire:target="editAlbum({{ $album->id }})">...</span>
                        </button>
                        <button @click="openConfirm('deleteAlbum', {{ $album->id }}, 'Hapus album ini?', 'Album dan item terkait akan dihapus permanen.', 'Ya, Hapus', 'danger')" class="p-1 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900 rounded transition text-xs">Hapus</button>
                    </div>
                </div>
                @if($album->deskripsi_singkat)
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-3 line-clamp-2">{{ $album->deskripsi_singkat }}</p>
                @endif
                <button wire:click="openAlbum({{ $album->id }})" wire:loading.attr="disabled" wire:target="openAlbum({{ $album->id }})" class="w-full py-2 text-sm font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition disabled:opacity-60">
                    <span wire:loading.remove wire:target="openAlbum({{ $album->id }})">Kelola Item →</span>
                    <span wire:loading wire:target="openAlbum({{ $album->id }})">Membuka...</span>
                </button>
            </div>
            @empty
            <div class="col-span-full text-center py-12 text-slate-400">Belum ada album.</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $albums->links() }}</div>
    </div>

    @else
    {{-- Album Items --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <button wire:click="backToAlbums" wire:loading.attr="disabled" wire:target="backToAlbums" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition disabled:opacity-60">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ $activeAlbum->judul_album }}</h3>
        </div>
        <button wire:click="addItem" wire:loading.attr="disabled" wire:target="addItem" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition disabled:opacity-60">
            <span wire:loading.remove wire:target="addItem">+ Tambah Item</span>
            <span wire:loading wire:target="addItem">Membuka...</span>
        </button>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($activeAlbum->items as $item)
        <div wire:key="album-item-{{ $item->id }}" class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden group">
            @if($item->tipe_file === 'foto')
                <img src="{{ Storage::url($item->file_path) }}" class="w-full h-40 object-cover" alt="{{ $item->caption }}">
            @else
                <div class="w-full h-40 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <svg class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            @endif
            <div class="p-3">
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $item->caption ?? 'Tanpa caption' }}</p>
                <button @click="openConfirm('deleteItem', {{ $item->id }}, 'Hapus item galeri?', 'Item yang dihapus tidak dapat dipulihkan.', 'Ya, Hapus', 'info')" class="mt-1 text-xs text-red-500 hover:text-red-700 transition">Hapus</button>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-slate-400">Belum ada item.</div>
        @endforelse
    </div>
    @endif

    {{-- Album Modal --}}
    @if($showAlbumModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="relative bg-white dark:bg-slate-900 rounded-xl shadow-xl w-full max-w-md mx-4 p-6 border border-slate-200 dark:border-slate-800">
            <div wire:loading.flex wire:target="saveAlbum" class="absolute inset-0 z-10 items-center justify-center rounded-xl bg-white/75 dark:bg-slate-900/75">
                <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">Menyimpan album...</div>
            </div>

            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">{{ $editAlbumId ? 'Edit' : 'Tambah' }} Album</h3>
            <form wire:submit="saveAlbum" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Judul Album *</label>
                    <input wire:model="judul_album" type="text" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @error('judul_album') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi</label>
                    <textarea wire:model="deskripsi_singkat" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal Kegiatan</label>
                    <input wire:model="tanggal_kegiatan" type="date" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showAlbumModal', false)" wire:loading.attr="disabled" wire:target="saveAlbum" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition disabled:opacity-60">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="saveAlbum" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveAlbum">Simpan</span>
                        <span wire:loading wire:target="saveAlbum">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Item Modal --}}
    @if($showItemModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="relative bg-white dark:bg-slate-900 rounded-xl shadow-xl w-full max-w-md mx-4 p-6 border border-slate-200 dark:border-slate-800">
            <div wire:loading.flex wire:target="saveItem" class="absolute inset-0 z-10 items-center justify-center rounded-xl bg-white/75 dark:bg-slate-900/75">
                <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">Menyimpan item...</div>
            </div>

            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Tambah Item</h3>
            <form wire:submit="saveItem" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tipe</label>
                    <select wire:model.live="tipe_file" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="foto">Foto</option>
                        <option value="video_url">Video URL</option>
                    </select>
                </div>
                @if($tipe_file === 'foto')
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Upload Foto *</label>
                    <input wire:model="file_path" type="file" accept="image/*" class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 dark:file:bg-blue-900 file:text-blue-700 dark:file:text-blue-300">
                    @error('file_path') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @else
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Video URL *</label>
                    <input wire:model="video_url" type="url" placeholder="https://youtube.com/..." class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @error('video_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Caption</label>
                    <input wire:model="caption" type="text" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showItemModal', false)" wire:loading.attr="disabled" wire:target="saveItem" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition disabled:opacity-60">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="saveItem" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveItem">Upload</span>
                        <span wire:loading wire:target="saveItem">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <x-admin.confirm-modal />
</div>
