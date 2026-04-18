<div x-data="adminConfirmModal()">
    <div class="sr-only" role="status" aria-live="polite" aria-atomic="true">
        <span wire:loading wire:target="create,edit,save">Sedang memproses formulir pengaturan.</span>
        <span wire:loading wire:target="delete">Sedang memperbarui daftar pengaturan.</span>
    </div>

    <div wire:loading.flex wire:target="delete" class="mb-4 items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 dark:border-blue-900/50 dark:bg-blue-900/20 dark:text-blue-300">
        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="10" class="opacity-25" stroke="currentColor" stroke-width="4"></circle>
            <path d="M4 12a8 8 0 0 1 8-8" class="opacity-75" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
        </svg>
        <span>Menyegarkan data pengaturan...</span>
    </div>

    <div class="flex justify-end mb-6">
        <button wire:click="create" wire:loading.attr="disabled" wire:target="create" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition disabled:opacity-60">
            <span wire:loading.remove wire:target="create">+ Tambah Setting</span>
            <span wire:loading wire:target="create">Membuka...</span>
        </button>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div wire:loading.block wire:target="delete" class="p-4">
            <x-admin.skeleton.table :columns="4" :rows="6" :key-prefix="'settings-table'" :show-actions="true" />
        </div>

        <div wire:loading.remove wire:target="delete">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Key</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Value</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Type</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($settings as $item)
                    <tr wire:key="setting-row-{{ $item->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-4 py-3 font-mono text-sm text-slate-800 dark:text-white">{{ $item->key }}</td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 truncate max-w-xs">{{ Str::limit($item->value, 60) }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ $item->type }}</span></td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <button wire:click="edit({{ $item->id }})" wire:loading.attr="disabled" wire:target="edit({{ $item->id }})" class="px-3 py-1 text-xs font-medium bg-yellow-50 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 rounded-lg hover:bg-yellow-100 transition disabled:opacity-60">
                                <span wire:loading.remove wire:target="edit({{ $item->id }})">Edit</span>
                                <span wire:loading wire:target="edit({{ $item->id }})">Memuat...</span>
                            </button>
                            <button @click="openConfirm('delete', {{ $item->id }}, 'Hapus setting ini?', 'Konfigurasi setting akan dihapus permanen.', 'Ya, Hapus', 'warning')" class="px-3 py-1 text-xs font-medium bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-100 transition">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada setting.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="relative bg-white dark:bg-slate-900 rounded-xl shadow-xl w-full max-w-md mx-4 p-6 border border-slate-200 dark:border-slate-800">
            <div wire:loading.flex wire:target="save" class="absolute inset-0 z-10 items-center justify-center rounded-xl bg-white/75 dark:bg-slate-900/75">
                <div class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">Menyimpan setting...</div>
            </div>

            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">{{ $editId ? 'Edit' : 'Tambah' }} Setting</h3>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Key *</label>
                    <input wire:model="key" type="text" placeholder="site_name" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-mono">
                    @error('key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Type</label>
                    <select wire:model="type" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="string">String</option>
                        <option value="boolean">Boolean</option>
                        <option value="json">JSON</option>
                        <option value="image">Image Path</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Value</label>
                    <textarea wire:model="value" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-mono"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showModal', false)" wire:loading.attr="disabled" wire:target="save" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition disabled:opacity-60">Batal</button>
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
