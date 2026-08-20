<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Opma_Sms_policyService
{
    protected $username;
    protected $password;
    protected $sourceAddress;
    protected $loginUrl = 'https://e-sms.dialog.lk/api/v2/user/login';
    protected $smsUrl   = 'https://e-sms.dialog.lk/api/v2/sms';

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
     * @return array
     */
    public function sendSms($recipientNumber, $message)
    {
        $token = $this->getAuthToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Authentication failed',
                'data'    => null,
            ];
        }

        $transactionId = $this->generateTransactionId();

         error_log('=== Generated transaction_id: ' . $transactionId . ' ===');

        $result = $this->postSms($token, $recipientNumber, $message, $transactionId);

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

            $result = $this->postSms($token, $recipientNumber, $message, $transactionId + 1);
        }

          // If transaction_id somehow still duplicate, retry once with a fresh id
    if (($result['errCode'] ?? null) == 104) {
        $result = $this->postSms($token, $recipientNumber, $message, $this->generateTransactionId());
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
     * Make the actual SMS POST request
     */
    protected function postSms($token, $recipientNumber, $message, $transactionId)
    {
        $client = new Client();

        try {
            $response = $client->post($this->smsUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'msisdn' => [
                        ['mobile' => $recipientNumber],
                    ],
                    'sourceAddress'  => $this->sourceAddress,
                    'message'        => $message,
                    'transaction_id' => (int) $transactionId,
                    'payment_method' => 0,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
                return json_decode($e->getResponse()->getBody()->getContents(), true);
            }

            Log::error('eSMS request exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get a cached auth token, or fetch a new one if missing/expired
     */
    protected function getAuthToken()
    {
        return Cache::remember('esms_auth_token', 660, function () {
            $client = new Client();

            try {
                $response = $client->post($this->loginUrl, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'username' => $this->username,
                        'password' => $this->password,
                    ],
                ]);

                $result = json_decode($response->getBody()->getContents(), true);

                if (($result['status'] ?? null) === 'success' && !empty($result['token'])) {
                    return $result['token'];
                }

                Log::error('eSMS login failed', $result ?? []);
                return null;

            } catch (\GuzzleHttp\Exception\RequestException $e) {
                Log::error('eSMS login exception', ['error' => $e->getMessage()]);
                return null;
            }
        });
    }

    protected function generateTransactionId()
    {
        $randomPart = str_pad(mt_rand(0, 999), 8, '0', STR_PAD_LEFT); // 3-digit random

        return (int) ($randomPart); // max 11 digits — safe on 64-bit, close on 32-bit
    }
}