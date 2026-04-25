<?php

namespace App\Support;

use App\Models\PpdbApplication;
use App\Models\PpdbPeriod;
use Illuminate\Support\Facades\DB;

class PpdbNipdAllocator
{
    protected const ACCEPTED_SELECTION_STATUSES = ['passed', 'lulus'];

    public function assignIfEligible(PpdbApplication $application): ?int
    {
        if (! $this->shouldAssign($application)) {
            return $application->nipd ? (int) $application->nipd : null;
        }

        return DB::transaction(function () use ($application): ?int {
            $lockedApplication = PpdbApplication::query()
                ->lockForUpdate()
                ->find($application->id);

            if (! $lockedApplication || ! $this->shouldAssign($lockedApplication)) {
                return $lockedApplication?->nipd ? (int) $lockedApplication->nipd : null;
            }

            $period = PpdbPeriod::query()
                ->lockForUpdate()
                ->find($lockedApplication->period_id);

            if (! $period) {
                return null;
            }

            $lastAssignedNumber = (int) PpdbApplication::query()
                ->where('period_id', $period->id)
                ->whereNotNull('nipd')
                ->lockForUpdate()
                ->max('nipd');

            $baseline = max((int) ($period->nipd_last_number ?? 0), $lastAssignedNumber);
            $nextNipd = $baseline + 1;

            $period->update(['nipd_last_number' => $nextNipd]);
            $lockedApplication->update(['nipd' => $nextNipd]);

            return $nextNipd;
        });
    }

    public function resolveLastAssignedForPeriod(int $periodId): int
    {
        return (int) PpdbApplication::query()
            ->where('period_id', $periodId)
            ->whereNotNull('nipd')
            ->max('nipd');
    }

    protected function shouldAssign(PpdbApplication $application): bool
    {
        if ($application->nipd) {
            return false;
        }

        if ($application->status_daftar_ulang !== 'verified') {
            return false;
        }

        if (! $application->program_diterima_id) {
            return false;
        }

        return in_array((string) $application->hasil_seleksi, self::ACCEPTED_SELECTION_STATUSES, true);
    }
}
