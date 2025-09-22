<?php

namespace App\Helpers;

use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use App\Utils\Tools;

class SecureTokenHelper
{
    private static function getSecondaryKey(): string
    {
        return hash('sha256', config('app.key') . 'email_verification_v2');
    }

    public static function generateEmailToken(User $user): array
    {
        $uuid = (string) Str::uuid();

        $data = [
            'exp' => now()->addHours(24)->timestamp,
            'user_id' => $user->id,
            'action' => 'email_verification',
            'issued_at' => now()->timestamp
        ];

        $encryptedData = encrypt(json_encode($data));
        $encodedData = base64_encode($encryptedData);
        $random = Str::random(12);

        $firstLayerToken = "{$uuid}.{$encodedData}.{$random}";

        $iv = openssl_random_pseudo_bytes(16);
        $secondLayerEncrypted = openssl_encrypt(
            $firstLayerToken,
            'AES-256-CBC',
            self::getSecondaryKey(),
            0,
            $iv
        );

        $finalToken = base64_encode($iv . $secondLayerEncrypted);

        $hashedToken = hash('sha256', $finalToken);

        return [
            'token' => $finalToken,
            'hashed_token' => $hashedToken
        ];
    }

    public static function validateEmailToken(string $token): ?array
    {
        try {
            $decoded = base64_decode($token);
            if (!$decoded) {
                return null;
            }

            $iv = substr($decoded, 0, 16);
            $encryptedData = substr($decoded, 16);

            $firstLayerToken = openssl_decrypt(
                $encryptedData,
                'AES-256-CBC',
                self::getSecondaryKey(),
                0,
                $iv
            );

            if (!$firstLayerToken) {
                return null;
            }

            $parts = explode('.', $firstLayerToken);

            if (count($parts) !== 3) {
                return null;
            }

            [$uuid, $encodedData, $random] = $parts;

            $decryptedData = decrypt(base64_decode($encodedData));
            $data = json_decode($decryptedData, true);

            if (!$data) {
                return null;
            }

            if ($data['exp'] < now()->timestamp) {
                return null;
            }

            if ($data['action'] !== 'email_verification') {
                return null;
            }

            return $data;

        } catch (Exception $e) {
            return null;
        }
    }
}
