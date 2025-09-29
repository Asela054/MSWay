<!DOCTYPE html>
<!-- Website - www.codingnepalweb.com -->
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title><?= (isset($page_stitle)) ? $page_stitle : ' ShapeUP HRM- By Erav Technology' ?></title>
    <link rel="icon" type="image/x-icon" href="{{url('/images/hrm.png')}}" /> 
    <!-- Styles -->
    <link href="{{ url('/css/styles.css') }}" rel="stylesheet"/>
    <link href="{{ url('/css/custom_styles.css') }}" rel="stylesheet"/>
    <link href="{{ url('/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet"/>
    <link href="{{ url('/css/full_calendar.min.css') }}" rel="stylesheet"/>
    <link href="{{ url('/css/font/flaticon.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ url('/css/fontawesome/css/all.min.css') }}">

    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet"/>
    <!--link href="{{ asset('css/app.css') }}" rel="stylesheet"-->
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.24.1/feather.min.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <!-- <link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css" rel="stylesheet"/> -->

    <style> 
        .calendar {
            width: 310px;
            border-radius: 6px;
            overflow: hidden;
            background: #fff;
            /* box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); */
        }
        @media (max-width: 575.98px) {
            .calendar {
                width: 310px;
                border-radius: 6px;
                overflow: hidden;
                background: #fff;
                /* box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); */
            }
        }

        /* Header */
        .calendar-header {
            background: #1AC6D9;
            color: white;
            text-align: center;
            padding: 20px 10px;
        }

        .calendar-header .year {
            font-size: 16px;
            opacity: 0.8;
        }

        .calendar-header .date {
            font-size: 30px;
            margin-top: 5px;
            font-weight: bold;
        }

        /* Month navigation */
        .calendar-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            font-weight: bold;
        }

        .calendar-nav button {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }

        /* Calendar grid */
        .calendar-body {
            padding: 0 10px 15px;
        }

        .calendar-body table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .calendar-body table thead {
            background-color: #fff;
        }

        .calendar-body th {
            color: #666;
            font-weight: normal;
            padding: 8px 0;
        }

        .calendar-body td {
            padding: 10px;
            cursor: pointer;
            border-radius: 50%;
            font-size: 14px;
            position: relative;
        }

        .calendar-body td:hover {
            background: #eee;
        }

        .selected {
            background: #1AC6D9;
            color: white !important;
            border-radius: 50%;
        }

        /* Event Dots */
        td .dot-container {
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 2px;
        }

        td .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        /* Event List Section */
        .event-list {
            padding: 10px 20px 20px;
            border-top: 1px solid #eee;
        }

        .event-list h2 {
            font-size: 18px;
            color: #5d4697;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .event-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .event-item .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .no-events {
            color: #999;
            font-style: italic;
            font-size: 14px;
        }


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

        /* Profile css */
        .card-profile {
            display: flex;
            flex-direction: column;
            width: 100%;
            background-color: #fff;
            border-radius: 25px;
            padding: 25px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .card-profile::before {
            content: '';
            position: absolute;
            height: 18%;
            width: 100%;
            /* background-image: url('/images/profilebg.jpg');
            background-position: center;
            background-repeat: none;
            background-size: cover; */
            background-image: -moz-linear-gradient(135deg, rgba(26, 198, 217, 0.9) 0%, rgba(18, 145, 162, 0.9) 100%);
            background-image: -webkit-linear-gradient(135deg, rgba(26, 198, 217, 0.9) 0%, rgba(18, 145, 162, 0.9) 100%);
            background-image: linear-gradient(135deg, rgba(26, 198, 217, 0.9) 0%, rgba(18, 145, 162, 0.9) 100%);
                border-radius: 25px 25px 0 0;
                top: 0;
                left: 0;
            }

            .image {
                position: relative;
                height: 150px;
                width: 150px;
                background-color: white;
                padding: 3px;
                border-radius: 50%;
                margin-bottom: 10px;
                margin-left: auto;
                margin-right: auto;
            }

            .image .profile-image {
                height: 100%;
                width: 100%;
                border-radius: 50%;
                border: 3px solid white;
            }

            .card-profile .text-data {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .text-data .name {
                font-size: 23px;
                font-weight: 600;
                margin-bottom: 5px;
            }

            .text-data .job {
                font-size: 15px;
                font-weight: 550;
            }

            .dotted-join {
                display: flex;
                justify-content: space-between;
                position: relative;
                padding-bottom: 4px;
            }

            .dotted-join::after {
                content: '';
                position: absolute;
                bottom: 10px;
                left: 0;
                right: 0;
                border-bottom: 2px dotted #ccc;
                z-index: 0;
            }

            .dotted-join > span:first-child {
                background: white;
                padding-right: 4px;
                z-index: 1;
                position: relative;
            }

            .dotted-join > span:last-child {
                background: white;
                padding-left: 4px;
                z-index: 1;
                position: relative;
            }

            a,
            a:hover,
            a:focus,
            a:active {
                text-decoration: none;
            }

            .about-us-bg {
                background-position: right;
                background-repeat: no-repeat;
                -webkit-background-size: contain;
                background-size: contain;
            }

            .section-common-space {
                padding: 100px 0;
            }

            .section-header-v2 .section-title {
                text-transform: initial;
                margin-bottom: 30px;
                font-size: 36px;
                color: #1c1c1c;
            }

            /*==================   ABOUT US  ==================*/
            .tabbed-about-us .tab-pane {
                margin-bottom: 80px;
                display: none;
                border: none;
            }

            .tabbed-about-us .tab-content>.tab-pane {
                border: none;
            }

            .tabbed-about-us .tab-pane.active {
                display: block;
                -webkit-animation: fadeIn .5s ease-in-out .15s both;
                animation: fadeIn .5s ease-in-out .15s both;
            }

            .tabbed-about-us .img-wrapper {
                position: relative;
                min-height: 400px;
            }

            .tabbed-about-us .img-wrapper img {
                -webkit-box-shadow: 0px 40px 70px 0px rgba(0, 0, 0, 0.22);
                box-shadow: 0px 40px 70px 0px rgba(0, 0, 0, 0.22);
            }

            .tabbed-about-us .img-wrapper .img-one {
                position: absolute;
                top: 0;
                left: 0;
                display: none;
            }

            .tabbed-about-us .img-wrapper .img-two {
                position: absolute;
                top: 120px;
                left: 138px;
                display: none;
            }

            .tabbed-about-us .img-wrapper .img-three {
                position: absolute;
                top: 40px;
                left: 345px;
                display: none;
            }

            .tabbed-about-us .tab-pane.active .img-one {
                display: block;
                -webkit-animation: fadeIn .5s ease-in-out .15s both;
                animation: fadeIn .5s ease-in-out .15s both;
            }

            .tabbed-about-us .tab-pane.active .img-two {
                display: block;
                -webkit-animation: fadeIn .5s ease-in-out .50s both;
                animation: fadeIn .5s ease-in-out .50s both;
            }

            .tabbed-about-us .tab-pane.active .img-three {
                display: block;
                -webkit-animation: fadeIn .5s ease-in-out .85s both;
                animation: fadeIn .5s ease-in-out .85s both;
            }

            .tabbed-about-us .details-wrapper {
                padding-left: 30px;
            }

            .tabbed-about-us .details .title {
                text-transform: uppercase;
                color: #1c1c1c;
                margin-bottom: 50px;
            }

            .tabbed-about-us .details p {
                margin-bottom: 30px;
            }

            .tabbed-about-us .details p:last-child {
                margin-bottom: 0;
            }

            .tabbed-about-us .work-progress {
                margin-top: 60px;
            }

            .tabbed-about-us .tabs-nav {
                padding: 30px 0 25px;
                border: none;
                text-align: center;
                border-radius: 4px;
            }

            .tabbed-about-us .tabs-nav li {
                display: inline-block;
                text-transform: uppercase;
                text-align: center;
                margin-right: 130px;
                position: relative;
            }

            .tabbed-about-us .tabs-nav li:last-child {
                margin-right: 0;
            }

            .tabbed-about-us .tabs-nav li * {
                color: #666666;
                border-radius: 1.35rem;
            }

            .tabbed-about-us .tabs-nav li span.icon {
                display: block;
                font-size: 30px;
                padding-bottom: 5px;
                -webkit-transition: .3s;
                transition: .3s;
            }

            .tabbed-about-us .tabs-nav li:after {
                content: "";
                position: absolute;
                bottom: -25px;
                left: 0;
                width: 0;
                height: 2px;
                -webkit-transition: .3s;
                transition: .3s;
            }

            .tabbed-about-us .tabs-nav li.active:after {
                width: 100%;
            }

            /* ABOUT US VARAITONS **************************/
            /*about-us-bg*/
            .about-us-bg {
                background-position: right;
                background-repeat: no-repeat;
                -webkit-background-size: contain;
                background-size: contain;
            }

            .about-us-bg .section-header-v2 {
                margin-bottom: 90px;
            }

            /*tabbed-about-us-v2*/
            .tabbed-about-us-v2.tabbed-about-us .tabs-nav {
                background-color: transparent;
                padding: 0;
            }

            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li {
                display: table;
                margin-right: 30px;
                margin-bottom: 30px;
                background-color: #e9ecef;
                width: 144px;
                height: 144px;
                float: left;
                border-radius: 2px;
                -webkit-transition: .3s;
                transition: .3s;
                border-radius: 1.35rem;
            }

            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li>a {
                display: table-cell;
                vertical-align: middle;
                position: relative;
                z-index: 10;
            }

            .tabbed-about-us-v2.tabbed-about-us .details-wrapper {
                padding-left: 0;
            }

            .tabbed-about-us-v2.tabbed-about-us .details p {
                font-size: 18px;
                font-weight: 300;
            }

            .tabbed-about-us-v2.tabbed-about-us .bgcolor-major-gradient-overlay {
                z-index: 1;
                opacity: 0;
            }

            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li:hover .bgcolor-major-gradient-overlay,
            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li.active .bgcolor-major-gradient-overlay {
                opacity: 1;
            }

            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li:hover *,
            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li.active * {
                color: #fff;
            }

            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li:hover,
            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li.active {
                -webkit-box-shadow: 0px 25px 55px 0px rgba(0, 0, 0, 0.21), 0px 16px 28px 0px rgba(0, 0, 0, 0.22);
                box-shadow: 0px 25px 55px 0px rgba(0, 0, 0, 0.21), 0px 16px 28px 0px rgba(0, 0, 0, 0.22);
            }

            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li.active:hover:after,
            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li.active:after {
                display: none;
            }

            .tabbed-about-us-v2.tabbed-about-us .work-progress {
                margin-top: 40px;
            }

            /*------ End of about us  ------*/

            .bgcolor-major-gradient-overlay,
            .bgcolor-major-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10;
                -webkit-transition: .3s;
                transition: .3s;
            }

            .tabbed-about-us-v2.tabbed-about-us .work-progress .circle>span {
                color: #f3474b;
            }


            .tabbed-about-us .tabs-nav li:hover *,
            .tabbed-about-us .tabs-nav li.active * {
                color: #f3474b;
            }

            .bgcolor-major-gradient-overlay {
                background-image: -moz-linear-gradient(135deg, rgba(26, 198, 217, 0.9) 0%, rgba(18, 145, 162, 0.9) 100%);
                background-image: -webkit-linear-gradient(135deg, rgba(26, 198, 217, 0.9) 0%, rgba(18, 145, 162, 0.9) 100%);
                background-image: linear-gradient(135deg, rgba(26, 198, 217, 0.9) 0%, rgba(18, 145, 162, 0.9) 100%);
        }

        /* Large Devices, Wide Screens */
        @media only screen and (max-width : 1200px) {
            .tabbed-about-us .details-wrapper {
                padding-left: 0;
            }

            .tabbed-about-us .details .title {
                margin-bottom: 20px;
            }

            .tabbed-about-us .work-progress {
                margin-top: 30px;
            }

            .tabbed-about-us .tabs-nav li {
                margin-right: 100px;
            }
        }

        /* Medium Devices, Desktops */
        @media only screen and (max-width : 992px) {

            /*about us*/
            .tabbed-about-us .tabs-nav li {
                margin-right: 40px;
            }

            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li {
                width: 120px;
                height: 120px;
                margin-right: 20px;
                margin-bottom: 20px;
            }
        }


        /* Small Devices, Tablets */
        @media only screen and (max-width : 768px) {

            /*tabbed-about-us*/
            .tabbed-about-us .details .title {
                margin-bottom: 20px;
            }

            .tabbed-about-us .details-wrapper {
                padding-left: 0;
            }

            .tabbed-about-us .work-progress {
                margin-top: 30px;
            }

            .work-progress .each-item {
                margin-right: 25px;
                margin-bottom: 15px;
            }

            .tabbed-about-us .tab-pane {
                margin-bottom: 30px;
            }

            .tabbed-about-us:not(.tabbed-about-us-v2) .tabs-nav {
                overflow: hidden;
                padding: 15px 15px 5px;
            }

            .tabbed-about-us-v2 .tabs-nav {
                float: left;
            }

            .tabbed-about-us .tabs-nav li {
                margin-right: 15px;
                margin-bottom: 10px;
            }

            .tabbed-about-us .tabs-nav li:after {
                display: none;
            }

            .tabbed-about-us .tabs-nav li a {
                font-size: 13px;
            }

            .tabbed-about-us .tabs-nav li span.icon {
                display: none;
            }

            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li {
                height: initial;
                padding: 15px 10px;
            }

            .tabbed-about-us-v2.tabbed-about-us .details p {
                font-size: 14px;
                margin-bottom: 15px;
            }

            /*.pie*/
            .pie-value {
                font-size: 13px;
            }
        }

        /* Extra Small Devices, Phones */
        @media only screen and (max-width : 510px) {

            /*tabbed-about-us*/
            .tabbed-about-us .img-wrapper {
                min-height: 350px;
            }

            .tabbed-about-us .img-wrapper .img-one {
                width: 150px;
            }

            .tabbed-about-us .img-wrapper .img-two {
                width: 200px;
                top: 80px;
                left: 90px;
            }

            .tabbed-about-us .img-wrapper .img-three {
                width: 150px;
            }

            .about-us-bg {
                background: none !important;
            }

            .tabbed-about-us-v2.tabbed-about-us .tabs-nav li {
                width: 43%;
            }
        }

        /* Custom, iPhone Retina */
        @media only screen and (max-width : 360px) {
            .tabbed-about-us .img-wrapper .img-one {
                width: 130px;
            }

            .tabbed-about-us .img-wrapper .img-two {
                width: 160px;
                top: 70px;
                left: 30px;
            }

            .tabbed-about-us .img-wrapper .img-three {
                width: 130px;
                right: 0;
                left: initial;
            }
        }

        .tabbed-about-us .tab-pane ol, ul, dl {
            margin-top: 0;
            margin-bottom: 0;
        }

        .phone-display {
            display: flex;
            align-items: center;
        }
        
        .phone-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: #e9ecef;
            color: #000;
            border-radius: 50%;
            margin-right: 15px;
            font-size: 18px;
        }
        
        .phone-content {
            display: flex;
            flex-direction: column;
        }
        
        .phone-label {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 3px;
            font-weight: 500;
        }
        
        .phone-number {
            font-size: 14px;
            color: #343a40;
            font-weight: 600;
        }
    </style>
    @php if(Auth::user()->hasRole('Employee')){ @endphp
    <style>
        .home-section{
            position: relative;
            background: #E4E9F7;
            min-height: 100vh;
            top: 0;
            left: 0px;
            width: 100%;
            transition: all 0.5s ease;
        }
        @media (max-width: 575.98px) {
            .home-section{
                position: relative;
                background: #E4E9F7;
                min-height: 100vh;
                top: 0;
                left: 78px;
                width: calc(100% - 78px);
                transition: all 0.5s ease;
                /* z-index: 2; */
            }
        }
    </style>
    @php } @endphp
    @yield('style')
  </head>
  <body class="nav-fixed">
    <div id="app">
        <nav class="topnav navbar navbar-expand shadow navbar-light topnavbarcolor" id="sidenavAccordion">
            <a class="navbar-brand" href="{{ url('/home') }}" style="color: white">
                <img class="img-fluid" src="{{ url('/images/toplogo.png') }}" alt="Employee Photo"/>
            </a>
            @unless(Auth::user()->hasRole('Employee'))
                <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 mr-lg-2" id="sidebarToggle" href="#"><i class="fas fa-bars text-light"></i></button>
                {{-- @include('layouts.breadcrumblist') --}}
            @endunless
            <ul class="navbar-nav align-items-center ml-auto">
                @if (Auth::guest())
                    <li class="nav-item dropdown no-caret mr-3 dropdown-user"><a href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item dropdown no-caret mr-3 dropdown-user"><a href="{{ route('register') }}">Register</a>
                    </li>
                @else
                    <li class="nav-item dropdown no-caret mr-3 dropdown-user">
                        <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage"
                           href="javascript:void(0);" role="button" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false">
                           <!-- <img class="img-fluid" src="/images/{{ \App\EmployeePicture::where(['emp_id' =>  $empid=Auth::user()->emp_id ])->pluck('emp_pic_filename')->first() }}"/> -->
                            @php 
                                $id = Auth::user()->emp_id;

                                $employeePicture = \App\EmployeePicture::join('employees', 'employee_pictures.emp_id', '=', 'employees.id')
                                    ->where('employees.emp_id', $id)
                                    ->select('employee_pictures.emp_pic_filename')
                                    ->first();
                                    
                                $imagePath = '';
                                
                                if ($employeePicture && file_exists(public_path("images/{$employeePicture->emp_pic_filename}"))) {
                                    $imagePath = asset("images/{$employeePicture->emp_pic_filename}");
                                } else {
                                    $employeeGender = \App\Employee::where('emp_id', $id)->pluck('emp_gender')->first();
                                    if(empty($employeeGender)){
                                        $employeeGender = "Male";
                                    }
                                    $imagePath = $employeeGender == "Male" 
                                        ? asset("images/man.png") 
                                        : asset("images/girl.png");
                                }
                            @endphp

                            <img class="img-fluid" src="{{ $imagePath }}" alt="Employee Photo"/>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right border-0 shadow animated--fade-in-up"
                             aria-labelledby="navbarDropdownUserImage">
                            <h6 class="dropdown-header d-flex align-items-center">
                                <!-- <img class="dropdown-user-img"
                                     src="{{url('/images/user-profile.png')}}"/> -->
                                <img class="dropdown-user-img" src="{{ $imagePath }}" alt="Employee Photo"/>
                                <div class="dropdown-user-details">
                                    <div class="dropdown-user-details-name"> {{ Auth::user()->name }}</div>
                                    <div class="dropdown-user-details-email">{{ Auth::user()->email }}</div>
                                </div>
                            </h6>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('password-reset') }}">
                                <div class="dropdown-item-icon"><i data-feather="settings"></i></div>
                                Reset Password
			                </a>
                            <a class="dropdown-item" href="#!">
                                <div class="dropdown-item-icon"><i data-feather="settings"></i></div>
                                Account
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();  document.getElementById('logout-form').submit();">
                                <div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </li>
                @endif
            </ul>
        </nav>
        <div id="layoutSidenav">
            @unless(Auth::user()->hasRole('Employee'))
            <div>
                @include('layouts.side_bar')
            </div>
            @endunless
            <div id="layoutSidenav_content">
                <section class="home-section">
                    @yield('content')
                    <footer class="footer mt-auto footer-light">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6 small">Copyright &copy;  ShapeUP HRM V3.0 <?php echo date('Y') ?>
                                    Made  by <a href="https://www.erav.lk" target="_blank">eRAV
                                        technologies</a>
                                </div>
                                <div class="col-md-6 text-md-right small">
                                    <a href="#!">Privacy Policy</a>
                                    &middot;
                                    <a href="#!">Terms &amp; Conditions</a>
                                </div>
                            </div>
                        </div>
                    </footer>
                </section>
            </div>
        </div>
    </div>
<!-- Scripts -->
<script src="{{ url('/js/app.js') }}"></script>
<script src="{{ url('/js/jquery-3.4.1.min.js') }}"></script>
<script src="{{ url('/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ url('/js/scripts.js') }}"></script>
<script src="{{ url('/js/moment.js') }}"></script>
<script src="{{ url('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>

<!--script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js" ></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script-->

<!--script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.24.1/feather.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/components/prism-core.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/plugins/autoloader/prism-autoloader.min.js" crossorigin="anonymous"></script-->
{{-- <script src="{{ asset('/js/scripts.js') }}"></script> --}}
{{--    <script src="{{ asset('/js/bootstrap-datetimepicker.js') }}"></script>--}}


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function () {
        var isEmployee = {{ Auth::user()->hasRole('Employee') ? 'true' : 'false' }};
        
        function toggleSidebar() {
            // $('#sidebar').toggleClass('open', $(window).width() >= 992);
            $('body').toggleClass('sidenav-toggled', $(window).width() >= 992);
        }

        if(isEmployee==false){
            toggleSidebar(); // run on page load

            $(window).on('resize', toggleSidebar); // run on resize

            $('#sidebarToggle').on('click', function (e) {
                e.preventDefault();
                // if ($(window).width() < 992) {
                    $('#sidebar').toggleClass('open');
                    $('body').toggleClass('sidenav-toggled');
                // }
            });
        }
        else{
            $('body').toggleClass('sidenav-toggled', $(window).width() >= 992);
        }
    });
    $(document).ready(function(){
        window.scripturl = '{{ url('/scripts') }}';
    });
    $.extend(true, $.fn.dataTable.defaults, {
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 50  // Force 25 entries per page
    });

    function Otherconfirmation(title){
        return Swal.fire({
            title: "Are you sure?",
            text: title,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm",
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-danger'
            }
        }).then((result) => {
            return result.isConfirmed;
        });
    }

    function action(data) {
        if(data!=''){
            var obj=JSON.parse(data);
            var icon, title;

            if(obj.type=='success'){icon='success'; title=obj.message;}
            else if(obj.type=='primary'){icon='success'; title=obj.message;}
            else if(obj.type=='warning'){icon='warning'; title=obj.message;}
            else if(obj.type=='danger'){icon='error'; title=obj.message;}

            Swal.fire({
                position: "top-end",
                icon: icon,
                title: title,
                showConfirmButton: false,
                timer: 2500
            });
        }
    }

    function actionreload(data) {
        if(data!=''){
            var obj=JSON.parse(data);
            var icon, title;

            if(obj.type=='success'){icon='success'; title=obj.message;}
            else if(obj.type=='primary'){icon='success'; title=obj.message;}
            else if(obj.type=='warning'){icon='warning'; title=obj.message;}
            else if(obj.type=='danger'){icon='error'; title=obj.message;}

            Swal.fire({
                position: "top-end",
                icon: icon,
                title: title,
                showConfirmButton: false,
                timer: 2500
            }).then(() => {
                window.location.reload();
            });
        }
    }
</script>

@yield('script')
  </body>
</html>
