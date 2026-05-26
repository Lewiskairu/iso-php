<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Service for generating and verifying secure tokens for email verification, password reset, etc.
 */
final class TokenService
{
    private string $secret;

    public function __construct()
    {
        // Use a secret from environment or fallback
        $this->secret = (string) (getenv('APP_KEY') ?: 'base64:758694038275193048576dbe654321');
    }

    /**
     * Generate a signed token containing payload and expiration.
     */
    public function generate(array $payload, int $expiresInSeconds = 3600): string
    {
        $data = [
            'p' => $payload,
            'e' => time() + $expiresInSeconds,
        ];
        $json = json_encode($data);
        $encoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($json));
        $signature = hash_hmac('sha256', $encoded, $this->secret);

        return $encoded . '.' . $signature;
    }

    /**
     * Verify a signed token and return payload if valid.
     */
    public function verify(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return null;
        }

        [$encoded, $signature] = $parts;
        
        // Re-calculate signature on the encoded string as it came from the URL
        $expectedSignature = hash_hmac('sha256', $encoded, $this->secret);

        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        // Now decode the base64url back to json
        $remainder = strlen($encoded) % 4;
        $base64 = str_replace(['-', '_'], ['+', '/'], $encoded);
        if ($remainder) {
            $base64 .= str_repeat('=', 4 - $remainder);
        }
        $json = base64_decode($base64);
        $data = json_decode($json, true);

        if (!$data || !isset($data['p'], $data['e'])) {
            return null;
        }

        if (time() > $data['e']) {
            return null; // Expired
        }

        return $data['p'];
    }
}
