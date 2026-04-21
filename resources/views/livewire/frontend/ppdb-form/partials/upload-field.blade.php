{{-- Upload Field Partial with Alpine.js Client-Side Preview --}}
@php
    $fieldId = 'upload_' . str_replace(['.', ' '], '_', $wireModel);
    $hasExisting = !empty($fileVar);
    $existingName = $hasExisting ? $fileVar->getClientOriginalName() : '';
    $existingSize = $hasExisting ? number_format($fileVar->getSize() / 1024, 0) : '';
    $existingPreview = null;
    if ($hasExisting && !($isPdf ?? true)) {
        try { $existingPreview = $fileVar->temporaryUrl(); } catch (\Throwable $e) { $existingPreview = null; }
    }
@endphp

<div
    id="{{ $fieldId }}"
    wire:key="{{ $fieldId }}"
    x-data="{
        preview: @js($existingPreview),
        name: @js($hasExisting ? $existingName : null),
        size: @js($hasExisting ? $existingSize : null),
        hasFile: @js($hasExisting),
        isPdf: @js($isPdf ?? true),
        uploading: false,
        pickFile() {
            this.$refs.{{ $fieldId }}_input.click();
        },
        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.name = file.name;
            this.size = (file.size / 1024).toFixed(0);
            this.hasFile = true;
            this.uploading = true;
            if (!this.isPdf && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (ev) => { this.preview = ev.target.result; };
                reader.readAsDataURL(file);
            } else {
                this.preview = null;
            }
        }
    }"
    x-on:livewire-upload-finish.window="uploading = false"
    x-on:livewire-upload-error.window="uploading = false"
>
    <label class="block text-sm font-semibold text-slate-700 mb-2">
        {{ $label }} @if($required ?? false)<span class="text-red-500">*</span>@endif
    </label>

    {{-- Hidden file input (always present for wire:model binding) --}}
    <input
        wire:model="{{ $wireModel }}"
        x-ref="{{ $fieldId }}_input"
        type="file"
        accept="{{ $accept ?? '.pdf' }}"
        class="sr-only"
        @change="onFileChange($event)"
    >

    {{-- Preview state (file selected) --}}
    <div x-show="hasFile" x-cloak class="rounded-xl border-2 border-emerald-200 bg-emerald-50/50 p-4 space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2 min-w-0">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-emerald-800 truncate max-w-[180px] sm:max-w-[240px]" x-text="name"></p>
                    <p class="text-[10px] text-emerald-600"><span x-text="size"></span> KB</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                {{-- Uploading indicator --}}
                <span x-show="uploading" x-cloak class="inline-flex items-center gap-1.5 text-[10px] font-bold text-blue-600">
                    <svg class="animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Mengunggah...
                </span>
                <button type="button" @click="pickFile()" class="inline-flex text-[10px] font-bold text-blue-600 hover:text-blue-700 cursor-pointer transition">
                    Ganti
                </button>
            </div>
        </div>

        {{-- Image Preview (client-side via FileReader) --}}
        <template x-if="!isPdf && preview">
            <div class="rounded-lg overflow-hidden border border-emerald-200 bg-white">
                <img :src="preview" alt="Preview" class="w-full max-h-48 object-contain">
            </div>
        </template>

        {{-- PDF indicator --}}
        <template x-if="isPdf && hasFile">
            <div class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg border border-emerald-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <span class="text-[11px] font-medium text-slate-600">Dokumen PDF siap diunggah</span>
            </div>
        </template>
    </div>

    {{-- Empty state (no file selected) --}}
    <div x-show="!hasFile" class="cursor-pointer" @click="pickFile()">
        <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 py-6 px-4 hover:border-blue-300 hover:bg-blue-50/30 transition-all group">
            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center mb-2 group-hover:bg-blue-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400 group-hover:text-blue-500 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            </div>
            <span class="text-xs font-semibold text-slate-500 group-hover:text-blue-600 transition">Pilih file</span>
            @if($hint ?? '')
                <span class="text-[10px] text-slate-400 mt-0.5">{{ $hint }}</span>
            @endif
        </div>
    </div>

    {{-- Global upload loading (wire:loading) --}}
    <div wire:loading wire:target="{{ $wireModel }}" class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-blue-600">
        <svg class="animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
        Sedang mengunggah ke server...
    </div>

    @error($wireModel) <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>
