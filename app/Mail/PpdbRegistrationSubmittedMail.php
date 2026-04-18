<?php

namespace App\Mail;

use App\Models\PpdbApplication;
use App\Support\PpdbSecureDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PpdbRegistrationSubmittedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public PpdbApplication $application,
        public string $downloadUrl
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'PPDB SMK Negeri 1 Kolaka - Pendaftaran Berhasil'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ppdb-registration-submitted'
        );
    }

    public function attachments(): array
    {
        $application = $this->application->loadMissing([
            'period',
            'track',
            'pilihanProgram1',
            'pilihanProgram2',
            'achievements',
        ]);

        $document = app(PpdbSecureDocument::class)->renderPdf($application);
        $context = $document['context'];

        $safeNumber = preg_replace('/[^A-Za-z0-9\-]+/', '-', (string) $application->nomor_pendaftaran) ?: 'pendaftaran';
        $filename = 'formulir-ppdb-' . strtolower($safeNumber) . '-' . strtolower($context['signature_short']) . '.pdf';

        return [
            Attachment::fromData(
                fn (): string => $document['binary'],
                $filename
            )->withMime('application/pdf'),
        ];
    }
}
