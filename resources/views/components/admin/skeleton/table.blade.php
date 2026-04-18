@props([
    'columns' => 5,
    'rows' => 6,
    'keyPrefix' => 'skeleton-table',
    'showActions' => true,
])

@php($columnsCount = max((int) $columns, 1))
@php($rowsCount = max((int) $rows, 1))

<div {{ $attributes->class(['overflow-x-auto p-4']) }}>
    <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700 animate-pulse">
        <thead class="bg-slate-50 dark:bg-slate-800/70">
            <tr>
                @foreach(range(1, $columnsCount) as $columnIndex)
                    <th wire:key="{{ $keyPrefix }}-head-{{ $columnIndex }}" class="px-4 py-3 text-left">
                        <div @class([
                            'h-3 rounded bg-slate-200 dark:bg-slate-700',
                            'w-12 ml-auto' => $columnIndex === $columnsCount,
                            'w-20' => $columnIndex !== $columnsCount,
                        ])></div>
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 bg-white dark:divide-slate-800 dark:bg-slate-900">
            @foreach(range(1, $rowsCount) as $rowIndex)
                <tr wire:key="{{ $keyPrefix }}-row-{{ $rowIndex }}">
                    @foreach(range(1, $columnsCount) as $columnIndex)
                        <td wire:key="{{ $keyPrefix }}-row-{{ $rowIndex }}-column-{{ $columnIndex }}" class="px-4 py-3">
                            @if((bool) $showActions && $columnIndex === $columnsCount)
                                <div class="flex justify-end gap-2">
                                    <div class="h-8 w-14 rounded-lg bg-slate-200 dark:bg-slate-800"></div>
                                    <div class="h-8 w-20 rounded-lg bg-slate-200 dark:bg-slate-800"></div>
                                    <div class="h-8 w-14 rounded-lg bg-slate-200 dark:bg-slate-800"></div>
                                </div>
                            @else
                                <div @class([
                                    'h-4 rounded bg-slate-200 dark:bg-slate-800',
                                    'w-24' => $columnIndex === 1,
                                    'w-40' => $columnIndex === 2,
                                    'w-20' => ! in_array($columnIndex, [1, 2], true),
                                ])></div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
