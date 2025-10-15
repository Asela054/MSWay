<!DOCTYPE html>
<html>
<head>
    <title>Custom Encrypted QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Custom Encrypted QR Code</h3>
                    </div>
                    <div class="card-body text-center">
                        <p><strong>Contains encrypted JSON data with URL and metadata</strong></p>
                        
                        <!-- Display QR Code -->
                        <div class="mb-3">
                            {!! $qrCode !!}
                        </div>
                        
                        <a href="{{ route('qr.generate') }}" class="btn btn-primary">Back to Simple QR</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>