@props([
    'cards' => 4,
    'columns' => 'grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4',
    'keyPrefix' => 'skeleton-card-grid',
])

@php($cardsCount = max((int) $cards, 1))

<div {{ $attributes->class([$columns, 'animate-pulse']) }}>
    @for($cardIndex = 1; $cardIndex <= $cardsCount; $cardIndex++)
        <div wire:key="{{ $keyPrefix }}-card-{{ $cardIndex }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="h-3 w-28 rounded bg-slate-200 dark:bg-slate-700"></div>
            <div class="mt-3 h-8 w-24 rounded bg-slate-200 dark:bg-slate-700"></div>
            <div class="mt-3 h-3 w-11/12 rounded bg-slate-100 dark:bg-slate-800"></div>
        </div>
    @endfor
</div>
