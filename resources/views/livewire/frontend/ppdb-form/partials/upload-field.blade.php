{{-- Upload Field Partial with Preview --}}
@props(['wireModel', 'label', 'required' => false, 'accept' => '.pdf', 'hint' => '', 'fileVar' => null, 'isPdf' => true])

<div>
    <label class="block text-sm font-semibold text-slate-700 mb-2">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>

    @if($fileVar)
        {{-- Preview state --}}
        <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50/50 p-4 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-emerald-800 truncate max-w-[140px] sm:max-w-[220px]">{{ $fileVar->getClientOriginalName() }}</p>
                        <p class="text-[10px] text-emerald-600">{{ number_format($fileVar->getSize() / 1024, 0) }} KB</p>
                    </div>
                </div>
                <label class="inline-flex text-[10px] font-bold text-blue-600 hover:text-blue-700 cursor-pointer transition">
                    Ganti
                    <input wire:model="{{ $wireModel }}" type="file" accept="{{ $accept }}" class="sr-only">
                </label>
            </div>

            {{-- Image Preview --}}
            @if(!$isPdf && method_exists($fileVar, 'temporaryUrl'))
                <div class="rounded-lg overflow-hidden border border-emerald-200 bg-white">
                    <img src="{{ $fileVar->temporaryUrl() }}" alt="Preview" class="w-full max-h-48 object-contain">
                </div>
            @endif

            {{-- PDF indicator --}}
            @if($isPdf)
                <div class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg border border-emerald-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span class="text-[11px] font-medium text-slate-600">Dokumen PDF siap diunggah</span>
                </div>
            @endif
        </div>
    @else
        {{-- Empty state --}}
        <label class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 py-6 px-4 cursor-pointer hover:border-blue-300 hover:bg-blue-50/30 transition-all group">
            <input wire:model="{{ $wireModel }}" type="file" accept="{{ $accept }}" class="sr-only">
            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center mb-2 group-hover:bg-blue-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400 group-hover:text-blue-500 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            </div>
            <span class="text-xs font-semibold text-slate-500 group-hover:text-blue-600 transition">Pilih file</span>
            @if($hint)
                <span class="text-[10px] text-slate-400 mt-0.5">{{ $hint }}</span>
            @endif
        </label>
    @endif

    @error($wireModel) <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>
