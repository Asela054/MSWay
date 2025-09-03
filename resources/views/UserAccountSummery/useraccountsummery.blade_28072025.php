
@extends('layouts.app')

@section('content')
<main>
    <div class="page-header shadow">
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
                            <a class="nav-link" href="#attendent" data-toggle="tab">Attendent</a>
                        </li>
                    </ul>
                </div>
        </div>
        </div>
    </div>

    <div class="container-fluid mt-4">

        <div class="tab-content container-fluid">
            <div class="tab-pane fade show active" id="account-details">
                <div class="card mb-2">
                    <div class="card-body">

                        <div class="row d-flex justify-content-center py-4">
                                    <div class="col-3">
                                        <form id="profileImageForm" action="{{ route('employees.update-image', $employee->emp_id) }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
    <input type="hidden" name="_method" value="PUT">
        
        <!-- Image preview with upload button -->
        <div class="profile-image-container position-relative">
          
          @php
    $imagePath = '';

    if (!empty($employee->emp_pic_filename) && file_exists(public_path('images/' . $employee->emp_pic_filename))) {
        $imagePath = asset('public/images/' . $employee->emp_pic_filename);
    } else {
        $employeeGender = $employee->emp_gender ?? 'Male'; // Default to Male if null
        $imagePath = $employeeGender === "Male"
            ? asset('public/images/user-profile.png')
            : asset('public/images/girl.png');
    }
@endphp

<img id="profileImagePreview" 
     src="{{ $imagePath }}" 
     alt="Profile_Pic" 
     class="rounded-circle"
     style="width: 180px; height: 180px; border: 16px solid rgb(226 230 237); position: relative;">
            
            <!-- Upload button -->
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
                                   
                                    <div class="col-6">
                                        <h3 class="text-left" id="username"> {{$employee->emp_name_with_initial}}</h3>
                                         <h4 class="text-left" id="jobtitle">{{$employee->title}}</h4>
                                    </div>
                                </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">User Name</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="username">{{$employee->emp_name_with_initial}}</p>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">EPF No</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="epfno">{{$employee->emp_etfno}}</p>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">NIC</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="nic">{{$employee->emp_national_id}}</p>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">Address</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="address">{{$employee->emp_address}}</p>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">Mobile No</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="mobileno">{{$employee->emp_mobile}}</p>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">Telephone</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="telephone">{{$employee->emp_work_telephone}}</p>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">Date of Birth</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="dateofbirth">{{$employee->emp_birthday}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">Join Date</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="joindate">{{$employee->emp_join_date}}</p>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">Job Title</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="jobtitle">{{$employee->title}}</p>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">Job Status</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="jobstatus">{{$employee->emp_statusname}}</p>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">Company</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="company">{{$employee->companyname}}</p>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">Location</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="location">{{$employee->location}}</p>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">Department</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="department">{{$employee->departmentname}}</p>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <p class="text-left">Job Category</p>
                                    </div>
                                    <div style="width: 5px">:</div>
                                    <div class="col-6">
                                        <p class="text-left" id="jobcategory">{{$employee->category}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="monthly-summary">
                <div class="card mb-2">
                    <div class="card-body">
                        <form class="form-horizontal" id="formFilter">
                            <div class="">
                                <div class="row justify-content-between ">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <div class="row d-flex justify-content-center align-items-center">
                                             
                                        
                                        
                                        <img id="profileImagePreview" 
                 src="{{ $imagePath }}" 
                 alt="Profile_Pic" 
                 class="rounded-circle img-fluid"
                 style="width: 80px; height: 80px;  position: relative; " >
                                            <h3 class="text-left" id="username" style="margin-bottom: 0px">{{$employee->emp_name_with_initial}}</h3>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <label class="small font-weight-bold text-dark">Month</label>
                                        <div class="input-group mb-3">
                                       

                                        <select name="selectedmonth" id="selectedmonth" class="custom-select" style="" >
                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                        @foreach($payment_period as $schedule)
                                                        
                                                        <option value="{{$schedule->id}}" data-selectedmonth="{{ \Carbon\Carbon::parse($schedule->payment_period_fr)->format('Y-m') }}" data-payroll="{{$schedule->payroll_process_type_id}}" data-lastday="{{$schedule->payment_period_to}}" data-payroll="{{$schedule->payroll_process_type_id}}">{{$schedule->payment_period_fr}} to {{$schedule->payment_period_to}}</option>
                                                        @endforeach
                                                        
                                                   </select>
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary btn-sm" id="btn-filter">
                                                    Filter</button>
                                                <p id="locationerrormsg"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>                          
                            </div>
        
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 text-center">
                                <h4 style="margin-bottom: 24px;"> Attendance Summery</h4>
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Working Week Days</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="workingdays">0</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Default Working Week Days</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="working_week_days_arr">0</p>
                                    </div>
                                </div>
                                {{-- <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Absent Days</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="absentdays">0</p>
                                    </div>
                                </div> --}}
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Leave Days</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="leave_days">0</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">No Pay Days</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="no_pay_days">0</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 text-center">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4>Monthly Salary Summary</h4>

                                    <form id="frmExport" method="post" target="_blank" action="{{ url('get_employee_salarysheet') }}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="payslip_id" id="payslip_id" value="" />
                                    <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" value="" />
                                    <input type="hidden" name="period" id="period" value="" />
                                    <input type="hidden" name="month" id="month" value="" />
                                    <button type="submit" id="print_record" class="btn btn-sm btn-success">Download PaySlip</button>
                               
                                    </form>
                                </div>
                            
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Basic </p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="basic">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">BRA I</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="bra1">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">BRA II</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="bra2">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">No-pay </p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="nopay">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Total Before Nopay</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="totalbeforenopay">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Arrears</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="arrears">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Weekly Attendance </p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="weeklyattendance">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Incentive</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="incentive">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Director Incentiv</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="directorincentive">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Salary Arrears </p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="salaryarrears">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Normal</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="normal">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Double</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="double">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Total
                                            Earned </p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="totalearned">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Total for Tax</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="totalfortax">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">EPF-8</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="epf8">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Salary Advance </p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="salaryadvance">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Loans</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="loan">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">IOU Deduction</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="iou">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Funeral Fund </p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="funaralfund">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">P.A.Y.E.</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="paye">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Other</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="other">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">Total Deductions </p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="totaldeduction">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left font-weight-bold text-dark">Balance Pay</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right font-weight-bold text-dark" style="border-bottom: 3px double #343a40;" id="balancepay">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">EPF-12</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="epf12">0.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-left">ETF-3</p>
                                    </div>
                                    <div class="col-1">:</div>
                                    <div class="col-3">
                                        <p class="text-right" id="etf3">0.00</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr style="height: 2px;background: rgb(119, 119, 119)">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="leave-apply">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="card-body p-0 p-2">
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right"
                                            name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Leave
                                    </button>
                                </div>
                                <div class="col-12">
                                    <hr class="border-dark">
                                </div>
                                <div class="col-12">
                                    <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="divicestable">
                                        <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Leave Type</th>
                                            <th>Leave Type *</th>
                                            <th>Leave From</th>
                                            <th>Leave To</th>
                                            <th>Reason</th>
                                            <th>Covering Person</th>
                                            <th>Status</th>
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
            </div>
			
			<div class="tab-pane fade" id="attendent">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="card-body p-0 p-2">
                            <div class="row">
                               <div id="attdata" class="response w-100">
                               </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Add Leave</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 40rem; overflow-y: auto;">
                <div class="row">
                    <div class="col">
                        <span id="form_result"></span>
                        <form method="post" id="formTitle" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="form-row mb-1">
                                <div class="col">
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
                                                <td> <span id="annual_total"></span> </td>
                                                <td> <span id="annual_taken"></span> </td>
                                                <td> <span id="annual_available"></span> </td>
                                            </tr>
                                            <tr>
                                                <td> <span> Casual </span> </td>
                                                <td> <span id="casual_total"></span> </td>
                                                <td> <span id="casual_taken"></span> </td>
                                                <td> <span id="casual_available"></span> </td>
                                            </tr>
                                            <tr>
                                                <td> <span>Medical</span> </td>
                                                <td> <span id="med_total"></span> </td>
                                                <td> <span id="med_taken"></span> </td>
                                                <td> <span id="med_available"></span> </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <span id="leave_msg"></span>
                                </div>

                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Leave Type</label>
                                    <select name="leavetype" id="leavetype" class="form-control form-control-sm">
                                        <option value="">Select</option>
                                        @foreach($leavetype as $leavetypes)
                                        <option value="{{$leavetypes->id}}">{{$leavetypes->leave_type}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col d-none">
                                    <label class="small font-weight-bold text-dark">Select Employee</label>
                                    <select name="employee" id="employee" class="form-control form-control-sm" style="pointer-events: none;">
                                        <option value="">Select</option>

                                    </select>
                                </div>
                         
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Covering Employee</label>
                                    <select name="coveringemployee" id="coveringemployee"
                                        class="form-control form-control-sm">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">From</label>
                                    <input type="date" name="fromdate" id="fromdate"
                                        class="form-control form-control-sm" placeholder="YYYY-MM-DD" />
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">To</label>
                                    <input type="date" name="todate" id="todate" class="form-control form-control-sm"
                                        placeholder="YYYY-MM-DD" />
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Half Day/ Short <span
                                            id="half_short_span"></span> </label>
                                    <select name="half_short" id="half_short" class="form-control form-control-sm">
                                        <option value="0.00">Select</option>
                                        <option value="0.25">Short Leave</option>
                                        <option value="0.5">Half Day</option>
                                        <option value="1.00">Full Day</option>
                                    </select>
                                </div>
                            
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">No of Days</label>
                                    <input type="number" step="0.01" name="no_of_days" id="no_of_days"
                                        class="form-control form-control-sm" required />
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Reason</label>
                                    <input type="text" name="reson" id="reson" class="form-control form-control-sm" />
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Approve Person</label>
                                    <select name="approveby" id="approveby" class="form-control form-control-sm">
                                        <option value="">Select</option>
                                                 @foreach($employees as $employee)
                                                <option value="{{$employee->emp_id}}">{{$employee->emp_name_with_initial}}
                                                </option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group d-none">
                                    <label class="small font-weight-bold text-dark">Email Body</label>
                                    <textarea id="emailBody" class="form-control" rows="10"></textarea>
                                </div>

                                <div class="form-group mt-3">

                                    <input type="submit" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4" value="Add"/>
                                </div>
                                <input type="hidden" name="companyemail" id="companyemail"/>
                                <input type="hidden" name="employeeemail" id="employeeemail"/>
                                <input type="hidden" name="coveringemail" id="coveringemail"/>
                                <input type="hidden" name="approveemail" id="approveemail"/>
                                <input type="hidden" name="companyname" id="companyname"/>

                                <input type="hidden" name="action" id="action" value="Add"/>
                                <input type="hidden" name="hidden_id" id="hidden_id"/>
                                <input type="hidden" name="request_id" id="request_id"/>
                            

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

@endsection
@section('script')
<script>
    $(document).ready(function () {
        $('#user_information_menu_link').addClass('active');
        $('#user_information_menu_link_icon').addClass('active');
        // $("#print_record").prop('disabled', true);
        var emprecordid={{$emprecordid}};
        var empid={{$emp_id}};
        var emplocation={{$emp_location}};
        var emp_name_with_initial='{{$emp_name_with_initial}}';
        var calling_name='{{$calling_name}}';

        let employee_f = $('#employee');
            
        if (empid && emp_name_with_initial) {
            var option = new Option(emp_name_with_initial, empid, true, true);
            employee_f.append(option).trigger('change');
        }

        if (emplocation=='' || emplocation==null || emplocation==0) {
            $('#btn-filter').prop('disabled', true);
            $('#locationerrormsg').text('Work Location Not Assign!!');
        }else{
            $('#btn-filter').prop('disabled', false);
            $('#locationerrormsg').text('');
        }

        $('#btn-filter').click(function() {
            let selectedOption = $('#selectedmonth option:selected'); // ✅ Get selected <option>
            let selectedmonthid = $('#selectedmonth').val();          // Get value of <select>
            let selectedmonth = selectedOption.data('selectedmonth');
            let lastday = selectedOption.data('lastday');
  
            $('#month').val(selectedmonth);
            $('#period').val(selectedmonthid);
            
           

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
                    selectedid: selectedmonthid,
                    selectedmonth: selectedmonth,
                    lastday:lastday,
                    emprecordid:emprecordid,
                    empid:empid,
                    emplocation:emplocation

                },
                success: function (data) {
                    $('#workingdays').text(data.result.workingdays);
                    $('#working_week_days_arr').text(data.result.working_week_days_arr);
                    $('#leave_days').text(data.result.leave_days);
                    $('#absentdays').text(data.result.absentdays);
                    $('#no_pay_days').text(data.result.no_pay_days);
                    $('#payroll_profile_id').val(data.payroll_profile_id);
                    $('#payslip_id').val(data.payslip_id);

                    // salary part
                    $('#basic').text(parseFloat(data.salaryresult.BASIC).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#bra1').text(parseFloat(data.salaryresult.BRA_I).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#bra2').text(parseFloat(data.salaryresult.add_bra2).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#nopay').text(parseFloat(data.salaryresult.NOPAY).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#totalbeforenopay').text(parseFloat(data.salaryresult.tot_bnp).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#arrears').text(parseFloat(data.salaryresult.sal_arrears1).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#weeklyattendance').text(parseFloat(data.salaryresult.ATTBONUS_W).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#incentive').text(parseFloat(data.salaryresult.INCNTV_EMP).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#directorincentive').text(parseFloat(data.salaryresult.INCNTV_DIR).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#salaryarrears').text(parseFloat(data.salaryresult.sal_arrears2).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#normal').text(parseFloat(data.salaryresult.OTHRS1).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#double').text(parseFloat(data.salaryresult.OTHRS2).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#totalearned').text(parseFloat(data.salaryresult.tot_earn).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#totalfortax').text(parseFloat(data.salaryresult.tot_fortax).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#epf8').text(parseFloat(data.salaryresult.EPF8).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#salaryadvance').text(parseFloat(data.salaryresult.sal_adv).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#loan').text(parseFloat(data.salaryresult.LOAN).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#iou').text(parseFloat(data.salaryresult.ded_IOU).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#funaralfund').text(parseFloat(data.salaryresult.ded_fund_1).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#paye').text(parseFloat(data.salaryresult.PAYE).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#other').text(parseFloat(data.salaryresult.ded_other).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#totaldeduction').text(parseFloat(data.salaryresult.tot_ded).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#balancepay').text(parseFloat(data.salaryresult.NETSAL).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#epf12').text(parseFloat(data.salaryresult.EPF12).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $('#etf3').text(parseFloat(data.salaryresult.ETF3).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    $("#print_record").prop('disabled', false);
                    // $('#btn-filter').prop('disabled', false);
                    // $('#btn-filter').html('<span class="button-text">Filter</span>');
                }
            });
        });          
     
        // leave apply part
        let c_employee = $('#coveringemployee');
        c_employee.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            parent: '#formModal',
            ajax: {
                url: '{{url("employee_list_sel2")}}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    }
                },
                cache: true
            }
        });

        load_dt(empid);

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let department = $('#department_f').val();
            let employee = $('#employee_f').val();
            let location = $('#location_f').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            load_dt(department, employee, location, from_date, to_date);
        });

        $(document).on('change', '#fromdate', function () {
            show_no_of_days();
        });

        $(document).on('change', '#todate', function () {
            show_no_of_days();
        });

        $(document).on('change', '#half_short', function () {
            show_no_of_days();
        });

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
            let half_short = $('#half_short').val();
            let no_of_days = 0;

            if (from_date != '' && to_date != ''){
                no_of_days = parseFloat(daysBetween(from_date, to_date)) + parseFloat(half_short) ;
                $('#no_of_days').val(no_of_days);
            }
        }

        $('#employee').change(function () {
            var _token = $('input[name="_token"]').val();
            var leavetype = $('#leavetype').val();
            var emp_id = $('#employee').val();
            var status = $('#employee option:selected').data('id');

            if (emp_id != '') {
                $.ajax({
                    url: "getEmployeeLeaveStatus",
                    method: "POST",
                    data: {status: status, emp_id: emp_id, leavetype: leavetype, _token: _token},
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

                        if(data.leave_msg != ''){
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
                    url: "getEmployeeLeaveStatus",
                    method: "POST",
                    data: {status: status, emp_id: emp_id, leavetype: leavetype, _token: _token},
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

                        if(data.leave_msg != ''){
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
                    url: "getEmployeeCategory",
                    method: "POST",
                    dataType: 'json',
                    data: { emp_id: emp_id, _token: _token},
                    success: function (data) {

                       let short_leave_enabled = data.short_leave_enabled;
                       if (short_leave_enabled == 0){
                           $("#half_short option[value*='0.25']").prop('disabled',true);
                           $('#half_short_span').html('<text class="text-warning"> Short Leave Disabled by Job Category </text>');
                       }else{
                           $("#half_short option[value*='0.25']").prop('disabled',false);
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
                    url: "getEmployeeCategory",
                    method: "POST",
                    dataType: 'json',
                    data: { emp_id: emp_id, _token: _token},
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
                $('#message').html("<div class='alert alert-danger'>You Cant Apply, You Have " + assign_leave + " Days Only</div>");
            } else {
                $('#message').html("");

            }


        });

        $('#create_record').click(function () {
            $('.modal-title').text('Apply Leave');
            $('#action_button').val('Add');
            $('#action').val('Add');
            $('#form_result').html('');

            $('#formModal').modal('show');
        });

        $('#formTitle').on('submit', function (event) {
            event.preventDefault();
            var action_url = '';


            if ($('#action').val() == 'Add') {
                action_url = "{{ route('addLeaveApply') }}";
            }


            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('LeaveApply.update') }}";
            }


            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {

                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        const emailBody = generateEmailBody();
                        
                        var emailData = {
                            'inquire_now': 'HR Department - ' + $('#companyname').val(),
                            'replyto': [
                                $('#employeeemail').val(),
                                $('#companyemail').val(),
                                $('#coveringemail').val(),
                                $('#approveemail').val()
                            ].filter(email => email).join(';'),
                            'contsubj': 'Leave Application - ' + $('#employee option:selected').text(),
                            'contbody': emailBody
                        };

                        // Create a temporary iframe
                        var iframe = document.createElement('iframe');
                        iframe.name = 'emailIframe';
                        iframe.style.display = 'none';
                        
                        // Create the form
                        var form = document.createElement('form');
                        form.target = 'emailIframe';
                        form.method = 'POST';
                        form.action = 'https://aws.erav.lk/Temp/bf360/eravawsmail.php';

                        // Add form inputs
                        Object.keys(emailData).forEach(function(key) {
                            var input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = emailData[key];
                            form.appendChild(input);
                        });

                        // First show the initial success message
                        var html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#form_result').html(html).show();
                        $('#formTitle')[0].reset();

                        // Add to document and submit
                        document.body.appendChild(iframe);
                        document.body.appendChild(form);
                        form.submit();

                            html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#formTitle')[0].reset();
                        setTimeout(function() { $('#formModal').modal('hide'); }, 1000);

                    }
                    $('#form_result').html(html);
                }
            });
        });


        $(document).on('click', '.edit', function () {
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "LeaveApply/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    $('#leavetype').val(data.result.leave_type);

                    let empOption = $("<option selected></option>").val(data.result.emp_id).text(data.result.employee.emp_name_with_initial);
                    $('#employee').append(empOption).trigger('change');

                    let coveringemployeeOption = $("<option selected></option>").val(data.result.emp_covering).text(data.result.covering_employee.emp_name_with_initial);
                    $('#coveringemployee').append(coveringemployeeOption).trigger('change');

                    let approvebyOption = $("<option selected></option>").val(data.result.leave_approv_person).text(data.result.approve_by.emp_name_with_initial);
                    $('#approveby').append(approvebyOption).trigger('change');

                    $('#employee').val(data.result.emp_id);
                    $('#fromdate').val(data.result.leave_from);
                    $('#todate').val(data.result.leave_to);
                    $('#half_short').val(data.result.half_short);
                    $('#no_of_days').val(data.result.no_of_days);
                    $('#reson').val(data.result.reson);
                    $('#comment').val(data.result.comment);
                    $('#coveringemployee').val(data.result.emp_covering);
                    $('#approveby').val(data.result.leave_approv_person);
                    $('#available_leave').val(data.result.total_leave);
                    $('#assign_leave').val(data.result.assigned_leave);
                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Leave');
                    $('#action_button').val('Edit');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
            })
        });

        var user_id;

        $(document).on('click', '.delete', function () {
            user_id = $(this).attr('id');
            $('#confirmModal').modal('show');
        });

        // Bind the function to all relevant fields
        $('#approveby').change(function() {
            generateEmailBody();
        
        });

        function fetchEmployeeData() {
            var _token = $('input[name="_token"]').val();
            var emp_id = $('#employee').val();

            if (emp_id != '') {
                $.ajax({
                    url: "getEmployeeCategory",
                    method: "POST",
                    dataType: 'json',
                    data: { emp_id: emp_id, _token: _token },
                    success: function (data) {
                        $('#companyemail').val(data.result.company_email);
                        $('#companyname').val(data.result.company_name);
                        $('#employeeemail').val(data.result.employee_email);
                    }
                });

                // getleaverequests(emp_id);
            }
        }

        // Run on change
        $('#employee').change(fetchEmployeeData);

        // Also run on page load
        fetchEmployeeData();

        attendent_load_dt();
    });

    function load_dt(employee){
        $('#divicestable').DataTable({
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-default',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Print',
                    className: 'btn btn-default',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                }
            ],
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{!! route('user_leave_list') !!}",
                "data": {'employee':employee},
            },
            columns: [
                { data: 'emp_id', name: 'emp_id' },
                { data: 'leave_type', name: 'leave_type' },
                { data: 'half_or_short', name: 'half_or_short' },
                { data: 'leave_from', name: 'leave_from' },
                { data: 'leave_to', name: 'leave_to' },
                { data: 'reson', name: 'reson' },
                { data: 'covering_emp', name: 'covering_emp' },
                { data: 'status', name: 'status' },
            ],
            "bDestroy": true,
            "order": [
                [5, "desc"]
            ]
        });
    }
    
    // profile image update
    function previewImage(event) {
        var input = event.target;
        var preview = document.getElementById('profileImagePreview');
        var uploadButton = document.getElementById('uploadButton');
        
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                uploadButton.classList.remove('d-none');
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // AJAX form submission
    document.getElementById('profileImageForm').addEventListener('submit', function(e) {
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
            if(data.success) {
                document.getElementById('uploadStatus').innerHTML = 
                    '<div class="text-success">'+data.message+'</div>';
                document.getElementById('uploadButton').classList.add('d-none');
            } else {
                document.getElementById('uploadStatus').innerHTML = 
                    '<div class="text-danger">'+data.message+'</div>';
            }
        })
        .catch(error => {
            document.getElementById('uploadStatus').innerHTML = 
                '<div class="text-danger">Upload failed</div>';
        });
        
        
        
    });

    function attendent_load_dt() {

        $('#attdata').html('');

        let element = $('.filter-btn');
        element.attr('disabled', true);
        element.html('<i class="fa fa-spinner fa-spin"></i>');

        //add loading to element button
        $(element).val('<i class="fa fa-spinner fa-spin"></i>');
        //disable
        $(element).prop('disabled', true);

        $.ajax({
            url: "{{ route('get_employee_attendance') }}",
            method: "POST",
            data: {
                _token: '{{csrf_token()}}'
            },
            success: function (res) {
                element.html('Filter');
                element.prop('disabled', false);

                $('#attdata').html(res);


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

</script>

@endsection