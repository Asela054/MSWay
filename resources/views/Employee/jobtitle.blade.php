@extends('layouts.app')

@section('content')

<main>
                    <div class="page-header pb-10 page-header-dark bg-gradient-primary-to-secondary">
                        <div class="container-fluid">
                            <div class="page-header-content">
                                <h1 class="page-header-title">
                                    <div class="page-header-icon"><i data-feather="edit-3"></i></div>
                                    <span>Job Details</span>
                                </h1>
                                
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid mt-n10">
                        <div class="row">
                            <div class="col-lg-9">
                                <div id="default">
                                    <div class="card mb-4">
                                       <div class="card-header">Job</div>
                                        <div class="card-body">
                                            <div class="sbp-preview">
                                                <div class="sbp-preview-content">
                                                    

<form>
    
	<div class="form-group col-lg-8">
	<label for="exampleFormControlInput1">
       Job Title</label>
	<input class="form-control" id="exampleFormControlInput1" type="text">
	</div>
	<div class="form-group col-lg-8">
	<label for="exampleFormControlInput1">
     Job Specification</label>
	<input class="form-control" id="exampleFormControlInput1" type="text">
	</div>
	<div class="form-group col-lg-8">
	<label for="exampleFormControlInput1">
      Employment Status</label>
	<input class="form-control" id="exampleFormControlInput1" type="text">
	</div>
	<div class="form-group col-lg-8">
	<label for="exampleFormControlInput1">
       Job Category</label>
	<input class="form-control" id="exampleFormControlInput1" type="text">
	</div>
	<div class="form-group col-lg-8">
	<label for="exampleFormControlInput1">
      Joind  Date</label>
                <div class='input-group date' id='datetimepicker1'>
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
	
                    </span>
                </div>
    </div>
	<div class="form-group col-lg-8">
	<label for="exampleFormControlInput1">
     Sub Unit</label>
	<input class="form-control" id="exampleFormControlInput1" type="text">
	</div>
	<div class="form-group col-lg-8">
	<label for="exampleFormControlInput1">
      Location</label>
	<input class="form-control" id="exampleFormControlInput1" type="text">
	</div>
	<h4>Employment Contract</h4>
	
	<div class="form-group col-lg-8">
	<label for="exampleFormControlInput1">
      Start  Date</label>
                <div class='input-group date' id='datetimepicker1'>
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
	
                    </span>
                </div>
    </div>
	<div class="form-group col-lg-8">
	<label for="exampleFormControlInput1">
      End  Date</label>
                <div class='input-group date' id='datetimepicker1'>
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
	
                    </span>
                </div>
    </div>
	
	<div class="form-group col-lg-8">
	<label for="exampleFormControlInput1">Contract Details </label>
	
	</div>

	 
	
    
    <button type="submit" class="btn btn-primary">Save</button>
    <button type="reset" class="btn btn-success">Clear</button>
</form>


                                                </div>
                                           </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                              
                            </div>
                            @include('layouts.employeeRightBar')
                           
                        </div>
                    </div>
					 
					  <div class="container-fluid mt-3">
                        <div class="card mb-4">
                             <div class="card-header">Attachments</div>
                            <div class="card-body">
                               <div class="sbp-preview">
                                                <div class="sbp-preview-content">
                                                    <form>
												
												
												  <div class="form-row">
												  	  <label class="form-label col-md-2">Select File</label>
												  <div class="custom-file  col-lg-6">
											
													<input type="file" class="custom-file-input" id="validatedCustomFile" required>
													<label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
													<div class="invalid-feedback">Example invalid custom file feedback</div>
												  </div>
												  </div>
													
												  <div class="form-row mt-1">
												   <label for="inputEmail3" class="col-sm-2 col-form-label">Comment</label>
												  <div class="form-group">
													 <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
													</div>
												  </div>
												  
												  
												 
												  <button type="submit" class="btn btn-primary">Save</button>
												</form>
                                                </div>
                                           </div>
                                        </div>
                            </div>
                        </div>
                       
                    
                </main>
                @endsection
