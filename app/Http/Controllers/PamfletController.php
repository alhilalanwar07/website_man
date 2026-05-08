<?php

namespace App\Http\Controllers;

use App\Models\PpdbPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves pamphlet images via obfuscated signed URLs.
 * This prevents exposing actual storage paths/filenames to users.
 */
class PamfletController extends Controller
{
    public function show(Request $request, string $token): Response
    {
        // Decode the signed token
        $decoded = $this->decodeToken($token);

        if (!$decoded) {
            abort(404);
        }

        $period = PpdbPeriod::find($decoded['id']);

        if (!$period) {
            abort(404);
        }

        $field = $decoded['type'] === 'mobile' ? 'pamflet_mobile' : 'pamflet_desktop';
        $path = $period->getRawOriginal($field);

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($path);
        $mime = Storage::disk('public')->mimeType($path);

        return response($file, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * Generate an obfuscated token for a pamflet image.
     */
    public static function generateToken(int $periodId, string $type): string
    {
        $payload = json_encode(['id' => $periodId, 'type' => $type, 'ts' => time()]);

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    /**
     * Decode the obfuscated token back to payload.
     */
    protected function decodeToken(string $token): ?array
    {
        try {
            $json = base64_decode(strtr($token, '-_', '+/'));
            $data = json_decode($json, true);

            if (!is_array($data) || !isset($data['id'], $data['type'])) {
                return null;
            }

            if (!in_array($data['type'], ['desktop', 'mobile'], true)) {
                return null;
            }

            return $data;
        } catch (\Throwable) {
            return null;
        }
    }
}
