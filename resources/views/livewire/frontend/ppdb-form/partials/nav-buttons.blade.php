{{-- Navigation Buttons Partial --}}
<div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-4">
    @if($showPrev ?? false)
        <button type="button" wire:click="previousStep" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 focus:ring-2 focus:ring-slate-200 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali
        </button>
    @else
        <div class="hidden sm:block"></div>
    @endif

    @if($showNext ?? false)
        <button type="button" wire:click="nextStep" wire:loading.attr="disabled" wire:target="nextStep" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/20 transition-all shadow-lg shadow-blue-500/25 disabled:opacity-50">
            <span wire:loading.remove wire:target="nextStep">Lanjutkan</span>
            <span wire:loading wire:target="nextStep">Memproses...</span>
            <svg wire:loading.remove wire:target="nextStep" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
    @endif
</div>
