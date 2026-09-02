<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
            $transactionId = $this->generateTransactionId();
            $result = $this->postSms($token, $recipientNumber, $message, $transactionId);
        }

            if (($result['status'] ?? null) === 'success') {
                $this->logSms($recipientNumber, $message, $transactionId, 'success', $result['comment'] ?? null);
                
                return [
                    'success' => true,
                    'message' => $result['comment'] ?? 'SMS sent successfully',
                    'data'    => $result['data'] ?? null,
                ];
            }

            $this->logSms($recipientNumber, $message, $transactionId, 'failed', $result['comment'] ?? null);
            Log::error('eSMS send failed', $result ?? []);

            return [
                'success' => false,
                'message' => $result['comment'] ?? 'Failed to send SMS',
                'data'    => null,
            ];
    }

     protected function logSms($mobile, $message, $transactionId, $status, $responseMessage = null)
    {
        try {
            DB::table('opma_sms_logs')->insert([
                'mobile'            => $mobile,
                'message'           => $message,
                'transaction_id'    => $transactionId,
                'status'            => $status,
                'response_message'  => $responseMessage,
                'sent_at'           => date('Y-m-d H:i:s'),
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to write sms_logs record', ['error' => $e->getMessage()]);
        }
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


        /**
     * Send the same SMS message to MULTIPLE recipients in one campaign
     * (single eSMS call, shared transaction_id). Automatically chunks
     * the list if it exceeds the API's reliable recipient limit.
     *
     * @param array  $recipientNumbers array of 9-digit mobile numbers
     * @param string $message
     * @return array
     */
    public function sendBulkSms($recipientNumbers, $message)
    {
        $recipientNumbers = array_values(array_unique(array_filter($recipientNumbers)));

        if (empty($recipientNumbers)) {
            return [
                'success' => false,
                'message' => 'No valid recipient numbers provided',
                'data'    => null,
            ];
        }

        // eSMS docs: reliably tested up to 1000 recipients per POST request
        $chunks = array_chunk($recipientNumbers, 1000);

        $overallSuccess = true;
        $messages       = [];
        $lastData       = null;

        foreach ($chunks as $chunk) {
            $result = $this->sendBulkChunk($chunk, $message);

            if (!$result['success']) {
                $overallSuccess = false;
            }

            $messages[] = $result['message'];
            $lastData   = $result['data'];
        }

        return [
            'success' => $overallSuccess,
            'message' => implode(' | ', $messages),
            'data'    => $lastData,
        ];
    }

    /**
     * Send one chunk (<=1000 numbers) as a single campaign
     */
    protected function sendBulkChunk($recipientNumbers, $message)
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

        error_log('=== Generated transaction_id (bulk): ' . $transactionId . ' ===');

        $result = $this->postBulkSms($token, $recipientNumbers, $message, $transactionId);

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

            $result = $this->postBulkSms($token, $recipientNumbers, $message, $transactionId + 1);
        }

        // If transaction_id somehow still duplicate, retry once with a fresh id
        if (($result['errCode'] ?? null) == 104) {
            $transactionId = $this->generateTransactionId();
            $result = $this->postBulkSms($token, $recipientNumbers, $message, $transactionId);
        }

        if (($result['status'] ?? null) === 'success') {
            $this->logBulkSms($recipientNumbers, $message, $transactionId, 'success', $result['comment'] ?? null);

            return [
                'success' => true,
                'message' => $result['comment'] ?? 'SMS sent successfully',
                'data'    => $result['data'] ?? null,
            ];
        }

        $this->logBulkSms($recipientNumbers, $message, $transactionId, 'failed', $result['comment'] ?? null);
        Log::error('eSMS bulk send failed', $result ?? []);

        return [
            'success' => false,
            'message' => $result['comment'] ?? 'Failed to send SMS',
            'data'    => null,
        ];
    }

    /**
     * Log one row per recipient, all sharing the same transaction_id
     */
    protected function logBulkSms($mobiles, $message, $transactionId, $status, $responseMessage = null)
    {
        $now  = date('Y-m-d H:i:s');
        $rows = [];

        foreach ($mobiles as $mobile) {
            $rows[] = [
                'mobile'            => $mobile,
                'message'           => $message,
                'transaction_id'    => $transactionId,
                'status'            => $status,
                'response_message'  => $responseMessage,
                'sent_at'           => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }

        try {
            DB::table('opma_sms_logs')->insert($rows);
        } catch (\Exception $e) {
            Log::error('Failed to write bulk sms_logs records', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Make the actual bulk SMS POST request (multiple msisdn, one campaign)
     */
    protected function postBulkSms($token, $recipientNumbers, $message, $transactionId)
    {
        $client = new Client();

        $msisdn = [];
        foreach ($recipientNumbers as $number) {
            $msisdn[] = ['mobile' => $number];
        }

        try {
            $response = $client->post($this->smsUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'msisdn'         => $msisdn,
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

            Log::error('eSMS bulk request exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}