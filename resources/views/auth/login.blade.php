@extends('layouts.default')

@section('content')
<main>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 mt-sm-0 mt-lg-5 pt-sm-0 pt-lg-5">
                <div class="card mt-5 mb-5">
                    <div class="row no-gutters">
                        <div class="col-md-5">
                            <img src="{{url('/public/images/hrm.png')}}" class="card-img-top" alt="">
                        </div>
                        <div class="col-md-7">
                            <div class="card-body">
                                <form class="form-horizontal mt-sm-0 mt-lg-3" method="POST" action="{{ route('login') }}" autocomplete="off">
                                    {{ csrf_field() }}
                                    <div class="form-group mb-1 {{ $errors->has('email') ? ' has-error' : '' }}"><label
                                            class="small mb-2" for="inputEmailAddress">Email</label><input
                                            class="form-control form-control-sm" name="email" id="email" type="email"
                                            placeholder="Enter email address" value="{{ old('email') }}" required autofocus />
                                    </div>
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                    <div class="form-group mb-1 {{ $errors->has('password') ? ' has-error' : '' }}">
                                        <label class="small mb-2" for="inputPassword">Password</label>
                                        <div class="input-group">
                                            <input style="height: 35px;" class="form-control form-control-sm" id="password"
                                                name="password" type="password" placeholder="Enter password" required />
                                            <div class="input-group-append">
                                                <span class="input-group-text" onclick="togglePasswordVisibility()"
                                                    style="cursor: pointer;">
                                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox mt-2">
                                            <input type="checkbox" class="custom-control-input" name="remember"
                                                id="rememberPasswordCheck" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="rememberPasswordCheck">Remember
                                                password</label>
                                        </div>
                                    </div>
                                    <div class="form-group text-right mt-4 mb-0">
                                        <button type="submit" class="btn btn-primary btn-sm px-4">Login</button>
                                    </div>
                                </form>
                                <hr>
                                <div class="row justify-content-between mt-2">
                                    <div class="col">
                                        <a href="https://play.google.com/store/apps/details?id=com.shapeup.hr"
                                            class="text-decoration-none text-dark" target="_blank"><img
                                                src="images/googleplay.png" alt="" class="img-fluid"></a>
                                    </div>
                                    <div class="col-2 align-self-center text-right">
                                        <button type="button" class="btn btn-link text-decoration-none text-dark p-0" data-toggle="modal" data-target="#qrModal">
                                            <i class="fal fa-qrcode fa-2x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-laugfs">
                        <div class="row">
                            <div class="col-md-12 small text-right">
                                Copyright &copy; <a href="https://erav.lk/" target="_blank" class="no-link-style">eRav
                                    Technology</a> <?php echo date('Y') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- QR Code Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title" id="qrModalLabel">Scan QR Code</h5> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrCodeContainer">
                        <!-- QR code will be loaded here -->
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading QR Code...</p>
                    </div>
                    <p class="text-muted small mt-3">
                        Scan this QR code to access the application
                    </p>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="downloadQRCode()">Download QR</button>
                </div> -->
            </div>
        </div>
    </div>
</main>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Focus on email input when the page loads
        $('#email').focus();

        // Load QR code when modal is shown
        $('#qrModal').on('show.bs.modal', function () {
            loadQRCode();
        });
    });

    function togglePasswordVisibility() {
        var passwordField = document.getElementById('password');
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
                $('#qrCodeContainer').html(
                    '<div class="alert alert-danger">Failed to load QR code. Please try again.</div>'
                );
            }
        });
    }

    function downloadQRCode() {
        window.open('{{ route("qr.download") }}', '_blank');
    }
</script>
@endsection
