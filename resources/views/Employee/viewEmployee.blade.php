@extends('layouts.app')

@section('content')

<main>
	<div class="page-header shadow">
		<div class="container-fluid d-none d-sm-block shadow">
			@include('layouts.employee_nav_bar')
		</div>
		<div class="container-fluid">
			<div class="page-header-content py-3 px-2">
				<h1 class="page-header-title ">
					<div class="page-header-icon"><i class="fa-light fa-users-gear"></i></div>
					<span>Personal Details</span>
				</h1>
			</div>
		</div>
	</div>    
	<div class="container-fluid mt-2 p-0 p-2">
		<div class="card">
			<div class="card-body p-2">
				<div class="row">
					<div class="col-lg-9 col-md-12">
						@if(session()->has('success'))
							<div class="alert alert-success">
								{{ session()->get('success') }}
							</div>
						@endif
						@if(session()->has('error'))
							<div class="alert alert-danger">
								{{ session()->get('error') }}
							</div>
						@endif

						<form id="PdetailsForm" class="form-horizontal" method="POST" action="{{ route('empoyeeUpdate') }}" enctype="multipart/form-data">
							{{ csrf_field() }}

							<!-- Personal Information -->
							<div class="card shadow-none mb-3">
								<div class="card-body bg-light">
									<h6 class="title-style my-3"><span>Personal Information</span></h6>
									<div class="form-row mb-1">
										<div class="col-md-4 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">First Name</label>
											<input type="text" class="form-control form-control-sm" id="firstname" name="firstname" placeholder="First Name" value="{{$employee->emp_first_name ?? ''}}">
										</div>
										<div class="col-md-4 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Middle Name</label>
											<input type="text" class="form-control form-control-sm" id="middlename" name="middlename" placeholder="Middle Name" value="{{$employee->emp_med_name ?? ''}}">
										</div>
										<div class="col-md-4 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Last Name</label>
											<input type="text" class="form-control form-control-sm" id="lastname" name="lastname" placeholder="Last Name" value="{{$employee->emp_last_name ?? ''}}">
										</div>
									</div>
									<div class="form-row mb-1">
										<div class="col-md-4 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Name with Initial</label>
											<input type="text" class="form-control form-control-sm {{ $errors->has('emp_name_with_initial') ? ' has-error' : '' }}" name="emp_name_with_initial" id="emp_name_with_initial" placeholder="Name with Initial" value="{{$employee->emp_name_with_initial ?? ''}}">
											@if ($errors->has('emp_name_with_initial'))
												<span class="help-block">
													<strong>{{ $errors->first('emp_name_with_initial') }}</strong>
												</span>
											@endif
										</div>
										<div class="col-md-4 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Calling Name</label>
											<input type="text" class="form-control form-control-sm {{ $errors->has('calling_name') ? ' has-error' : '' }}" name="calling_name" id="calling_name" placeholder="Calling Name" value="{{$employee->calling_name ?? ''}}">
											@if ($errors->has('calling_name'))
												<span class="help-block">
													<strong>{{ $errors->first('calling_name') }}</strong>
												</span>
											@endif
										</div>
										<div class="col-md-4 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Identity Card No</label>
											<input type="text" class="form-control form-control-sm" id="nicnumber" name="nicnumber" value="{{$employee->emp_national_id ?? ''}}">
										</div>
									</div>
									<div class="form-row mb-1">
										<div class="col-12 mb-2">
											<label class="small font-weight-bold text-dark">Full Name</label>
											<input type="text" class="form-control form-control-sm {{ $errors->has('emp_fullname') ? ' has-error' : '' }}" name="fullname" id="fullname" placeholder="Full Name" value="{{$employee->emp_fullname ?? ''}}">
											@if ($errors->has('emp_fullname'))
												<span class="help-block">
													<strong>{{ $errors->first('emp_fullname') }}</strong>
												</span>
											@endif
										</div>
									</div>
								</div>
							</div>

							<!-- Contact Information -->
							<div class="card shadow-none mb-3">
								<div class="card-body bg-light">
									<h6 class="title-style my-3"><span>Contact Information</span></h6>
									<div class="form-row mb-1">
										<div class="col-md-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Employee Permanent Address</label>
											<input type="text" class="form-control form-control-sm" id="address1" name="address1" value="{{$employee->emp_address ?? ''}}">
										</div>
										<div class="col-md-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Employee Temporary Address</label>
											<input type="text" class="form-control form-control-sm" id="addressT1" name="addressT1" value="{{$employee->emp_addressT1 ?? ''}}">
										</div>
									</div>
									<div class="form-row mb-1">
										<div class="col-md-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Employee Email</label>
											<input type="email" class="form-control form-control-sm" id="employee_mail" name="employee_mail" value="{{$employee->emp_email ?? ''}}">
										</div>
										<div class="col-md-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Employee Personal Email</label>
											<input type="email" class="form-control form-control-sm" id="employee_other_mail" name="employee_other_mail" value="{{$employee->emp_other_email ?? ''}}">
										</div>
									</div>
									<div class="form-row mb-1">
										<div class="col-md-4 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Personal Number</label>
											<input type="text" name="telephone" id="telephone" value="{{$employee->tp1}}" class="form-control form-control-sm {{ $errors->has('telephone') ? ' has-error' : '' }}" />
											@if ($errors->has('telephone'))
												<span class="help-block">
													<strong>{{ $errors->first('telephone') }}</strong>
												</span>
											@endif
										</div>
										<div class="col-md-4 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Mobile Number</label>
											<input type="text" name="emp_mobile" id="emp_mobile" value="{{$employee->emp_mobile}}" class="form-control form-control-sm {{ $errors->has('emp_mobile') ? ' has-error' : '' }}" />
											@if ($errors->has('emp_mobile'))
												<span class="help-block">
													<strong>{{ $errors->first('emp_mobile') }}</strong>
												</span>
											@endif
										</div>
										<div class="col-md-4 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Office Extension</label>
											<input type="text" name="emp_work_telephone" id="emp_work_telephone" value="{{$employee->emp_work_telephone}}" class="form-control form-control-sm {{ $errors->has('emp_work_telephone') ? ' has-error' : '' }}" />
											@if ($errors->has('emp_work_telephone'))
												<span class="help-block">
													<strong>{{ $errors->first('emp_work_telephone') }}</strong>
												</span>
											@endif
										</div>
									</div>
									<div class="form-row mb-1">
										<div class="col-md-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Photograph</label>
											<input type="file" data-preview="#preview" class="form-control form-control-sm {{ $errors->has('photograph') ? ' has-error' : '' }}" name="photograph" id="photograph">
											<img class="mt-2" id="preview" src="" style="max-width: 100%; display: none;">
											@if ($errors->has('photograph'))
												<span class="help-block">
													<strong>{{ $errors->first('photograph') }}</strong>
												</span>
											@endif
										</div>
										<div class="col-md-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Current Photo</label>
											<div>
												@php
													$hasPhoto = false;
													$photoPath = '';
													
													if(isset($employee) && $employee->employeePicture) {
														$filename = $employee->employeePicture->emp_pic_filename ?? null;
														if($filename && file_exists(public_path('/images/' . $filename))) {
															$hasPhoto = true;
															$photoPath = asset('images/' . $filename);
														}
													}
												@endphp
												
												@if($hasPhoto)
													<img class="img-thumbnail" style="max-width: 200px;"
														src="{{ $photoPath }}" 
														alt="Employee Photo">
												@else
													<p class="text-muted">No photo uploaded</p>
												@endif
											</div>
										</div>	
									</div>
								</div>
							</div>

							<!-- Other Information -->
							<div class="card shadow-none mb-3">
								<div class="card-body bg-light">
									<h6 class="title-style my-3"><span>Other Information</span></h6>
									<div class="form-row mb-1">
										<div class="col-md-4 col-sm-6 col-12 mb-2">
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
										<div class="col-md-4 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Marital Status</label>
											<select id="marital_status" name="marital_status" class="form-control form-control-sm">
												<option>Select</option>
												<option value="Married" {{$employee->emp_marital_status == 'Married'  ? 'selected' : ''}}>MARRIED</option>
												<option value="Unmarried" {{$employee->emp_marital_status == 'Unmarried'  ? 'selected' : ''}}>SINGLE</option>
											</select>
										</div>
										<div class="col-md-4 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Nationality</label>
											<select id="nationality" class="form-control form-control-sm" name="nationality">
												<option selected>Select</option>
												<option value="Srilankan" {{$employee->emp_nationality == 'Srilankan'  ? 'selected' : ''}}>SRI LANKAN</option>
											</select>
										</div>
									</div>
									<div class="form-row mb-1">
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Date of Birth</label>
											<input type="date" class="form-control form-control-sm" id="birthday" name="birthday" value="{{$employee->emp_birthday ? date('Y-m-d', strtotime($employee->emp_birthday)) : ''}}">
											@if ($errors->has('nic_validation'))
												<span class="text-danger">
													<strong>{{ $errors->first('nic_validation') }}</strong>
												</span>
											@endif
										</div>
									</div>
								</div>
							</div>

							<!-- Work Information -->
							<div class="card shadow-none mb-3">
								<div class="card-body bg-light">
									<h6 class="title-style my-3"><span>Work Information</span></h6>
									<div class="form-row mb-1">
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Employee EPF/ETF No</label>
											<input type="text" class="form-control form-control-sm {{ $errors->has('emp_etfno') ? ' is-invalid' : '' }}" id="emp_etfno" name="emp_etfno" value="{{$employee->emp_etfno ? $employee->emp_etfno : ''}}">
											@if ($errors->has('emp_etfno'))
												<span class="invalid-feedback">
													<strong>{{ $errors->first('emp_etfno') }}</strong>
												</span>
											@endif
										</div>
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Employee No</label>
											<input type="text" class="form-control form-control-sm {{ $errors->has('emp_id') ? ' is-invalid' : '' }}" id="emp_id" name="emp_id" value="{{$employee->emp_id ? $employee->emp_id : ''}}">
											@if ($errors->has('emp_id'))
												<span class="invalid-feedback">
													<strong>{{ $errors->first('emp_id') }}</strong>
												</span>
											@endif
										</div>
									</div>
									<div class="form-row mb-1">
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Driver's License Number</label>
											<input type="text" class="form-control form-control-sm" id="licensenumber" name="licensenumber" value="{{$employee->emp_drive_license ? $employee->emp_drive_license : ''}}">
										</div>
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">License Expiry Date</label>
											<input type="date" class="form-control form-control-sm" id="licenseexpiredate" name="licenseexpiredate" value="{{$employee->emp_license_expire_date ? date('Y-m-d', strtotime($employee->emp_license_expire_date)) : ''}}">
										</div>
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Date Assigned</label>
											<input type="date" class="form-control form-control-sm" id="dateassign" name="dateassign" value="{{$employee->emp_assign_date ? date('Y-m-d', strtotime($employee->emp_assign_date)) : ''}}">
										</div>
									</div>
									<div class="form-row mb-1">
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Join Date</label>
											<input type="date" class="form-control form-control-sm" id="joindate" name="joindate" value="{{$employee->emp_join_date ? date('Y-m-d', strtotime($employee->emp_join_date)) : ''}}">
										</div>
										<div class="col-md-5 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Job Title</label>
											<select id="jobtitle" class="form-control form-control-sm" name="jobtitle">
												<option selected>Select</option>
												@foreach($jobtitles as $jobtitle)
													<option value="{{$jobtitle->id}}" {{$jobtitle->id == $employee->emp_job_code  ? 'selected' : ''}}>
														{{$jobtitle->title}}
													</option>
												@endforeach
											</select>
										</div>
										<div class="col-md-4 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Job Status</label>
											<select id="jobstatus" class="form-control form-control-sm" name="jobstatus">
												<option selected>Choose...</option>
												@foreach($employmentstatus as $employmentstatu)
													<option value="{{$employmentstatu->id}}"
														{{$employmentstatu->id== $employee->emp_status  ? 'selected' : ''}}>
														{{$employmentstatu->emp_status}}
													</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-row mb-1">
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Position</label>
											<select id="hierarchy_id" class="form-control form-control-sm" name="hierarchy_id">
												<option selected>Choose...</option>
												@foreach($empposition as $emppositions)
													<option value="{{$emppositions->id}}"
														{{$emppositions->id== $employee->hierarchy_id  ? 'selected' : ''}}>
														{{$emppositions->position}}
													</option>
												@endforeach
											</select>
										</div>
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Financial Category</label>
											<select id="financial_id" class="form-control form-control-sm" name="financial_id">
												<option selected>Choose...</option>
												@foreach($empfinancial as $empfinancials)
													<option value="{{$empfinancials->id}}"
														{{$empfinancials->id== $employee->financial_id  ? 'selected' : ''}}>
														{{$empfinancials->category}}
													</option>
												@endforeach
											</select>
										</div>
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Leave Approval Person</label>
											<br>
											<div class="form-check-inline">
												<label class="form-check-label">
													<input type="radio" class="form-check-input leave_approve_person" name="leave_approve_person" id="ot_allowed_0" value="0" 
														{{ $employee->leave_approve_person == 0 ? 'checked' : '' }}>No
												</label>
											</div>
											<div class="form-check-inline">
												<label class="form-check-label">
													<input type="radio" class="form-check-input leave_approve_person" name="leave_approve_person" id="ot_allowed_1" value="1"
														{{ $employee->leave_approve_person == 1 ? 'checked' : '' }}>Yes
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Work Location Information -->
							<div class="card shadow-none mb-3">
								<div class="card-body bg-light">
									<h6 class="title-style my-3"><span>Work Location Information</span></h6>
									<div class="form-row mb-1">
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Company</label>
											<select name="employeecompany" id="company" class="form-control form-control-sm {{ $errors->has('employeecompany') ? ' has-error' : '' }} shipClass" onchange="resetDepartment()">
												<option value="">Please Select</option>
												@foreach($company as $companies)
													<option value="{{$companies->id}}"
														{{$companies->id == $employee->emp_company  ? 'selected' : ''}}>
														{{$companies->name}}
													</option>
												@endforeach
											</select>
											@if ($errors->has('employeecompany'))
												<span class="help-block">
													<strong>{{ $errors->first('employeecompany') }}</strong>
												</span>
											@endif
										</div>
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Location</label>
											<select name="location" class="form-control form-control-sm {{ $errors->has('location') ? ' has-error' : '' }} shipClass">
												<option value="">Select</option>
												@foreach($branch as $branches)
													<option value="{{$branches->id}}"
														{{$branches->id == $employee->emp_location  ? 'selected' : ''}}>
														{{$branches->location}}
													</option>
												@endforeach
											</select>
											@if ($errors->has('location'))
												<span class="help-block">
													<strong>{{ $errors->first('location') }}</strong>
												</span>
											@endif
										</div>
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Department</label>
											<select name="department" id="department"  class="form-control form-control-sm shipClass {{ $errors->has('department') ? ' has-error' : '' }}" required>
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
									<div class="form-row mb-1">
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Work Shift</label>
											<select name="shift" class="form-control form-control-sm {{ $errors->has('shift') ? ' has-error' : '' }} shipClass">
												<option value="">Please Select</option>
												@foreach($shift_type as $shift_types)
													<option value="{{$shift_types->id}}"
														{{$shift_types->id == $employee->emp_shift  ? 'selected' : ''}}>
														{{$shift_types->shift_name}}
													</option>
												@endforeach
											</select>
											@if ($errors->has('shift'))
												<span class="help-block">
													<strong>{{ $errors->first('shift') }}</strong>
												</span>
											@endif
										</div>
										
										<div class="col-md-3 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Work Category</label>
											<select name="job_category_id" id="job_category_id"  class="form-control form-control-sm shipClass {{ $errors->has('job_category_id') ? ' has-error' : '' }}">
												<option value="">Select</option>
												@foreach($job_categories as $cat)
													<option value="{{$cat->id}}"
															{{$cat->id == $employee->job_category_id  ? 'selected' : ''}}
													>{{$cat->category}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-row" hidden>
										<div class="col-md-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">No of Casual Leaves</label>
											<input type="text" min="0" class="form-control form-control-sm num" id="no_of_casual_leaves" name="no_of_casual_leaves" value="{{$employee->no_of_casual_leaves ?? ''}}">
										</div>
										<div class="col-md-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">No of Annual Leaves</label>
											<input type="text" min="0" class="form-control form-control-sm num" id="no_of_annual_leaves" name="no_of_annual_leaves" value="{{$employee->no_of_annual_leaves ?? ''}}">
										</div>
									</div>
								</div>
							</div>

							<!-- Additional Information -->
							<div class="card shadow-none mb-3">
								<div class="card-body bg-light">
									<h6 class="title-style my-3"><span>Additional Information</span></h6>
									<div class="form-row mb-1">
										<div class="col-md-6 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">DS Division</label>
											<select name="dsdivision" id="dsdivision" class="form-control form-control-sm shipClass">
												<option value="">Select</option>
												@foreach($dsdivisions as $dsdivision)
													<option value="{{$dsdivision->id}}"
														{{$dsdivision->id == $employee->ds_divition  ? 'selected' : ''}}>
														{{$dsdivision->ds_division}}
													</option>
												@endforeach
											</select>
										</div>
										<div class="col-md-6 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">GSN Division</label>
											<select name="gsndivision" id="gsndivision" class="form-control form-control-sm shipClass">
												<option value="">Select</option>
												@foreach($gsndivision as $gsndivisions)
													<option value="{{$gsndivisions->id}}"
														{{$gsndivisions->id == $employee->gsn_divition  ? 'selected' : ''}}>
														{{$gsndivisions->gns_division}}
													</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-row mb-1">
										<div class="col-md-6 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">GSN's Name</label>
											<input type="text" class="form-control form-control-sm" id="gsnname" name="gsnname" value="{{$employee->gsn_name ?? ''}}">
										</div>
										<div class="col-md-6 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Contact No</label>
											<input type="text" class="form-control form-control-sm" id="gsncontactno" name="gsncontactno" value="{{$employee->gsn_contactno ?? ''}}">
										</div>
									</div>
									<div class="form-row mb-1">
										<div class="col-md-6 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Closest Police Station</label>
											<select name="policestation" id="policestation" class="form-control form-control-sm shipClass">
												<option value="">Select</option>
												@foreach($policestation as $policestations)
													<option value="{{$policestations->id}}"
														{{$policestations->id == $employee->police_station  ? 'selected' : ''}}>
														{{$policestations->police_station}}
													</option>
												@endforeach
											</select>
										</div>
										<div class="col-md-6 col-sm-6 col-12 mb-2">
											<label class="small font-weight-bold text-dark">Contact No</label>
											<input type="text" class="form-control form-control-sm" id="policecontat" name="policecontat" value="{{$employee->police_contactno ?? ''}}">
										</div>
									</div>
								</div>
							</div>

							<input type="hidden" class="form-control form-control-sm" id="id" name="id" value="{{$employee->id}}" readonly>
							
							<div class="form-group mt-3 text-right">
								@can('employee-edit')
									<button type="submit" class="btn btn-primary btn-sm px-4"><i class="far fa-edit"></i>&nbsp;Update</button>
								@endcan
							</div>
						</form>
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
	$('#employee_menu_link').addClass('active');
	$('#employee_menu_link_icon').addClass('active');
	$('#employeeinformation').addClass('navbtnactive');
	$('#view_employee_link').addClass('active');

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

	function resetDepartment() {
		$('#department').val(null).trigger('change');
	}

	$('.num').keypress(function (event) {
		return isNumber(event, this)
	});

	function isNumber(evt, element) {
		var charCode = (evt.which) ? evt.which : event.keyCode
		if (
			(charCode != 46 || $(element).val().indexOf('.') != -1) &&
			(charCode < 48 || charCode > 57))
			return false;
		return true;
	}

	// Image preview functionality
	$('#photograph').change(function() {
		var reader = new FileReader();
		reader.onload = function(e) {
			$('#preview').attr('src', e.target.result).show();
		}
		reader.readAsDataURL(this.files[0]);
	});
</script>
@endsection