<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Opma_Sms_policyService
{
    protected string $username;
    protected string $password;
    protected string $sourceAddress;
    protected string $loginUrl = 'https://e-sms.dialog.lk/api/v2/user/login';
    protected string $smsUrl   = 'https://e-sms.dialog.lk/api/v2/sms';

    public function __construct()
    {
        $this->username      = 'OpmaEmbAPI';
        $this->password      = 'wpa#ZchuV2EWh#5';
        $this->sourceAddress = 'OPM EMB';
    }

    /**
     * Send SMS to a recipient
     *
     * @param string $recipientNumber 9-digit mobile number (e.g. 714551682)
     * @param string $message         SMS content
     * @return array                  ['success' => bool, 'message' => string, 'data' => array|null]
     */
    public function sendSms(string $recipientNumber, string $message): array
    {
        $token = $this->getAuthToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Authentication failed',
                'data'    => null,
            ];
        }

        $transactionId = time() . rand(100, 999); // unique per request

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json',
        ])->post($this->smsUrl, [
            'msisdn' => [
                ['mobile' => $recipientNumber],
            ],
            'sourceAddress'  => $this->sourceAddress,
            'message'        => $message,
            'transaction_id' => (int) $transactionId,
            'payment_method' => 0,
        ]);

        $result = $response->json();

        // If token expired mid-flight, refresh once and retry
        if (($result['errCode'] ?? null) == 100) {
            Cache::forget('esms_auth_token');
            $token = $this->getAuthToken();

            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Authentication failed on retry',
                    'data'    => null,
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ])->post($this->smsUrl, [
                'msisdn' => [
                    ['mobile' => $recipientNumber],
                ],
                'sourceAddress'  => $this->sourceAddress,
                'message'        => $message,
                'transaction_id' => (int) ($transactionId + 1),
                'payment_method' => 0,
            ]);

            $result = $response->json();
        }

        if (($result['status'] ?? null) === 'success') {
            return [
                'success' => true,
                'message' => $result['comment'] ?? 'SMS sent successfully',
                'data'    => $result['data'] ?? null,
            ];
        }

        Log::error('eSMS send failed', $result ?? []);

        return [
            'success' => false,
            'message' => $result['comment'] ?? 'Failed to send SMS',
            'data'    => null,
        ];
    }

    /**
     * Get a cached auth token, or fetch a new one if missing/expired
     */
    protected function getAuthToken(): ?string
    {
        return Cache::remember('esms_auth_token', now()->addHours(11), function () {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->loginUrl, [
                'username' => $this->username,
                'password' => $this->password,
            ]);

            $result = $response->json();

            if (($result['status'] ?? null) === 'success' && !empty($result['token'])) {
                return $result['token'];
            }

            Log::error('eSMS login failed', $result ?? []);
            return null;
        });
    }
}