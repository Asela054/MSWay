@extends('layouts.app')

@section('content')



                <main>
                    
                    <div class="container-fluid mt-4">
                        <div class="row">
                       
                           
                            <div class="col-lg-12">
                                <div id="default">
                                <div class="card card-header-actions mb-4">
                                    <div class="card-header">
                                    Attendance Report Data Range
                                   </div>
                                      
                                        <div class="card-body">
                                        <div class="row">
                    <div class="container box">
  
   <div class="panel panel-default">
    <div class="panel-heading">
     <div class="row">
      <div class="col-md-4">Total Records - <b><span id="total_records"></span></b></div>
      <div class="col-md-4">
       <div class="input-group input-daterange">
           <input type="text" name="from_date" id="from_date" readonly class="form-control" />
           <div class="input-group-addon p-2">To</div>
           <input type="text"  name="to_date" id="to_date" readonly class="form-control" />
       </div>
      </div>
      <div class="col-md-4">
      
       <form action="{{ route('attendentfilter') }}" method="POST">
      
       <div class="input-group " style="visibility:hidden">
           <input type="text"  name="from_date_sub" id="from_date_sub"  class="form-control" />           
           <input type="text"  name="to_date_sub" id="to_date_sub"  class="form-control" />
           {{ csrf_field() }}
           
       </div>
       <button type="button" name="filter" id="filter" class="btn btn-info btn-sm">Filter</button>
       <button type="button" name="refresh" id="refresh" class="btn btn-warning btn-sm">Refresh</button>
       <button type="submit" id="exportpdf"  class="btn btn-info btn-sm">Export</button>
       </form>
      </div>
     </div>
    </div>
   
   </div>
  </div>
                                        <div class="datatable table-responsive pt-3">
                                        <table class="table table-striped table-bordered" id="attendreporttable">
                                          <thead>
                                            <tr>
                                            <th width="10%">UID</th>
                                            <th width="10%">Name with Inintial</th>
                                            <th width="15%">Date</th>                                           
                                            <th width="15%">Checkin Time</th>
                                            <th width="15%">CheckOut Time</th>
                                            <th width="5%">Working Hours</th>
                                            <th width="15%">Location</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                          
                                          </tbody>
                                          </table>
                                          {{ csrf_field() }}
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
  

$(document).ready(function(){

 var date = new Date();

 $('.input-daterange').datepicker({
  todayBtn: 'linked',
  format: 'yyyy-mm-dd',
  autoclose: true
 });

 var _token = $('input[name="_token"]').val();

 //fetch_data();

 function fetch_data(from_date = '', to_date = '')
 {
  $.ajax({
   url:"{{ route('daterange.fetch_data') }}",
   method:"POST",
   data:{from_date:from_date, to_date:to_date, _token:_token},
   dataType:"json",
   success:function(data)
   {
    var output = '';
    $('#total_records').text(data.length);
    for(var count = 0; count < data.length; count++)
    {
    var cin=  data[count].timestamp;
    var cout=  data[count].lasttimestamp;    
    var date1 = new Date(cin); // current date
    var date2 = new Date(cout); // mm/dd/yyyy format
    var timeDiff = Math.abs(date2.getTime() - date1.getTime()); // in miliseconds
    var timeDiffInSecond = Math.ceil(timeDiff / 1000); // in second
    var diff = Math.abs(date1 - date2) / 3600000;
    var workhours = parseInt(diff)

     output += '<tr>';
     output += '<td>' + data[count].uid + '</td>';     
     output += '<td>' + data[count].emp_name_with_initial + '</td>';
     output += '<td>' + data[count].date + '</td>';
     output += '<td>' + data[count].timestamp + '</td>';
     output += '<td>' + data[count].lasttimestamp + '</td>';
     output += '<td>' + workhours  + '</td>';
     output += '<td>' + data[count].location + '</td></tr>';
    }
    $('tbody').html(output);
   }
  })
 }


 $('#filter').click(function(){
  var from_date = $('#from_date').val();
  var to_date = $('#to_date').val();
  if(from_date != '' &&  to_date != '')
  {
   fetch_data(from_date, to_date);
   $('#from_date_sub').val(from_date);
  $('#to_date_sub').val(to_date);

  }
  else
  {
   alert('Both Date is required');
  }
 });


 $('#exportpdf').click(function(){
  var from_date_sub = $('#from_date_sub').val();
  var to_date_sub = $('#to_date_sub').val();
  if(from_date_sub == '' &&  to_date_sub == '')
  {
    alert('Both Date is required');
  }
  else
  {
      
  }
  
 });



 $('#refresh').click(function(){
  $('#from_date').val('');
  $('#to_date').val('');
  alert(to_date);
 });


});
</script>




@endsection