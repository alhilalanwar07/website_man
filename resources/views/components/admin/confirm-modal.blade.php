<div
    x-cloak
    x-show="confirmOpen"
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
    @click="closeConfirm()"
    @keydown.escape.window="closeConfirm()"
>
    <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900" @click.stop>
        <div class="flex items-start gap-4 border-b border-slate-100 p-6 dark:border-slate-800">
            <div
                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                :class="{
                    'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300': confirmTone === 'danger',
                    'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300': confirmTone === 'warning',
                    'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300': confirmTone === 'info'
                }"
            >
                <template x-if="confirmTone === 'danger'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </template>
                <template x-if="confirmTone === 'warning'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                </template>
                <template x-if="confirmTone === 'info'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </template>
            </div>
            <div class="min-w-0">
                <h3 class="text-lg font-black text-slate-900 dark:text-white" x-text="confirmTitle"></h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300" x-text="confirmMessage"></p>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 rounded-b-2xl bg-slate-50 p-5 dark:bg-slate-800/60">
            <button type="button" @click="closeConfirm()" class="rounded-xl px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700">
                Batal
            </button>
            <button
                type="button"
                @click="runConfirm()"
                class="rounded-xl px-5 py-2.5 text-sm font-bold text-white transition"
                :class="{
                    'bg-rose-600 hover:bg-rose-700': confirmTone === 'danger',
                    'bg-amber-600 hover:bg-amber-700': confirmTone === 'warning',
                    'bg-blue-600 hover:bg-blue-700': confirmTone === 'info'
                }"
            >
                <span x-text="confirmLabel"></span>
            </button>
        </div>
    </div>
</div>
