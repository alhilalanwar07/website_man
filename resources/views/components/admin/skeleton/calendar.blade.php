@props([
    'days' => 42,
    'keyPrefix' => 'skeleton-calendar',
])

@php($dayCount = max((int) $days, 7))

<div {{ $attributes->class(['grid grid-cols-7 gap-2 p-4 animate-pulse']) }}>
    @for($headIndex = 1; $headIndex <= 7; $headIndex++)
        <div wire:key="{{ $keyPrefix }}-head-{{ $headIndex }}" class="h-8 rounded-lg bg-slate-200 dark:bg-slate-800"></div>
    @endfor

    @for($dayIndex = 1; $dayIndex <= $dayCount; $dayIndex++)
        <div wire:key="{{ $keyPrefix }}-day-{{ $dayIndex }}" class="h-28 rounded-lg border border-slate-200 bg-white p-2 dark:border-slate-700 dark:bg-slate-900">
            <div class="h-4 w-8 rounded bg-slate-200 dark:bg-slate-800"></div>
            <div class="mt-2 space-y-1.5">
                <div class="h-3 w-full rounded bg-slate-100 dark:bg-slate-800/70"></div>
                <div class="h-3 w-5/6 rounded bg-slate-100 dark:bg-slate-800/70"></div>
            </div>
        </div>
    @endfor
</div>
