<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PpdbApplication;
use App\Support\PpdbSecureDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PpdbRegistrationDocumentController extends Controller
{
    public function download(Request $request, PpdbApplication $application): Response
    {
        abort_unless($request->hasValidSignature(), 403);

        $application->loadMissing([
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

        return response($document['binary'], 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    public function verify(Request $request, PpdbApplication $application): View
    {
        $issuedAt = (string) $request->query('issued', '');
        $signature = strtolower((string) $request->query('signature', ''));
        $secureDocument = app(PpdbSecureDocument::class);

        $isValid = $secureDocument->verify($application, $issuedAt, $signature);

        $verifiedContext = null;

        if ($isValid) {
            $verifiedContext = $secureDocument->createDocumentContext($application, $issuedAt);
        }

        return view('frontend.ppdb-document-verification', [
            'application' => $application,
            'isValid' => $isValid,
            'issuedAt' => $issuedAt,
            'signature' => $signature,
            'verifiedContext' => $verifiedContext,
        ]);
    }
}
