@extends('layouts.app')

@section('content')

<main>
                    
                    <div class="container-fluid mt-4">
                        <div class="row">
                        
                           
                            <div class="col-lg-12">
                                <div id="default">
                                <div class="card card-header-actions mb-4">
                                    <div class="card-header">
                                    Work Level
                                    <button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm">Add Level </button>
                                    </div>
                                      
                                        <div class="card-body">
                                        <div class="datatable table-responsive">
                                    <table class="table table-bordered table-hover" id="jobtable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID </th>
                                                <th>Work Level</th> 
                                                <th>Action</th>   
                                            </tr>
                                        </thead>
                                      
                                        <tbody>
                                        @foreach($worklevel as $worklevels)
                                            <tr>
                                                <td>{{$worklevels->id}}</td>
                                                <td>{{$worklevels->level}}</td>
                                                <td>  <button name="edit" id="{{$worklevels->id}}" class="edit btn btn-primary btn-sm" type="submit">Edit</button>  
                                                       <button type="submit" name="delete" id="{{$worklevels->id}}" class="delete btn btn-danger btn-sm">Delete</button></td>
                                                                                          
                                               
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
        

          <h5 class="modal-title" id="exampleModalLabel">Add New title</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn btn-danger" aria-hidden="true">X</span></button>
    
        </div>
        <div class="modal-body">
         <span id="form_result"></span>
         <form method="post" id="formTitle" class="form-horizontal">
         {{ csrf_field() }}	
          <div class="form-group">
            <label class="control-label col-md-4" >Worklevel: </label>
            <div class="col-md-8">
             <input type="text" name="level" id="level" class="form-control" />
            </div>
           </div>
          
                <br />
                <div class="form-group" align="center">
                 <input type="hidden" name="action" id="action" value="Add" />
                 <input type="hidden" name="hidden_id" id="hidden_id" />
                 <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="Add" />
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
    $(document).ready(function() {
    $('#jobtable').DataTable();
} );
 
    $('#create_record').click(function(){
  $('.modal-title').text('Add Job Title');
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
   action_url = "{{ route('addWorklevel') }}";
  }
  if($('#action').val() == 'Edit')
  {
   action_url = "{{ route('Worklevel.update') }}";
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
     location.reload()
    }
    $('#form_result').html(html);
   }
  });
 });

 $(document).on('click', '.edit', function(){
  var id = $(this).attr('id');
  $('#form_result').html('');
  $.ajax({
   url :"Worklevel/"+id+"/edit",
   dataType:"json",
   success:function(data)
   {
    $('#title').val(data.result.level);
    $('#hidden_id').val(id);
    $('.modal-title').text('Edit Job Title');
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
   url:"Worklevel/destroy/"+user_id,
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
    location.reload()
   }
  })
 });

});
</script>

@endsection