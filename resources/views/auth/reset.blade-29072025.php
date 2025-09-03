<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title><?= (isset($page_stitle)) ? $page_stitle : ' ShapeUP HRM- By Erav Technology' ?></title>
    <link rel="icon" type="image/x-icon" href="{{url('/public/images/hrm.png')}}" /> 
    <!-- Styles -->
    <link href="{{ url('public/css/styles.css') }}" rel="stylesheet"/>
    <link href="{{ url('public/css/custom_styles.css') }}" rel="stylesheet"/>
    <link href="{{ url('public/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet"/>
    <link href="{{ url('public/css/full_calendar.min.css') }}" rel="stylesheet"/>
    <link href="{{ url('public/css/font/flaticon.css') }}" rel="stylesheet"/>

    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet"/>
<!--link href="{{ asset('css/app.css') }}" rel="stylesheet"-->
    <script data-search-pseudo-elements defer
            src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.24.1/feather.min.js"
            crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css" rel="stylesheet"/>

    <style> 
        .no-underline {
            text-decoration: none;
            color: inherit; 
            font-weight: bold;
        }
        .no-underline:hover {
            text-decoration: none;
            color: inherit;
        }
        .custom-table {
                border-collapse: collapse;
                width: 100%;
            }

            .custom-table th, .custom-table td {
                text-align: center;
                font-size: 24px;
                padding: 20px;
            }

            .custom-table th:first-child {
                background-color: #fafbfd;
                color: #000;
            }
            .custom-table th:not(:first-child) {
                background-color: #87CEEB;
                color: #000;
            }

            .custom-table td:nth-child(2) {
                background-color: #32CD32; /* Green */
                color: #000;
            }

            .custom-table td:nth-child(3) {
                background-color: #FFA500; /* Orange */
                color: #000;
            }

            .custom-table td:nth-child(4) {
                background-color: #FF6347; /* Red */
                color: #000;
            }

            .row-label {
                font-weight: bold;
                font-size: 30px;
                text-align: right;
                padding-right: 20px;
                color: #000;
            }
    </style>
    </head>
    <body>

<div class="container">
    <h3>Manual Password Reset</h3>

    @if (session('status'))
        <div style="color: green;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('manual.reset.submit') }}">
                        {{ csrf_field() }}

                       

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Reset Password
                                </button>
                            </div>
                        </div>
                    </form>

                    <a href="{{ route('login') }}">Login</a>
</div>
 </body>
</html>

