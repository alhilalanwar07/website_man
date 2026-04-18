<?php

namespace App\Support;

use App\Models\Pengumuman;
use App\Models\PpdbApplication;
use App\Models\PpdbPeriod;
use App\Models\SchoolHoliday;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class SchoolHolidayWorkflow
{
    private const DECISION_KEY_PREFIX = 'school_holiday.confirmation.';

    public function checkTomorrowAndNotifyAdmins(bool $force = false): array
    {
        if (! $this->isAutomationEnabled()) {
            return [
                'status' => 'disabled',
                'message' => 'Automasi cek libur sekolah dinonaktifkan.',
            ];
        }

        $targetDate = now()->addDay()->startOfDay();

        if (! $targetDate->isWeekday()) {
            return [
                'status' => 'not-school-day',
                'message' => 'Besok bukan hari sekolah (weekend), tidak perlu konfirmasi.',
                'date' => $targetDate->toDateString(),
            ];
        }

        $events = $this->holidayEventsForDate($targetDate);

        if ($events->isEmpty()) {
            return [
                'status' => 'no-holiday',
                'message' => 'Besok tidak terdeteksi tanggal merah/perayaan.',
                'date' => $targetDate->toDateString(),
            ];
        }

        $existingDecision = $this->decisionForDate($targetDate);

        if (is_array($existingDecision) && ! $force) {
            return [
                'status' => 'already-recorded',
                'message' => 'Konfirmasi untuk tanggal ini sudah pernah dibuat.',
                'decision' => $existingDecision,
            ];
        }

        $chatIds = $this->adminChatIds();

        if ($chatIds->isEmpty()) {
            return [
                'status' => 'no-admin-chat',
                'message' => 'TELEGRAM_ADMIN_CHAT_IDS/TELEGRAM_ALLOWED_CHAT_IDS belum diisi.',
            ];
        }

        $payload = [
            'date' => $targetDate->toDateString(),
            'events' => $events->values()->all(),
            'status' => 'pending',
            'asked_at' => now()->toIso8601String(),
            'asked_to_chat_ids' => $chatIds->values()->all(),
            'approved_by_chat_id' => null,
            'rejected_by_chat_id' => null,
            'responded_at' => null,
            'website_announcement_id' => null,
            'student_email_sent' => 0,
            'student_email_failed' => 0,
            'student_whatsapp_sent' => 0,
            'student_whatsapp_failed' => 0,
        ];

        Setting::setValue($this->decisionKey($targetDate), $payload, 'json');

        $prompt = $this->buildAdminPrompt($targetDate, $events);

        foreach ($chatIds as $chatId) {
            $this->sendTelegramMessage((string) $chatId, $prompt);
        }

        return [
            'status' => 'pending-sent',
            'message' => 'Konfirmasi libur berhasil dikirim ke Telegram admin.',
            'date' => $targetDate->toDateString(),
            'events' => $events->values()->all(),
        ];
    }

    public function handleAdminResponse(string $chatId, string $text): ?string
    {
        $normalized = Str::of($text)->lower()->squish()->toString();

        if ($normalized === '') {
            return null;
        }

        if (in_array($normalized, ['/cek-libur', '/libur cek', 'cek libur', 'libur cek'], true)) {
            $result = $this->checkTomorrowAndNotifyAdmins();

            return $result['message'] ?? 'Cek libur selesai dijalankan.';
        }

        if (in_array($normalized, ['/libur status', 'libur status'], true)) {
            $pending = $this->latestPendingDecision();

            if ($pending === null) {
                return 'Tidak ada konfirmasi libur yang menunggu saat ini.';
            }

            $eventText = collect($pending['events'] ?? [])->implode(', ');

            return "Menunggu konfirmasi libur untuk {$pending['date']}.\nPerayaan: {$eventText}\nBalas: LIBUR YA atau LIBUR TIDAK";
        }

        $isCommandDecision = preg_match('/^\\/?libur\\s+(ya|tidak)$/i', $normalized) === 1;
        $pending = $this->latestPendingDecision();

        if (! $isCommandDecision && $pending === null) {
            return null;
        }

        if (! $isCommandDecision && ! in_array($normalized, ['ya', 'iya', 'yes', 'tidak', 'ga', 'gak', 'no'], true)) {
            return null;
        }

        $decision = $this->parseDecision($normalized);

        if ($decision === null) {
            return null;
        }

        if ($pending === null) {
            return 'Tidak ada konfirmasi libur yang menunggu. Gunakan /cek-libur untuk memulai pengecekan manual.';
        }

        if (($pending['status'] ?? '') !== 'pending') {
            return 'Konfirmasi untuk tanggal tersebut sudah ditutup.';
        }

        $date = Carbon::parse((string) $pending['date'])->startOfDay();
        $events = collect($pending['events'] ?? [])->filter()->values();

        if ($decision === 'reject') {
            $pending['status'] = 'rejected';
            $pending['rejected_by_chat_id'] = $chatId;
            $pending['responded_at'] = now()->toIso8601String();
            Setting::setValue($this->decisionKey($date), $pending, 'json');

            return "Dikonfirmasi: BESOK TETAP MASUK.\nTidak ada notifikasi libur yang dikirim ke siswa.";
        }

        $announcement = $this->publishWebsiteAnnouncement($date, $events);
        $emailStats = $this->notifyStudentsByEmail($date, $events);
        $whatsAppStats = $this->notifyStudentsByWhatsApp($date, $events);

        $pending['status'] = 'approved';
        $pending['approved_by_chat_id'] = $chatId;
        $pending['responded_at'] = now()->toIso8601String();
        $pending['website_announcement_id'] = $announcement?->id;
        $pending['student_email_sent'] = $emailStats['sent'];
        $pending['student_email_failed'] = $emailStats['failed'];
        $pending['student_whatsapp_sent'] = $whatsAppStats['sent'];
        $pending['student_whatsapp_failed'] = $whatsAppStats['failed'];

        Setting::setValue($this->decisionKey($date), $pending, 'json');

        $eventText = $events->implode(', ');
        $websiteInfo = $announcement
            ? "Pengumuman website aktif (ID {$announcement->id})."
            : 'Pengumuman website gagal dibuat.';

        $whatsAppInfo = $whatsAppStats['enabled']
            ? "WhatsApp siswa terkirim: {$whatsAppStats['sent']}\nWhatsApp siswa gagal: {$whatsAppStats['failed']}"
            : 'WhatsApp gateway nonaktif atau belum dikonfigurasi.';

        return "Dikonfirmasi: BESOK LIBUR SEKOLAH.\nPerayaan: {$eventText}\nEmail siswa terkirim: {$emailStats['sent']}\nEmail siswa gagal: {$emailStats['failed']}\n{$whatsAppInfo}\n{$websiteInfo}";
    }

    protected function parseDecision(string $text): ?string
    {
        if (preg_match('/^\\/?libur\\s+ya$/i', $text) === 1 || in_array($text, ['ya', 'iya', 'yes'], true)) {
            return 'approve';
        }

        if (preg_match('/^\\/?libur\\s+tidak$/i', $text) === 1 || in_array($text, ['tidak', 'ga', 'gak', 'no'], true)) {
            return 'reject';
        }

        return null;
    }

    protected function publishWebsiteAnnouncement(CarbonInterface $date, Collection $events): ?Pengumuman
    {
        $authorId = $this->resolveAnnouncementAuthorId();

        if ($authorId === null) {
            Log::warning('School holiday announcement skipped because no author user exists.');

            return null;
        }

        $eventText = $events->implode(', ');
        $title = 'Informasi Libur Sekolah - ' . $date->translatedFormat('d M Y');

        return Pengumuman::create([
            'user_id' => $authorId,
            'judul_pengumuman' => $title,
            'slug' => Str::slug($title) . '-' . Str::lower(Str::random(5)),
            'isi_pengumuman' => trim(implode("\n", [
                'Berdasarkan konfirmasi admin melalui sistem Telegram, kegiatan belajar pada tanggal ' . $date->translatedFormat('d M Y') . ' dinyatakan libur.',
                'Keterangan kalender: ' . $eventText . '.',
                'Silakan pantau kanal resmi sekolah untuk pembaruan jadwal berikutnya.',
            ])),
            'tanggal_mulai_tampil' => now()->toDateString(),
            'tanggal_akhir_tampil' => $date->copy()->addDay()->toDateString(),
        ]);
    }

    protected function notifyStudentsByEmail(CarbonInterface $date, Collection $events): array
    {
        $activePeriodId = PpdbPeriod::query()->active()->value('id');

        $query = PpdbApplication::query()
            ->whereNotNull('email')
            ->where('email', '!=', '');

        if ($activePeriodId) {
            $query->where('period_id', $activePeriodId);
        }

        $emails = $query
            ->pluck('email')
            ->map(fn ($email) => strtolower(trim((string) $email)))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();

        $subject = 'Informasi Libur Sekolah - ' . $date->translatedFormat('d M Y');
        $body = $this->holidayNoticeMessage($date, $events);

        $sent = 0;
        $failed = 0;

        foreach ($emails as $email) {
            try {
                Mail::raw($body, function ($message) use ($email, $subject): void {
                    $message->to($email)->subject($subject);
                });

                $sent++;
            } catch (Throwable $exception) {
                $failed++;

                Log::warning('Failed to send school holiday email.', [
                    'email' => $email,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    protected function notifyStudentsByWhatsApp(CarbonInterface $date, Collection $events): array
    {
        $enabled = (bool) config('services.whatsapp_gateway.enabled', false);
        $endpoint = trim((string) config('services.whatsapp_gateway.url', ''));

        if (! $enabled || $endpoint === '') {
            return ['enabled' => false, 'sent' => 0, 'failed' => 0];
        }

        $message = $this->holidayNoticeMessage($date, $events);
        $recipients = $this->collectStudentWhatsappRecipients();

        $sent = 0;
        $failed = 0;

        foreach ($recipients as $phone) {
            $ok = $this->sendWhatsAppMessage($phone, $message);

            if ($ok) {
                $sent++;
            } else {
                $failed++;
            }
        }

        return ['enabled' => true, 'sent' => $sent, 'failed' => $failed];
    }

    protected function holidayNoticeMessage(CarbonInterface $date, Collection $events): string
    {
        $eventText = $events->implode(', ');

        return trim(implode("\n\n", [
            'Yth. Siswa/Orang Tua,',
            'Sekolah menetapkan LIBUR pada tanggal ' . $date->translatedFormat('d M Y') . '.',
            'Keterangan kalender: ' . $eventText . '.',
            'Informasi ini juga ditampilkan pada website resmi sekolah.',
            'Terima kasih.',
        ]));
    }

    protected function collectStudentWhatsappRecipients(): Collection
    {
        $activePeriodId = PpdbPeriod::query()->active()->value('id');

        $query = PpdbApplication::query();

        if ($activePeriodId) {
            $query->where('period_id', $activePeriodId);
        }

        return $query
            ->get(['nomor_hp', 'nomor_hp_orang_tua'])
            ->flatMap(fn (PpdbApplication $application) => [
                $application->nomor_hp,
                $application->nomor_hp_orang_tua,
            ])
            ->map(fn ($phone) => $this->normalizeIndonesianPhone($phone))
            ->filter()
            ->unique()
            ->values();
    }

    protected function normalizeIndonesianPhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) ($phone ?? ''));

        if (! is_string($digits) || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        if (! str_starts_with($digits, '62')) {
            return null;
        }

        return strlen($digits) >= 10 && strlen($digits) <= 15 ? $digits : null;
    }

    protected function holidayEventsForDate(CarbonInterface $date): Collection
    {
        $dateString = $date->toDateString();
        $apiBase = rtrim((string) config('services.school_calendar.api_url', 'https://libur.deno.dev/api'), '/');

        $apiEvents = collect();

        try {
            $apiResponse = Cache::remember("school-calendar:id:{$dateString}", now()->addHours(6), function () use ($apiBase, $date) {
                return Http::acceptJson()
                    ->timeout(20)
                    ->get($apiBase, [
                        'year' => (int) $date->format('Y'),
                        'month' => (int) $date->format('n'),
                        'day' => (int) $date->format('j'),
                    ])
                    ->throw()
                    ->json();
            });

            if (is_array($apiResponse) && array_key_exists('is_holiday', $apiResponse)) {
                if ((bool) ($apiResponse['is_holiday'] ?? false)) {
                    $apiEvents = collect($apiResponse['holiday_list'] ?? [])
                        ->map(fn ($eventName) => trim((string) $eventName))
                        ->filter();
                }
            } elseif (is_array($apiResponse)) {
                // Fallback jika API mengembalikan format daftar tanggal libur.
                $apiEvents = collect($apiResponse)
                    ->filter(fn ($event) => is_array($event) && data_get($event, 'date') === $dateString)
                    ->map(fn ($event) => trim((string) data_get($event, 'name', '')))
                    ->filter();
            }
        } catch (Throwable $exception) {
            Log::warning('Failed fetching school calendar holiday API.', [
                'date' => $dateString,
                'message' => $exception->getMessage(),
            ]);
        }

        $manualEvents = SchoolHoliday::query()
            ->active()
            ->whereDate('holiday_date', $dateString)
            ->orderBy('id')
            ->get()
            ->map(function (SchoolHoliday $holiday): string {
                $name = trim((string) $holiday->name);
                $description = trim((string) ($holiday->description ?? ''));

                return $description !== '' ? ($name . ' - ' . $description) : $name;
            })
            ->filter();

        return $apiEvents->merge($manualEvents)->unique()->values();
    }

    protected function buildAdminPrompt(CarbonInterface $date, Collection $events): string
    {
        $eventLines = $events->map(fn ($event) => '- ' . $event)->implode("\n");

        return trim(implode("\n", [
            'Deteksi kalender otomatis:',
            'Besok (' . $date->translatedFormat('l, d M Y') . ') terdeteksi tanggal merah/perayaan berikut:',
            $eventLines,
            '',
            'Sumber deteksi: API hari libur nasional + daftar libur manual admin.',
            '',
            'Apakah BESOK dinyatakan libur sekolah?',
            'Balas: LIBUR YA',
            'atau: LIBUR TIDAK',
            'Cek status: /libur status',
        ]));
    }

    protected function decisionForDate(CarbonInterface $date): ?array
    {
        $decision = Setting::getValue($this->decisionKey($date));

        return is_array($decision) ? $decision : null;
    }

    protected function latestPendingDecision(): ?array
    {
        $records = Setting::query()
            ->where('key', 'like', self::DECISION_KEY_PREFIX . '%')
            ->where('type', 'json')
            ->orderByDesc('updated_at')
            ->get();

        foreach ($records as $record) {
            $data = json_decode((string) $record->value, true);

            if (is_array($data) && ($data['status'] ?? null) === 'pending') {
                return $data;
            }
        }

        return null;
    }

    protected function adminChatIds(): Collection
    {
        $configured = config('services.telegram.admin_chat_ids', []);

        if (! is_array($configured) || $configured === []) {
            $configured = config('services.telegram.allowed_chat_ids', []);
        }

        return collect($configured)
            ->map(fn ($id) => trim((string) $id))
            ->filter()
            ->unique()
            ->values();
    }

    protected function decisionKey(CarbonInterface $date): string
    {
        return self::DECISION_KEY_PREFIX . $date->toDateString();
    }

    protected function isAutomationEnabled(): bool
    {
        return (bool) config('services.school_calendar.enabled', true);
    }

    protected function resolveAnnouncementAuthorId(): ?int
    {
        $adminUser = User::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['admin', 'ppdb-admin']))
            ->oldest('id')
            ->first();

        if ($adminUser) {
            return $adminUser->id;
        }

        return User::query()->oldest('id')->value('id');
    }

    protected function sendTelegramMessage(string $chatId, string $text): void
    {
        $token = (string) config('services.telegram.bot_token', '');

        if ($token === '' || $chatId === '') {
            return;
        }

        Http::acceptJson()
            ->timeout(20)
            ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'disable_web_page_preview' => false,
            ]);
    }

    protected function sendWhatsAppMessage(string $phone, string $message): bool
    {
        $endpoint = trim((string) config('services.whatsapp_gateway.url', ''));

        if ($endpoint === '') {
            return false;
        }

        $timeout = (int) config('services.whatsapp_gateway.timeout', 20);
        $token = trim((string) config('services.whatsapp_gateway.token', ''));
        $tokenInBody = (bool) config('services.whatsapp_gateway.token_in_body', false);
        $tokenField = (string) config('services.whatsapp_gateway.token_field', 'token');
        $phoneField = (string) config('services.whatsapp_gateway.phone_field', 'to');
        $messageField = (string) config('services.whatsapp_gateway.message_field', 'message');
        $authHeader = trim((string) config('services.whatsapp_gateway.authorization_header', 'Authorization'));
        $authPrefix = trim((string) config('services.whatsapp_gateway.authorization_prefix', 'Bearer'));

        $payload = [
            $phoneField => $phone,
            $messageField => $message,
        ];

        $staticParams = config('services.whatsapp_gateway.static_params', []);

        if (is_array($staticParams) && $staticParams !== []) {
            $payload = array_merge($staticParams, $payload);
        }

        if ($token !== '' && $tokenInBody && $tokenField !== '') {
            $payload[$tokenField] = $token;
        }

        $request = Http::acceptJson()->timeout($timeout);

        if ($token !== '' && ! $tokenInBody && $authHeader !== '') {
            $headerValue = $authPrefix !== '' ? ($authPrefix . ' ' . $token) : $token;
            $request = $request->withHeaders([$authHeader => $headerValue]);
        }

        try {
            $response = $request->post($endpoint, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::warning('Failed to send school holiday WhatsApp message.', [
                'phone' => $phone,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Failed to send school holiday WhatsApp message.', [
                'phone' => $phone,
                'message' => $exception->getMessage(),
            ]);
        }

        return false;
    }
}
