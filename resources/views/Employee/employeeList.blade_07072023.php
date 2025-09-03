@extends('layouts.app')

@section('content')

<main>
                    <div class="page-header pb-10 page-header-dark bg-gradient-primary-to-secondary">
                        <div class="container-fluid">
                            <div class="page-header-content">
                                <h1 class="page-header-title">
                                    <div class="page-header-icon"><i data-feather="edit-3"></i></div>
                                    <span>Employee Information</span>
                                </h1>
                                
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid mt-n10">
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="default">
                                    <div class="card mb-4">
                                       <div class="card-header">Employee Information</div>
                                        <div class="card-body">
                                            <div class="sbp-preview">
                                                <div class="sbp-preview-content">
                                                    <form>
												  <div class="form-row">
													<div class="form-group col-md-3">
													  <label for="inputEmail4">Employee Id</label>
													  <input type="text" class="form-control" id="inputEmail4" placeholder="Type Employee Id">
													</div>
													<div class="form-group col-md-3">
													  <label for="inputPassword4">Employee Name</label>
													  <input type="text" class="form-control" id="inputPassword4" placeholder="Type Employee Name">
													</div>
													<div class="form-group col-md-3">
													  <label for="inputPassword4">Employment Status</label>
													   <select id="inputState" class="form-control">
														<option selected>Choose...</option>
														<option>...</option>
													  </select>
													</div>
													<div class="form-group col-md-3">
													  <label for="inputPassword4">Include</label>
													   <select id="inputState" class="form-control">
														<option selected>Choose...</option>
														<option>...</option>
													  </select>
													</div>
												  </div>
												   <div class="form-row">
												  <div class="form-group col-md-3">
													<label for="inputAddress">Supervisor Name</label>
													<input type="text" class="form-control" id="inputAddress" placeholder="Type Supervisor Name">
												  </div>
												  <div class="form-group col-md-3">
													<label for="inputAddress2">Job Title</label>
													<select id="inputState" class="form-control">
														<option selected>Choose...</option>
														<option>...</option>
													  </select>
												  </div>												 
													<div class="form-group col-md-3">
													  <label for="inputCity">Sub Unit</label>
													  <select id="inputState" class="form-control">
														<option selected>Choose...</option>
														<option>...</option>
													  </select>
													</div>				
												  
												  </div>
												 
												  <button type="submit" class="btn btn-primary">Search</button>
												  <button type="reset" class="btn btn-success">Reset</button>
												</form>
                                                </div>
                                           </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                              
                            </div>
                           
                        </div>
                    </div>
                    <div class="container-fluid mt-n1">
                        <div class="card mb-4">
                            
                            <div class="card-body">
                                <div class="datatable table-responsive">
                                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Office</th>                                                
                                                <th>Start date</th>
                                                <th>Salary</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                     
                                        <tbody>
                                        @foreach($employee as $employees)
                                            <tr>
                                                <td><a href="/viewEmployee/{{$employees->id}}">{{$employees->id}}</a></td>
                                                <td>{{$employees->emp_first_name}}</td>
                                                <td>{{$employees->branch}}</td>
                                                <td>{{$employees->emp_join_date}}</td>                                               
                                                <td></td>
                                                <td><div class="badge badge-primary badge-pill">{{$employees->emp_status}}</div></td>
                                                <td>
                                                <a class="btn btn-datatable btn-icon btn-transparent-dark" href="/viewEmployee/{{$employees->id}}"><i data-feather="clipboard"></i></a>
                                                <button class="btn btn-datatable btn-icon btn-transparent-dark addfp" data-id="{{$employees->id}}"><i data-feather="log-in"></i></button>
                                                <button class="btn btn-datatable btn-icon btn-transparent-dark delete" id="{{$employees->id}}"><i data-feather="trash-2"></i></button>
                                                </td>
                                            </tr>

                                            @endforeach
                                            
                                         
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                    <div id="formModal" class="modal fade" role="dialog">
 <div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Employee to Fingerprint</h4>
        </div>
        <div class="modal-body">
         <span id="form_result"></span>
         <form id="formTitle" method="post">
         {{ csrf_field() }}	
          <div class="form-group">
            <label class="control-label col-md-4" >ID: </label>
            <div class="col-md-8">
             <input type="text" name="id" id="id" class="form-control" readonly/>
            </div>
            <label class="control-label col-md-4" >Id: </label>
            <div class="col-md-8">
             <input type="text" name="userid" id="userid" class="form-control" readonly/>
            </div>
            <label class="control-label col-md-4" >name: </label>
            <div class="col-md-8">
             <input type="text" name="name" id="name" class="form-control" />
            </div>
            <label class="control-label col-md-4" >cardno: </label>
            <div class="col-md-8">
             <input type="text" name="cardno" id="cardno" class="form-control" />
            </div>
            <label class="control-label col-md-4" >role: </label>
            <div class="col-md-8">
             <input type="text" name="role" id="role" class="form-control" />
            </div>
            <label class="control-label col-md-4" >password: </label>
            <div class="col-md-8">
             <input type="text" name="password" id="password" class="form-control" />
            </div>
            <label class="control-label col-md-4" >FP Location: </label>
            <div class="col-md-8">
            <select name="devices" class="custom-select shipClass" >
                <option value="">Please Select</option>
                @foreach($device as $devices)
                <option value="{{$devices->ip}}">{{$devices->name}}</option>
                @endforeach
            </select>
            </div>
           
           </div>
          
                <br />
                <div class="form-group" align="center">
                <input type="hidden" name="action" id="action"  />
                 <input type="hidden" name="hidden_id" id="hidden_id" />
                 <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="Edit" />
                </div>
         </form>
        </div>
     </div>
    </div>
</div>

                    <div id="confirmModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title">Confirmation</h2>
            </div>
            <div class="modal-body">
                <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
            </div>
            <div class="modal-footer">
             <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
                </main>
                
              
@endsection
@section('script')



<script>
$(document).ready(function(){

  $('.addfp').click(function(){     
  $('.modal-title').text('Add Employee to Fingerprint');
  $('#action_button').val('Add');
  $('#id').val($(this).attr('data-id'));
  $('#userid').val($(this).attr('data-id'));
  $('#action').val('Add');
  $('#form_result').html('');

  $('#formModal').modal('show');
 });
 
 $('#formTitle').on('submit', function(event){
  event.preventDefault();
  var action_url = '';

  if($('#action').val() == 'Add')
  {
   action_url = "{{ route('addFingerprintUser') }}";
  }
  if($('#action').val() == 'Edit')
  {
   action_url = "{{ route('JobTitle.update') }}";
  }
  

  $.ajax({
   url: action_url,
   method:"POST",
   data:$(this).serialize(),
   dataType:"json",
   success:function(data)
   {
       
    var html = '';
    if(data.errors)
    {
        html = '<div class="alert alert-danger">';
     for(var count = 0; count < data.errors.length; count++)
     {
      html += '<p>' + data.errors[count] + '</p>';
     }
     html += '</div>';
    }
    if(data.success)
    {
     html = '<div class="alert alert-success">' + data.success + '</div>';
     $('#formTitle')[0].reset();
     //$('#titletable').DataTable().ajax.reload();
     location.reload();
    }
    $('#form_result').html(html);
   }
  });
 });

 $(document).on('click', '.edit', function(){
  var id = $(this).attr('id');
  $('#form_result').html('');
  $.ajax({
   url :"/JobTitle/"+id+"/edit",
   dataType:"json",
   success:function(data)
   {
    $('#title').val(data.result.title);
    $('#hidden_id').val(id);
    $('.modal-title').text('Edit Title');
    $('#action_button').val('Edit');
    $('#action').val('Edit');
    $('#formModal').modal('show');
   }
  })
 });

 var user_id;

 $(document).on('click', '.delete', function(){
  user_id = $(this).attr('id');  
  $('#confirmModal').modal('show');
 });

 $('#ok_button').click(function(){
  $.ajax({
   url:"EmployeeDestroy/destroy/"+user_id,
   beforeSend:function(){
    $('#ok_button').text('Deleting...');
   },
   success:function(data)
   {
    setTimeout(function(){
     $('#confirmModal').modal('hide');
     $('#user_table').DataTable().ajax.reload();
     alert('Data Deleted');
    }, 2000);
    location.reload();
   }
  })
 });

});
</script>

@endsection