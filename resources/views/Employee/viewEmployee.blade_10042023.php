@extends('layouts.app')

@section('content')

<main>
	<div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            <div class="page-header-content py-3">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="fas fa-user"></i></div>
                    <span>Personal Details</span>
                </h1>
            </div>
        </div>
    </div>
	<div class="container-fluid mt-3">
		<div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-9">
						@if(session()->has('success'))
							<div class="alert alert-success">
								{{ session()->get('success') }}
							</div>
						@endif
						@if(session()->has('error'))
							<div class="alert alert-danger">
								@foreach(session()->get('error') as $er)
									{{ $er }}
									<br>
								@endforeach
							</div>
						@endif

						<form id="PdetailsForm" class="form-horizontal" method="POST" action="{{ route('empoyeeUpdate') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">First Name</label>
									
									<input type="text" class="form-control form-control-sm" id="firstname" name="firstname" placeholder="First Name" value="{{$employee->emp_first_name}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Middle Name</label>
									<input type="text" class="form-control form-control-sm" id="middlename" name="middlename" placeholder="Middle Name" value="{{$employee->emp_med_name}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Last Name</label>
									<input type="text" class="form-control form-control-sm" id="lastname" name="lastname" placeholder="Last Name" value="{{$employee->emp_last_name}}">
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Name with Initial</label>
									<input type="text" class="form-control form-control-sm {{ $errors->has('emp_name_with_initial') ? ' has-error' : '' }}" name="emp_name_with_initial" id="emp_name_with_initial" placeholder=" Name with Initial" value="{{$employee->emp_name_with_initial}}">
									@if ($errors->has('emp_name_with_initial'))
									<span class="help-block">
										<strong>{{ $errors->first('emp_name_with_initial') }}</strong>
									</span>
									@endif
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Calling Name</label>
									<input type="text" class="form-control form-control-sm {{ $errors->has('calling_name') ? ' has-error' : '' }}" name="calling_name" id="calling_name" placeholder="Calling Name" value="{{$employee->calling_name}}">
									@if ($errors->has('calling_name'))
										<span class="help-block">
										<strong>{{ $errors->first('calling_name') }}</strong>
									</span>
									@endif
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Full Name</label>
									<input type="text" class="form-control form-control-sm {{ $errors->has('emp_fullname') ? ' has-error' : '' }}" name="fullname" id="fullname" placeholder=" Full Name" value="{{$employee->emp_fullname}}">
									@if ($errors->has('emp_fullname'))
									<span class="help-block">
										<strong>{{ $errors->first('emp_fullname') }}</strong>
									</span>
									@endif
								</div>
							</div>
							<hr>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Employee EPF No</label>
									<input type="text" class="form-control form-control-sm" id="emp_etfno" name="emp_etfno" value="{{$employee->emp_etfno}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Employee No</label>
									<input type="text" class="form-control form-control-sm" id="emp_id" name="emp_id" value="{{$employee->emp_id}}">
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Identity Card No</label>
									<input type="text" class="form-control form-control-sm" id="nicnumber" name="nicnumber" value="{{$employee->emp_national_id}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Driver's License Number</label>
									<input type="text" class="form-control form-control-sm" id="licensenumber" name="licensenumber" value="{{$employee->emp_drive_license}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">License Expiry Date</label>
									<input type="text" class="form-control form-control-sm" id="licenseexpiredate" name="licenseexpiredate" value="{{$employee->emp_license_expire_date}}">
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Employee Permanent Address 1</label>
									<input type="text" class="form-control form-control-sm" id="address1" name="address1" value="{{$employee->emp_address}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Employee Permanent Address 2</label>
									<input type="text" class="form-control form-control-sm" id="address2" name="address2" value="{{$employee->emp_address_2}}">
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Employee Temporary Address 1</label>
									<input type="text" class="form-control form-control-sm" id="addressT1" name="addressT1" value="{{$employee->emp_addressT1}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Employee Temporary Address 2</label>
									<input type="text" class="form-control form-control-sm" id="addressT2" name="addressT2" value="{{$employee->emp_address_T2}}">
								</div>
							</div>
							<div class="form-row mb-1">
								<div class="col">
									<label class="small font-weight-bold text-dark">Telephone</label>
									<input type="text" name="telephone" id="telephone" value="{{$employee->tp1}}" class="form-control form-control-sm {{ $errors->has('telephone') ? ' has-error' : '' }}" />
									@if ($errors->has('telephone'))
										<span class="help-block">
                                            <strong>{{ $errors->first('telephone') }}</strong>
                                        </span>
									@endif
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Mobile No</label>
									<input type="text" name="emp_mobile" id="emp_mobile" value="{{$employee->emp_mobile}}" class="form-control form-control-sm {{ $errors->has('emp_mobile') ? ' has-error' : '' }}" />
									@if ($errors->has('emp_mobile'))
										<span class="help-block">
                                            <strong>{{ $errors->first('emp_mobile') }}</strong>
                                        </span>
									@endif
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Office Telephone</label>
									<input type="text" name="emp_work_telephone" id="emp_work_telephone" value="{{$employee->emp_work_telephone}}" class="form-control form-control-sm {{ $errors->has('emp_work_telephone') ? ' has-error' : '' }}" />
									@if ($errors->has('emp_work_telephone'))
										<span class="help-block">
                                            <strong>{{ $errors->first('emp_work_telephone') }}</strong>
                                        </span>
									@endif
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Photograph</label>
									<input type="file" data-preview="#preview" class="form-control form-control-sm {{ $errors->has('photograph') ? ' has-error' : '' }}" name="photograph" id="photograph">
									<img class="col-sm-6" id="preview" src="">
									@if ($errors->has('photograph'))
									<span class="help-block">
										<strong>{{ $errors->first('photograph') }}</strong>
									</span>
									@endif
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Preview</label>
									@isset($employee->emp_pic_filename)
									<img class="card-img-top w-25 m-3 d-flex justify-content-center"
										src="../images/{{ $employee->emp_pic_filename }}" alt="Card image cap">
									@endisset
								</div>								
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Gender</label><br>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="gender1" name="gender" class="custom-control-input" value="Male"
											{{ ($employee->emp_gender=="Male")? "checked" : "" }}>
										<label class="custom-control-label" for="gender1">Male</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="gender2" name="gender" class="custom-control-input" value="Female" {{ ($employee->emp_gender=="Female")? "checked" : "" }}>
										<label class="custom-control-label" for="gender2">Female</label>
									</div>
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Marital Status</label>
									<select id="marital_status" name="marital_status" class="form-control form-control-sm"
										value="{{$employee->emp_marital_status}}">
										<option>Select</option>
										<option value="Married" {{$employee->emp_marital_status == 'Married'  ? 'selected' : ''}}>
											Married</option>
										<option value="Unmarried" {{$employee->emp_marital_status == 'Unmarried'  ? 'selected' : ''}}>
											Unmarried</option>
									</select>
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Nationality</label>
									<select id="nationality" class="form-control form-control-sm" name="nationality">
										<option selected>Select</option>
										<option value="Srilankan" {{$employee->emp_nationality == 'Srilankan'  ? 'selected' : ''}}>
											Srilankan</option>
									</select>
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Date of Birth</label>
									<input type="text" class="form-control form-control-sm" id="birthday" name="birthday" value="{{$employee->emp_birthday}}">
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Join Date</label>
									<input type="text" class="form-control form-control-sm" id="joindate" name="joindate" value="{{$employee->emp_join_date}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Job Title</label>
									<select id="jobtitle" class="form-control form-control-sm" name="jobtitle">
										<option selected>Select</option>
										@foreach($jobtitles as $jobtitle)
										<option value="{{$jobtitle->id}}" {{$jobtitle->id == $employee->emp_job_code  ? 'selected' : ''}}>
											{{$jobtitle->title}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Job Status</label>
									<select id="jobstatus" class="form-control form-control-sm" name="jobstatus">
										<option selected>Choose...</option>
										@foreach($employmentstatus as $employmentstatu)

										<option value="{{$employmentstatu->id}}"
											{{$employmentstatu->id== $employee->emp_status  ? 'selected' : ''}}>
											{{$employmentstatu->emp_status}}</option>
										@endforeach
									</select>
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Date Assigned</label>
									<input type="text" class="form-control form-control-sm" id="dateassign" name="dateassign" value="{{$employee->emp_assign_date}}">
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Location</label>
									<select name="location" class="form-control form-control-sm {{ $errors->has('location') ? ' has-error' : '' }} shipClass">
										<option value="">Select</option>
										@foreach($branch as $branches)
										<option value="{{$branches->id}}"
											{{$branches->id == $employee->emp_location  ? 'selected' : ''}}>
											{{$branches->location}}</option>
										@endforeach
									</select>
									@if ($errors->has('location'))
									<span class="help-block">
										<strong>{{ $errors->first('location') }}</strong>
									</span>
									@endif
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Work Shift</label>
									<select name="shift" class="form-control form-control-sm {{ $errors->has('shift') ? ' has-error' : '' }} shipClass">
										<option value="">Please Select</option>
										@foreach($shift_type as $shift_types)
										<option value="{{$shift_types->id}}"
											{{$shift_types->id == $employee->emp_shift  ? 'selected' : ''}}>
											{{$shift_types->shift_name}}</option>
										@endforeach
									</select>
									@if ($errors->has('shift'))
									<span class="help-block">
										<strong>{{ $errors->first('shift') }}</strong>
									</span>
									@endif
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Company</label>
									<select name="employeecompany" id="company" class="form-control form-control-sm {{ $errors->has('employeecompany') ? ' has-error' : '' }} shipClass">
										<option value="">Please Select</option>
										@foreach($company as $companies)
										<option value="{{$companies->id}}"
											{{$companies->id == $employee->emp_company  ? 'selected' : ''}}>
											{{$companies->name}}</option>
										@endforeach
									</select>
									@if ($errors->has('employeecompany'))
									<span class="help-block">
										<strong>{{ $errors->first('employeecompany') }}</strong>
									</span>
									@endif
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Department</label>
									<select name="department" id="department"  class="form-control form-control-sm shipClass {{ $errors->has('department') ? ' has-error' : '' }}">
										<option value="">Select</option>
										@foreach($departments as $dept)
											<option value="{{$dept->id}}"
													{{$dept->id == $employee->emp_department  ? 'selected' : ''}}
											>{{$dept->name}}</option>
										@endforeach
									</select>
									@if ($errors->has('department'))
										<span class="help-block">
                                            <strong>{{ $errors->first('department') }}</strong>
                                        </span>
									@endif
								</div>
							</div>
							<div class="form-row" hidden>
								<div class="col">
									<label class="small font-weight-bold text-dark">No of Casual Leaves</label>
									<input type="text" min="0" class="form-control form-control-sm num" id="no_of_casual_leaves" name="no_of_casual_leaves" value="{{$employee->no_of_casual_leaves}}"

									>
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">No of Annual Leaves</label>
									<input type="text" min="0" class="form-control form-control-sm num" id="no_of_annual_leaves" name="no_of_annual_leaves" value="{{$employee->no_of_annual_leaves}}"

									>
								</div>
							</div>

							<input type="hidden" class="form-control form-control-sm" id="id" name="id" placeholder="First Name" value="{{$employee->id}}" readonly>
							<div class="form-group mt-3 text-right">
								@can('employee-edit')
									<button type="submit" class="btn btn-outline-primary btn-sm  px-4"><i class="fas fa-pencil-alt mr-2"></i>&nbsp;Edit</button>
								@endcan
							</div>
						</form>
						<div class="row">
							<div class="col-12">
								<hr class="border-dark">
								<h5 class="font-weight-normal">Attachments</h5>
								<form class="form-horizontal" method="POST" action="{{ route('empoyeeAttachment') }}" enctype="multipart/form-data">
									{{ csrf_field() }}
									<div class="form-row">
										<div class="col">
											<label class="small font-weight-bold text-dark">Select File</label><br>
											<input type="file" class="form-control form-control-sm" id="empattachment" name="empattachment">
											@if ($errors->has('empattachment'))
											<span class="help-block">
												<strong>{{ $errors->first('empattachment') }}</strong>
											</span>
											@endif
										</div>
										<div class="col">
											<label class="small font-weight-bold text-dark">Comment</label>
											<textarea class="form-control form-control-sm" id="empcomment" name="empcomment" rows="3"></textarea>
											@if ($errors->has('empcomment'))
											<span class="help-block">
												<strong>{{ $errors->first('empcomment') }}</strong>
											</span>
											@endif
										</div>
									</div>
									<div class="form-group mt-3">
										@can('employee-edit')
											<button type="submit" name="" id="" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-save"></i>&nbsp;Save</button>
										@endcan
									</div>
									<input type="hidden" class="form-control" id="id" name="id" value="{{$employee->id}}">
								</form>
							</div>
						</div>
					</div>
					@include('layouts.employeeRightBar')
				</div>
			</div>
		</div>
	</div>
</main>


@endsection

@section('script')
<script>

	$('#employee_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
	$('#employee_collapse').addClass('show');
	$('#employee_add_link').addClass('active');

	let company = $('#company');
	let department = $('#department');

	department.select2({
		placeholder: 'Select...',
		width: '100%',
		allowClear: true,
		ajax: {
			url: '{{url("department_list_sel2")}}',
			dataType: 'json',
			data: function(params) {
				return {
					term: params.term || '',
					page: params.page || 1,
					company: company.val()
				}
			},
			cache: true
		}
	});

	// $('#licenseexpiredate').datepicker({
	// 	format: "yyyy/mm/dd",
	// 	autoclose: true
	// });
	// $('#birthday').datepicker({
	// 	format: "yyyy/mm/dd",
	// 	autoclose: true
	// });
	// $('#joindate').datepicker({
	// 	format: "yyyy/mm/dd",
	// 	autoclose: true
	// });
	// $('#dateassign').datepicker({
	// 	format: "yyyy/mm/dd",
	// 	autoclose: true
	// });

	$('.num').keypress(function (event) {
		return isNumber(event, this)
	});

	function isNumber(evt, element) {
		var charCode = (evt.which) ? evt.which : event.keyCode
		if (
				(charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
				(charCode < 48 || charCode > 57))
			return false;
		return true;
	}

</script>
@endsection