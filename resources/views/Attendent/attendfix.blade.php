@extends('layouts.app')

@section('content')

<main>
               
                    <div class="container-fluid mt-4">
                        <div class="row">
                       
                        
                            <div class="col-lg-12">
                                <div id="default">
                                
                                    <div class="card mb-4">
                                     <div class="modal-header">
                                     <div align="right">
                                
                                <!--button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm">Attendance</button-->
                                <button type="button" name="getdata" id="getdata" class="btn btn-success btn-sm getdata">Getdata</button>
                                
                                </div>
                                </div>
                                        <div class="card-body">
                                        
                                        <div class="datatable table-responsive">
                                    <table class="table table-bordered table-hover" id="attendtable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID </th>
                                                <th>Employee ID</th> 
                                                <th>Name</th>   
                                                <th>Check In</th>  
                                                <th>Check Out</th>   
                                                
                                                <th>Action</th>  
                                            </tr>
                                        </thead>
                                      
                                        <tbody>
                                        @foreach($attendance as $attendances)


                                      
                                            <tr>
                                                <td>{{$attendances->id}}</td>
                                                <td>{{$attendances->uid}}</td>
                                                <td>{{$attendances->emp_name_with_initial}}</td>
                                                <td> {{$attendances->firsttimestamp}} </td>
                                                <td>  {{$attendances->lasttimestamp}}   </td>                                            
                                                <td> 
                                                @if($attendances->lasttimestamp<=$attendances->firsttimestamp)
                                                 <button type="submit" name="view_button" uid="{{$attendances->uid}}" data-date="{{$attendances->date}}" class="view_button btn btn-warning btn-sm">View</button>
                                                 @endif    
                                                     
                                                       </td>
                                                                                          
                                               
                                                                                                    
                                               
                                            </tr>
                                           @endforeach
                                         
                                        </tbody>
                                    </table>
                                    
                                </div>
                                <a href="exportAttendance" class="btn btn-success btn-sm"> Export data</a>
                                
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                              
                            </div>
                           
                        </div>
                    </div>

                    


                    <!--div id="formModal" class="modal fade" role="dialog">
 <div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
    
          <h5 class="modal-title" id="exampleModalLabel">Add New Attendance</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn btn-danger" aria-hidden="true">X</span></button>
    
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
            <label class="control-label col-md-4" >User Id: </label>
            <div class="col-md-8">
             <input type="text" name="uid" id="uid" class="form-control" />
            </div>
            <label class="control-label col-md-4" >State: </label>
            <div class="col-md-8">
             <input type="text" name="state" id="state" class="form-control" />
            </div>
            <label class="control-label col-md-4" >Timestamp: </label>
            <div class="col-md-8">
             <input type="text" name="timestamp" id="timestamp" class="form-control" />
            </div>
            <label class="control-label col-md-4" >Type: </label>
            <div class="col-md-8">
             <input type="text" name="type" id="type" class="form-control" />
            </div>
           </div>
          
                <br />
                <div class="form-group" align="center">
                <input type="hidden" name="action" id="action" value="Edit" />
                 <input type="hidden" name="hidden_id" id="hidden_id" />
                 <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="Edit" />
                </div>
         </form>
        </div>
     </div>
    </div>
</div-->
<div id="formModaladd" class="modal fade" role="dialog">
 <div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
   <h5 class="modal-title" id="exampleModalLabel">Add New Attendance</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn btn-danger" aria-hidden="true">X</span></button>
    
        </div>
        <div class="modal-body">
         <span id="form_result1"></span>
         <form id="formAdd" method="post">
         {{ csrf_field() }}	
          <div class="form-group">
            <label class="control-label col-md-4" >ID: </label>
            <div class="col-md-8">
             <input type="text" name="id" id="id" class="form-control" readonly/>
            </div>
            <label class="control-label col-md-4" >User Id: </label>
            <div class="col-md-8">
            <select name="uid" id="uid" class="custom-select shipClass" >
                                                            <option value="">Please Select</option>
                                                            @foreach($user as $users)
                                                            <option value="{{$users->userid}}">{{$users->name}}</option>
                                                            @endforeach
                                                        </select>
            </div>
            
            <label class="control-label col-md-4" >Timestamp: </label>
            <div class="col-md-8">
            <input type="text" name="timestamp" id="timestamp" class="form-control"  data-date="1979-09-16T05:25:07Z" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1" />
             <input type="hidden" name="userid" id="userid" class="form-control"  value="{{ Auth::user()->id }}"/>
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
<div id="AttendviewModal" class="modal fade bd-example-modal-lg" role="dialog">
<div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Attendent Update</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn btn-danger" aria-hidden="true">X</span></button>
    
            </div>
            <div class="modal-body">
            <div id="message"></div>
            <table  id='attendTable' class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                        <th>User id</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                    </table>
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

<div id="getdataModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn btn-danger" aria-hidden="true">X</span></button>
    
            </div>
            <div class="modal-body">
                <h4 align="center" style="margin:0;">Please check the devices connection and comfirm?</h4>
            </div>
            <div class="modal-footer">
             <button type="button" name="comfirm_button" id="comfirm_button" class="btn btn-danger">comfirm</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>					 
                    
                </main>
              
@endsection


@section('script')

<script>


$(document).ready(function() {
    $('#attendtable').DataTable( {
        "order": [[ 3, "desc" ]]
    } );
} );
$(document).ready(function(){


    var date = new Date();

$('#formModaladd #timestamp').datepicker({
 todayBtn: 'linked',
 format: 'yyyy-mm-dd',
 autoclose: true
});

$('#formModal #adtimestamp').datepicker({
 todayBtn: 'linked',
 format: 'yyyy-mm-dd',
 autoclose: true
});

    $('#create_record').click(function(){
  $('.modal-title').text('Add New Attendance');
  $('#action_button').val('Add');
  $('#action').val('Add');
  $('#form_result').html('');

  $('#formModaladd').modal('show');
 });
 $('#formModaladd #uid').change(function(){
    var id=$(this).val();
   // alert(id);
    $('#formModaladd #id').val(id);
})


 $('#formAdd').on('submit', function(event){
  event.preventDefault();
  var action_url = '';
  
 
  if($('#action').val() == 'Add')
  {
   action_url = "{{ route('Attendance.store') }}";
  }
  
  if($('#action').val() == 'Edit')
  {
   action_url = "{{ route('Attendance.update') }}";
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
     $('#formAdd')[0].reset();
    // $('#titletable').DataTable().ajax.reload();
    //location.reload();
    }
    $('#form_result1').html(html);
   }
  });
 });


 
 $(document).on('click', '.edit', function(){
  var aid = $(this).attr('id');
 // alert(aid);
  $('#form_result').html('');
  $.ajax({
   url :"/Attendance/"+aid+"/edit",
   dataType:"json",
   success:function(data)
   {
    $('#uid').val(data.result.uid);
    $('#id').val(data.result.id);
    $('#state').val(data.result.state);
    $('#timestamp').val(data.result.timestamp);
    $('#hidden_id').val(aid);
    $('.modal-title').text('Edit Attendent');
    $('#action_button').val('Edit');
    $('#action').val('Edit');
    $('#formModaladd').modal('show');
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
   url:"Attendance/destroy/"+user_id,
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

 $(document).on('click', '.getdata', function(){
  user_id = $(this).attr('id');  
  $('#getdataModal').modal('show');
 });

 $('#comfirm_button').click(function(){
    $.ajaxSetup({
 
 headers: {
  
   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  
 }
  
 });
  $.ajax({
   url:"Attendance/getdevicedata",
   method:"POST",
   dataType:"json",
   beforeSend:function(){
    $('#comfirm_button').text('Procesing...');
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


 

  
 
 $(document).on('click', '.view_button', function(){
    id = $(this).attr('uid'); 
   date = $(this).attr('data-date'); 

 
   var formdata = {
   _token: $('input[name=_token]').val(),
      id: id,
      date: date
};
 // alert(date);
  $('#form_result').html('');
  $.ajax({
   url :"AttendentUpdate",
   dataType:"json",
   data:formdata,
   success:function(data)
   {
    $('#AttendviewModal').modal('show');
    var html = '';
    html += '<tr>';
    html += '<td id="aduserid">'+id+'</td>';
    html += '<td contenteditable> <input type="datetime-local" id="adtimestamp" name="adtimestamp" placeholder="YYYY-MM-DD - HH:ii p" ></td>';
    html += '<td><button type="button" class="btn btn-success btn-xs" id="add">Add</button></td></tr>';
    for(var count=0; count < data.length; count++)
    {
     html +='<tr>';
     html +='<td  >'+data[count].uid+'</td>';
     html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="'+data[count].id+'">'+data[count].timestamp+'</td>';
     html += '<td><button type="button" class="btn btn-danger btn-xs addelete" id="'+data[count].id+'">Delete</button></td></tr>';
    }
    $('#attendTable tbody').html(html);
   }
  })
 });

 $(document).on('click', '#add', function(){
    var _token = $('input[name="_token"]').val();
  var userid = $('#aduserid').text();
  var timestamp = $('#adtimestamp').val();
  //alert(userid);
  if(userid != '' && timestamp != '')
  {
   $.ajax({
    url:"AttendentInsertLive",
    method:"POST",
    data:{userid:userid, timestamp:timestamp, _token:_token},
    success:function(data)
    {
     $('#message').html(data);
     fetch_data();
    }
   });
  }
  else
  {
   $('#message').html("<div class='alert alert-danger'>Both Fields are required</div>");
  }
 });

 $(document).on('blur', '.timestamp', function(){
    var _token = $('input[name="_token"]').val();
  var timestamp = $(this).data("timestamp");
  var timestamp = $(this).text();
  var id = $(this).data("id");

  if(timestamp != '')
  {

    
   $.ajax({
    url:"AttendentUpdateLive",
    method:"POST",
    data:{id:id, timestamp:timestamp, _token:_token},
    success:function(data)
    {
     $('#message').html(data);
    }
   })
  }
  else
  {
   $('#message').html("<div class='alert alert-danger'>Enter some value</div>");
  }
 });

 $(document).on('click', '.addelete', function(){
  var id = $(this).attr("id");
  var _token = $('input[name="_token"]').val();
 
  if(confirm("Are you sure you want to delete this records?"))
  {
   $.ajax({
    url:"AttendentDeleteLive",
    method:"POST",
    data:{id:id, _token:_token},
    success:function(data)
    {
     $('#message').html(data);
     fetch_data();
    }
   });
  }
 });

});
</script>

@endsection