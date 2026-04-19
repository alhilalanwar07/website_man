<div
    x-data="{
        draftKey: 'ppdb-form-draft-v1',
        draftSaveTimer: null,
        showDraftRestoredNotice: false,
        draftSaveAt: null,
        isDraftRestoreInProgress: false,
        scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' })
        },
        initDraft() {
            this.restoreDraftFromStorage()
        },
        resolveModelName(element) {
            const modelAttribute = element.getAttributeNames().find((attribute) => attribute.startsWith('wire:model'))
            return modelAttribute ? element.getAttribute(modelAttribute) : null
        },
        collectDraftData() {
            const form = this.$refs.ppdbForm

            if (!form) {
                return {}
            }

            const draftData = {}
            const fields = form.querySelectorAll('input, select, textarea')

            fields.forEach((field) => {
                const modelName = this.resolveModelName(field)

                if (!modelName || field.disabled || field.type === 'file') {
                    return
                }

                if (field.type === 'radio') {
                    if (field.checked) {
                        draftData[modelName] = field.value
                    }

                    return
                }

                if (field.type === 'checkbox') {
                    draftData[modelName] = field.checked
                    return
                }

                draftData[modelName] = field.value
            })

            draftData.currentStep = Number(this.$wire.currentStep ?? 1)
            draftData.selectedPeriod = String(this.$wire.selectedPeriod ?? '')

            return draftData
        },
        scheduleDraftSave() {
            if (this.draftSaveTimer !== null) {
                clearTimeout(this.draftSaveTimer)
            }

            this.draftSaveTimer = setTimeout(() => {
                this.saveDraftToStorage()
            }, 450)
        },
        saveDraftToStorage() {
            if (this.isDraftRestoreInProgress || !this.$refs.ppdbForm) {
                return
            }

            const data = this.collectDraftData()

            if (Object.keys(data).length === 0) {
                return
            }

            try {
                const payload = {
                    savedAt: Date.now(),
                    data,
                }

                localStorage.setItem(this.draftKey, JSON.stringify(payload))
                this.draftSaveAt = payload.savedAt
            } catch (error) {
                console.warn('Gagal menyimpan draft PPDB di browser.', error)
            }
        },
        restoreDraftFromStorage() {
            const raw = localStorage.getItem(this.draftKey)

            if (!raw) {
                return
            }

            let parsed

            try {
                parsed = JSON.parse(raw)
            } catch (error) {
                return
            }

            if (!parsed || typeof parsed !== 'object' || typeof parsed.data !== 'object') {
                return
            }

            this.isDraftRestoreInProgress = true

            this.$wire.applyDraft(parsed.data)
                .then(() => {
                    this.showDraftRestoredNotice = true
                    this.draftSaveAt = parsed.savedAt ?? null
                    setTimeout(() => {
                        this.showDraftRestoredNotice = false
                    }, 3500)
                })
                .catch(() => {})
                .finally(() => {
                    this.isDraftRestoreInProgress = false
                })
        },
        clearDraftStorage() {
            localStorage.removeItem(this.draftKey)
            this.draftSaveAt = null
        },
    }"
    x-init="initDraft()"
    x-effect="if ($wire.submittedNumber) { clearDraftStorage() }"
    x-on:input.debounce.500ms="scheduleDraftSave()"
    x-on:change.debounce.300ms="scheduleDraftSave()"
    x-on:step-changed.window="scrollToTop(); scheduleDraftSave()"
    x-on:ppdb-draft-clear.window="clearDraftStorage()"
>

    {{-- ═══════ PERIOD SELECTOR (top bar) ═══════ --}}
    @if(!$submittedNumber)
    <div class="bg-white border-b border-slate-200/60">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span class="font-semibold">Periode:</span>
            </div>
            <select wire:model.live="selectedPeriod" class="flex-1 max-w-xs rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-700 outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400/30">
                @foreach($availablePeriods as $periodOption)
                    <option wire:key="period-opt-{{ $periodOption->id }}" value="{{ $periodOption->id }}">{{ $periodOption->full_label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @endif

    {{-- ═══════ STEPPER (progress indicator) ═══════ --}}
    @if(!$submittedNumber)
    <div class="bg-white border-b border-slate-200/60 sticky top-16 z-40">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-4">
            {{-- Progress bar --}}
            <div class="h-1 bg-slate-100 rounded-full overflow-hidden mb-4">
                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all duration-500 ease-out" style="width: {{ $this->progressPercent }}%"></div>
            </div>

            {{-- Step indicators --}}
            <div class="flex items-center justify-between">
                @foreach(self::STEP_LABELS as $stepNum => $label)
                    <button
                        wire:key="step-btn-{{ $stepNum }}"
                        wire:click="goToStep({{ $stepNum }})"
                        @class([
                            'flex flex-col items-center gap-1.5 group transition-all',
                            'cursor-pointer' => $stepNum < $currentStep,
                            'cursor-default' => $stepNum >= $currentStep,
                        ])
                        @if($stepNum > $currentStep) disabled @endif
                    >
                        <div @class([
                            'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300',
                            'bg-blue-600 text-white shadow-lg shadow-blue-500/30 scale-110' => $stepNum === $currentStep,
                            'bg-emerald-500 text-white' => $stepNum < $currentStep,
                            'bg-slate-100 text-slate-400' => $stepNum > $currentStep,
                        ])>
                            @if($stepNum < $currentStep)
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            @else
                                {{ $stepNum }}
                            @endif
                        </div>
                        <span @class([
                            'text-[10px] font-semibold leading-tight text-center hidden sm:block',
                            'text-blue-600' => $stepNum === $currentStep,
                            'text-emerald-600' => $stepNum < $currentStep,
                            'text-slate-400' => $stepNum > $currentStep,
                        ])>{{ $label }}</span>
                    </button>

                    @if(!$loop->last)
                        <div @class([
                            'flex-1 h-0.5 mx-1 rounded-full transition-colors duration-300',
                            'bg-emerald-400' => $stepNum < $currentStep,
                            'bg-slate-100' => $stepNum >= $currentStep,
                        ])></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════ MAIN FORM AREA ═══════ --}}
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

        @if(!$submittedNumber)
            <div x-show="showDraftRestoredNotice" x-cloak class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
                Draft pendaftaran terakhir berhasil dipulihkan di perangkat ini.
            </div>

            <div class="mb-4 rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-500">
                <p class="font-semibold text-slate-700">Auto-save aktif</p>
                <p class="mt-0.5">Data yang sudah diisi akan tersimpan otomatis di browser ini (kecuali file upload).</p>
                <p x-show="draftSaveAt" x-cloak class="mt-1 text-[11px] text-slate-400" x-text="`Terakhir disimpan: ${new Date(draftSaveAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}`"></p>
            </div>
        @endif

        @if($submittedNumber)
            {{-- ═══════ SUCCESS STATE ═══════ --}}
            @include('livewire.frontend.ppdb-form.step-success')
        @else
            @if(!$period)
                {{-- No period available --}}
                <div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-8 text-center">
                    <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Pendaftaran Belum Dibuka</h2>
                    <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">Periode PPDB belum dipublikasikan. Silakan kembali ke halaman informasi PPDB untuk jadwal terbaru.</p>
                    <a href="{{ route('ppdb.index') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition">Info PPDB</a>
                </div>
            @elseif(!$period->isRegistrationOpen())
                {{-- Period closed --}}
                <div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-8 text-center">
                    <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Pendaftaran Belum Dibuka</h2>
                    <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">Periode <strong>{{ $period->nama_periode }}</strong> saat ini belum menerima pendaftaran baru. Coba pilih gelombang lain atau hubungi panitia.</p>
                </div>
            @else
                <form wire:submit="submitApplication" x-ref="ppdbForm">
                    {{-- Step 1: Pernyataan --}}
                    <div x-show="$wire.currentStep === 1" x-cloak>
                        @include('livewire.frontend.ppdb-form.step-1-pernyataan')
                    </div>

                    {{-- Step 2: Data Pribadi --}}
                    <div x-show="$wire.currentStep === 2" x-cloak>
                        @include('livewire.frontend.ppdb-form.step-2-data-pribadi')
                    </div>

                    {{-- Step 3: Sekolah & Jurusan --}}
                    <div x-show="$wire.currentStep === 3" x-cloak>
                        @include('livewire.frontend.ppdb-form.step-3-sekolah-jurusan', ['period' => $period, 'programs' => $programs])
                    </div>

                    {{-- Step 4: Data Orang Tua --}}
                    <div x-show="$wire.currentStep === 4" x-cloak>
                        @include('livewire.frontend.ppdb-form.step-4-data-ortu')
                    </div>

                    {{-- Step 5: Prestasi --}}
                    <div x-show="$wire.currentStep === 5" x-cloak>
                        @include('livewire.frontend.ppdb-form.step-5-prestasi')
                    </div>

                    {{-- Step 6: Berkas --}}
                    <div x-show="$wire.currentStep === 6" x-cloak>
                        @include('livewire.frontend.ppdb-form.step-6-berkas')
                    </div>

                    {{-- Step 7: Konfirmasi --}}
                    <div x-show="$wire.currentStep === 7" x-cloak>
                        @include('livewire.frontend.ppdb-form.step-7-konfirmasi', ['period' => $period, 'programs' => $programs])
                    </div>
                </form>

                @error('period')
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700 font-medium">{{ $message }}</div>
                @enderror
            @endif
        @endif
    </div>
</div>