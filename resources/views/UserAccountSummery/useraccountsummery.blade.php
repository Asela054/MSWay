@extends('layouts.app')

@section('content')
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
                    $imagePath = asset('images/' . $employee->emp_pic_filename);
                } else {
                    $employeeGender = $employee->emp_gender ?? 'Male'; // Default to Male if null
                    $imagePath = $employeeGender === "Male"
                    ? asset('images/user-profile.png')
                    : asset('images/girl.png');
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
                                                <li role="presentation"><a href="#salaryslip" aria-controls="salaryslip" role="tab" data-toggle="tab"><span class="icon"><i class="fas fa-receipt"></i></span>Salary Slips</a><span class="bgcolor-major-gradient-overlay"></span></li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                            <div class="tab-content">
                                                <!-- ========== TAB PANE ========== -->
                                                <div role="tabpanel" class="tab-pane active" id="profileinfo">
                                                    <div class="card shadow-none bg-transparent">
                                                        <div class="card-body">
                                                            Profile Info
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
                                                                    <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right px-3" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Leave </button>
                                                                </div>
                                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                                    <hr>
                                                                    <div class="center-block fix-width scroll-inner">
                                                                        <table class="table table-striped table-sm small nowrap" style="width: 100%" id="divicestable">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>ID</th>
                                                                                    <th>LEAVE TYPE</th>
                                                                                    <th>LEAVE CATEGORY</th>
                                                                                    <th>LEAVE FROM</th>
                                                                                    <th>LEAVE TO</th>
                                                                                    <th>REASON</th>
                                                                                    <th>COVERING PERSON</th>
                                                                                    <th>STATUS</th>
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
                                                                     <label for="selectedmonth" class="col-form-label col-form-label-sm font-weight-bold text-dark">Salary Period</label>
                                                                     <select name="selectedmonth" id="selectedmonth" class="form-control form-control-sm">
                                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                                        @foreach($payment_period as $schedule)
                                                                        <option value="{{$schedule->id}}"
                                                                            data-selectedmonth="{{ \Carbon\Carbon::parse($schedule->payment_period_fr)->format('Y-m') }}"
                                                                            data-payroll="{{$schedule->payroll_process_type_id}}"
                                                                            data-lastday="{{$schedule->payment_period_to}}"
                                                                            data-payroll="{{$schedule->payroll_process_type_id}}">
                                                                            {{$schedule->payment_period_fr}} to {{$schedule->payment_period_to}}
                                                                        </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                                                                    <label for="selectedmonth" class="col-form-label col-form-label-sm font-weight-bold text-dark">&nbsp;</label><br>
                                                                    <form id="frmExport" method="post" target="_blank" action="{{ url('get_employee_salarysheet') }}">
                                                                        {{ csrf_field() }}

                                                                        <input type="hidden" name="payslip_id" id="payslip_id" value="" />
                                                                        <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" value="" />
                                                                        <input type="hidden" name="period" id="period" value="" />
                                                                        <input type="hidden" name="month" id="month" value="" />

                                                                        <input type="hidden" name="rpt_location_id" id="rpt_location_id" value="" />
                                                                        <input type="hidden" name="rpt_period_id" id="rpt_period_id" value="" />
                                                                        <input type="hidden" name="rpt_emp_id" id="rpt_emp_id" value="" />

                                                                        <button type="submit" id="print_record" class="btn btn-sm btn-success">Download PaySlip</button>
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
                                    <select name="employee" id="employee" class="form-control form-control-sm"
                                        style="pointer-events: none;">
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

                                <input type="submit" id="action_button"
                                    class="btn btn-outline-primary btn-sm fa-pull-right px-4" value="Add" />
                            </div>
                            <input type="hidden" name="companyemail" id="companyemail" />
                            <input type="hidden" name="employeeemail" id="employeeemail" />
                            <input type="hidden" name="coveringemail" id="coveringemail" />
                            <input type="hidden" name="approveemail" id="approveemail" />
                            <input type="hidden" name="companyname" id="companyname" />

                            <input type="hidden" name="action" id="action" value="Add" />
                            <input type="hidden" name="hidden_id" id="hidden_id" />
                            <input type="hidden" name="request_id" id="request_id" />


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
        var emprecordid = {{$emprecordid}};
        var empid = {{$emp_id}};
        var empcompany = {{$emp_company}};
        var emp_name_with_initial = '{{$emp_name_with_initial}}';
        var calling_name = '{{$calling_name}}';

        load_dt(empid);
        attendent_load_dt(empid);

        let employee_f = $('#employee');

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

        $(document).on('change', '#fromdate', function () {
            show_no_of_days();
        });

        $(document).on('change', '#todate', function () {
            show_no_of_days();
        });

        $(document).on('change', '#half_short', function () {
            show_no_of_days();
        });

        $('#employee').change(function () {
            var _token = $('input[name="_token"]').val();
            var leavetype = $('#leavetype').val();
            var emp_id = $('#employee').val();
            var status = $('#employee option:selected').data('id');

            if (emp_id != '') {
                $.ajax({
                    url: "getEmployeeLeaveStatus",
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
                    url: "getEmployeeLeaveStatus",
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
                    url: "getEmployeeCategory",
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
                    url: "getEmployeeCategory",
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
                            'contsubj': 'Leave Application - ' + $(
                                '#employee option:selected').text(),
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
                        Object.keys(emailData).forEach(function (key) {
                            var input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = emailData[key];
                            form.appendChild(input);
                        });

                        // First show the initial success message
                        var html = '<div class="alert alert-success">' + data.success +
                            '</div>';
                        $('#form_result').html(html).show();
                        $('#formTitle')[0].reset();

                        // Add to document and submit
                        document.body.appendChild(iframe);
                        document.body.appendChild(form);
                        form.submit();

                        html = '<div class="alert alert-success">' + data.success +
                        '</div>';
                        $('#formTitle')[0].reset();
                        setTimeout(function () {
                            $('#formModal').modal('hide');
                        }, 1000);

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

                    let empOption = $("<option selected></option>").val(data.result.emp_id)
                        .text(data.result.employee.emp_name_with_initial);
                    $('#employee').append(empOption).trigger('change');

                    let coveringemployeeOption = $("<option selected></option>").val(data
                        .result.emp_covering).text(data.result.covering_employee
                        .emp_name_with_initial);
                    $('#coveringemployee').append(coveringemployeeOption).trigger('change');

                    let approvebyOption = $("<option selected></option>").val(data.result
                        .leave_approv_person).text(data.result.approve_by
                        .emp_name_with_initial);
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
    });

    function load_dt(emp_id) {            
        $('#divicestable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{!! route('user_leave_list') !!}",
                "data": {
                    'emp_id': emp_id
                },
            },
            columns: [{
                    data: 'emp_id',
                    name: 'emp_id'
                },
                {
                    data: 'leave_type',
                    name: 'leave_type'
                },
                {
                    data: 'half_or_short',
                    name: 'half_or_short'
                },
                {
                    data: 'leave_from',
                    name: 'leave_from'
                },
                {
                    data: 'leave_to',
                    name: 'leave_to'
                },
                {
                    data: 'reson',
                    name: 'reson'
                },
                {
                    data: 'covering_emp',
                    name: 'covering_emp'
                },
                {
                    data: 'status',
                    name: 'status'
                },
            ],
            "bDestroy": true,
            "order": [
                [5, "desc"]
            ]
        });
    }

    function fetchEmployeeData() {
        var _token = $('input[name="_token"]').val();
        var emp_id = $('#employee').val();

        if (emp_id != '') {
            $.ajax({
                url: "getEmployeeCategory",
                method: "POST",
                dataType: 'json',
                data: {
                    emp_id: emp_id,
                    _token: _token
                },
                success: function (data) {
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
        var attendancemonth = $('#attendancemonth').val();
        $('#attendtable').DataTable({
            processing: true,
            serverSide: true,
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

            ],
            "bDestroy": true,
            "order": [
                [2, "desc"]
            ]
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