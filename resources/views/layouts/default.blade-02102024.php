<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>  ShapeUP HRM - By eRav Technology</title>
    <!-- Styles -->
    <link href="{{ url('/public/css/styles.css') }}" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="{{url('/images/hrm.png')}}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .bg-laugfs {
            background-color: #ffcb05 !important;
        }
    </style>
</head>
<body>
    <div id="app">
        @if (Auth::guest())

        @else
        <nav class="topnav navbar navbar-expand shadow navbar-light bg-white" id="sidenavAccordion">
            <a class="navbar-brand d-none d-sm-block" href="{{ url('/') }}">
                ShapeUP HRM  - By eRav Technology
            </a><button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 mr-lg-2" id="sidebarToggle" href="#"><i data-feather="menu"></i></button>
           
            <ul class="navbar-nav align-items-center ml-auto">
                @if (Auth::guest())
                <li class="nav-item dropdown no-caret mr-3 dropdown-user"><a href="{{ route('login') }}">Login</a></li>
                <!--li class="nav-item dropdown no-caret mr-3 dropdown-user"><a href="{{ route('register') }}">Register</a></li-->
                @else
                <li class="nav-item dropdown no-caret mr-3 dropdown-user">
                    <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="img-fluid" src="></a>
                    <div class="dropdown-menu dropdown-menu-right border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                        <h6 class="dropdown-header d-flex align-items-center">
                            <img class="dropdown-user-img" src="" />
                            <div class="dropdown-user-details">
                                <div class="dropdown-user-details-name"> {{ Auth::user()->name }}</div>
                                <div class="dropdown-user-details-email">{{ Auth::user()->mail }}</div>
                            </div>
                        </h6>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#!"
                            ><div class="dropdown-item-icon"><i data-feather="settings"></i></div>
                            Account</a
                        ><a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();  document.getElementById('logout-form').submit();"><div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
                            Logout</a
                        >
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                    </div>
                </li>
                @endif
            </ul>
        </nav>
        @endif

        @yield('content')
    </div>
    <!-- Scripts -->
    <script src="{{ url('/public/js/app.js') }}"></script>
</body>
</html>
