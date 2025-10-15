<!DOCTYPE html>
<html>
<head>
    <title>Encrypted QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Encrypted QR Code</h3>
                    </div>
                    <div class="card-body text-center">
                        <p><strong>Original URL:</strong> {{ $originalUrl }}</p>
                        
                        <!-- Display QR Code -->
                        <div class="mb-3">
                            {!! $qrCode !!}
                        </div>
                        
                        <p class="text-muted">
                            <small>This QR contains encrypted data that needs to be decrypted when scanned.</small>
                        </p>
                        
                        <a href="{{ route('qr.download') }}" class="btn btn-primary">Download QR Code</a>
                        <a href="{{ route('qr.custom') }}" class="btn btn-secondary">Generate Custom QR</a>
                    </div>
                </div>

                <!-- Decryption Test Form - Changed to GET -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Test QR Decryption</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('qr.process') }}" method="GET">
                            <!-- No CSRF token needed for GET requests -->
                            <div class="mb-3">
                                <label for="qr_data" class="form-label">Paste encrypted QR data here:</label>
                                <textarea class="form-control" id="qr_data" name="qr_data" rows="3" placeholder="Paste encrypted data from QR scanner"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Decrypt and Process</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>