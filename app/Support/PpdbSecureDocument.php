<?php

namespace App\Support;

use App\Models\PpdbApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Illuminate\Support\Carbon;

class PpdbSecureDocument
{
    public function createDocumentContext(PpdbApplication $application, ?string $issuedAt = null): array
    {
        $issuedAtValue = $issuedAt ?: now()->format('YmdHis');
        $signature = $this->sign($application, $issuedAtValue);
        $documentCode = $this->buildDocumentCode($application, $issuedAtValue, $signature);

        return [
            'issued_at' => $issuedAtValue,
            'issued_at_human' => Carbon::createFromFormat('YmdHis', $issuedAtValue)->translatedFormat('d F Y H:i:s'),
            'signature' => $signature,
            'signature_short' => strtoupper(substr($signature, 0, 12)),
            'document_code' => $documentCode,
            'verification_url' => route('ppdb.document.verify', [
                'application' => $application->id,
                'issued' => $issuedAtValue,
                'signature' => $signature,
            ]),
        ];
    }

    public function verify(PpdbApplication $application, string $issuedAt, string $signature): bool
    {
        if (! preg_match('/^\d{14}$/', $issuedAt)) {
            return false;
        }

        if (! preg_match('/^[a-f0-9]{64}$/', $signature)) {
            return false;
        }

        return hash_equals($this->sign($application, $issuedAt), strtolower($signature));
    }

    public function renderPdf(PpdbApplication $application, ?string $issuedAt = null): array
    {
        $context = $this->createDocumentContext($application, $issuedAt);

        $application->loadMissing([
            'period',
            'period.contactPersons',
            'period.importantDates',
            'period.documentRequirements',
            'period.mapColorRules.programKeahlian',
            'track',
            'pilihanProgram1',
            'pilihanProgram2',
            'pilihanProgram3',
            'achievements',
            'documents',
        ]);

        $pdf = Pdf::loadView('pdf.ppdb-registration-form', [
            'application' => $application,
            'documentSecurity' => $context,
        ])->setPaper([0, 0, 595.28, 935.43], 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $this->applyProtection($dompdf, $context['signature']);

        return [
            'binary' => $dompdf->output(),
            'context' => $context,
        ];
    }

    protected function applyProtection(Dompdf $dompdf, string $signature): void
    {
        $canvas = $dompdf->getCanvas();

        if (! method_exists($canvas, 'get_cpdf')) {
            return;
        }

        $cpdf = $canvas->get_cpdf();

        if (! method_exists($cpdf, 'setEncryption')) {
            return;
        }

        $ownerPassword = hash('sha256', $signature . '|' . $this->resolveSigningKey());

        // Allow printing but block modification/copy in common PDF readers.
        $cpdf->setEncryption('', $ownerPassword, ['print']);
    }

    protected function sign(PpdbApplication $application, string $issuedAt): string
    {
        return hash_hmac(
            'sha256',
            implode('|', [
                'ppdb-secure-doc',
                (string) $application->id,
                (string) $application->nomor_pendaftaran,
                (string) $application->submitted_at,
                (string) $application->created_at,
                $issuedAt,
            ]),
            $this->resolveSigningKey()
        );
    }

    protected function buildDocumentCode(PpdbApplication $application, string $issuedAt, string $signature): string
    {
        return sprintf(
            'DOC-PPDB-%d-%s-%s',
            $application->id,
            substr($issuedAt, -6),
            strtoupper(substr($signature, 0, 6))
        );
    }

    protected function resolveSigningKey(): string
    {
        $key = (string) config('app.key');

        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);

            if ($decoded !== false) {
                return $decoded;
            }
        }

        return $key;
    }
}
