@extends('layouts.app')

@section('content')

<main>
                    
                    <div class="container-fluid mt-4">
                        <div class="row">
                       
                           
                            <div class="col-lg-12">
                                <div id="default">
                                <div class="card card-header-actions mb-4">
                                    <div class="card-header">
                                    Shift
                                    <button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm">Shift</button>
                                    </div>
                                      
                                        <div class="card-body">
                                        <div class="datatable table-responsive">
                                    <table class="table table-bordered table-hover" id="shifttable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Emp Id </th>
                                                <th>Assign Shift </th> 
                                                <th>Start Time</th>                                                
                                                <th>End Time</th>   
                                                <th>Shift Assign</th>   
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
                                
                                
                                
                              
                            </div>
                           
                        </div>
                    </div>

					 
                    
                </main>
              
@endsection


@section('script')

<script>
$(document).ready(function() {
    id = $(this).attr('uid'); 
   date = $(this).attr('data-date'); 
   emp_name_with_initial = $(this).attr('data-name');

 
   var formdata = {
   _token: $('input[name=_token]').val(),
      id: id,
      date: date
};
 // alert(date);
  $('#form_result').html('');
  $.ajax({
   url :"Getshift",
   dataType:"json",
   data:formdata,
   success:function(data,shifttype)
   {
    for(var count=0; count < shifttype.length; count++)
    {
        shifttype[count].id
    }
    var html = '';
   
    html += '<tr>';
  
    for(var count=0; count < data.length; count++)
    {
     html +='<tr>';
     html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="'+data[count].id+'">'+data[count].id+'</td>';
     html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="'+data[count].emp_first_name+'">'+data[count].emp_first_name+'</td>';
     html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="'+data[count].id+'">'+data[count].shift_name+'</td>';
     html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="'+data[count].id+'">'+data[count].onduty_time+'</td>';
     html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="'+data[count].id+'">'+data[count].offduty_time+'</td>';
     html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="'+data[count].id+'"><input type="radio" id="shift" name="shift" value="morning"><input type="radio" id="shift" name="shift" value="evening"></td>';
     
    }
  
    $('#shifttable tbody').html(html);
   }
  })
 });

 $(document).on('checked', '#shift', function(){
     
    var _token = $('input[name="_token"]').val();
  var userid = $('#aduserid').text();
  var shift = $('#shift').val();
  alert(shift);
  if(shift != '')
  {
   $.ajax({
    url:"AttendentInsertLive",
    method:"POST",
    data:{userid:userid, timestamp:timestamp, _token:_token},
    success:function(data)
    {
     $('#message').html(data);
     $('#AttendviewModal').modal('hide');
     location.reload();
    }
   });
  }
  else
  {
   $('#message').html("<div class='alert alert-danger'>Both Fields are required</div>");
  }
 });

</script>

@endsection