<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\EncryptionHelper;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Validator;

class QRController extends Controller
{
    /**
     * Generate encrypted QR code for modal (returns only QR HTML)
     */
    public function generateQR()
    {
        $originalUrl = url()->to('/');
        
        // Encrypt the URL
        $encryptedData = EncryptionHelper::encrypt($originalUrl);

        // Generate QR code with encrypted data
        $qrCode = QrCode::size(250) // Smaller size for modal
            ->backgroundColor(255, 255, 255)
            ->color(0, 0, 0)
            ->generate($encryptedData);

        // Return only the QR code SVG for AJAX requests
        if (request()->ajax()) {
            return $qrCode;
        }

        // For non-AJAX requests, return the full view (backward compatibility)
        return view('qr-code', compact('qrCode', 'originalUrl'));
    }

    /**
     * Generate QR code with custom encryption
     */
    public function generateCustomQR()
    {
        $data = [
            'url' => url()->to('/'),
            'timestamp' => Carbon::now()->timestamp,
            'type' => 'redirect'
        ];

        // Convert to JSON and encrypt
        $jsonData = json_encode($data);
        $encryptedData = EncryptionHelper::encrypt($jsonData);

        $qrCode = QrCode::size(300)->generate($encryptedData);

        return view('qr-code-custom', compact('qrCode'));
    }

    /**
     * Download QR code as PNG
     */
    public function downloadQR()
    {
        $originalUrl = url()->to('/');
        $encryptedData = EncryptionHelper::encrypt($originalUrl);

        return response()->stream(function () use ($encryptedData) {
            echo QrCode::format('png')
                ->size(300)
                ->generate($encryptedData);
        }, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="encrypted-qr-' . time() . '.png"',
        ]);
    }

    /**
     * Decrypt and process scanned QR data - Using GET method
     */
    public function processQR(Request $request)
    {
        // For Laravel 5 - Use Validator class instead of $request->validate()
        $validator = Validator::make($request->all(), [
            'qr_data' => 'required|string'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $encryptedData = $request->input('qr_data');
        
        try {
            // Decrypt the data
            $decryptedUrl = EncryptionHelper::decrypt($encryptedData);
            
            // Validate URL
            if (filter_var($decryptedUrl, FILTER_VALIDATE_URL)) {
                echo $decryptedUrl;
            } else {
                // If it's not a URL, check if it's JSON data
                $data = json_decode($decryptedUrl, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($data['url'])) {
                    return redirect()->away($data['url']);
                } else {
                    return back()->with('error', 'Invalid URL in QR code data');
                }
            }
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to decrypt QR data: ' . $e->getMessage());
        }
    }

    /**
     * Simple test method to verify encryption/decryption
     */
    public function testEncryption()
    {
        $testUrl = url()->to('/');
        
        // Test encryption
        $encrypted = EncryptionHelper::encrypt($testUrl);
        $decrypted = EncryptionHelper::decrypt($encrypted);
        
        return response()->json([
            'original' => $testUrl,
            'encrypted' => $encrypted,
            'decrypted' => $decrypted,
            'success' => $testUrl === $decrypted
        ]);
    }
}