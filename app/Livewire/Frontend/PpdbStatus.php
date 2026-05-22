<?php

namespace App\Livewire\Frontend;

use App\Mail\PpdbRegistrationSubmittedMail;
use App\Models\PpdbApplication;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL as SignedUrl;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

#[Layout('components.layouts.app')]
#[Title('Cek Status PPDB - MAN 2 Kolaka')]
class PpdbStatus extends Component
{
    protected const RE_REGISTRATION_PREFILL_SESSION_KEY = 'ppdb.re_registration_prefill';

    public string $nomor_pendaftaran = '';
    public string $tanggal_lahir = '';
    public ?PpdbApplication $result = null;
    public bool $searched = false;

    #[Locked]
    public ?int $resultId = null;

    public ?string $resultDownloadUrl = null;
    public ?string $actionFeedbackMessage = null;
    public ?string $actionFeedbackType = null;

    public function search(): void
    {
        $keyword = $this->normalizeKeyword();

        $this->validate([
            'nomor_pendaftaran' => 'required|string',
            'tanggal_lahir' => 'required|date',
        ]);

        $this->searched = true;
        $result = PpdbApplication::with(['period', 'track', 'pilihanProgram1', 'pilihanProgram2', 'programDiterima', 'documents'])
            ->where(function ($query) use ($keyword) {
                $query->where('nomor_pendaftaran', $keyword)
                    ->orWhere('nisn', $keyword);
            })
            ->whereDate('tanggal_lahir', $this->tanggal_lahir)
            ->first();

        $this->result = $result;
        $this->resultId = $result?->id;
        $this->resultDownloadUrl = $result ? $this->buildDownloadUrl($result) : null;
        $this->clearActionFeedback();
    }

    public function resendConfirmationEmail(): void
    {
        $this->clearActionFeedback();

        if (! $this->resultId) {
            $this->setActionFeedback('error', 'Data pendaftar belum tersedia. Silakan cari data terlebih dahulu.');

            return;
        }

        $application = PpdbApplication::query()
            ->with(['period', 'track', 'pilihanProgram1', 'pilihanProgram2', 'achievements'])
            ->find($this->resultId);

        if (! $application) {
            $this->setActionFeedback('error', 'Data pendaftar tidak ditemukan lagi. Silakan cari ulang.');

            return;
        }

        $email = strtolower(trim((string) $application->email));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setActionFeedback('error', 'Email pendaftar tidak valid sehingga tidak dapat dikirim ulang otomatis.');

            return;
        }

        $throttleKey = sprintf('ppdb:resend-confirmation:%d:%s', $application->id, request()->ip() ?? 'unknown');

        if (Cache::has($throttleKey)) {
            $this->setActionFeedback('error', 'Permintaan kirim ulang terlalu cepat. Coba lagi dalam 1 menit.');

            return;
        }

        Cache::put($throttleKey, true, now()->addMinute());

        try {
            $downloadUrl = $this->buildDownloadUrl($application);

            Mail::to($email)->send(new PpdbRegistrationSubmittedMail($application, $downloadUrl));

            $this->resultDownloadUrl = $downloadUrl;
            $this->setActionFeedback('success', 'Email konfirmasi berhasil dikirim ulang ke ' . $email . '.');
        } catch (Throwable $exception) {
            Cache::forget($throttleKey);

            Log::warning('Failed to resend PPDB confirmation email from status page.', [
                'application_id' => $application->id,
                'email' => $email,
                'message' => $exception->getMessage(),
            ]);

            $this->setActionFeedback('error', 'Gagal mengirim ulang email saat ini. Silakan coba beberapa saat lagi.');
        }
    }

    public function continueToReRegistration(): void
    {
        $this->clearActionFeedback();

        if (! $this->resultId) {
            $this->setActionFeedback('error', 'Data pendaftar belum tersedia. Silakan cari data terlebih dahulu.');

            return;
        }

        $application = PpdbApplication::query()
            ->with('period')
            ->find($this->resultId);

        if (! $application) {
            $this->setActionFeedback('error', 'Data pendaftar tidak ditemukan lagi. Silakan cari ulang.');

            return;
        }

        if (! $application->period?->isAnnouncementPublished() || ! $application->hasPassedSelection()) {
            $this->setActionFeedback('error', 'Daftar ulang hanya tersedia setelah pengumuman resmi untuk peserta yang dinyatakan lulus.');

            return;
        }

        session()->put(self::RE_REGISTRATION_PREFILL_SESSION_KEY, [
            'nomor_pendaftaran' => (string) $application->nomor_pendaftaran,
            'tanggal_lahir' => (string) $application->tanggal_lahir?->format('Y-m-d'),
        ]);

        $this->redirectRoute('ppdb.daftar-ulang', navigate: true);
    }

    public function resetSearch(): void
    {
        $this->reset([
            'nomor_pendaftaran',
            'tanggal_lahir',
            'result',
            'searched',
            'resultId',
            'resultDownloadUrl',
            'actionFeedbackMessage',
            'actionFeedbackType',
        ]);
        $this->resetValidation();
    }

    protected function normalizeKeyword(): string
    {
        $keyword = trim($this->nomor_pendaftaran);
        $this->nomor_pendaftaran = $keyword;

        return $keyword;
    }

    protected function buildDownloadUrl(PpdbApplication $application): string
    {
        return SignedUrl::temporarySignedRoute(
            'ppdb.form.download',
            now()->addDays(30),
            ['application' => $application->id]
        );
    }

    protected function clearActionFeedback(): void
    {
        $this->actionFeedbackMessage = null;
        $this->actionFeedbackType = null;
    }

    protected function setActionFeedback(string $type, string $message): void
    {
        $this->actionFeedbackType = $type;
        $this->actionFeedbackMessage = $message;
    }

    public function render()
    {
        return view('livewire.frontend.ppdb-status');
    }
}
