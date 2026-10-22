@extends('layouts.app')

@section('content')
@php $myDepartment = $employee->emp_department ?? null; @endphp
<main>
    <!-- <div class="page-header shadow">
        <div class="container-fluid">
            <div class="container-fluid">
                <div class="page-header-tabs">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#account-details" data-toggle="tab">Account Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#monthly-summary" data-toggle="tab">Monthly Summary</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#leave-apply" data-toggle="tab">Leave Apply</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#attendent" data-toggle="tab">Attendants</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div> -->

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                @php
                $imagePath = '';

                if (!empty($employee->emp_pic_filename) && file_exists(public_path('images/' .
                    $employee->emp_pic_filename))) {
                    $imagePath = asset('public/images/' . $employee->emp_pic_filename);
                } else {
                    $employeeGender = $employee->emp_gender ?? 'Male'; // Default to Male if null
                    $imagePath = $employeeGender === "Male"
                    ? asset('public/images/man.png')
                    : asset('public/images/girl.png');
                }
                @endphp
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                        <div class="card-profile">
                            <div class="image">
                                <img src="{{ $imagePath }}"  id="profileImagePreview" alt="" class="profile-image">
                                <form id="profileImageForm" action="{{ route('employees.update-image', $employee->emp_id) }}" method="POST" enctype="multipart/form-data">{{ csrf_field() }}
                                    <input type="hidden" name="_method" value="PUT">
                                    <div class="position-absolute bottom-0 end-0" style="bottom: 0;right: 30px;">
                                        <label for="profileImageInput" class="btn btn-primary btn-sm rounded-circle p-2" title="Upload new photo">
                                            <i class="fa fa-camera"></i>
                                            <input type="file" 
                                                id="profileImageInput" 
                                                name="profile_image" 
                                                accept="image/*" 
                                                class="d-none"
                                                onchange="previewImage(event)">
                                        </label>
                                    </div>
                                    <!-- Upload button and status message -->
                                    <div class="mt-2 text-center">
                                        <button type="submit" class="btn btn-sm btn-success d-none" id="uploadButton">
                                            Upload Image
                                        </button>
                                        <div id="uploadStatus" class="text-small mt-1"></div>
                                    </div>
                                </form>
                            </div>
                            <div class="text-data">
                                <span class="h2 font-weight-bold">
                                    {{$employee->emp_name_with_initial}}
                                </span>
                                <span class="h6 font-weight-lighter">
                                    {{$employee->title}}
                                </span>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 mt-3 small">
                                    <h6 class="font-weight-bold">Basic Information</h6>
                                    <div class="phone-display mt-2">
                                        <div class="phone-icon">
                                            <i class="fas fa-file"></i>
                                        </div>
                                        <div class="phone-content">
                                            <span class="phone-label">EPF No</span>
                                            <span class="phone-number">{{$employee->emp_etfno}}</span>
                                        </div>
                                    </div>
                                    <div class="phone-display mt-2">
                                        <div class="phone-icon">
                                            <i class="fas fa-passport"></i> 
                                        </div>
                                        <div class="phone-content">
                                            <span class="phone-label">NIC</span>
                                            <span class="phone-number">{{$employee->emp_national_id}}</span>
                                        </div>
                                    </div>
                                    <div class="phone-display mt-2">
                                        <div class="phone-icon">
                                            <i class="fas fa-map-marker"></i>
                                        </div>      
                                        <div class="phone-content">
                                            <span class="phone-label">Address</span>
                                            <span class="phone-number">{{$employee->emp_address}}</span>
                                        </div>
                                    </div>
                                    <div class="phone-display mt-2">
                                        <div class="phone-icon">
                                            <i class="fas fa-mobile"></i>
                                        </div>
                                        <div class="phone-content">
                                            <span class="phone-label">Mobile No</span>
                                            <span class="phone-number">{{$employee->emp_mobile}}</span>
                                        </div>
                                    </div>
                                    <div class="phone-display mt-2">
                                        <div class="phone-icon">
                                            <i class="fas fa-birthday-cake"></i>
                                        </div>
                                        <div class="phone-content">
                                            <span class="phone-label">Date of Birth</span>
                                            <span class="phone-number">{{$employee->emp_birthday}}</span>
                                        </div>
                                    </div>
                                    <div class="phone-display mt-2">
                                        <div class="phone-icon">
                                            <i class="fas fa-calendar-day"></i>
                                        </div>
                                        <div class="phone-content">
                                            <span class="phone-label">Join Date</span>
                                            <span class="phone-number">{{$employee->emp_join_date}}</span>  
                                        </div>
                                    </div>
                                    <div class="phone-display mt-2">
                                        <div class="phone-icon">
                                            <i class="fas fa-id-badge"></i>
                                        </div>
                                        <div class="phone-content">
                                            <span class="phone-label">Job Status</span>
                                            <span class="phone-number">{{$employee->emp_statusname}}</span>
                                        </div>
                                    </div>
                                    <div class="phone-display mt-2">
                                        <div class="phone-icon">
                                            <i class="fas fa-code-branch"></i>
                                        </div>
                                        <div class="phone-content">
                                            <span class="phone-label">Location</span>
                                            <span class="phone-number">{{$employee->location}}</span>
                                        </div>
                                    </div>
                                    <div class="phone-display mt-2">
                                        <div class="phone-icon">
                                            <i class="fas fa-bezier-curve"></i>
                                        </div>
                                        <div class="phone-content">
                                            <span class="phone-label">Department</span>
                                            <span class="phone-number">{{$employee->departmentname}}</span>
                                        </div>
                                    </div>                                        
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-9 col-xl-9">
                        <div class="card mt-sm-0 mt-4">
                            <div class="card-body">
                                <div class="tabbed-about-us tabbed-about-us-v2">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <!-- ========== NAV TABS ========== -->
                                            <ul class="tabs-nav" role="tablist">
                                                <li class="active" role="presentation"><a href="#profileinfo" aria-controls="profileinfo" role="tab" data-toggle="tab"><span class="icon"><i class="fas fa-user"></i></span>Profile Info</a><span class="bgcolor-major-gradient-overlay"></span></li>
                                                <li role="presentation"><a href="#attendanceinfo" aria-controls="attendanceinfo" role="tab" data-toggle="tab"><span class="icon"><i class="fas fa-calendar"></i></span>Attendance</a><span class="bgcolor-major-gradient-overlay"></span></li>
                                                <li role="presentation"><a href="#leaveinfo" aria-controls="leaveinfo" role="tab" data-toggle="tab"><span class="icon"><i class="fas fa-calendar-week"></i></span>Leave Info</a><span class="bgcolor-major-gradient-overlay"></span></li>
                                                <li role="presentation"><a href="#leaveapproval" aria-controls="leaveapproval" role="tab" data-toggle="tab"><span class="icon"><i class="fas fa-calendar-week"></i></span>Leave Approval</a><span class="bgcolor-major-gradient-overlay"></span></li>
                                                <li role="presentation"><a href="#salaryadvance" aria-controls="salaryadvance" role="tab" data-toggle="tab"><span class="icon"><i class="fas fa-money-bill"></i></span>Salary Advance</a><span class="bgcolor-major-gradient-overlay"></span></li>
                                                <li role="presentation"><a href="#salaryslip" aria-controls="salaryslip" role="tab" data-toggle="tab"><span class="icon"><i class="fas fa-receipt"></i></span>Salary Slips</a><span class="bgcolor-major-gradient-overlay"></span></li>
                                                {{--<li role="presentation"><a href="#production" aria-controls="production" role="tab" data-toggle="tab"><span class="icon"><i class="fa-light fa-ballot-check"></i></span>My Jobs</a><span class="bgcolor-major-gradient-overlay"></span></li>--}}
                                                <li role="presentation"><a href="#markattendance" aria-controls="markattendance" role="tab" data-toggle="tab"><span class="icon"><i class="fas fa-clock"></i></span>Mark Attendance</a><span class="bgcolor-major-gradient-overlay"></span></li>
                                                <li role="presentation"><a href="#attendancetimesheet" aria-controls="attendancetimesheet" role="tab" data-toggle="tab"><span class="icon"><i class="fas fa-file-contract"></i></span>Time Sheet</a><span class="bgcolor-major-gradient-overlay"></span></li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                            <div class="tab-content">
                                                <!-- ========== TAB PANE ========== -->
                                                <div role="tabpanel" class="tab-pane active" id="profileinfo">
                                                    <div class="card shadow-none bg-transparent">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                                    <h6 class="title-style small my-3"><span>About</span></h6>
                                                                    <label class="small font-weight-bolder mb-0">Full Name</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_fullname}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Name with Name</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_name_with_initial}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Gender</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_gender}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Marital Status</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_marital_status}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Nationality</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_nationality}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Date of Birth</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_birthday}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">NIC</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_national_id}}</h6>
                                                                </div>
                                                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                                    <h6 class="title-style small my-3"><span>Contact</span></h6>
                                                                    <label class="small font-weight-bolder mb-0">Permanent Address</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_address}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Temporary Address</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_addressT1}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Email</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_email}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Mobile no</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_mobile}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Office</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->tp1}}</h6>
                                                                </div>
                                                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                                    <h6 class="title-style small my-3"><span>Official</span></h6>
                                                                    <label class="small font-weight-bolder mb-0">EPF/ETF No</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_etfno}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">EMP No</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_id}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Join Date</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_join_date}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Company</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->companyname}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Location</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->location}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Department</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->departmentname}}</h6>
                                                                    <label class="small font-weight-bolder mb-0">Job Status</label>
                                                                    <h6 class="small my-0 text-uppercase">{{$employee->emp_statusname}}</h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>         
                                                <div role="tabpanel" class="tab-pane" id="attendanceinfo">   
                                                    <div class="card shadow-none bg-transparent">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                                                                    <div class="form-group">
                                                                        <label for="attendancemonth" class="col-form-label col-form-label-sm font-weight-bold text-dark">Month</label>
                                                                        <input type="month" class="form-control form-control-sm" id="attendancemonth" name="attendancemonth" value="{{ date('Y-m') }}" max="{{ date('Y-m') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                                <hr>
                                                                <div class="center-block fix-width scroll-inner">
                                                                    <table class="table table-striped table-sm small nowrap" style="width: 100%" id="attendtable">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>NAME</th>
                                                                                <th>LOCATION</th>
                                                                                <th>DATE</th>
                                                                                <th>CHECK IN</th>
                                                                                <th>CHECK OUT</th>
                                                                                <th>LATE TIMES</th>
                                                                                <th>OT HOUR</th>
                                                                                <th>DOUBLE OT</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody></tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>         
                                                <div role="tabpanel" class="tab-pane" id="leaveinfo"> 
                                                    <div class="card shadow-none bg-transparent">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 text-right">
                                                                    <button type="button" class="btn btn-primary btn-sm fa-pull-right px-3" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Leave Request</button>
                                                                </div>
                                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                                    <hr>
                                                                    <div class="center-block fix-width scroll-inner">
                                                                        <table class="table table-striped table-sm small nowrap" style="width: 100%" id="divicestable">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>ID</th>
                                                                                    <th>EMPLOYEE</th>
                                                                                    <th>DEPARTMENT</th>
                                                                                    <th>REQUEST LEAVE</th>
                                                                                    <th>LEAVE FROM</th>
                                                                                    <th>LEAVE TO</th>
                                                                                    <th>REASON</th>
                                                                                    <th>APPROVE STATUS</th>
                                                                                    <th>LEAVE TYPE</th>
                                                                                    <th>APPROVED LEAVE</th>
                                                                                    <th>LEAVE APPROVE STATUS</th>
                                                                                    <th class="text-right">ACTION</th>
                                                                                    <th class="d-none">empname</th>
                                                                                    <th class="d-none">empname</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>  
                                                </div>         
                                                <div role="tabpanel" class="tab-pane" id="leaveapproval"> 
                                                    <div class="card shadow-none bg-transparent">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                                        <div class="form-check">
                                                                            <input type="checkbox" class="form-check-input checkallocate" id="la_selectAll">
                                                                            <label class="form-check-label" for="la_selectAll">Select All Records</label>
                                                                        </div>
                                                                </div>
                                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 text-right">
                                                                        <button id="la_allapproveel" class="btn btn-primary btn-sm px-3"><i
                                                                                class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Approve All
                                                                            Leave</button>
                                                                </div>
                                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                                    <hr>
                                                                    <div class="center-block fix-width scroll-inner">
                                                                        <table class="table table-striped table-sm small nowrap" style="width: 100%" id="leaveapprovaltable">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th></th>
                                                                                    <th>EMP ID</th>
                                                                                    <th>NAME WITH INITIAL</th>
                                                                                    <th>DEPARTMENT </th>
                                                                                    <th>LEAVE TYPE</th>
                                                                                    <th>LEAVE FROM</th>
                                                                                    <th>LEAVE TO</th>
                                                                                    <th class="nowrap">REASON</th>
                                                                                    <th>COVERING BY</th>
                                                                                    <th>STATUS</th>
                                                                                    <th>APPROVAL STAGE</th>
                                                                                    <th class="text-right">ACTION</th>
                                                                                    <th class="d-none">ID</th>
                                                                                    <th class="d-none">EMPNAME</th>
                                                                                    <th class="d-none">CALLING</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>  
                                                </div>         
                                                <div role="tabpanel" class="tab-pane" id="salaryadvance"> 
                                                    <div class="card shadow-none bg-transparent">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 text-right">
                                                                    <button type="button" class="btn btn-primary btn-sm fa-pull-right px-3" name="create_record_advance" id="create_record_advance"><i class="fas fa-plus mr-2"></i>Salary Advance Request</button>
                                                                </div>
                                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                                    <hr>
                                                                    <div class="center-block fix-width scroll-inner">
                                                                        <table class="table table-striped table-sm small nowrap" style="width: 100%" id="salaryadvancetable">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>EMP ID</th>
                                                                                    <th>EMPLOYEE</th>
                                                                                    <th>DEPARTMENT</th>
                                                                                    <th>DATE</th>
                                                                                    <th>REQUEST AMOUNT</th>
                                                                                    <th>PAID AMOUNT</th>
                                                                                    <th>APPROVE STATUS</th>
                                                                                    <th class="text-right">ACTION</th>
                                                                                    <th class="d-none">id</th>
                                                                                    <th class="d-none">emp_name_with_initial</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>  
                                                </div>         
                                                <div role="tabpanel" class="tab-pane" id="salaryslip">
                                                    <div class="card shadow-none bg-transparent">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                                                                    <label for="selectedmonth" class="col-form-label col-form-label-sm font-weight-bold text-dark">Salary  Period</label>
                                                                    <select name="selectedmonth" id="selectedmonth" class="form-control form-control-sm">
                                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                                        @foreach($payment_period as $schedule)
                                                                        <option value="{{$schedule->id}}"
                                                                            data-selectedmonth="{{ \Carbon\Carbon::parse($schedule->payment_period_fr)->format('Y-m') }}"
                                                                            data-payroll="{{$schedule->payroll_process_type_id}}"
                                                                            data-lastday="{{$schedule->payment_period_to}}"
                                                                            data-payroll="{{$schedule->payroll_process_type_id}}">
                                                                            {{$schedule->payment_period_fr}} to
                                                                            {{$schedule->payment_period_to}}
                                                                        </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                                                                    <label for="selectedmonth"  class="col-form-label col-form-label-sm font-weight-bold text-dark">&nbsp;</label><br>
                                                                    <form id="frmExport" method="post" target="_blank" action="{{ url('get_employee_salarysheet') }}">
                                                                        {{ csrf_field() }}

                                                                        <input type="hidden" name="payslip_id" id="payslip_id" value="" />
                                                                        <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" value="" />
                                                                        <input type="hidden" name="period" id="period" value="" />
                                                                        <input type="hidden" name="month" id="month" value="" />

                                                                        <input type="hidden" name="rpt_location_id" id="rpt_location_id" value="" />
                                                                        <input type="hidden" name="rpt_period_id" id="rpt_period_id" value="" />
                                                                        <input type="hidden" name="rpt_emp_id" id="rpt_emp_id" value="" />

                                                                        <button type="submit" id="print_record" class="btn btn-sm btn-success" disabled>Download PaySlip</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                                    <hr>
                                                                    <div class="center-block fix-width scroll-inner">
                                                                        <div id="salaryinfo"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div role="tabpanel" class="tab-pane" id="production">   
                                                    <div class="card shadow-none bg-transparent">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                                                                    <div class="form-group">
                                                                        <label for="timesheetmonth" class="col-form-label col-form-label-sm font-weight-bold text-dark">Month</label>
                                                                        <input type="month" class="form-control form-control-sm" id="timesheetmonth" name="timesheetmonth" value="{{ date('Y-m') }}" max="{{ date('Y-m') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                                <hr>
                                                                <div class="mb-2">
                                                                    <span class="badge px-2 mb-2" style="background-color:#f8d7da;color:#842029;">&nbsp;</span> Not Approved
                                                                    <span class="badge px-2 mb-2" style="background-color:#fff3cd;color:#664d03;">&nbsp;</span> Pending
                                                                </div>
                                                                <div class="center-block fix-width scroll-inner">
                                                                    <table class="table table-striped table-sm small nowrap" style="width: 100%" id="productiontable">
                                                                       <thead>
                                                                            <tr>
                                                                                <th>DATE</th>
                                                                                <th>IN TIME</th>
                                                                                <th>OUT TIME</th>
                                                                                <th>LATE</th>
                                                                                <th>MC NO</th>
                                                                                <th>STYLE DETAILS</th>
                                                                                <th>TARGET</th>
                                                                                <th>PRODUCE</th>
                                                                                <th>PLUS/MINUS</th>
                                                                                <th>PRO AVG</th>
                                                                                <th>WEIGHTED AVG</th>
                                                                                <th>PRO INS</th>
                                                                                <th>OT</th>
                                                                                <th>TRP: ALL</th>
                                                                                <th>ATT: ALL</th>
                                                                                <th>NIG: ALL</th>
                                                                                <th>TRG: BO</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody></tbody>
                                                                        <tbody id="productionMissingTbody"></tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="markattendance">
                                                    <div class="card shadow-none bg-transparent">
                                                        <div class="card-body">
                                                            <div class="row mb-3 align-items-end">
                                                                <div class="col-12 col-md-4">
                                                                    <label class="small font-weight-bold text-dark">Select Location</label>
                                                                    <select id="mark_location" class="form-control form-control-sm">
                                                                        <option value="">-- Select Location --</option>
                                                                        @foreach($branches as $branch)
                                                                            <option value="{{ $branch->id }}">{{ $branch->location }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div id="reasonBox" class="mt-3" style="display:none;">
                                                                        <textarea id="reason_text" maxlength="255" class="form-control form-control-sm" rows="3" placeholder="Enter the reason" style="resize:none;"></textarea>
                                                                        <button type="button" id="submitReasonBtn" class="btn btn-primary btn-sm btn-block mt-2">Submit with Reason</button>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-md-8 mt-2">
                                                                    <div class="d-flex align-items-center flex-wrap">
                                                                        <button type="button" id="clockBtn" class="btn btn-success btn-sm px-4 flex-shrink-0" disabled>
                                                                            <i class="fas fa-clock mr-1"></i><span id="clockBtnText">Clock In</span>
                                                                        </button>
                                                                        <span id="clock_status" class="ml-2 font-weight-bold small" style="word-break:break-word;min-width:0;"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div role="tabpanel" class="tab-pane" id="attendancetimesheet">   
                                                    <div class="card shadow-none bg-transparent">
                                                        <div class="card-body">
                                                            <div class="row align-items-end">
                                                                <div class="col-sm-12 col-md-3">
                                                                    <div class="form-group mb-0">
                                                                        <label for="ts_from_date" class="col-form-label col-form-label-sm font-weight-bold text-dark">From Date*</label>
                                                                        <input type="date" id="ts_from_date" name="ts_from_date"
                                                                            class="form-control form-control-sm" value="{{date('Y-m-d') }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-12 col-md-3">
                                                                    <div class="form-group mb-0">
                                                                        <label for="ts_to_date" class="col-form-label col-form-label-sm font-weight-bold text-dark">To Date*</label>
                                                                        <input type="date" id="ts_to_date" name="ts_to_date"
                                                                            class="form-control form-control-sm" value="{{date('Y-m-d') }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-12 col-md-4 d-flex align-items-end" style="gap:6px;">
                                                                    <button type="button" id="ts_search_btn" class="btn btn-primary btn-sm px-3">
                                                                        <i class="fas fa-search mr-1"></i> Search
                                                                    </button>
                                                                    <button type="button" id="ts_export_pdf_btn" class="btn btn-danger btn-sm px-3 d-none">
                                                                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 mt-3">
                                                                <hr>
                                                                <div id="ts_employee_list"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>  

                                            </div>
                                        </div>
                                    </div>      
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Area Start -->
<div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Add Leave Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <span id="form_result"></span>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <table class="table table-sm small">
                                <thead>
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>Total</th>
                                        <th>Taken</th>
                                        <th>Available</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td> <span> Annual </span> </td>
                                        <td> <span id="ua_annual_total"></span> </td>
                                        <td> <span id="ua_annual_taken"></span> </td>
                                        <td> <span id="ua_annual_available"></span> </td>
                                    </tr>
                                    <tr>
                                        <td> <span> Casual </span> </td>
                                        <td> <span id="ua_casual_total"></span> </td>
                                        <td> <span id="ua_casual_taken"></span> </td>
                                        <td> <span id="ua_casual_available"></span> </td>
                                    </tr>
                                    <tr>
                                        <td> <span>Medical</span> </td>
                                        <td> <span id="ua_med_total"></span> </td>
                                        <td> <span id="ua_med_taken"></span> </td>
                                        <td> <span id="ua_med_available"></span> </td>
                                    </tr>
                                    <tr>
                                        <td> <span id="ua_weekly_title"></span> </td>
                                        <td> <span id="ua_weekly_total"></span> </td>
                                        <td> <span id="ua_weekly_taken"></span> </td>
                                        <td> <span id="ua_weekly_available"></span> </td>
                                    </tr>
                                </tbody>
                            </table>
                            <span id="ua_leave_msg"></span>
                        </div>
                        <form method="post" id="formTitle" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="form-row mb-1">
                                <div class="col-sm-12 col-md-12">
                                    <label class="small font-weight-bolder text-dark">Leave Type</label>
                                    <select name="leavetype" id="ua_leavetype" class="form-control form-control-sm">
                                        <option value="">Select</option>
                                        @foreach($leavetype as $leavetypes)
                                        <option value="{{$leavetypes->id}}">{{$leavetypes->leave_type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col-sm-12 col-md-12">
                                    <label class="small font-weight-bolder text-dark">Select Employee*</label>
                                    <select name="employee_f" id="employee_f" class="form-control form-control-sm" required>
                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row mb-1">
                                <div class="col-sm-12 col-md-6">
                                    <label class="small font-weight-bolder text-dark">From*</label>
                                    <input type="date" name="fromdate" id="fromdate"
                                            class="form-control form-control-sm" placeholder="YYYY-MM-DD" required/>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <label class="small font-weight-bolder text-dark">To*</label>
                                    <input type="date" name="todate" id="todate"
                                            class="form-control form-control-sm" placeholder="YYYY-MM-DD" required/>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col-sm-12 col-md-12">
                                    <label class="small font-weight-bolder text-dark">Half Day/ Short* <span id="half_short_span"></span> </label>
                                    <select name="half_short" id="half_short" class="form-control form-control-sm" required>
                                        <option value="0.00">Select</option>
                                        <option value="0.25">Short Leave</option>
                                        <option value="0.5">Half Day</option>
                                        <option value="1.00">Full Day</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col-sm-12 col-md-12">
                                    <label class="small font-weight-bolder text-dark">Reason</label>
                                    <input type="text" name="reason" id="reason" class="form-control form-control-sm"/>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col-sm-12 col-md-12">
                                    <label class="small font-weight-bolder text-dark">Covering Employee</label>
                                    <select name="coveringemployee" id="coveringemployee"
                                        class="form-control form-control-sm">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group mt-3">
                                <input type="submit" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-3" value="Add"/>
                            </div>
                            
                            <input type="hidden" name="action" id="action" value="Add"/>
                            <input type="hidden" name="hidden_id" id="hidden_id"/>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header p-2">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col text-center">
                        <h4 class="font-weight-normal">Are you sure you want to remove this data?</h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" name="ok_button" id="ok_button" class="btn btn-danger px-3 btn-sm">OK
                </button>
                <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- ========== SALARY ADVANCE MODAL ========== -->
<div class="modal fade" id="saFormModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="saModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="saModalLabel">Salary Advance Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <span id="sa_form_result"></span>
                        <form method="post" id="saFormTitle" class="form-horizontal">
                            {{ csrf_field() }}

                            <div class="form-row mb-2">
                                <div class="col-md-6">
                                    <label class="small font-weight-bolder text-dark">Select Employee*</label>
                                    <select name="employee" id="sa_employee" class="form-control form-control-sm"
                                        style="pointer-events: none; background-color: #e9ecef;" required>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="small font-weight-bolder text-dark">Date*</label>
                                    <input type="date" name="date" id="sa_date" class="form-control form-control-sm" required>
                                    <span id="sa_date_error" class="text-danger small"></span>
                                </div>
                            </div>

                            <div class="form-row mb-2">
                                <div class="col-md-6">
                                    <label class="small font-weight-bold text-dark">Available Amount</label>
                                    <input type="number" name="available_amount" id="sa_available_amount"
                                        class="form-control form-control-sm" placeholder="Available Amount"
                                        step="0.01" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="small font-weight-bold text-dark">Request Amount*</label>
                                    <input type="number" name="request_amount" id="sa_request_amount"
                                        class="form-control form-control-sm" placeholder="Request Amount"
                                        step="0.01" required>
                                </div>
                            </div>

                            <div class="form-row mb-2">
                                <div class="col-md-6">
                                    <label class="small font-weight-bold text-dark">Reason</label>
                                    <input type="text" name="remark" id="sa_remark"
                                        class="form-control form-control-sm" placeholder="Reason">
                                </div>
                            </div>

                            <div class="form-group mt-2">
                                <button type="submit" name="sa_action_button" id="sa_action_button"
                                    class="btn btn-primary btn-sm fa-pull-right px-4">
                                    <i class="fas fa-plus"></i>&nbsp;Add
                                </button>
                            </div>
                            <input type="hidden" name="action" id="sa_action" value="Add" />
                            <input type="hidden" name="hidden_id" id="sa_hidden_id" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ========== END SALARY ADVANCE MODAL ========== -->

<!-- ========== LEAVE APPROVAL MODALS ========== -->
<!-- Single record approval -->
<div class="modal fade" id="la_confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="la_confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="la_confirmModalLabel">Approval Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span id="la_form_result"></span>
                <div class="form-group">
                    <label class="small font-weight-bolder text-dark">Comment</label>
                    <textarea class="form-control form-control-sm" id="la_comment" name="la_comment" rows="3"></textarea>
                </div>
                <div class="radio">
                    <label><input type="radio" name="la_status" value="Approved"> Approve</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="la_status" value="Rejected"> Reject</label>
                </div>
                <input type="hidden" id="la_id" />
                <input type="hidden" id="la_emp_id" />
                <input type="hidden" id="la_app_level" value="1" />
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-primary px-3 btn-sm" id="la_approve"><i class="fas fa-check mr-1"></i>Confirm</button>
            </div>
        </div>
    </div>
</div>
<!-- Approve-All confirmation -->
<div class="modal fade" id="la_approveconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="la_approveconfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="la_approveconfirmModalLabel">All Approval Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span id="la_all_form_result"></span>
                <div class="form-group">
                    <label class="small font-weight-bolder text-dark">Comment</label>
                    <textarea class="form-control form-control-sm" id="la_allcomment" name="la_allcomment" rows="3"></textarea>
                </div>
                <div class="radio">
                    <label><input type="radio" name="la_allstatus" value="Approved"> Approve</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="la_allstatus" value="Rejected"> Reject</label>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-primary px-3 btn-sm" id="la_approveall"><i class="fas fa-check mr-1"></i>Confirm All</button>
            </div>
        </div>
    </div>
</div>
<!-- ========== END LEAVE APPROVAL MODALS ========== -->

@endsection
@section('script')
<script>
    var empid = {{$emp_id}};
    var emprecordid = {{$emprecordid}};
    var emp_name_with_initial = '{{$emp_name_with_initial}}';
    var calling_name = '{{$calling_name}}';
    $(document).ready(function () {
        $('#user_information_menu_link').addClass('active');
        $('#user_information_menu_link_icon').addClass('active');
        // $("#print_record").prop('disabled', true);
        var empcompany = {{$emp_company}};
        var emp_name_with_initial = '{{$emp_name_with_initial}}';
        var calling_name = '{{$calling_name}}';

        load_dt(empid);
        attendent_load_dt(empid);

        let employee_f = $('#employee_f');
        console.log(empid, emp_name_with_initial);
        
        if (empid && emp_name_with_initial) {
            var option = new Option(emp_name_with_initial, empid, true, true);
            employee_f.append(option).trigger('change');
        }

        if (empcompany == '' || empcompany == null || empcompany == 0) {
            $('#btn-filter').prop('disabled', true);
            $('#locationerrormsg').text('Work Location Not Assign!!');
        } else {
            $('#btn-filter').prop('disabled', false);
            $('#locationerrormsg').text('');
        }

        $('#selectedmonth').change(function () {
            let selectedOption = $('#selectedmonth option:selected'); // ✅ Get selected <option>
            let selectedmonthid = $('#selectedmonth').val(); // Get value of <select>
            let selectedmonth = selectedOption.data('selectedmonth');
            let lastday = selectedOption.data('lastday');
            $("#print_record").prop('disabled', true);

            $('#rpt_location_id').val(empcompany);
            $('#rpt_period_id').val(selectedmonthid);
            $('#rpt_emp_id').val(empid);

            if (!selectedmonth) {
                $('#selectedmonth').focus();
                return false;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                url: '{!! route("get_employee_monthlysummery") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    salaryperiodid: selectedmonthid,
                    selectedmonth: selectedmonth,
                    lastday: lastday,
                    emprecordid: emprecordid,
                    empid: empid,
                    empcompany: empcompany

                },
                success: function (data) {
                    console.log(data);
                    $("#print_record").prop('disabled', false);
                    $("#salaryinfo").html(data.htmlcontent);
                }
            });

        });

        // leave apply part
        // let c_employee = $('#coveringemployee');
        $('#coveringemployee').select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            parent: '#formModal',
            ajax: {
                url: '{{url("employee_list_sel2")}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    }
                },
                cache: true
            }
        });

        $('#formFilter').on('submit', function (e) {
            e.preventDefault();
            let department = $('#department_f').val();
            let employee = $('#employee_f').val();
            let location = $('#location_f').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            load_dt(department, employee, location, from_date, to_date);
        });

        // $(document).on('change', '#fromdate', function () {
        //     show_no_of_days();
        // });

        // $(document).on('change', '#todate', function () {
        //     show_no_of_days();
        // });

        $(document).on('change', '#half_short', function () {
            // show_no_of_days();
        });

        $('#employee').change(function () {
            var _token = $('input[name="_token"]').val();
            var leavetype = $('#leavetype').val();
            var emp_id = $('#employee').val();
            var status = $('#employee option:selected').data('id');

            if (emp_id != '') {
                $.ajax({
                    url: "{{ url('getEmployeeLeaveStatus') }}",
                    method: "POST",
                    data: {
                        status: status,
                        emp_id: emp_id,
                        leavetype: leavetype,
                        _token: _token
                    },
                    success: function (data) {

                        $('#leave_msg').html('');

                        $('#annual_total').html(data.total_no_of_annual_leaves);
                        $('#annual_taken').html(data.total_taken_annual_leaves);
                        $('#annual_available').html(data.available_no_of_annual_leaves);

                        $('#casual_total').html(data.total_no_of_casual_leaves);
                        $('#casual_taken').html(data.total_taken_casual_leaves);
                        $('#casual_available').html(data.available_no_of_casual_leaves);

                        $('#med_total').html(data.total_no_of_med_leaves);
                        $('#med_taken').html(data.total_taken_med_leaves);
                        $('#med_available').html(data.available_no_of_med_leaves);

                        let msg = '' +
                            '<div class="alert alert-warning text-sm" style="padding: 3px;"> ' +
                            data.leave_msg +
                            '</div>'

                        if (data.leave_msg != '') {
                            $('#leave_msg').html(msg);
                        }

                    }
                });
            }




        });

        $('#leavetype').change(function () {
            var _token = $('input[name="_token"]').val();
            var leavetype = $('#leavetype').val();
            var emp_id = $('#employee').val();
            var status = $('#employee option:selected').data('id');

            if (leavetype != '' && emp_id != '') {
                $.ajax({
                    url: "{{ url('getEmployeeLeaveStatus') }}",
                    method: "POST",
                    data: {
                        status: status,
                        emp_id: emp_id,
                        leavetype: leavetype,
                        _token: _token
                    },
                    success: function (data) {

                        $('#leave_msg').html('');

                        $('#annual_total').html(data.total_no_of_annual_leaves);
                        $('#annual_taken').html(data.total_taken_annual_leaves);
                        $('#annual_available').html(data.available_no_of_annual_leaves);

                        $('#casual_total').html(data.total_no_of_casual_leaves);
                        $('#casual_taken').html(data.total_taken_casual_leaves);
                        $('#casual_available').html(data.available_no_of_casual_leaves);

                        $('#med_total').html(data.total_no_of_med_leaves);
                        $('#med_taken').html(data.total_taken_med_leaves);
                        $('#med_available').html(data.available_no_of_med_leaves);

                        let msg = '' +
                            '<div class="alert alert-warning text-sm" style="padding: 3px;"> ' +
                            data.leave_msg +
                            '</div>'

                        if (data.leave_msg != '') {
                            $('#leave_msg').html(msg);
                        }

                    }
                });
            }

        });

        $('#employee').change(function () {
            var _token = $('input[name="_token"]').val();
            var emp_id = $('#employee').val();

            if (emp_id != '') {
                $.ajax({
                    url: "{{ url('getEmployeeCategory') }}",
                    method: "POST",
                    dataType: 'json',
                    data: {
                        emp_id: emp_id,
                        _token: _token
                    },
                    success: function (data) {

                        let short_leave_enabled = data.short_leave_enabled;
                        if (short_leave_enabled == 0) {
                            $("#half_short option[value*='0.25']").prop('disabled', true);
                            $('#half_short_span').html(
                                '<text class="text-warning"> Short Leave Disabled by Job Category </text>'
                                );
                        } else {
                            $("#half_short option[value*='0.25']").prop('disabled', false);
                            $('#half_short_span').html('');
                        }

                    }
                });
            }

        });

        // Get approve person Email address
        $('#approveby').change(function () {
            var _token = $('input[name="_token"]').val();
            var emp_id = $('#approveby').val();

            if (emp_id != '') {
                $.ajax({
                    url: "{{ url('getEmployeeCategory') }}",
                    method: "POST",
                    dataType: 'json',
                    data: {
                        emp_id: emp_id,
                        _token: _token
                    },
                    success: function (data) {
                        $('#approveemail').val(data.result.employee_email);
                    }
                });
            }

        });

        $('#todate').change(function () {

            var assign_leave = $('#assign_leave').val();


            var todate = $('#fromdate').val();
            var fromdate = $('#todate').val();
            var date1 = new Date(todate);
            var date2 = new Date(fromdate);
            var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10);

            var leaveavailable = $('#available_leave').val();
            var assign_leave = $('#assign_leave').val();

            if (leaveavailable != '') {
                $('#available_leave').val(leaveavailable);
            } else {
                $('#available_leave').val(assign_leave);
            }


            if (leaveavailable <= diffDays) {
                $('#message').html("<div class='alert alert-danger'>You Cant Apply, You Have " + assign_leave +
                    " Days Only</div>");
            } else {
                $('#message').html("");

            }


        });
        
        // Function to load leave balance for the fixed employee
        function loadUaLeaveStatus() {
            var _token = $('meta[name="csrf-token"]').attr('content');
            var leavetype = $('#ua_leavetype').val();
            var fromdate = $('#fromdate').val();
            var todate = $('#todate').val();

            $.ajax({
                url: "{{ url('getEmployeeLeaveStatus') }}",
                method: "POST",
                data: {
                    emp_id: empid,
                    leavetype: leavetype,
                    fromdate: fromdate,
                    todate: todate,
                    _token: _token
                },
                success: function (data) {
                    $('#ua_annual_total').html(data.total_no_of_annual_leaves);
                    $('#ua_annual_taken').html(data.total_taken_annual_leaves);
                    $('#ua_annual_available').html(data.available_no_of_annual_leaves);

                    $('#ua_casual_total').html(data.total_no_of_casual_leaves);
                    $('#ua_casual_taken').html(data.total_taken_casual_leaves);
                    $('#ua_casual_available').html(data.available_no_of_casual_leaves);

                    $('#ua_med_total').html(data.total_no_of_med_leaves);
                    $('#ua_med_taken').html(data.total_taken_med_leaves);
                    $('#ua_med_available').html(data.available_no_of_med_leaves);

                    $('#ua_weekly_total').html(data.total_no_of_weekly_leaves);
                    $('#ua_weekly_taken').html(data.total_taken_weekly_leaves);
                    $('#ua_weekly_available').html(data.available_no_of_weekly_leaves);

                    $('#ua_weekly_title').html(data.other_leaves_title);

                    $('#ua_leave_msg').html('');
                    if (data.leave_msg && data.leave_msg != '') {
                        $('#ua_leave_msg').html('<div class="alert alert-warning text-sm" style="padding: 3px;">' + data.leave_msg + '</div>');
                    }
                }
            });
        }

        $('#create_record').click(function () {
            // $('.modal-title').text('Apply Leave');
            // $('#action_button').val('Add');
            // $('#action').val('Add');
            // $('#form_result').html('');

            $('#formModal').modal('show');
            loadUaLeaveStatus();
        });

        // Reload leave balance when leave type changes
        $('#ua_leavetype').on('change', function () {
            loadUaLeaveStatus();
        });

        // Reload leave balance when to-date changes
        $('#todate').on('change', function () {
            loadUaLeaveStatus();
        });

        $('#formTitle').on('submit', function (event) {
            event.preventDefault();
            var action_url = '';

            if ($('#action').val() == 'Add') {
                action_url = "{{ route('leaverequestinsert') }}";
            }
            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('leaverequestupdate') }}";
            }

            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {
                    if (data.errors) {
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: '',
                            message: 'Record Error',
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        action(actionJSON);
                    }
                    if (data.success) {
                        const actionObj = {
                            icon: 'fas fa-save',
                            title: '',
                            message: data.success,
                            url: '',
                            target: '_blank',
                            type: 'success'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        $('#formTitle')[0].reset();
                        actionreload(actionJSON);
                    }
                }
            });
        });

        // $(document).on('click', '.edit', function () {
        //     var id = $(this).attr('id');
        //     $('#form_result').html('');
        //     $.ajax({
        //         url: "LeaveApply/" + id + "/edit",
        //         dataType: "json",
        //         success: function (data) {
        //             $('#leavetype').val(data.result.leave_type);

        //             let empOption = $("<option selected></option>").val(data.result.emp_id)
        //                 .text(data.result.employee.emp_name_with_initial);
        //             $('#employee').append(empOption).trigger('change');

        //             let coveringemployeeOption = $("<option selected></option>").val(data
        //                 .result.emp_covering).text(data.result.covering_employee
        //                 .emp_name_with_initial);
        //             $('#coveringemployee').append(coveringemployeeOption).trigger('change');

        //             let approvebyOption = $("<option selected></option>").val(data.result
        //                 .leave_approv_person).text(data.result.approve_by
        //                 .emp_name_with_initial);
        //             $('#approveby').append(approvebyOption).trigger('change');

        //             $('#employee').val(data.result.emp_id);
        //             $('#fromdate').val(data.result.leave_from);
        //             $('#todate').val(data.result.leave_to);
        //             $('#half_short').val(data.result.half_short);
        //             $('#no_of_days').val(data.result.no_of_days);
        //             $('#reson').val(data.result.reson);
        //             $('#comment').val(data.result.comment);
        //             $('#coveringemployee').val(data.result.emp_covering);
        //             $('#approveby').val(data.result.leave_approv_person);
        //             $('#available_leave').val(data.result.total_leave);
        //             $('#assign_leave').val(data.result.assigned_leave);
        //             $('#hidden_id').val(id);
        //             $('.modal-title').text('Edit Leave');
        //             $('#action_button').val('Edit');
        //             $('#action').val('Edit');
        //             $('#formModal').modal('show');
        //         }
        //     })
        // });
        $(document).on('click', '.edit',async function () {
            var r = await Otherconfirmation("You want to Edit this ? ");
            if (r == true) {
                var id = $(this).attr('id');
                $('#form_result').html('');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                $.ajax({
                    url: '{!! route("leaverequestedit") !!}',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        id: id
                    },
                    success: function (data) {
                        let empOption = $("<option selected></option>").val(data.result.emp_id).text(data.result.emp_name);
                        $('#employee_f').append(empOption).trigger('change');
                        $('#employee_f').val(data.result.emp_id);
                        $('#fromdate').val(data.result.from_date);
                        $('#todate').val(data.result.to_date);
                        $('#half_short').val(data.result.leave_category);
                        $('#reason').val(data.result.reason);

                        let covempOption = $("<option selected></option>").val(data.result.covering_emp).text(data.result.covering_emp_name);
                        $('#coveringemployee').append(covempOption).trigger('change');
                        $('#coveringemployee').val(data.result.covering_emp);

                        $('#hidden_id').val(id);
                        $('.modal-title').text('Edit Leave Request');
                        $('#action_button').val('Edit');
                        $('#action').val('Edit');
                        $('#formModal').modal('show');
                    }
                })
            }
        });

        var user_id;

        // $(document).on('click', '.delete', function () {
        //     user_id = $(this).attr('id');
        //     $('#confirmModal').modal('show');
        // });
        $(document).on('click', '.delete',async function () {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (r == true) {
                    user_id = $(this).attr('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                    })
                $.ajax({
                    url: '{!! route("leaverequestdelete") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: user_id },
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        const actionObj = {
                            icon: 'fas fa-trash-alt',
                            title: '',
                            message: 'Record Remove Successfully',
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        actionreload(actionJSON);
                            }
                })
            }
        });

        $('#ok_button').click(function () {
            $.ajax({
                url: "LeaveApply/destroy/" + user_id,
                beforeSend: function () {
                    $('#ok_button').text('Deleting...');
                },
                success: function (data) {
                    setTimeout(function () {
                        $('#confirmModal').modal('hide');
                        $('#divicestable').DataTable().ajax.reload();
                        alert('Data Deleted');
                    }, 2000);
                    // location.reload();
                }
            })
        });

        // Bind the function to all relevant fields
        $('#approveby').change(function () {
            generateEmailBody();

        });

        // Run on change
        $('#employee').change(fetchEmployeeData);

        // Also run on page load
        fetchEmployeeData();

        $('#attendancemonth').change(function () {
            attendent_load_dt(empid);
        });

        // Attendance Timesheet tab
        $('#timesheetmonth').change(function () {
            load_timesheet(empid);
        });

        $('a[href="#production"]').on('shown.bs.tab', function () {
            load_timesheet(empid);
        });

        // Time Sheet tab — search button
        $('#ts_search_btn').on('click', function () {
            loadMyTimesheet();
        });

        // Auto-load when tab is first shown
        $('a[href="#attendancetimesheet"]').one('shown.bs.tab', function () {
            loadMyTimesheet();
        });

        // Leave Approval tab — lazy-load on first open
        $('a[href="#leaveapproval"]').one('shown.bs.tab', function () {
            load_leave_approval_dt(empid);
        });

    });

    function load_dt(empid){
        $('#divicestable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Leave Request Details',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Leave Request Details', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Leave Request Details',
                    className: 'btn btn-primary btn-sm',
                    text: '<i class="fas fa-print mr-2"></i> Print',
                    customize: function(win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    },
                },
            ],
            ajax: {
                    url: scripturl + '/leave_request_list.php',
                type: 'POST',
                data : {'employee':empid},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'employee_display', name: 'employee_display' },
                { data: 'dep_name', name: 'dep_name' },
                { 
                    data: 'leave_category', name: 'leave_category', render: function(data, type, row) {
                        if (data == 1) {
                            return "Full Day";
                        } else if (data == 0.50) {
                            return "Half Day";
                        } else if (data == 0.25) {
                            return "Short Leave";
                        } else {
                            return "";
                        }
                    }
                },
                { data: 'from_date', name: 'from_date' },
                { data: 'to_date', name: 'to_date' },
                { data: 'reason', name: 'reason'},
                { 
                    data: 'approvestatus', name: 'approvestatus', render: function(data, type, row) {
                        if (data == 0) {
                            return "Not Approved";
                        } else {
                            return "Approved";
                        }
                    }
                },
                { data: 'leave_type', name: 'leave_type' },
                { 
                    data: 'half_short', name: 'half_short', render: function(data, type, row) {
                        if (data == 1) {
                            return "Full Day";
                        } else if (data == 0.50) {
                            return "Half Day";
                        } else if (data == 0.25) {
                            return "Short Leave";
                        } else {
                            return "";
                        }
                    }
                },
                { data: 'leave_status', name: 'leave_status' },
                    {
                data: 'id',
                name: 'action',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    if (row.approvestatus == 0 ) {
                        buttons += '<button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm" style="margin:1px;" type="submit" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                        buttons += '<button type="submit" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm" style="margin:1px;" data-toggle="tooltip" title="Remove" ><i class="far fa-trash-alt"></i></button>';
                    }

                    if (row.approvestatus != 0 && row.approvestatus != 2) {
                        buttons += '<button type="button" id="'+row.id+'" class="leave-pdf-export btn btn-danger btn-sm" style="margin:1px;" data-toggle="tooltip" title="Export PDF"><i class="fas fa-file-pdf"></i></button>';
                    }

                    return buttons;
                }
            }, 
            { data: "emp_name_with_initial", 
                name: "emp_name_with_initial", 
                visible: false
            },
            {   data: "calling_name",
                name: "calling_name", 
                visible: false
            }
            ],
            order: [[0, "desc"]],
                drawCallback: function(settings) {
                        $('[data-toggle="tooltip"]').tooltip();
                    }
        });
    }

    // Leave PDF helpers
    function leavePdfParts(str) {
        if (!str) return ['', '', ''];
        return str.substring(0, 10).split('-');
    }

    function leavePdfAddDays(str, n) {
        var d = new Date(str.substring(0, 10) + 'T00:00:00');
        d.setDate(d.getDate() + n);
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var dd = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + dd;
    }

    function leavePdfLeaveDays(from, to, category) {
        var f = new Date(from.substring(0, 10) + 'T00:00:00');
        var t = new Date(to.substring(0, 10) + 'T00:00:00');
        var diff = Math.round((t - f) / 86400000) + 1;
        return diff * parseFloat(category || 1);
    }

    function generateLeavePdfSummary(d, row) {
        var jsPDFLib = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : window.jsPDF;
        var doc = new jsPDFLib({ unit: 'mm', format: 'a4' });

        var THIN = 0.2, THICK = 0.7;
        var X0 = 10, X1 = 200;

        function box(x, y, w, h) {
            doc.setLineWidth(THIN);
            doc.rect(x, y, w, h);
        }
        function thickLine(x1, y1, x2, y2) {
            doc.setLineWidth(THICK);
            doc.line(x1, y1, x2, y2);
        }
        function thickRect(x, y, w, h) {
            doc.setLineWidth(THICK);
            doc.rect(x, y, w, h);
        }
        function fitSize(t, maxW, size, bold, minSize) {
            doc.setFont('helvetica', bold ? 'bold' : 'normal');
            doc.setFontSize(size);
            while (size > minSize && doc.getTextWidth(t) > maxW) {
                size -= 0.5;
                doc.setFontSize(size);
            }
            return size;
        }
        function label(t, x, y, h) {
            t = String(t || '');
            fitSize(t, 31, 9, true, 6);
            doc.text(t, x + 2, y + h / 2, { baseline: 'middle' });
        }
        function cellText(t, x, y, w, h, size, bold) {
            t = String(t === null || t === undefined ? '' : t);
            var maxW = w - 3;
            var s = fitSize(t, maxW, size || 9, bold, 6);
            if (doc.getTextWidth(t) > maxW) {
                var lines = doc.splitTextToSize(t, maxW);
                var lh = s * 0.3528 * 1.15;
                var startY = y + h / 2 - ((lines.length - 1) * lh) / 2;
                doc.text(lines, x + w / 2, startY, { align: 'center', baseline: 'middle' });
                return;
            }
            doc.text(t, x + w / 2, y + h / 2, { align: 'center', baseline: 'middle' });
        }
        function center(t, x, y, w, bold, size) {
            doc.setFont('helvetica', bold ? 'bold' : 'normal');
            doc.setFontSize(size || 9);
            doc.text(t, x + w / 2, y, { align: 'center' });
        }
        function tick(cx, cy) {
            doc.setLineWidth(0.6);
            doc.line(cx - 2, cy, cx - 0.5, cy + 1.5);
            doc.line(cx - 0.5, cy + 1.5, cx + 2.5, cy - 2);
        }

        var empName    = d.emp_name || row.employee_display || '';
        var dept       = d.dep_name || row.dep_name || '';
        var empNo      = d.emp_no || d.emp_id || '';
        var desig      = d.designation || '';
        var section    = d.section || '';
        var location_  = d.location || '';
        var reqDate    = d.created_at ? d.created_at : new Date().toISOString();
        var days       = leavePdfLeaveDays(d.from_date, d.to_date, d.leave_category);
        var resume     = leavePdfAddDays(d.to_date, 1);
        var companyName = d.company_name || 'AgroVentures Plantations (Pvt) Ltd';

        center(companyName, 0, 16, 210, true, 14);
        center('Leave Application Form', 0, 23, 210, true, 11);
        center('Office Staff', 0, 28, 210, true, 9);

        var y = 32, h = 9;
        var rows = [
            ['Name:', empName, 'Department:', dept],
            ['EMP No:', empNo, 'Section:', section],
            ['Designation:', desig, 'Location:', location_]
        ];
        for (var i = 0; i < rows.length; i++) {
            var ry = y + i * h;
            box(X0, ry, 35, h);  label(rows[i][0], X0, ry, h);
            box(45, ry, 90, h);  cellText(rows[i][1], 45, ry, 90, h);
            box(135, ry, 25, h); label(rows[i][2], 135, ry, h);
            box(160, ry, 40, h); cellText(rows[i][3], 160, ry, 40, h);
        }

        var ly = 59;
        box(X0, ly, 35, h); label('No of Leave Days:', X0, ly, h);
        box(45, ly, 35, h); cellText(days, 45, ly, 35, h);

        function dateRow(text, dateStr, yy) {
            var p = leavePdfParts(dateStr);
            box(X0, yy, 35, h); label(text, X0, yy, h);
            box(45, yy, 15, h); cellText(p[0], 45, yy, 15, h);
            box(60, yy, 10, h); cellText(p[1], 60, yy, 10, h);
            box(70, yy, 10, h); cellText(p[2], 70, yy, 10, h);
        }
        dateRow('Leave Request Date:', reqDate, 68);
        dateRow('Leave Start Date:', d.from_date, 77);
        dateRow('Date of Resuming Date:', resume, 86);

        box(X0, 95, 35, 30); label('Reason for Leave:', X0, 95, 30);
        box(45, 95, 35, 30);
        doc.setFont('helvetica', 'normal'); doc.setFontSize(8);
        var reasonLines = doc.splitTextToSize(d.reason || '', 31);
        var lineH = 3.5;
        var startY = 95 + 15 - ((reasonLines.length - 1) * lineH) / 2;
        doc.text(reasonLines, 45 + 17.5, startY, { align: 'center', baseline: 'middle' });

        box(80, 59, 120, 27);
        box(80, 86, 120, 39);
        doc.setFontSize(8); doc.setFont('helvetica', 'normal');
        doc.text('.............................', 110, 78, { align: 'center' });
        center('Applicant', 80, 83, 60, false, 8);
        doc.text('...............................', 170, 78, { align: 'center' });
        center('Section Head', 140, 83, 60, false, 8);
        doc.text('.............................', 110, 118, { align: 'center' });
        center('Head of Department', 80, 123, 60, false, 8);
        doc.text('...............................', 170, 118, { align: 'center' });
        center('Approved by CEO', 140, 123, 60, false, 8);

        box(X0, 125, 190, 7);
        center('HR Department', X0, 129.5, 190, true, 9);

        var types = ['AL', 'CL', 'Sick', 'ML', 'Other', 'No Pay', ''];
        var CW = 11;
        box(X0, 132, 35, 16); cellText('Leave Type', X0, 132, 35, 16, 9, true);
        for (var t = 0; t < types.length; t++) {
            var tx = 45 + t * CW;
            box(tx, 132, CW, 7); cellText(types[t], tx, 132, CW, 7, 8, true);
            box(tx, 139, CW, 9);
        }
        var cmX = 45 + types.length * CW;
        box(cmX, 132, X1 - cmX, 16);
        doc.setTextColor(150);
        cellText('More Comments', cmX, 132, X1 - cmX, 16, 8, false);
        doc.setTextColor(0);

        var lt = (row.leave_type || '').toLowerCase();
        var idx = 4;
        if (lt.indexOf('annual') > -1) idx = 0;
        else if (lt.indexOf('casual') > -1) idx = 1;
        else if (lt.indexOf('sick') > -1) idx = 2;
        else if (lt.indexOf('medical') > -1) idx = 3;
        else if (lt.indexOf('no pay') > -1 || lt.indexOf('nopay') > -1) idx = 5;
        tick(45 + idx * CW + CW / 2, 143.5);

        doc.setFont('helvetica', 'normal'); doc.setFontSize(8);
        doc.text('...............................', 45, 160, { align: 'center' });
        center('HR Manager', X0, 168, 70, false, 8);
        center('Leave', 95, 159, 20, true, 10);
        center('Updated By', 95, 165, 20, true, 10);
        doc.setFont('helvetica', 'normal'); doc.setFontSize(8);
        doc.text('...............................', 165, 160, { align: 'center' });
        center('Payroll Officer', 130, 168, 70, false, 8);

        thickRect(X0, 32, 190, 27);
        thickLine(135, 32, 135, 59);
        thickLine(X0, 59, X1, 59);
        thickLine(80, 59, 80, 125);
        thickLine(X1, 59, X1, 125);
        thickLine(80, 86, X1, 86);
        thickLine(X0, 59, X0, 125);
        thickLine(X0, 125, X1, 125);
        thickRect(X0, 125, 190, 7);
        thickRect(X0, 152, 190, 20);
        thickLine(X0, 132, X0, 148);
        thickLine(X1, 132, X1, 148);
        thickLine(X0, 148, X1, 148);

        doc.save('Leave_Application_' + (empNo || d.emp_id) + '_' + d.from_date + '.pdf');
    }

    //  Leave PDF click handler (Leave Info tab) 
    $(document).on('click', '.leave-pdf-export', function () {
        var id = $(this).attr('id');
        var row = $('#divicestable').DataTable().row($(this).closest('tr')).data();

        $.ajax({
            url: '{!! route("leaverequestedit") !!}',
            type: 'POST',
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { id: id },
            success: function (data) {
                generateLeavePdfSummary(data.result, row);
            }
        });
    });

    function fetchEmployeeData() {
        var _token = $('input[name="_token"]').val();
        var emp_id = $('#employee').val();

        if (emp_id != '') {
            $.ajax({
                url: "{{ url('getEmployeeCategory') }}",
                method: "POST",
                dataType: 'json',
                data: {
                    emp_id: emp_id,
                    _token: _token
                },
                success: function (data) {
                    if (!data.result) return;
                    $('#companyemail').val(data.result.company_email);
                    $('#companyname').val(data.result.company_name);
                    $('#employeeemail').val(data.result.employee_email);
                }
            });

            // getleaverequests(emp_id);
        }
    }

    function treatAsUTC(date) {
        var result = new Date(date);
        result.setMinutes(result.getMinutes() - result.getTimezoneOffset());
        return result;
    }

    function daysBetween(startDate, endDate) {
        var millisecondsPerDay = 24 * 60 * 60 * 1000;
        return (treatAsUTC(endDate) - treatAsUTC(startDate)) / millisecondsPerDay;
    }

    function show_no_of_days() {
        let from_date = $('#fromdate').val();
        let to_date = $('#todate').val();
        let half_short = $('#half_short').val() || 0;
        let empid = $('#employee').val();


        if (from_date && to_date) {
            $.ajax({
                url: '{!! route("calculate-working-days") !!}',
                type: 'POST',
                data: {
                    from_date: from_date,
                    to_date: to_date,
                    half_short: half_short,
                    empid: empid,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#no_of_days').val(response.working_days);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('Error calculating working days');
                }
            });
        }
    }

    // profile image update
    function previewImage(event) {
        var input = event.target;
        var preview = document.getElementById('profileImagePreview');
        var uploadButton = document.getElementById('uploadButton');

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                uploadButton.classList.remove('d-none');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    // AJAX form submission
    document.getElementById('profileImageForm').addEventListener('submit', function (e) {
        e.preventDefault();

        var form = e.target;
        var formData = new FormData(form);

        fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('uploadStatus').innerHTML =
                        '<div class="text-success">' + data.message + '</div>';
                    document.getElementById('uploadButton').classList.add('d-none');
                } else {
                    document.getElementById('uploadStatus').innerHTML =
                        '<div class="text-danger">' + data.message + '</div>';
                }
            })
            .catch(error => {
                document.getElementById('uploadStatus').innerHTML =
                    '<div class="text-danger">Upload failed</div>';
            });



    });

    function attendent_load_dt(emp_id) {
        checkTodayAttendanceStatus();
        var attendancemonth = $('#attendancemonth').val();
        $('#attendtable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Attendance Details',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Attendance Details', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Attendance Details',
                    className: 'btn btn-primary btn-sm',
                    text: '<i class="fas fa-print mr-2"></i> Print',
                    customize: function(win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    },
                },
            ],
            ajax: {
                "url": "{!! route('get_employee_attendance') !!}",
                "data": {
                    'emp_id': emp_id,
                    'attendancemonth': attendancemonth
                },
            },
            columns: [{
                    data: 'emp_name_with_initial',
                    name: 'emp_name_with_initial'
                },
                {
                    data: 'location',
                    name: 'location'
                },
                {
                    data: 'formatted_date',
                    name: 'formatted_date'
                },
                {
                    data: 'first_time_stamp',
                    name: 'first_time_stamp'
                },
                {
                    data: 'last_time_stamp',
                    name: 'last_time_stamp'
                },
                {
                    data: 'late_minites',
                    name: 'late_minites'
                },
                {
                    data: 'ot_hours',
                    name: 'ot_hours'
                },
                {
                    data: 'double_hours',
                    name: 'double_hours'
                },

            ],
            "bDestroy": true,
            "order": [
                [2, "desc"]
            ]
        });
    }

    // Attendance Timesheet
    function load_timesheet(emp_id) {
        var month = $('#timesheetmonth').val();
        if (!month) return;

        // Clear missing rows from previous load immediately
        $('#productionMissingTbody').empty();

        if ($.fn.DataTable.isDataTable('#productiontable')) {
            $('#productiontable').DataTable().destroy();
        }

        $.ajax({
            url: "{{ route('user_production_data') }}",
            method: "POST",
            data: {
                emp_id: emp_id,
                month: month,
                _token: '{{ csrf_token() }}'
            },
            success: function(result) {
                if ($.fn.DataTable.isDataTable('#productiontable')) {
                    $('#productiontable').DataTable().destroy();
                }
                var tbody = $('#productiontable tbody');
                tbody.empty();

                $('#productiontable').DataTable({
                    processing: false,
                    serverSide: false,          
                    data: result.data,          
                    dom:    "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    buttons: [
                                { 
                                    extend: 'csv', 
                                    className: 'btn btn-success btn-sm', 
                                    title: 'Attendance Time Sheet', 
                                    text: '<i class="fas fa-file-csv mr-2"></i> CSV' 
                                },
                                { 
                                    extend: 'pdf', 
                                    className: 'btn btn-danger btn-sm', 
                                    title: 'Attendance Time Sheet', 
                                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                    orientation: 'landscape', 
                                    pageSize: 'legal'
                                },
                                { 
                                    extend: 'print', 
                                    className: 'btn btn-primary btn-sm', 
                                    title: 'Attendance Time Sheet', 
                                    text: '<i class="fas fa-print mr-2"></i> Print' 
                                }
                            ],
                    columns: [
                            { data: 'formatted_date', name: 'date' },
                            { data: 'in_time', name: 'on_time' },
                            { data: 'out_time', name: 'off_time' },
                            { data: 'late_min', name: 'late_minites' },
                            { 
                                data: 'mc_no',
                                name: 'machine_id',
                                render: function(data) {
                                    return data ? data.replace(/,/g, '<br>') : '';
                                }
                            },
                            { 
                                data: 'style_details',
                                name: 'style_id',
                                render: function(data) {
                                    return data ? data.replace(/,/g, '<br>') : '';
                                }
                            },
                            { 
                                data: 'target',
                                name: 'target',
                                render: function(data) {
                                    return data ? data.replace(/,/g, '<br>') : '';
                                }
                            },
                            { 
                                data: 'produced',
                                name: 'produced',
                                render: function(data) {
                                    return data ? data.replace(/,/g, '<br>') : '';
                                }
                            },
                            {
                                data: 'plus_minus',
                                name: 'plus_minus',
                                render: function(data) {
                                    var val = parseFloat(data);
                                    var color = val >= 0 ? 'green' : 'red';
                                    return '<span style="color:' + color + ';font-weight:bold;">' + (val >= 0 ? '+' : '') + val + '</span>';
                                }
                            },
                            { 
                                data: 'pro_avg',
                                name: 'average',
                                render: function(data) {
                                    return data ? data.replace(/,/g, '<br>') : '';
                                }
                            },
                            { 
                                data: 'weighted_avg', 
                                name: 'average',
                                render: function(data) {
                                    return data ? data.replace(/,/g, '<br>') : '';
                                }
                            },
                                { data: 'pro_ins', name: 'pro_ins', render: function(data) { return data == 1 ? '<span class="badge badge-success">Allowed</span>' : '<span class="badge badge-danger">Not Allowed</span>'; } },
                                { data: 'ot', name: 'normal_ot', render: function(data) { return data == 1 ? '<span class="badge badge-success">Allowed</span>' : '<span class="badge badge-danger">Not Allowed</span>'; } },
                                { data: 'trp_all', name: 'trp_all', render: function(data) { return data == 1 ? '<span class="badge badge-success">Allowed</span>' : '<span class="badge badge-danger">Not Allowed</span>'; } },
                                { data: 'att_all', name: 'att_all', render: function(data) { return data == 1 ? '<span class="badge badge-success">Allowed</span>' : '<span class="badge badge-danger">Not Allowed</span>'; } },
                                { data: 'nig_all', name: 'nig_all', render: function(data) { return data == 1 ? '<span class="badge badge-success">Allowed</span>' : '<span class="badge badge-danger">Not Allowed</span>'; } },
                                { data: 'trg_bo', name: 'target_bonus' }
                        ],
                    order: [[0, 'asc']],
                    // Re-attach the static missing-rows tbody after every DataTable draw,
                    // because DataTable.destroy() + re-init detaches it from the DOM.
                    initComplete: function() {
                        var missingTbody = document.getElementById('productionMissingTbody');
                        if (missingTbody) {
                            document.getElementById('productiontable').appendChild(missingTbody);
                        }
                    }
                });

                // Render missing dates below the DataTable
                var missingData = result.missing_data || [];
                if (missingData.length === 0) {
                    return;
                }

                var html = '';
                // Separator row to visually separate approved from missing
                html += '<tr><td colspan="17" style="border-top:2px dashed #dee2e6;padding:2px 0;"></td></tr>';

                // Today at midnight (local), for date comparisons
                var today = new Date();
                today.setHours(0, 0, 0, 0);

                $.each(missingData, function(i, row) {
                    if (row.type === 'not_approved') {
                        var rowDate = new Date(row.formatted_date);
                        rowDate.setHours(0, 0, 0, 0);
                        var isPast = rowDate < today;

                        // Styling based on date
                        var trClass   = isPast ? 'table-danger'  : 'table-warning';
                        var tdStyle   = isPast ? 'color:#842029;' : 'color:#664d03;';
                        var icon      = isPast ? 'fa-times-circle' : 'fa-clock';
                        var label     = isPast ? 'Not Approved by Admin Department' : 'Pending';

                        if (row.date_rowspan > 0) {
                            // First row for this date — DATE cell spans all allocation rows
                            html += '<tr class="' + trClass + '">' +
                                '<td rowspan="' + row.date_rowspan + '" class="align-middle font-weight-bold" style="white-space:nowrap;">' +
                                    row.formatted_date +
                                '</td>' +
                                '<td></td>' +
                                '<td></td>' +
                                '<td></td>' +
                                '<td>' + (row.mc_no || '') + '</td>' +
                                '<td>' + (row.style_details || '') + '</td>' +
                                '<td colspan="11" class="text-center font-weight-bold" style="' + tdStyle + '">' +
                                    '<i class="fas ' + icon + ' mr-1"></i>' + label +
                                '</td>' +
                            '</tr>';
                        } else {
                            // Continuation row — date cell already rowspanned
                            html += '<tr class="' + trClass + '">' +
                                '<td></td>' +
                                '<td></td>' +
                                '<td></td>' +
                                '<td>' + (row.mc_no || '') + '</td>' +
                                '<td>' + (row.style_details || '') + '</td>' +
                                '<td colspan="11" class="text-center font-weight-bold" style="' + tdStyle + '">' +
                                    '<i class="fas ' + icon + ' mr-1"></i>' + label +
                                '</td>' +
                            '</tr>';
                        }
                    } else {
                        // not_allocated
                        html += '<tr>' +
                            '<td class="font-weight-bold" style="white-space:nowrap;">' + row.formatted_date + '</td>' +
                            '<td colspan="16" class="text-center text-muted">' +
                                '<i class="fas fa-calendar-times mr-1"></i>Not Allocated' +
                            '</td>' +
                        '</tr>';
                    }
                });

                $('#productionMissingTbody').html(html);
            },
            error: function() {
                $('#productiontable tbody').html('<tr><td colspan="17" class="text-center text-danger">Error loading data.</td></tr>');
            }
        });
    }

    function generateEmailBody() {
        let body = "LEAVE APPLICATION DETAILS<br>";
        body += "=========================<br><br>";

        // Employee details
        const employeeName = $('#employee option:selected').text();
        const employeeId = $('#employee').val();
        if (employeeName) {
            body += "EMPLOYEE: " + employeeName + "<br>";
            body += "EMPLOYEE ID: " + (employeeId || 'N/A') + "<br>";
        }

        // Leave type
        const leaveType = $('#leavetype option:selected').text();
        if (leaveType) {
            body += "LEAVE TYPE: " + leaveType + "<br>";
        }

        // Dates
        const fromDate = $('#fromdate').val();
        const toDate = $('#todate').val();
        if (fromDate) {
            body += "FROM DATE: " + fromDate + "<br>";
        }
        if (toDate) {
            body += "TO DATE: " + toDate + "<br>";
        }

        // Days
        const noOfDays = $('#no_of_days').val();
        if (noOfDays) {
            body += "NUMBER OF DAYS: " + noOfDays + "<br>";
        }

        // Reason
        const reason = $('#reson').val();
        if (reason) {
            body += "REASON:" + reason + "<br>";
        }

        // Covering employee
        const coveringEmployee = $('#coveringemployee option:selected').text();
        if (coveringEmployee) {
            body += "COVERING EMPLOYEE:" + coveringEmployee + "<br>";
        }

        // Approving person
        const approvingPerson = $('#approveby option:selected').text();
        if (approvingPerson) {
            body += "APPROVING PERSON:" + approvingPerson + "<br>";
        }

        // Half/Short leave type
        const halfShort = $('#half_short option:selected').text();
        if (halfShort && halfShort !== "Select") {
            body += "LEAVE DURATION:" + halfShort + "<br>";
        }

        // Add closing signature
        body += "<br>Regards,<br>";
        body += employeeName || "Employee";

        $('#emailBody').val(body);
        return body;
    }

    // MARK ATTENDANCE 
    var userLat = null, userLng = null;
    var ALLOWED_RADIUS_METERS = 200;
    var locationStatus = null;

    function getDistanceMeters(lat1, lon1, lat2, lon2) {
        var R = 6371000;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a = Math.sin(dLat/2)*Math.sin(dLat/2) +
                Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*
                Math.sin(dLon/2)*Math.sin(dLon/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    // Location permission
    $('a[href="#markattendance"]').on('click', function() {

        checkTodayAttendanceStatus();

        if (!navigator.geolocation) {
            $('#clock_status').text('Geolocation not supported by your browser.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function(pos) {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;
            if ($('#mark_location').val()) {
                checkLocationDistance();
            } else {
                $('#clock_status').text('Select a location.');
            }
        }, function(err) {
            userLat = null;
            userLng = null;
            if (err.code === 1) {
                $('#clock_status').text('Location access denied. Please enable it in and try again.');
            } else {
                $('#clock_status').text('Unable to get your location. Please try again.');
            }
        });

        // Check permission for location
        if (navigator.permissions) {
            navigator.permissions.query({ name: 'geolocation' }).then(function(status) {
                status.onchange = function() {
                    if (status.state === 'granted') {
                        navigator.geolocation.getCurrentPosition(function(pos) {
                            userLat = pos.coords.latitude;
                            userLng = pos.coords.longitude;
                            $('#clock_status').text('Location acquired. Select a location.');
                            if ($('#mark_location').val()) checkLocationDistance();
                        });
                    } else {
                        userLat = null;
                        $('#clock_status').text('Location access denied. Please enable it and try again.');
                    }
                };
            });
        }
    });

    // On location dropdown
    function checkLocationDistance() {
        var branchId = $('#mark_location').val();
        if (!branchId || userLat === null) return;

        $.get('{{ url("get-branch-coords") }}/' + branchId, function(branch) {
            var dist = getDistanceMeters(userLat, userLng, parseFloat(branch.latitude), parseFloat(branch.longitude));
            $('#reasonBox').hide();
           
            if (dist <= ALLOWED_RADIUS_METERS) {
                 $('#clockBtn').prop('disabled', false);
                locationStatus = 1;
                $('#clock_status').text('Location verified.');
            } else {
                locationStatus = 2;
                $('#clockBtn').prop('disabled', true);
                $('#clock_status').text('Location not verified.');
                Swal.fire({
                    icon: 'error',
                    title: 'Wrong Location',
                    text: 'You are not in the selected location or you selected wrong location. Please select the correct location.',
                    timer: 4000,
                    timerProgressBar: true
                }).then(function() {
                     $('#reasonBox').show();
            });
               
            }
        });
    }

    // On location change
    $('#mark_location').on('change', function() {
        if ($('#clockBtn').attr('data-done') === '1') return;
        $('#clockBtn').prop('disabled', true);
        $('#reasonBox').hide();
        var branchId = $(this).val();
        if (!branchId) return;
        if (userLat === null) {
            $('#clock_status').text('Acquiring GPS, please wait...');
            return;
        }
        checkLocationDistance();
    });


    var isClockIn = true;

    function checkTodayAttendanceStatus() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.get('{{ url("check-attendance-completed") }}/' + empid, function(res) {
            if (res.completed) {
                isClockIn = false;
                $('#clockBtnText').text('Done');
                $('#clockBtn').prop('disabled', true).attr('data-done', '1');
                $('#mark_location').prop('disabled', true);
                $('#clock_status').text('');
                return;
            }
            $.post('/api/v1/Getattendanceshift', { empid: empid, date: new Date(new Date().getTime() + 5.5*60*60*1000).toISOString().slice(0,10) }, function(shiftData) {
                var shift = shiftData.data ? shiftData.data : shiftData;
                if (shift.attendanceinserttype == 1) {
                    isClockIn = true;
                    $('#clockBtnText').text('Clock In');
                    $('#clockBtn').removeAttr('data-done');
                } else {
                    isClockIn = false;
                    $('#clockBtnText').text('Clock Out');
                    $('#clockBtn').removeAttr('data-done');

                    // Clock out - disable and wait till location selection
                    $('#clockBtn').prop('disabled', true);
                    $('#clock_status').text('Please select location to clock out.');
                }
            }, 'json');
        });
    }

    function getAttendanceShiftAndInsert(reason) {
        var branchId = $('#mark_location').val();
        var today = new Date(new Date().getTime() + 5.5*60*60*1000).toISOString().slice(0,10);

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $.ajax({
            url: '/api/v1/Getattendanceshift',
            method: 'POST',
            data: { empid: empid, date: today },
            dataType: 'json',
            success: function(shiftData) {
                var shift = shiftData.data ? shiftData.data : shiftData;

                $.ajax({
                    url: '/api/v1/Singlelocationattendanceinsert',
                    method: 'POST',
                    data: {
                        location_id: branchId,
                        emp_id: empid,
                        timestamp: (function(){ var d = new Date(new Date().getTime() + 5.5*60*60*1000); return d.toISOString().slice(0,19).replace('T',' '); })(),
                        attendaceshift: shift.attendaceshift,
                        attendancedate: shift.attendancedate,
                        attendanceinserttype: shift.attendanceinserttype,
                        reason: (shift.attendanceinserttype == 1) ? (reason || '') : '',
                        off_reason: (shift.attendanceinserttype == 2) ? (reason || '') : '',
                        location_status: locationStatus,
                    },
                    dataType: 'json',
                    success: function(data) {
                      if (isClockIn) {
                        isClockIn = false;
                        locationStatus = null;
                        $('#clockBtnText').text('Clock Out');
                       
                        // location re-verification for clock-out
                        $('#clockBtn').prop('disabled', true);
                        $('#clock_status').text('Please select location to clock out.');
                    } else {
                        isClockIn = true;
                        $('#clockBtnText').text('Done');
                        $('#clockBtn').prop('disabled', true).attr('data-done', '1');
                        $('#mark_location').prop('disabled', true);
                        $('#clock_status').text('');
                    }
                    Swal.fire({ 
                        icon: 'success', 
                        position: 'top-end',
                        title: 'Attendance Marked Successfully.', 
                        timer: 2000, 
                        timerProgressBar: true, 
                        showConfirmButton: false 
                    });
                    $('#reasonBox').hide();
                    $('#reason_text').val('');
                    $('#submitReasonBtn').prop('disabled', false);
                    },
                    error: function(xhr) {
                        $('#clockBtn').prop('disabled', false);
                        $('#submitReasonBtn').prop('disabled', false);
                        Swal.fire({ 
                            icon: 'error', 
                            title: 'Error', 
                            text: 'Failed to mark attendance.'
                        });
                    }
                });
            },
            error: function(xhr) {
                 $('#clockBtn').prop('disabled', false);
                 $('#submitReasonBtn').prop('disabled', false);
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Error', 
                    text: 'Failed to fetch shift info.'
                });
            }
        });
    }

    // Clock button 
    $('#clockBtn').on('click', function(e) {
        e.preventDefault();
        
        if (locationStatus === 2) {
            $('#reasonBox').show();
            return;
        }
        $('#clockBtn').prop('disabled', true);
        getAttendanceShiftAndInsert('');
    });

    // Submit reason
    $('#submitReasonBtn').on('click', function(e) {
        e.preventDefault();
        var reason = $('#reason_text').val().trim();
        if (!reason) {
            Swal.fire({ 
                icon: 'warning', 
                title: 'Reason Required', 
                text: 'Please enter a reason.' });
            return;
    }
    $('#submitReasonBtn').prop('disabled', true);
    getAttendanceShiftAndInsert(reason);
    });

    

    // SALARY ADVANCE TAB
    // Pre-fill the SA employee select with only the current employee (read-only, no AJAX search)
    // This mirrors the "Add Leave Request" pattern where the employee is locked to the current user.
    if (empid && emp_name_with_initial) {
        if (!$('#sa_employee').find('option[value="' + empid + '"]').length) {
            $('#sa_employee').append(new Option(emp_name_with_initial, empid, true, true));
        }
        $('#sa_employee').val(empid);
    }

    // Fetch available amount when employee or date changes
    function sa_fetchAvailableAmount() {
        var emp_id = $('#sa_employee').val();
        var date   = $('#sa_date').val();
        $('#sa_available_amount').val('');
        $('#sa_request_amount').removeAttr('max');
        $('#sa_date_error').text('');

        if (emp_id && date) {
            $.ajax({
                url: '{{ url("SalaryAdvance/available-amount") }}/' + emp_id,
                type: 'GET',
                data: { date: date },
                success: function(data) {
                    if (data.errors) {
                        $('#sa_available_amount').val(0);
                        $('#sa_request_amount').attr('max', 0);
                        $('#sa_date_error').text(data.errors);
                    } else {
                        $('#sa_date_error').text('');
                        $('#sa_available_amount').val(data.available_amount);
                        $('#sa_request_amount').attr('max', data.available_amount);
                    }
                }
            });
        }
    }

    $('#sa_employee').on('change', sa_fetchAvailableAmount);
    $('#sa_date').on('change', sa_fetchAvailableAmount);

    // Clamp request amount to available amount on client side
    $('#sa_request_amount').on('input', function() {
        var available = parseFloat($('#sa_available_amount').val()) || 0;
        var entered   = parseFloat($(this).val()) || 0;
        if (available > 0 && entered > available) {
            $(this).val(available);
        }
    });

    // Load salary advance DataTable
    function load_salary_advance_dt(empid) {
        if ($.fn.DataTable.isDataTable('#salaryadvancetable')) {
            $('#salaryadvancetable').DataTable().destroy();
        }
        $('#salaryadvancetable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                { extend: 'csv',   className: 'btn btn-success btn-sm', title: 'Salary Advance Information', text: '<i class="fas fa-file-csv mr-2"></i> CSV' },
                { extend: 'pdf',   className: 'btn btn-danger btn-sm',  title: 'Salary Advance Information', text: '<i class="fas fa-file-pdf mr-2"></i> PDF', orientation: 'portrait', pageSize: 'legal', customize: function(doc) { doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split(''); } },
                { extend: 'print', className: 'btn btn-primary btn-sm', title: 'Salary Advance Information', text: '<i class="fas fa-print mr-2"></i> Print', customize: function(win) { $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit'); } }
            ],
            order: [[0, 'desc']],
            ajax: {
                url: scripturl + '/salary_advance_list.php',
                type: 'POST',
                data: { employee: empid }
            },
            columns: [
                { data: 'emp_id',           name: 'emp_id' },
                { data: 'employee_display', name: 'employee_display' },
                { data: 'department_name',  name: 'department_name' },
                { data: 'date',             name: 'date' },
                { data: 'request_amount',   name: 'request_amount' },
                { data: 'paid_amount',      name: 'paid_amount' },
                {
                    data: 'approve_status',
                    name: 'approve_status',
                    render: function(data) {
                        if (data == 1) {
                            return '<span class="badge badge-success">Approved</span>';
                        }
                        return '<span class="badge badge-warning">Pending</span>';
                    }
                },
                {
                    data: 'id',
                    name: 'action',
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var buttons = '';
                        if (row.approve_status != 1) {
                            buttons += '<button style="margin:1px;" data-toggle="tooltip" title="Edit" class="sa_edit btn btn-primary btn-sm" id="' + row.id + '"><i class="fas fa-pencil-alt"></i></button>';
                            buttons += '<button style="margin:1px;" data-toggle="tooltip" title="Delete" class="sa_delete btn btn-danger btn-sm" id="' + row.id + '"><i class="far fa-trash-alt"></i></button>';
                        }
                        return buttons;
                    }
                },
                { data: 'id',                   name: 'id',                   visible: false },
                { data: 'emp_name_with_initial', name: 'emp_name_with_initial', visible: false }
            ],
            drawCallback: function() {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }

    // Load salary advance table when tab is activated
    $('a[href="#salaryadvance"]').on('shown.bs.tab', function() {
        load_salary_advance_dt(empid);
    });

    // Open Salary Advance Request modal
    $('#create_record_advance').click(function() {
        $('#saModalLabel').text('Salary Advance Request');
        $('#sa_action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
        $('#sa_action').val('Add');
        $('#sa_form_result').html('');
        $('#saFormTitle')[0].reset();
        $('#sa_date_error').text('');
        $('#sa_available_amount').val('');
        $('#sa_request_amount').removeAttr('max');

        // Lock employee to the current user (same behaviour as Add Leave Request)
        $('#sa_employee').val(empid).trigger('change');

        $('#saFormModal').modal('show');
    });

    // Submit Salary Advance form (Add / Edit)
    $('#saFormTitle').on('submit', function(event) {
        event.preventDefault();

        // Block if attendance validation failed
        if ($('#sa_date_error').text().trim() !== '') {
            return false;
        }

        // Block if available amount is 0
        var available = parseFloat($('#sa_available_amount').val()) || 0;
        if (available <= 0) {
            return false;
        }

        var action_url = '';
        if ($('#sa_action').val() === 'Add') {
            action_url = "{{ route('addSalaryAdvance') }}";
        } else {
            action_url = "{{ route('salaryAdvance.update') }}";
        }

        $.ajax({
            url: action_url,
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.errors) {
                    const actionObj = { icon: 'fas fa-warning', title: '', message: data.errors, url: '', target: '_blank', type: 'danger' };
                    action(JSON.stringify(actionObj, null, 2));
                }
                if (data.success) {
                    const actionObj = { icon: 'fas fa-save', title: '', message: data.success, url: '', target: '_blank', type: 'success' };
                    $('#saFormModal').modal('hide');
                    load_salary_advance_dt(empid);
                    actionreload(JSON.stringify(actionObj, null, 2));
                }
            }
        });
    });

    // Edit salary advance record
    $(document).on('click', '.sa_edit', async function() {
        var r = await Otherconfirmation('You want to Edit this ?');
        if (r === true) {
            var id = $(this).attr('id');
            $('#sa_form_result').html('');
            $('#sa_date_error').text('');

            $.ajax({
                url: '{{ url("salaryAdvance") }}/' + id + '/edit',
                dataType: 'json',
                success: function(data) {
                    // Set date first
                    $('#sa_date').val(data.result.date);

                    // Set employee option then trigger change with date already set
                    if ($('#sa_employee').find('option[value="' + data.result.employee_id + '"]').length === 0) {
                        $('#sa_employee').append('<option value="' + data.result.employee_id + '" selected>' + data.result.employee_name + '</option>');
                    }
                    $('#sa_employee').val(data.result.employee_id).trigger('change');

                    $('#sa_remark').val(data.result.remark);
                    $('#sa_hidden_id').val(id);
                    $('#saModalLabel').text('Edit Salary Advance');
                    $('#sa_action_button').html('<i class="fas fa-edit"></i>&nbsp;Update');
                    $('#sa_action').val('Edit');

                    // Fetch available amount and then set request_amount
                    $.ajax({
                        url: '{{ url("SalaryAdvance/available-amount") }}/' + data.result.employee_id,
                        type: 'GET',
                        data: { date: data.result.date },
                        success: function(res) {
                            if (res.errors) {
                                $('#sa_available_amount').val(0);
                                $('#sa_request_amount').attr('max', 0);
                                $('#sa_date_error').text(res.errors);
                            } else {
                                $('#sa_date_error').text('');
                                $('#sa_available_amount').val(res.available_amount);
                                $('#sa_request_amount').attr('max', res.available_amount);
                            }
                            $('#sa_request_amount').val(data.result.request_amount);
                        }
                    });

                    $('#saFormModal').modal('show');
                }
            });
        }
    });

    // Delete salary advance record
    $(document).on('click', '.sa_delete', async function() {
        var r = await Otherconfirmation('You want to remove this ?');
        if (r === true) {
            var id = $(this).attr('id');
            $.ajax({
                url: "{{ url('salaryAdvance/destroy/') }}/" + id,
                success: function(data) {
                    const actionObj = { icon: 'fas fa-trash-alt', title: '', message: 'Record Removed Successfully', url: '', target: '_blank', type: 'danger' };
                    load_salary_advance_dt(empid);
                    actionreload(JSON.stringify(actionObj, null, 2));
                }
            });
        }
    });

    // END SALARY ADVANCE TAB

    // TIME SHEET TAB — loads the logged-in employee's own attendance
    // time sheet via the existing EmployeetimesheetContrller@generatereport
    var ts_loading = false;

    function ts_timeToMinutes(timeStr) {
        if (!timeStr && timeStr !== 0) return 0;
        var str = String(timeStr);
        // If the server returned a plain number (no colon), treat it as total minutes
        if (str.indexOf(':') === -1) return parseInt(str, 10) || 0;
        var parts = str.split(':');
        return (parseInt(parts[0], 10) || 0) * 60 + (parseInt(parts[1], 10) || 0);
    }

    function ts_minutesToHM(totalMinutes) {
        var h = Math.floor(totalMinutes / 60);
        var m = totalMinutes % 60;
        return (h < 10 ? '0' + h : h) + ':' + (m < 10 ? '0' + m : m);
    }

    function ts_trimSeconds(timeStr) {
        if (!timeStr && timeStr !== 0) return '0';
        var str = String(timeStr);
        var parts = str.split(':');
        if (parts.length < 2) return str;
        return parts[0] + ':' + parts[1];
    }

    function ts_fetchMyRecord(from_date, to_date, lastEmpId, done, fail) {
        $.ajax({
            url: "{{ route('employeetimesheetgenerate') }}",
            method: 'POST',
            data: {
                department: {{ $myDepartment ?? 'null' }},
                from_date:  from_date,
                to_date:    to_date,
                last_emp_id: lastEmpId,
                _token: '{{ csrf_token() }}'
            },
            success: function (result) {
                var obj  = (typeof result === 'string') ? JSON.parse(result) : [result];
                var list = (obj[0] && obj[0].data) ? obj[0].data : [];
                var next = obj[0] ? obj[0].lastEmpId : null;

                var mine = list.find(function (i) { return parseInt(i.emp_id) === parseInt(empid); });
                if (mine) return done(mine);

                // not found: stop if no more data, otherwise fetch the next batch
                if (!list.length || !next || next <= lastEmpId) return done(null);
                ts_fetchMyRecord(from_date, to_date, next, done, fail);
            },
            error: fail
        });
    }

    function loadMyTimesheet() {
        if (ts_loading) return;

        var from_date = $('#ts_from_date').val();
        var to_date   = $('#ts_to_date').val();

        if (!from_date || !to_date) {
            alert('Please select both From Date and To Date.');
            return;
        }

        ts_loading = true;
        $('#ts_search_btn').html('<i class="fa fa-spinner fa-spin mr-1"></i> Searching').prop('disabled', true);
        $('#ts_export_pdf_btn').addClass('d-none');
        $('#ts_employee_list').empty();

        ts_fetchMyRecord(from_date, to_date, 0, function (myRecord) {
                ts_loading = false;
                $('#ts_search_btn').html('<i class="fas fa-search mr-1"></i> Search').prop('disabled', false);

                

                if (!myRecord) {
                    $('#ts_employee_list').html('<div class="alert alert-warning">No timesheet records found for your account in the selected date range. Please ensure your shift is configured.</div>');
                    return;
                }

                var html = '';
                var totlatemin = 0, totOtMinutes = 0, totDoubleOtMinutes = 0;

                html += '<table class="ts_exporttable" style="border-collapse: collapse; font-size: 12px; width:100%;">';
                // Employee header
                html += '<tr><td colspan="15" style="padding-bottom:8px;"><strong>' + myRecord.companyname + '</strong></td></tr>';
                html += '<tr><td colspan="15" style="border-bottom:1px solid #dee2e6;padding-bottom:8px;"><strong>Summarize Attendance</strong></td></tr>';
                html += '<tr>';
                html += '<td colspan="7" style="padding-top:8px;"><strong>Number - </strong>' + myRecord.emp_id + '</td>';
                html += '<td colspan="8" style="padding-top:8px;"><strong>Division - </strong>' + myRecord.departmentname + '</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td colspan="7"><strong>Name - </strong>' + myRecord.emp_fullname + '</td>';
                html += '<td colspan="8"><strong>Designation - </strong>' + (myRecord.jobtitlename || '') + '</td>';
                html += '</tr>';
                html += '<tr style="border-bottom:1px solid #dee2e6;">';
                html += '<td colspan="15" style="padding-bottom:8px;"><strong>Period: </strong>' + from_date + ' &nbsp;to&nbsp; ' + to_date + '</td>';
                html += '</tr>';
                // Column headers
                html += '<tr>';
                var cols = ['Date','Shift','Day','Remark','In','Out','In 2','Out 2','Late','OT','DOT','Days Pay','24Cont','Leave Type','Leave Amount'];
                $.each(cols, function(i, c) {
                    html += '<th style="padding:8px 6px;border-bottom:1px solid #333;white-space:nowrap;">' + c + '</th>';
                });
                html += '</tr>';

                var attendance = myRecord.attendance;
                $.each(attendance, function (j, att) {
                    var isManual = att.attendance_type == 2;
                    if (att.in_time == null && att.leave_type == '') {
                        var status = 'OFF';
                        if (att.day_type == 'Work') { status = 'Absent'; }
                        else if (att.day_type == 'Saturday' && myRecord.is_sat_ot_type_as_act != 0) { status = 'Absent'; }
                        else if (att.day_type == 'Sunday'   && myRecord.is_sun_ot_type_as_act == 2) { status = 'Absent'; }
                        html += '<tr>';
                        html += '<td>' + att.in_date + '</td>';
                        html += '<td>' + att.shift   + '</td>';
                        html += '<td>' + att.day_type + '</td>';
                        html += '<td' + (status == 'Absent' ? ' style="color:red;"' : '') + '>' + status + '</td>';
                        html += '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
                        html += '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
                        html += '<td>&nbsp;</td><td>&nbsp;</td>';
                        html += '</tr>';
                    } else if (att.in_time == null && att.leave_type != '') {
                        html += '<tr>';
                        html += '<td>' + att.in_date + '</td>';
                        html += '<td>' + att.shift   + '</td>';
                        html += '<td>' + att.day_type + '</td>';
                        html += '<td>Leave</td>';
                        html += '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
                        html += '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
                        html += '<td>' + att.leave_type + '</td>';
                        html += '<td>' + att.leave_days + '</td>';
                        html += '</tr>';
                    } else {
                        totlatemin += ts_timeToMinutes(att.late_min);
                        if (att.ot_hours > 0)    totOtMinutes     += ts_timeToMinutes(att.duration_time);
                        if (att.double_ot > 0)   totDoubleOtMinutes += ts_timeToMinutes(att.duration_time);

                        html += '<tr>';
                        html += '<td>' + att.in_date + '</td>';
                        html += '<td>' + att.shift   + '</td>';
                        html += '<td>' + att.day_type + '</td>';
                        html += '<td>' + (isManual ? 'Manual Attendance' : 'Present') + '</td>';
                        html += '<td>' + att.in_time  + '</td>';
                        html += '<td>' + att.out_time + '</td>';
                        html += '<td>' + (att.in_time2  || '') + '</td>';
                        html += '<td>' + (att.out_time2 || '') + '</td>';
                        html += '<td>' + ts_trimSeconds(att.late_min) + '</td>';
                        html += '<td>' + (att.ot_hours  > 0 ? ts_trimSeconds(att.duration_time) : '0') + '</td>';
                        html += '<td>' + (att.double_ot > 0 ? ts_trimSeconds(att.duration_time) : '0') + '</td>';
                        html += '<td>' + (att.days_pay  || '0') + '</td>';
                        html += '<td>' + (att.cont24    || '0') + '</td>';
                        html += '<td>' + att.leave_type + '</td>';
                        html += '<td>' + att.leave_days + '</td>';
                        html += '</tr>';
                    }
                });

                // Totals row
                html += '<tr style="font-weight:bold;border-top:2px solid #333;">';
                html += '<td colspan="8">&nbsp;</td>';
                html += '<td style="border-top:1px solid #333;border-bottom:2px double #333;">' + ts_minutesToHM(totlatemin) + '</td>';
                html += '<td style="border-top:1px solid #333;border-bottom:2px double #333;">' + ts_minutesToHM(totOtMinutes) + '</td>';
                html += '<td style="border-top:1px solid #333;border-bottom:2px double #333;">' + ts_minutesToHM(totDoubleOtMinutes) + '</td>';
                html += '<td colspan="4">&nbsp;</td>';
                html += '</tr>';

                html += '</table>';
                $('#ts_employee_list').html(html);
                $('#ts_export_pdf_btn').removeClass('d-none');
            }, function () {
                ts_loading = false;
                $('#ts_search_btn').html('<i class="fas fa-search mr-1"></i> Search').prop('disabled', false);
                $('#ts_employee_list').html('<div class="alert alert-danger">Error loading timesheet. Please try again.</div>');
        });
    }

    // Export PDF — mirrors the Employee Attendance Time Sheet page
    $('#ts_export_pdf_btn').on('click', function () {
        var { jsPDF } = window.jspdf;
        var doc = new jsPDF('l', 'pt', 'A4');
        var pageWidth  = doc.internal.pageSize.getWidth();
        var pageHeight = doc.internal.pageSize.getHeight();
        var marginLeft = 25, marginRight = 25;

        var table = document.querySelector('.ts_exporttable');
        if (!table) return;

        doc.autoTable({
            html: table,
            startY: 25,
            margin: { top: 25, left: marginLeft, right: marginRight, bottom: 40 },
            theme: 'plain',
            tableWidth: 'auto',
            styles: {
                fontSize: 8,
                cellPadding: { top: 4, bottom: 4, left: 4, right: 4 },
                valign: 'middle',
                overflow: 'linebreak',
                lineColor: [0, 0, 0],
                fillColor: [255, 255, 255],
                textColor: [0, 0, 0],
                lineWidth: 0
            },
            didParseCell: function (data) {
                var rowIndex    = data.row.index;
                var section     = data.section;
                var totalBodyRows = data.table.body.length;

                data.cell.styles.fillColor = [255, 255, 255];
                data.cell.styles.textColor = [0, 0, 0];

                if (section === 'body' && rowIndex === 0) {
                    data.cell.styles.fontStyle  = 'bold';
                    data.cell.styles.fontSize   = 13;
                    data.cell.styles.lineWidth  = 0;
                    data.cell.styles.cellPadding = { top: 6, bottom: 8, left: 4, right: 4 };
                } else if (section === 'body' && rowIndex === 1) {
                    data.cell.styles.fontStyle  = 'bold';
                    data.cell.styles.fontSize   = 10;
                    data.cell.styles.lineWidth  = { top: 0, right: 0, bottom: 1, left: 0 };
                    data.cell.styles.cellPadding = { top: 2, bottom: 8, left: 4, right: 4 };
                } else if (section === 'body' && (rowIndex === 2 || rowIndex === 3)) {
                    data.cell.styles.fontSize   = 9;
                    data.cell.styles.lineWidth  = 0;
                    data.cell.styles.cellPadding = { top: 4, bottom: 4, left: 4, right: 4 };
                } else if (section === 'body' && rowIndex === 4) {
                    data.cell.styles.fontSize   = 9;
                    data.cell.styles.lineWidth  = { top: 0, right: 0, bottom: 1, left: 0 };
                    data.cell.styles.cellPadding = { top: 4, bottom: 10, left: 4, right: 4 };
                } else if (section === 'head') {
                    data.cell.styles.fontStyle  = 'bold';
                    data.cell.styles.fontSize   = 8;
                    data.cell.styles.lineWidth  = 0.5;
                    data.cell.styles.halign     = 'center';
                    data.cell.styles.cellPadding = { top: 8, bottom: 8, left: 3, right: 3 };
                } else if (section === 'body' && rowIndex === totalBodyRows - 1) {
                    data.cell.styles.fontStyle  = 'bold';
                    data.cell.styles.lineWidth  = { top: 1, right: 0, bottom: 0, left: 0 };
                    data.cell.styles.cellPadding = { top: 6, bottom: 6, left: 4, right: 4 };
                } else if (section === 'body') {
                    data.cell.styles.halign     = 'center';
                    data.cell.styles.lineWidth  = 0.5;
                    data.cell.styles.cellPadding = { top: 3, bottom: 3, left: 3, right: 3 };
                }
            }
        });

        // Footer line + page info
        doc.setLineWidth(0.5);
        doc.setDrawColor(0, 0, 0);
        doc.line(marginLeft, pageHeight - 30, pageWidth - marginRight, pageHeight - 30);
        doc.setFontSize(8);
        doc.setFont(undefined, 'normal');
        doc.text('Printed on: ' + new Date().toLocaleString(), marginLeft, pageHeight - 18);
        doc.text('Page 1 of 1', pageWidth - marginRight, pageHeight - 18, { align: 'right' });

        var from_date = $('#ts_from_date').val();
        var to_date   = $('#ts_to_date').val();
        doc.save('attendance_timesheet_from_' + from_date + '_to_' + to_date + '.pdf');
    });

    // END TIME SHEET TAB


    // LEAVE APPROVAL TAB

    function load_leave_approval_dt(approverEmpId) {
        if ($.fn.DataTable.isDataTable('#leaveapprovaltable')) {
            $('#leaveapprovaltable').DataTable().destroy();
        }
        $('#leaveapprovaltable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                { extend: 'csv',   className: 'btn btn-success btn-sm', title: 'Leave Approve Details', text: '<i class="fas fa-file-csv mr-2"></i> CSV' },
                { extend: 'pdf',   className: 'btn btn-danger btn-sm',  title: 'Leave Approve Details', text: '<i class="fas fa-file-pdf mr-2"></i> PDF', orientation: 'landscape', pageSize: 'legal', customize: function(doc) { doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split(''); } },
                { extend: 'print', className: 'btn btn-primary btn-sm', title: 'Leave Approve Details', text: '<i class="fas fa-print mr-2"></i> Print', customize: function(win) { $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit'); } }
            ],
            ajax: {
                url: scripturl + '/user_leave_approve_list.php',
                type: 'POST',
                // Pass the logged-in user's emp_id as the approver filter.
                // leave_approv_person / leave_approv_person_2 to match.
                data: { approver: approverEmpId }
            },
            columns: [
                {
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        if (data === 'Approved') {
                            return '<i class="fas fa-check-circle text-success"></i>';
                        } else if (data === 'Rejected') {
                            return '<i class="fas fa-times-circle text-danger"></i>';
                        } else {
                            return '<input type="checkbox" class="la-row-checkbox selectCheck removeIt">';
                        }
                    },
                    orderable: false, searchable: false
                },
                { data: 'emp_id',          name: 'emp_id' },
                { data: 'employee_display', name: 'employee_display' },
                { data: 'dep_name',        name: 'dep_name' },
                { data: 'leave_type',      name: 'leave_type' },
                { data: 'leave_from',      name: 'leave_from' },
                { data: 'leave_to',        name: 'leave_to' },
                { data: 'reson',           name: 'reson' },
                { data: 'covering_emp',    name: 'covering_emp' },
                {
                    data: 'status',
                    name: 'status_badge',
                    render: function(data) {
                        if (data === 'Pending')  return '<span class="badge badge-primary">Pending</span>';
                        if (data === 'Approved') return '<span class="badge badge-success">Approved</span>';
                        if (data === 'Rejected') return '<span class="badge badge-danger">Rejected</span>';
                        return '';
                    },
                    orderable: false, searchable: false
                },
                {
                    data: 'status',
                    name: 'approve_level',
                    className: 'text-dark',
                    render: function(data, type, row) {
                        if (row.approve_01 == 1 && row.approve_02 == 0) {
                            return '<i style="color:#f4a100" class="fas fa-level-up-alt"></i>&nbsp;&nbsp;2nd Approval';
                        } else if (row.approve_01 == 1 && row.approve_02 == 1) {
                            return '<i style="color:green" class="fas fa-check"></i>&nbsp;&nbsp;All Approved';
                        } else {
                            return '<i style="color:red" class="fas fa-level-up-alt"></i>&nbsp;&nbsp;1st Approval';
                        }
                    }
                },
                {
                    data: null,
                    name: 'action',
                    className: 'text-right',
                    render: function(data, type, row) {
                        if (row.status === 'Pending' || row.status === 'Approved' || row.status === 'Rejected') {
                            if (row.approve_01 == 0) {
                                return '<button name="la_appL1" data-id="' + row.id + '" data-empid="' + row.emp_id + '" class="la_appL1 btn btn-danger btn-sm" type="button"><i class="fas fa-level-up-alt"></i></button>';
                            } else if (row.approve_01 == 1 && row.approve_02 == 0) {
                                return '<button name="la_appL2" data-id="' + row.id + '" data-empid="' + row.emp_id + '" class="la_appL2 btn btn-warning btn-sm" type="button"><i class="fas fa-level-up-alt"></i></button>';
                            } else {
                                return '<button data-id="' + row.id + '" data-empid="' + row.emp_id + '" class="la_view btn btn-primary btn-sm" type="button" data-toggle="tooltip" title="View/Approve"><i class="fas fa-pencil-alt"></i></button>';
                            }
                        }
                        return '';
                    },
                    orderable: false, searchable: false
                },
                { data: 'id',                   name: 'id',                   visible: false },
                { data: 'emp_name_with_initial', name: 'emp_name_with_initial', visible: false },
                { data: 'calling_name',          name: 'calling_name',          visible: false },
                { data: 'leave_approv_person_2', name: 'leave_approv_person_2', visible: false }
            ],
            order: [[6, 'desc']],
            drawCallback: function() {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }

    // Level 1 Approval
    $(document).on('click', '.la_appL1', function() {
        var rowData = $('#leaveapprovaltable').DataTable().row($(this).closest('tr')).data();
        var hasSecondApprover = rowData && rowData.leave_approv_person_2 && rowData.leave_approv_person_2 != '0';
        $('#la_id').val($(this).data('id'));
        $('#la_emp_id').val($(this).data('empid'));
        // If there is no second approver, this Level-1 click is the final approval.
        // Send app_level=2 so the controller treats it as final and sends the email.
        $('#la_app_level').val(hasSecondApprover ? 1 : 2);
        $('input[name="la_status"]').prop('checked', false);
        $('#la_comment').val('');
        $('#la_form_result').html('');
        $('#la_confirmModal').modal('show');
    });

    // Level 2 Approval
    $(document).on('click', '.la_appL2', function() {
        $('#la_id').val($(this).data('id'));
        $('#la_emp_id').val($(this).data('empid'));
        $('#la_app_level').val(2);
        $('input[name="la_status"]').prop('checked', false);
        $('#la_comment').val('');
        $('#la_form_result').html('');
        $('#la_confirmModal').modal('show');
    });

    //View/Edit (already fully approved)
    $(document).on('click', '.la_view', function() {
        $('#la_id').val($(this).data('id'));
        $('#la_emp_id').val($(this).data('empid'));
        $('#la_app_level').val(2);
        $('input[name="la_status"]').prop('checked', false);
        $('#la_comment').val('');
        $('#la_form_result').html('');
        $('#la_confirmModal').modal('show');
    });

    // Confirm single approval
    $(document).on('click', '#la_approve', function() {
        var id        = $('#la_id').val();
        var emp_id    = $('#la_emp_id').val();
        var app_level = $('#la_app_level').val();
        var status    = $('input[name="la_status"]:checked').val();
        var comment   = $('#la_comment').val();

        $.ajax({
            url: "{{ route('approvelupdate') }}",
            type: 'POST',
            data: {
                id:        id,
                emp_id:    emp_id,
                status:    status,
                comment:   comment,
                app_level: app_level,
                _token:    "{{ csrf_token() }}"
            },
            success: function(data) {
                $('#la_confirmModal').modal('hide');
                $('#leaveapprovaltable').DataTable().ajax.reload(null, false);
                if (data.errors) {
                    const obj = { icon: 'fas fa-warning', title: '', message: data.errors, url: '', target: '_blank', type: 'danger' };
                    action(JSON.stringify(obj));
                }
                if (data.mail) { sendLeaveStatusMail(data.mail); }
                if (data.success) {
                    const obj = { icon: 'fas fa-save', title: '', message: data.success, url: '', target: '_blank', type: 'success' };
                    action(JSON.stringify(obj));
                }
                if (data.sms_warning) {
                    const obj = { icon: 'fas fa-warning', title: '', message: data.sms_warning, url: '', target: '_blank', type: 'warning' };
                    action(JSON.stringify(obj));
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                $('#la_confirmModal').modal('hide');
                action(JSON.stringify({ icon: 'fas fa-warning', title: '', message: 'Approval failed', url: '', target: '_blank', type: 'danger' }));
            }
        });
    });

    // Select All checkbox (Leave Approval tab) ---
    $(document).on('change', '#la_selectAll', function() {
        $('#leaveapprovaltable tbody input[type="checkbox"]').prop('checked', this.checked);
    });

    // Approve All button
    $('#la_allapproveel').on('click', async function() {
        var r = await Otherconfirmation('You want to Approve selected records?');
        if (r === true) {
            $('#la_allcomment').val('');
            $('input[name="la_allstatus"]').prop('checked', false);
            $('#la_approveconfirmModal').modal('show');
        }
    });

    // Confirm Approve All
    $(document).on('click', '#la_approveall', function() {
        var selectedRows = [];
        $('#leaveapprovaltable tbody .selectCheck:checked').each(function() {
            var rowData = $('#leaveapprovaltable').DataTable().row($(this).closest('tr')).data();
            if (rowData) {
                var appLevel = 1;
                if (rowData.approve_01 == 1 && rowData.approve_02 == 0)  appLevel = 2;
                else if (rowData.approve_01 == 1 && rowData.approve_02 == 1) appLevel = 3;
                selectedRows.push({
                    empid:    rowData.emp_id,
                    emp_name: rowData.employee_display,
                    laeaveid: rowData.id,
                    app_level: appLevel
                });
            }
        });

        if (selectedRows.length === 0) {
            Swal.fire({ position: 'top-end', icon: 'warning', title: 'Select Rows to Approve!', showConfirmButton: false, timer: 2500 });
            return;
        }

        var comment = $('#la_allcomment').val();
        var status  = $('input[name="la_allstatus"]:checked').val();

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            url: '{!! route("leaveapprove_batch") !!}',
            type: 'POST',
            dataType: 'json',
            data: { dataarry: selectedRows, comment: comment, status: status },
            success: function(data) {
                $('#la_approveconfirmModal').modal('hide');
                $('#la_selectAll').prop('checked', false);
                $('#leaveapprovaltable').DataTable().ajax.reload(null, false);
                if (data.errors) {
                    const obj = { icon: 'fas fa-warning', title: '', message: 'Record Error', url: '', target: '_blank', type: 'danger' };
                    action(JSON.stringify(obj));
                }
                if (data.success) {
                    const obj = { icon: 'fas fa-save', title: '', message: data.success, url: '', target: '_blank', type: 'success' };
                    action(JSON.stringify(obj));
                }
                if (data.mails && data.mails.length) {
                    data.mails.forEach(function(m, i) {
                        setTimeout(function() { sendLeaveStatusMail(m); }, i * 400);
                    });
                }
            }
        });
    });

    function escHtml(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function sendLeaveStatusMail(m) {
        var halfShortMap = { '0.25': 'Short Leave', '0.5': 'Half Day', '0.50': 'Half Day', '1': 'Full Day', '1.00': 'Full Day' };
        var duration = halfShortMap[String(m.half_short)] || '';

        var rowsData = [
            ['Employee', m.employee],
            ['Employee ID', m.employee_id],
            ['Leave Type', m.leave_type],
            ['Leave Duration', duration],
            ['From Date', m.from_date],
            ['To Date', m.to_date],
            ['Number of Days', m.no_of_days],
            ['Reason', m.reason],
            ['Covering Employee', m.covering],
            ['Approving Person', m.approver],
            ['Status', m.status],
            ['Comment', m.comment]
        ];

        var body = '<p>Dear Sir/Madam,</p>';
        body += '<p>The following leave application has been <b>' + escHtml(m.status) + '</b>:</p>';
        body += '<table cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:14px;">';
        for (var i = 0; i < rowsData.length; i++) {
            if (!rowsData[i][1]) { continue; }
            body += '<tr>' +
                '<td style="border:1px solid #ccc;background:#f5f5f5;"><b>' + rowsData[i][0] + '</b></td>' +
                '<td style="border:1px solid #ccc;">' + escHtml(rowsData[i][1]) + '</td>' +
                '</tr>';
        }
        body += '</table>';
        body += '<p>Regards,<br>HR Department</p>';

        var emailData = {
            'inquire_now': 'HR Department - ' + (m.company_name || ''),
            'replyto': (m.emails || []).filter(function (e) { return e; }).join(';'),
            'contsubj': 'Leave ' + m.status + ' - ' + m.employee,
            'contbody': body
        };

        var iframe = document.createElement('iframe');
        iframe.name = 'emailIframe';
        iframe.style.display = 'none';

        var form = document.createElement('form');
        form.target = 'emailIframe';
        form.method = 'POST';
        form.action = 'https://aws.erav.lk/Temp/bf360/eravawsmail.php';

        Object.keys(emailData).forEach(function (key) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = emailData[key];
            form.appendChild(input);
        });

        document.body.appendChild(iframe);
        document.body.appendChild(form);
        form.submit();
    }

    // END LEAVE APPROVAL TAB


</script>

@endsection