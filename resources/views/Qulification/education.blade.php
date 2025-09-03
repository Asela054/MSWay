@extends('layouts.app')

@section('content')

<main>
                    <div class="page-header pb-10 page-header-dark bg-gradient-primary-to-secondary">
                        <div class="container-fluid">
                            <div class="page-header-content">
                                <h1 class="page-header-title">
                                    <div class="page-header-icon"><i data-feather="edit-3"></i></div>
                                    <span>Education Level</span>
                                </h1>
                                
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid mt-n10">
                        <div class="row">
                        <div class="col-lg-4">
                        <div class="row" id="errors"></div>
                            <div class="card " >
                            <div class="card-header">Education</div>
                                <div class="card-body">
                                <div align="right">
                                
                                    <button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm">Create Record</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                           
                            <div class="col-lg-8">
                                <div id="default">
                                    <div class="card mb-4">
                                      
                                        <div class="card-body">
                                        <div class="datatable table-responsive">
                                    <table class="table table-bordered table-hover" id="titletable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID </th>
                                                <th>Education</th> 
                                                <th>Action</th>   
                                            </tr>
                                        </thead>
                                      
                                        <tbody>
                                        @foreach($education as $educations)
                                            <tr>
                                                <td>{{$educations->id}}</td>
                                                <td>{{$educations->education}}</td>
                                                <td>  <button name="edit" id="{{$educations->id}}" class="edit btn btn-primary btn-sm" type="submit">Edit</button>  
                                                       <button type="submit" name="delete" id="{{$educations->id}}" class="delete btn btn-danger btn-sm">Delete</button></td>
                                                                                          
                                               
                                            </tr>
                                           @endforeach
                                         
                                        </tbody>
                                    </table>
                                </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                              
                            </div>
                           
                        </div>
                    </div>

                    <div id="formModal" class="modal fade" role="dialog">
 <div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
   <h5 class="modal-title" id="exampleModalLabel"></h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn btn-danger" aria-hidden="true">X</span></button>
    
        </div>
        <div class="modal-body">
        <span id="form_result"></span>
         <form id="formTitle" method="post">
         {{ csrf_field() }}	
          <div class="form-group">
            <label class="control-label col-md-4" >Education</label>
            <div class="col-md-8">
             <input type="text" name="education" id="education" class="form-control" />
            </div>
           </div>
          
                <br />
                <div class="form-group" align="center">
                <input type="hidden" name="action" id="action"  />
                 <input type="hidden" name="hidden_id" id="hidden_id" />
                 <input type="submit" name="action_button" id="action_button" class="btn btn-warning" />
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
            <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn btn-danger" aria-hidden="true">X</span></button>
    
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

 

    $('#create_record').click(function(){
  $('.modal-title').text('Add Education');
  $('#action_button').val('Add');
  $('#action').val('Add');
  $('#form_result').html('');

  $('#formModal').modal('show');
 });
 
 $('#formTitle').on('submit', function(event){
  event.preventDefault();
  var action_url = '';
  
 
  if($('#action').val() == 'Add')
  {
   action_url = "{{ route('addEducation') }}";
  }
  
  
  if($('#action').val() == 'Edit')
  {
   action_url = "{{ route('Education.update') }}";
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
    // $('#titletable').DataTable().ajax.reload();
    }
    $('#form_result').html(html);
   }
  });
 });

 

 $(document).on('click', '.edit', function(){
  var id = $(this).attr('id');
  $('#form_result').html('');
  $.ajax({
   url :"/Education/"+id+"/edit",
   dataType:"json",
   success:function(data)
   {
    $('#education').val(data.result.education);
    $('#hidden_id').val(id);
    $('.modal-title').text('Edit Education');
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
   url:"Education/destroy/"+user_id,
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
   }
  })
 });

});
</script>

@endsection