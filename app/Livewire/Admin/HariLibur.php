<?php

namespace App\Livewire\Admin;

use App\Models\SchoolHoliday;
use App\Support\SchoolHolidayWorkflow;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

#[Layout('components.layouts.admin')]
#[Title('Hari Libur')]
class HariLibur extends Component
{
    use WithPagination;

    public ?int $editingHolidayId = null;
    public ?int $deletingHolidayId = null;
    public bool $isHolidayModalOpen = false;
    public bool $isDeleteHolidayModalOpen = false;
    public string $deletingHolidayName = '';
    public array $holidayForm = [];
    public string $search = '';
    public string $filterStatus = 'all';
    public string $filterType = 'all';
    public string $calendarMonth = '';

    public function mount(): void
    {
        $this->calendarMonth = now()->startOfMonth()->format('Y-m');
        $this->resetHolidayForm();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatedCalendarMonth(): void
    {
        $this->calendarMonth = $this->resolveCalendarMonth()->format('Y-m');
    }

    public function previousCalendarMonth(): void
    {
        $this->calendarMonth = $this->resolveCalendarMonth()->subMonthNoOverflow()->format('Y-m');
    }

    public function nextCalendarMonth(): void
    {
        $this->calendarMonth = $this->resolveCalendarMonth()->addMonthNoOverflow()->format('Y-m');
    }

    public function jumpToCurrentCalendarMonth(): void
    {
        $this->calendarMonth = now()->startOfMonth()->format('Y-m');
    }

    public function setHolidayDateFromCalendar(string $date): void
    {
        $normalizedDate = $this->normalizeHolidayDate($date);

        if ($normalizedDate === null) {
            return;
        }

        $this->openHolidayModal($normalizedDate);
    }

    public function openHolidayModal(?string $date = null): void
    {
        $this->resetHolidayForm();

        $normalizedDate = $this->normalizeHolidayDate($date);

        if ($normalizedDate !== null) {
            $this->holidayForm['holiday_date'] = $normalizedDate;
        }

        $this->isHolidayModalOpen = true;
    }

    public function closeHolidayModal(): void
    {
        $this->isHolidayModalOpen = false;
        $this->resetHolidayForm();
    }

    public function saveHoliday(): void
    {
        $validated = $this->validate([
            'holidayForm.holiday_date' => ['required', 'date'],
            'holidayForm.name' => ['required', 'string', 'max:255'],
            'holidayForm.description' => ['nullable', 'string', 'max:2000'],
            'holidayForm.type' => ['required', Rule::in(['manual', 'emergency'])],
            'holidayForm.is_active' => ['boolean'],
        ]);

        $payload = $validated['holidayForm'];
        $payload['is_active'] = (bool) ($payload['is_active'] ?? false);

        $description = trim((string) ($payload['description'] ?? ''));
        $payload['description'] = $description !== '' ? $description : null;

        if ($this->editingHolidayId) {
            $holiday = SchoolHoliday::findOrFail($this->editingHolidayId);
            $holiday->update($payload);

            $this->dispatch('toast', type: 'success', message: 'Libur manual berhasil diperbarui.');
        } else {
            $payload['created_by'] = auth()->id();
            SchoolHoliday::create($payload);

            $this->dispatch('toast', type: 'success', message: 'Libur manual berhasil ditambahkan.');
        }

        $this->isHolidayModalOpen = false;
        $this->resetHolidayForm();
    }

    public function editHoliday(int $holidayId): void
    {
        $holiday = SchoolHoliday::findOrFail($holidayId);

        $this->editingHolidayId = $holiday->id;
        $this->holidayForm = [
            'holiday_date' => $holiday->holiday_date?->format('Y-m-d') ?? '',
            'name' => $holiday->name,
            'description' => $holiday->description ?? '',
            'type' => $holiday->type,
            'is_active' => (bool) $holiday->is_active,
        ];

        $this->isHolidayModalOpen = true;
    }

    public function cancelHolidayEdit(): void
    {
        $this->closeHolidayModal();
    }

    public function openDeleteHolidayModal(int $holidayId): void
    {
        $holiday = SchoolHoliday::query()
            ->select(['id', 'name'])
            ->findOrFail($holidayId);

        $this->deletingHolidayId = $holiday->id;
        $this->deletingHolidayName = $holiday->name;
        $this->isDeleteHolidayModalOpen = true;
    }

    public function closeDeleteHolidayModal(): void
    {
        $this->isDeleteHolidayModalOpen = false;
        $this->deletingHolidayId = null;
        $this->deletingHolidayName = '';
    }

    public function toggleHolidayStatus(int $holidayId): void
    {
        $holiday = SchoolHoliday::findOrFail($holidayId);
        $holiday->update(['is_active' => ! $holiday->is_active]);

        if ($this->editingHolidayId === $holiday->id) {
            $this->holidayForm['is_active'] = (bool) $holiday->is_active;
        }

        $this->dispatch('toast', type: 'success', message: $holiday->is_active
            ? 'Libur manual diaktifkan kembali.'
            : 'Libur manual dinonaktifkan.');
    }

    public function deleteHoliday(int $holidayId): void
    {
        $holiday = SchoolHoliday::findOrFail($holidayId);
        $label = $holiday->name;
        $holiday->delete();

        if ($this->editingHolidayId === $holidayId) {
            $this->closeHolidayModal();
        }

        if ($this->deletingHolidayId === $holidayId) {
            $this->closeDeleteHolidayModal();
        }

        $this->dispatch('toast', type: 'success', message: 'Libur manual "' . $label . '" berhasil dihapus.');
    }

    public function triggerHolidayCheck(): void
    {
        $result = app(SchoolHolidayWorkflow::class)->checkTomorrowAndNotifyAdmins(true);
        $status = (string) ($result['status'] ?? 'unknown');
        $message = (string) ($result['message'] ?? 'Pengecekan libur selesai dijalankan.');

        $toastType = match ($status) {
            'pending-sent' => 'success',
            'no-admin-chat' => 'error',
            default => 'info',
        };

        $this->dispatch('toast', type: $toastType, message: $message);
    }

    public function render()
    {
        $selectedMonth = $this->resolveCalendarMonth();
        $calendarStart = $selectedMonth->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $selectedMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $calendarHolidayItems = SchoolHoliday::query()
            ->whereDate('holiday_date', '>=', $calendarStart->toDateString())
            ->whereDate('holiday_date', '<=', $calendarEnd->toDateString())
            ->orderBy('holiday_date')
            ->orderBy('id')
            ->get();

        $manualHolidayGroups = $calendarHolidayItems
            ->groupBy(fn (SchoolHoliday $holiday) => $holiday->holiday_date?->toDateString() ?? '')
            ->map(function (Collection $holidays): array {
                return $holidays
                    ->map(fn (SchoolHoliday $holiday): array => [
                        'id' => $holiday->id,
                        'name' => $holiday->name,
                        'type' => $holiday->type,
                        'source' => 'manual',
                        'is_active' => (bool) $holiday->is_active,
                    ])
                    ->values()
                    ->all();
            });

        $apiHolidayGroups = $this->fetchApiHolidayGroupsForCalendarRange($calendarStart, $calendarEnd);

        $calendarDays = collect();
        $cursor = $calendarStart->copy();

        while ($cursor->lte($calendarEnd)) {
            $dateString = $cursor->toDateString();
            $holidaysForDate = collect($apiHolidayGroups->get($dateString, []))
                ->merge($manualHolidayGroups->get($dateString, []))
                ->values();

            $apiEventCount = $holidaysForDate->where('source', 'api')->count();
            $manualEventCount = $holidaysForDate->where('source', 'manual')->count();

            $calendarDays->push([
                'date_string' => $dateString,
                'day_number' => (int) $cursor->format('j'),
                'is_current_month' => $cursor->month === $selectedMonth->month && $cursor->year === $selectedMonth->year,
                'is_today' => $cursor->isToday(),
                'is_weekend' => $cursor->isWeekend(),
                'holiday_count' => $holidaysForDate->count(),
                'api_event_count' => $apiEventCount,
                'manual_event_count' => $manualEventCount,
                'holidays' => $holidaysForDate->all(),
            ]);

            $cursor->addDay();
        }

        $calendarWeeks = $calendarDays->chunk(7)->values();

        $selectedMonthPrefix = $selectedMonth->format('Y-m-');
        $manualEntriesInSelectedMonth = $calendarHolidayItems
            ->filter(fn (SchoolHoliday $holiday): bool =>
                str_starts_with((string) optional($holiday->holiday_date)->toDateString(), $selectedMonthPrefix));

        $apiEntriesInSelectedMonth = $apiHolidayGroups
            ->filter(fn (array $events, string $date): bool => str_starts_with($date, $selectedMonthPrefix));

        $manualDateKeys = $manualEntriesInSelectedMonth
            ->pluck('holiday_date')
            ->filter()
            ->map(fn (Carbon $date) => $date->toDateString())
            ->unique();

        $apiDateKeys = $apiEntriesInSelectedMonth->keys();

        $calendarStats = [
            'month_label' => $selectedMonth->translatedFormat('F Y'),
            'entry_count' => $manualEntriesInSelectedMonth->count(),
            'active_count' => $manualEntriesInSelectedMonth->where('is_active', true)->count(),
            'holiday_dates_count' => $manualDateKeys->merge($apiDateKeys)->unique()->count(),
            'api_event_count' => $apiEntriesInSelectedMonth
                ->map(fn (array $events): int => count($events))
                ->sum(),
        ];

        $manualHolidays = SchoolHoliday::query()
            ->when($this->search !== '', function ($query): void {
                $term = '%' . trim($this->search) . '%';
                $query->where(function ($subQuery) use ($term): void {
                    $subQuery->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->when($this->filterStatus === 'active', fn ($query) => $query->where('is_active', true))
            ->when($this->filterStatus === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($this->filterType === 'manual', fn ($query) => $query->where('type', 'manual'))
            ->when($this->filterType === 'emergency', fn ($query) => $query->where('type', 'emergency'))
            ->orderByDesc('holiday_date')
            ->orderByDesc('id')
            ->paginate(12);

        return view('livewire.admin.hari-libur', compact('manualHolidays', 'calendarWeeks', 'calendarStats'));
    }

    protected function resetHolidayForm(): void
    {
        $this->editingHolidayId = null;
        $this->holidayForm = [
            'holiday_date' => now()->addDay()->toDateString(),
            'name' => '',
            'description' => '',
            'type' => 'manual',
            'is_active' => true,
        ];

        $this->resetValidation();
    }

    protected function resolveCalendarMonth(): Carbon
    {
        if (preg_match('/^(?<year>\d{4})-(?<month>\d{2})$/', trim($this->calendarMonth), $matches) !== 1) {
            return now()->startOfMonth();
        }

        $year = (int) $matches['year'];
        $month = (int) $matches['month'];

        if ($year < 2000 || $year > 2100 || $month < 1 || $month > 12) {
            return now()->startOfMonth();
        }

        return Carbon::create($year, $month, 1)->startOfMonth();
    }

    protected function normalizeHolidayDate(?string $date): ?string
    {
        $value = trim((string) ($date ?? ''));

        if ($value === '' || preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) !== 1) {
            return null;
        }

        try {
            $parsedDate = Carbon::createFromFormat('Y-m-d', $value);
        } catch (Throwable) {
            return null;
        }

        if ($parsedDate === false || $parsedDate->format('Y-m-d') !== $value) {
            return null;
        }

        return $value;
    }

    protected function fetchApiHolidayGroupsForCalendarRange(Carbon $calendarStart, Carbon $calendarEnd): Collection
    {
        $monthCursor = $calendarStart->copy()->startOfMonth();
        $lastMonth = $calendarEnd->copy()->startOfMonth();
        $grouped = collect();

        while ($monthCursor->lte($lastMonth)) {
            $monthlyGroups = $this->fetchApiHolidayGroupsForMonth($monthCursor);

            foreach ($monthlyGroups as $date => $events) {
                $combined = collect($grouped->get($date, []))
                    ->merge($events)
                    ->unique(fn (array $event): string => (string) ($event['name'] ?? ''))
                    ->values()
                    ->all();

                $grouped->put($date, $combined);
            }

            $monthCursor->addMonthNoOverflow();
        }

        return $grouped;
    }

    protected function fetchApiHolidayGroupsForMonth(Carbon $month): Collection
    {
        if (! (bool) config('services.school_calendar.enabled', true)) {
            return collect();
        }

        $apiBase = rtrim((string) config('services.school_calendar.api_url', 'https://libur.deno.dev/api'), '/');

        if ($apiBase === '') {
            return collect();
        }

        $monthKey = $month->format('Y-m');

        try {
            $apiResponse = Cache::remember('school-calendar:id:month:' . $monthKey, now()->addHours(6), function () use ($apiBase, $month) {
                return Http::acceptJson()
                    ->timeout(20)
                    ->get($apiBase, [
                        'year' => (int) $month->format('Y'),
                        'month' => (int) $month->format('n'),
                    ])
                    ->throw()
                    ->json();
            });
        } catch (Throwable $exception) {
            Log::warning('Failed fetching school calendar monthly holiday API.', [
                'month' => $monthKey,
                'message' => $exception->getMessage(),
            ]);

            return collect();
        }

        $events = collect();

        if (is_array($apiResponse) && array_key_exists('is_holiday', $apiResponse)) {
            $date = (string) data_get($apiResponse, 'date', '');

            if ((bool) data_get($apiResponse, 'is_holiday', false) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1) {
                $events = collect(data_get($apiResponse, 'holiday_list', []))
                    ->map(fn ($eventName): array => [
                        'date' => $date,
                        'name' => trim((string) $eventName),
                    ])
                    ->filter(fn (array $event): bool => $event['name'] !== '');
            }
        } elseif (is_array($apiResponse)) {
            $events = collect($apiResponse)
                ->filter(fn ($event): bool => is_array($event))
                ->map(fn (array $event): array => [
                    'date' => (string) data_get($event, 'date', ''),
                    'name' => trim((string) data_get($event, 'name', '')),
                ])
                ->filter(fn (array $event): bool =>
                    preg_match('/^\d{4}-\d{2}-\d{2}$/', $event['date']) === 1 && $event['name'] !== '');
        }

        return $events
            ->groupBy('date')
            ->map(function (Collection $eventsOnDate, string $date): array {
                return $eventsOnDate
                    ->pluck('name')
                    ->filter()
                    ->unique()
                    ->values()
                    ->map(fn (string $eventName, int $index): array => [
                        'id' => 'api-' . $date . '-' . $index,
                        'name' => $eventName,
                        'type' => 'api',
                        'source' => 'api',
                        'is_active' => true,
                    ])
                    ->all();
            });
    }
}
