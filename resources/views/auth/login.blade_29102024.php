@extends('layouts.default')

@section('content')
<main>

<div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-4">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header justify-content-center"><img src="{{url('/public/images/hrm.png')}}" class="img-fluid" alt="" style="width:200px; height:200px;"></div>
                                    <div class="card-body">
                                        <form class="form-horizontal" method="POST" action="{{ route('login') }}" autocomplete="off">
                                            {{ csrf_field() }}
                                            <div class="form-group mb-1 {{ $errors->has('email') ? ' has-error' : '' }}"><label class="small mb-2" for="inputEmailAddress">Email</label><input class="form-control form-control-sm" name="email" id="email" type="email" placeholder="Enter email address"  value="{{ old('email') }}" required autofocus/></div>
                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                            <div class="form-group mb-1 {{ $errors->has('password') ? ' has-error' : '' }}">
                                                <label class="small mb-2" for="inputPassword">Password</label>
                                                <div class="input-group">
                                                    <input style="height: 35px;" class="form-control form-control-sm" id="password" name="password" type="password" placeholder="Enter password" required/>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" onclick="togglePasswordVisibility()" style="cursor: pointer;">
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
                                                <div class="custom-control">
                                                    <input class="control-input" type="checkbox" name="remember" id="rememberPasswordCheck" {{ old('remember') ? 'checked' : '' }}> 
                                                    <label class="control-label" for="rememberPasswordCheck">Remember password</label></div>
                                            </div>
                                            <div class="form-group text-right mt-4 mb-0">
                                                <button type="submit" class="btn btn-primary btn-sm px-3">Login</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer bg-laugfs">
                                        <div class="row">
                                            <div class="col-md-12 small text-center">
                                                Copyright &copy; <a href="https://erav.lk" target="_blank" class="no-link-style">eRav Technology</a> <?php echo date('Y') ?>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
</main>

<script>
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
</script>
@endsection
