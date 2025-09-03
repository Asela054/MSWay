@extends('layouts.app')

@section('content')

<main>
	<div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            <div class="page-header-content py-3">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="fas fa-user"></i></div>
                    <span>Contact Details</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
		<div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-9">
						<form id="PdetailsForm" class="form-horizontal" method="POST" action="{{ route('contactUpdate') }}">
							{{ csrf_field() }}
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Address Street 1</label>
									<input class="form-control form-control-sm" id="address1" type="text" name="address1" value="{{$employee->emp_address}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Address Street 2</label>
									<input class="form-control form-control-sm" id="address2" type="text" name="address2" value="{{$employee->emp_address_2}}">
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">City</label>
									<input class="form-control form-control-sm" id="city" type="text" name="city" value="{{$employee->emp_city}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">State/Province</label>
									<input class="form-control form-control-sm" id="province" type="text" name="province" value="{{$employee->emp_province}}">
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Zip/Postal Code</label>
									<input class="form-control form-control-sm" id="postal_code" type="text" name="postal_code"
									value="{{$employee->emp_postal_code}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Home Telephone</label>
									<input class="form-control form-control-sm" id="home_no" type="text" name="home_no" value="{{$employee->emp_home_no}}">
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Mobile</label>
									<input class="form-control form-control-sm" id="mobile" type="text" name="mobile" value="{{$employee->emp_mobile}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Work Telephone</label>
									<input class="form-control form-control-sm" id="work_telephone" type="text" name="work_telephone"
									value="{{$employee->emp_work_phone_no}}">
								</div>
							</div>
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Email</label>
									<input class="form-control form-control-sm" id="work_email" type="email" name="work_email"
									value="{{$employee->emp_email}}">
								</div>
								<div class="col">
									<label class="small font-weight-bold text-dark">Other Email</label>
									<input class="form-control form-control-sm" id="other_email" type="email" name="other_email"
									value="{{$employee->emp_other_email}}">
								</div>								
							</div>
							<div class="form-group mt-3 text-right">
								<button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm px-4"><i class="fas fa-pencil-alt"></i>&nbsp;Edit</button>
							</div>
							<input type="hidden" class="form-control form-control-sm" id="id" name="id" placeholder="First Name" value="{{$employee->id}}">
						</form>
						<hr class="border-dark">
						<form class="form-horizontal" method="POST" action="{{ route('contactAttachment') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="form-row">
								<div class="col">
									<label class="small font-weight-bold text-dark">Select File</label>
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
							<div class="form-group mt-3 text-right">
								<button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm px-4"><i class="fas fa-save"></i>&nbsp;Save</button>
							</div>
							<input type="hidden" class="form-control form-control-sm" id="id" name="id" value="{{$employee->id}}">
						</form>
					</div>
					@include('layouts.employeeRightBar')
				</div>
			</div>
		</div>
	</div>
</main>
                
              
@endsection
