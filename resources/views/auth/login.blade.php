<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShapeUP HRM - By eRav Technology</title>
    <link rel="icon" type="image/x-icon" href="{{url('/public/images/hrm.png')}}" />
    <link href="{{ url('/public/css/styles.css') }}" rel="stylesheet" />
    <link href="{{ url('/public/css/custom_styles.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ url('/public/css/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body style="font-family:'Inter',sans-serif;background-color:#1bc1cb;height:100vh;width:100vw;overflow:hidden;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;margin:0;padding:0;box-sizing:border-box;">
    <div id="loginWrapper" style="display:flex;height:100vh;width:100%;">

        <div id="loginLeft" style="width:41.666%;display:flex;flex-direction:column;justify-content:center;padding:0 5rem;z-index:0;">
            <svg id="loginLogo" viewBox="0 0 140 140" style="width:128px;height:128px;margin-bottom:2.5rem;" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M 56 87 L 112 51" stroke="white" stroke-width="15" stroke-linecap="round"/>
                <path d="M 112 51 A 46 46 0 1 0 115 78" stroke="white" stroke-width="15" stroke-linecap="round"/>
                <g transform="rotate(-35, 45, 95)">
                    <rect x="25" y="65" width="40" height="64" rx="20" fill="#ffd400"/>
                    <line x1="45" y1="73" x2="45" y2="87" stroke="white" stroke-width="4" stroke-linecap="round"/>
                </g>
            </svg>
            <h1 id="loginHeading" style="font-size:3.75rem;line-height:1.1;font-weight:700;margin:0 0 1.5rem 0;letter-spacing:-0.025em;">
                <span style="color:#ffffff;display:block;">Empower Your</span>
                <span style="color:#ffd400;display:block;margin-top:0.25rem;">Workforce</span>
            </h1>
            <p id="loginDesc" style="color:rgba(255,255,255,0.9);font-size:1rem;line-height:1.625;font-weight:500;max-width:28rem;padding-right:2.5rem;margin:0;">
                Empowering Modern Enterprises with our Advanced Workforce Management and HR Automation System.
            </p>
        </div>

        <div id="loginRight" style="width:58.333%;background-color:#ffffff;border-top-left-radius:3.5rem;border-bottom-left-radius:3.5rem;box-shadow:-15px 0 40px rgba(0,0,0,0.15);display:flex;flex-direction:column;justify-content:center;align-items:center;z-index:10;">
            <div style="width:100%;max-width:420px;padding:0 1.5rem;">

                <div id="loginFormHeader" style="text-align:center;margin-bottom:2.5rem;">
                    <h2 id="loginFormTitle" style="font-size:32px;font-weight:700;color:#111827;margin:0 0 0.5rem 0;letter-spacing:-0.025em;">Welcome to ShapeUp HRMS !</h2>
                    <p style="font-size:15px;color:#6b7280;font-weight:500;margin:0;">Please Login to your Account</p>
                </div>

                <form class="form-horizontal" method="POST" action="{{ route('login') }}" autocomplete="off">
                    {{ csrf_field() }}

                    <div style="margin-bottom:1.5rem;position:relative;" class="{{ $errors->has('email') ? 'has-error' : '' }}">
                        <label style="display:block;font-size:13px;font-weight:600;color:#6b7280;margin-bottom:0.25rem;">Username / Email</label>
                        <input type="email" name="email" placeholder="@gmail.com" value="{{ old('email') }}" required autofocus autocomplete="username"
                            style="width:100%;border:none;border-bottom:1px solid #e5e7eb;background:transparent;padding:8px 0;font-size:15px;color:#1f2937;outline:none;box-shadow:none;border-radius:0;transition:border-color 0.2s;"
                            onfocus="this.style.borderBottomColor='#1bc1cb'" onblur="this.style.borderBottomColor='#e5e7eb'">
                        @if ($errors->has('email'))
                        <span style="display:block;font-size:12px;color:#ef4444;margin-top:4px;"><strong>{{ $errors->first('email') }}</strong></span>
                        @endif
                    </div>

                    <div style="margin-bottom:1.5rem;position:relative;" class="{{ $errors->has('password') ? 'has-error' : '' }}">
                        <label style="display:block;font-size:13px;font-weight:600;color:#6b7280;margin-bottom:0.25rem;">Password</label>
                        <input type="password" name="password" id="loginPassword" placeholder="password123" required autocomplete="current-password"
                            style="width:100%;border:none;border-bottom:1px solid #e5e7eb;background:transparent;padding:8px 40px 8px 0;font-size:15px;color:#1f2937;outline:none;box-shadow:none;border-radius:0;transition:border-color 0.2s;"
                            onfocus="this.style.borderBottomColor='#1bc1cb'" onblur="this.style.borderBottomColor='#e5e7eb'">
                        <button type="button" onclick="togglePasswordVisibility()"
                            style="position:absolute;right:0;bottom:10px;background:none;border:none;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center;color:#9ca3af;transition:color 0.2s;">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                        @if ($errors->has('password'))
                        <span style="display:block;font-size:12px;color:#ef4444;margin-top:4px;"><strong>{{ $errors->first('password') }}</strong></span>
                        @endif
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:0.5rem;padding-bottom:1rem;">
                        <label style="display:flex;align-items:center;font-size:13px;color:#4b5563;font-weight:500;cursor:pointer;margin:0;">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                                style="margin-right:10px;accent-color:#1bc1cb;width:16px;height:16px;cursor:pointer;">
                            Remember password
                        </label>
                        <a href="#" style="font-size:13px;font-weight:700;color:#1bc1cb;text-decoration:none;"
                            onmouseover="this.style.color='#18a8b1'" onmouseout="this.style.color='#1bc1cb'">Forgot Password?</a>
                    </div>

                    <button type="submit"
                        style="width:100%;background-color:#1bc1cb;color:#ffffff;border:none;border-radius:9999px;padding:14px 0;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 8px 20px -8px #1bc1cb;transition:all 0.2s;"
                        onmouseover="this.style.backgroundColor='#18a8b1'" onmouseout="this.style.backgroundColor='#1bc1cb'">
                        Login
                    </button>
                </form>

                <div style="display:flex;gap:1rem;margin-top:2rem;">
                    <a href="https://play.google.com/store/apps/details?id=com.shapeup.hr" target="_blank" rel="noopener noreferrer"
                        style="flex:1;display:flex;align-items:center;justify-content:center;gap:12px;border:1px solid #e5e7eb;border-radius:1rem;padding:12px 16px;background:transparent;cursor:pointer;text-decoration:none;color:inherit;transition:background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#374151"><path d="M8 5v14l11-7z"/></svg>
                        <div style="text-align:left;line-height:1;">
                            <span style="font-size:9px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;display:block;">GET IT ON</span>
                            <span style="font-size:13px;font-weight:700;color:#111827;margin-top:2px;display:block;">Google Play</span>
                        </div>
                    </a>

                    <button type="button" data-toggle="modal" data-target="#qrModal"
                        style="flex:1;display:flex;align-items:center;justify-content:center;gap:12px;border:1px solid #e5e7eb;border-radius:1rem;padding:12px 16px;background:transparent;cursor:pointer;transition:background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                            <path d="M14 14h7v7h-7z"></path>
                        </svg>
                        <div style="text-align:left;line-height:1;">
                            <span style="font-size:13px;font-weight:700;color:#111827;display:block;">Scan to Login</span>
                            <span id="mobileScanSub" style="display:none;font-size:10px;font-weight:500;color:#6b7280;margin-top:2px;">Use mobile app</span>
                        </div>
                    </button>
                </div>

                <div id="loginFooter" style="margin-top:5rem;text-align:center;">
                    <div id="footerLogos" style="display:flex;justify-content:center;align-items:center;gap:12px;margin-bottom:20px;">
                        <svg viewBox="0 0 140 140" style="width:24px;height:24px;" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M 56 87 L 112 51" stroke="#1bc1cb" stroke-width="16" stroke-linecap="round"/>
                            <path d="M 112 51 A 46 46 0 1 0 115 78" stroke="#1bc1cb" stroke-width="16" stroke-linecap="round"/>
                            <g transform="rotate(-35, 45, 95)">
                                <rect x="25" y="65" width="40" height="64" rx="20" fill="#ffd400"/>
                                <line x1="45" y1="73" x2="45" y2="87" stroke="#ffffff" stroke-width="4" stroke-linecap="round"/>
                            </g>
                        </svg>
                        <svg viewBox="0 0 100 100" style="width:28px;height:28px;" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="50,15 15,55 85,55" fill="#ffd400"/>
                            <text x="50" y="90" font-family="Inter,sans-serif" font-weight="900" font-size="44" fill="#1bc1cb" text-anchor="middle" letter-spacing="-1">UP</text>
                        </svg>
                    </div>
                    <p style="font-size:11px;font-weight:700;color:#9ca3af;letter-spacing:0.05em;text-transform:uppercase;margin:0;">
                        POWERED BY <span id="poweredBySep" style="display:none;margin:0 8px;color:#e5e7eb;font-weight:400;">|</span> <span style="color:#1f2937;">eRav Technologies</span>
                    </p>
                    <a href="Privacy-policy" target="_blank" rel="noopener noreferrer"
                        style="display:inline-block;margin-top:100px;font-size:12px;font-weight:600;color:#1bc1cb;text-decoration:none;letter-spacing:0.02em;"
                        onmouseover="this.style.color='#18a8b1'" onmouseout="this.style.color='#1bc1cb'">
                        Privacy &amp; Policy
                    </a>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrCodeContainer">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading QR Code...</p>
                    </div>
                    <p class="text-muted small mt-3">Scan this QR code to access the application</p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ url('/public/js/app.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function applyResponsive() {
            var w = window.innerWidth;
            var mobile = w <= 900;
            var wrapper = document.getElementById('loginWrapper');
            var left = document.getElementById('loginLeft');
            var right = document.getElementById('loginRight');
            var logo = document.getElementById('loginLogo');
            var heading = document.getElementById('loginHeading');
            var desc = document.getElementById('loginDesc');
            var formHeader = document.getElementById('loginFormHeader');
            var formTitle = document.getElementById('loginFormTitle');
            var footer = document.getElementById('loginFooter');
            var footerLogos = document.getElementById('footerLogos');
            var mobileScanSub = document.getElementById('mobileScanSub');
            var poweredBySep = document.getElementById('poweredBySep');

            if (mobile) {
                wrapper.style.flexDirection = 'column';
                left.style.cssText = 'width:100%;display:flex;flex-direction:column;justify-content:center;padding:3rem 1.5rem 2.5rem;align-items:center;text-align:center;z-index:0;';
                logo.style.cssText = 'width:72px;height:72px;margin-bottom:1rem;';
                heading.style.cssText = 'font-size:2.25rem;line-height:1.1;font-weight:700;margin:0;letter-spacing:-0.025em;';
                desc.style.display = 'none';
                right.style.cssText = 'width:100%;background-color:#ffffff;border-top-left-radius:2.5rem;border-top-right-radius:2.5rem;border-bottom-left-radius:0;box-shadow:0 -15px 40px rgba(0,0,0,0.1);display:flex;flex-direction:column;justify-content:center;align-items:center;z-index:10;padding:1.5rem 0;flex:1;overflow:hidden;';                formHeader.style.marginBottom = '2rem';
                formTitle.style.fontSize = '24px';
                footer.style.cssText = 'margin-top:1rem;text-align:center;padding-bottom:0.5rem;';
                var privacyLink = footer.querySelector('a');
                if (privacyLink) privacyLink.style.marginTop = '0.5rem';
                footerLogos.style.display = 'none';
                mobileScanSub.style.display = 'block';
                poweredBySep.style.display = 'inline-block';
            } else {
                wrapper.style.flexDirection = 'row';
                left.style.cssText = 'width:41.666%;display:flex;flex-direction:column;justify-content:center;padding:0 5rem;z-index:0;';
                logo.style.cssText = 'width:128px;height:128px;margin-bottom:2.5rem;';
                heading.style.cssText = 'font-size:3.75rem;line-height:1.1;font-weight:700;margin:0 0 1.5rem 0;letter-spacing:-0.025em;';
                desc.style.display = 'block';
                right.style.cssText = 'width:58.333%;background-color:#ffffff;border-top-left-radius:3.5rem;border-bottom-left-radius:3.5rem;box-shadow:-15px 0 40px rgba(0,0,0,0.15);display:flex;flex-direction:column;justify-content:center;align-items:center;z-index:10;';
                formHeader.style.marginBottom = '2.5rem';
                formTitle.style.fontSize = '32px';
                footer.style.cssText = 'margin-top:5rem;text-align:center;';
                var privacyLink = footer.querySelector('a');
                if (privacyLink) privacyLink.style.marginTop = '100px';
                footerLogos.style.display = 'flex';
                mobileScanSub.style.display = 'none';
                poweredBySep.style.display = 'none';
            }
        }

        applyResponsive();
        window.addEventListener('resize', applyResponsive);

        $(document).ready(function() {
            $('input[name="email"]').focus();
            $('#qrModal').on('show.bs.modal', function () {
                loadQRCode();
            });
        });

        function togglePasswordVisibility() {
            var passwordField = document.getElementById('loginPassword');
            var toggleIcon = document.getElementById('toggleIcon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function loadQRCode() {
            $.ajax({
                url: '{{ route("qr.generate") }}',
                type: 'GET',
                success: function(response) {
                    $('#qrCodeContainer').html(response);
                },
                error: function(xhr) {
                    $('#qrCodeContainer').html('<div class="alert alert-danger">Failed to load QR code. Please try again.</div>');
                }
            });
        }

        function downloadQRCode() {
            window.open('{{ route("qr.download") }}', '_blank');
        }
    </script>
</body>
</html>