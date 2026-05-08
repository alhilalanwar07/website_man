{{-- Pamflet/Flyer Modal - Shows promotional pamphlet for active PPDB period --}}
{{-- Conditionally rendered based on active period having uploaded pamflets --}}
@php
    $pamfletPeriod = \App\Models\PpdbPeriod::query()
        ->where('is_active', true)
        ->where('status', '!=', 'archived')
        ->where(function($q) {
            $q->whereNotNull('pamflet_desktop')
              ->orWhereNotNull('pamflet_mobile');
        })
        ->first();
@endphp

@if($pamfletPeriod)
    @php
        $desktopToken = $pamfletPeriod->pamflet_desktop
            ? \App\Http\Controllers\PamfletController::generateToken($pamfletPeriod->id, 'desktop')
            : null;
        $mobileToken = $pamfletPeriod->pamflet_mobile
            ? \App\Http\Controllers\PamfletController::generateToken($pamfletPeriod->id, 'mobile')
            : null;
        $storageKey = 'ppdb_pamflet_dismissed_' . $pamfletPeriod->id;
    @endphp

    <div
        x-data="{
            open: false,
            dismissed: false,
            dontShowAgain: false,
            imageLoaded: false,
            storageKey: '{{ $storageKey }}',
            hasDesktop: {{ $desktopToken ? 'true' : 'false' }},
            hasMobile: {{ $mobileToken ? 'true' : 'false' }},
            isMobileDevice: false,
            init() {
                try {
                    this.dismissed = localStorage.getItem(this.storageKey) === '1';
                } catch(e) {}

                if (this.dismissed) return;

                this.detectDevice();
                window.addEventListener('resize', () => this.detectDevice());

                this.$nextTick(() => {
                    setTimeout(() => {
                        if (!this.dismissed && this.hasApplicableImage()) {
                            this.open = true;
                        }
                    }, 800);
                });
            },
            detectDevice() {
                this.isMobileDevice = window.innerWidth < 768;
            },
            hasApplicableImage() {
                if (this.isMobileDevice) return this.hasMobile || this.hasDesktop;
                return this.hasDesktop || this.hasMobile;
            },
            onImageLoad() {
                this.imageLoaded = true;
            },
            close() {
                this.open = false;
                if (this.dontShowAgain) {
                    try {
                        localStorage.setItem(this.storageKey, '1');
                    } catch(e) {}
                }
            }
        }"
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6"
        style="display: none;"
        @keydown.escape.window="close()"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"
            @click="close()"
        ></div>

        {{-- Modal Content --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300 delay-100"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative max-h-[90vh] w-full overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-slate-900"
            :class="isMobileDevice ? 'max-w-sm' : 'max-w-3xl'"
            @click.stop
        >
            {{-- Close button (always visible) --}}
            <button
                @click="close()"
                class="absolute right-3 top-3 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/60 text-white transition hover:bg-slate-900/80 backdrop-blur-sm"
                aria-label="Tutup"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Loading skeleton (visible while image loads) --}}
            <div x-show="!imageLoaded" class="p-6">
                <div class="flex flex-col items-center gap-4">
                    {{-- Shimmer placeholder --}}
                    <div class="w-full overflow-hidden rounded-xl" :class="isMobileDevice ? 'aspect-[3/4]' : 'aspect-[16/9]'">
                        <div class="h-full w-full animate-pulse bg-gradient-to-r from-slate-200 via-slate-100 to-slate-200 dark:from-slate-700 dark:via-slate-600 dark:to-slate-700" style="background-size: 200% 100%; animation: shimmer 1.5s infinite;">
                        </div>
                    </div>
                    {{-- Loading text --}}
                    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span>Memuat pamflet...</span>
                    </div>
                </div>
            </div>

            {{-- Image Container (hidden until loaded, then fade in) --}}
            <div
                x-show="imageLoaded"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="overflow-auto"
                style="max-height: calc(90vh - 60px);"
            >
                @if($desktopToken && $mobileToken)
                    {{-- Both versions available - show based on device --}}
                    <img
                        x-show="!isMobileDevice"
                        src="{{ route('pamflet.show', $desktopToken) }}"
                        alt="Pamflet PPDB {{ $pamfletPeriod->nama_periode }}"
                        class="w-full object-contain"
                        loading="eager"
                        @load="onImageLoad()"
                    >
                    <img
                        x-show="isMobileDevice"
                        src="{{ route('pamflet.show', $mobileToken) }}"
                        alt="Pamflet PPDB {{ $pamfletPeriod->nama_periode }}"
                        class="w-full object-contain"
                        loading="eager"
                        @load="onImageLoad()"
                    >
                @elseif($desktopToken)
                    <img
                        src="{{ route('pamflet.show', $desktopToken) }}"
                        alt="Pamflet PPDB {{ $pamfletPeriod->nama_periode }}"
                        class="w-full object-contain"
                        loading="eager"
                        @load="onImageLoad()"
                    >
                @elseif($mobileToken)
                    <img
                        src="{{ route('pamflet.show', $mobileToken) }}"
                        alt="Pamflet PPDB {{ $pamfletPeriod->nama_periode }}"
                        class="w-full object-contain"
                        loading="eager"
                        @load="onImageLoad()"
                    >
                @endif
            </div>

            {{-- Footer with "Don't show again" — only visible after image loads --}}
            <div
                x-show="imageLoaded"
                x-transition:enter="transition ease-out duration-400 delay-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-800/60"
            >
                <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        x-model="dontShowAgain"
                        class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                    >
                    Jangan tampilkan lagi
                </label>
                <button
                    @click="close()"
                    class="rounded-lg bg-blue-600 px-4 py-1.5 text-xs font-bold text-white transition hover:bg-blue-700"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- Shimmer keyframes --}}
    <style>
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
@endif
