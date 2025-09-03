@extends('layouts.app')

@section('content')

<main>
    <div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            <div class="page-header-content py-3">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="fas fa-calendar-check"></i></div>
                    <span>Attendance Report By Employee</span>
                </h1>
            </div>
        </div>
    </div>
  <div class="container-fluid mt-4">
      <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <form class="form" method="POST" action="{{ route('attendentbyemployeefilter') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="form-row">
                                <div class="col-2">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Total Records - </span>
                                        </div>
                                        <input type="text" class="form-control" id="total_records" readonly>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <select name="employee" id="employee" class="form-control form-control-sm" required>
                                        <option value="">Employee</option>
                                        @foreach($employee as $employees)
                                        <option value="{{$employees->emp_id}}">
                                          {{$employees->emp_id}}-{{$employees->emp_name_with_initial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <button type="button" name="filter" id="filter" class="btn btn-outline-dark btn-sm"><i class="fas fa-search mr-2"></i>Filter</button>
                                    <button type="button" name="refresh" id="refresh" class="btn btn-outline-primary btn-sm"><i class="fas fa-sync-alt mr-2"></i>Refresh</button>
                                    <button type="submit" id="exportpdf"  class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel mr-2"></i>Export</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <table class="table table-striped table-bordered table-sm small" id="attendreporttable">
                            <thead>
                              <tr>
                                <th>UID</th>
                                <th>Name with Inintial</th>
                                <th>Date</th>
                                <th>Checkin Time</th>
                                <th>CheckOut Time</th>
                                <th>Working Hours</th>
                                <th>Location</th>
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
</main>
              
@endsection


@section('script')

  <script>
  

$(document).ready(function(){

 var _token = $('input[name="_token"]').val();

 //fetch_data();

 function fetch_data(employee = '') {


   $.ajax({
     url: "{{ route('employee.fetch_data') }}",
     method: "POST",
     data: {
       employee: employee,
       _token: _token
     },
     dataType: "json",
     success: function (data) {
       var output = '';
       $('#total_records').val(data.length);
       for (var count = 0; count < data.length; count++) {
         var cin = data[count].timestamp;
         var cout = data[count].lasttimestamp;

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
         output += '<td>' + workhours + '</td>';
         output += '<td>' + data[count].location + '</td></tr>';
       }
       $('tbody').html(output);
     }
   })
 }


 $('#filter').click(function () {
   var employee = $('#employee').val();
   if (employee != '') {
     fetch_data(employee);


   } else {
     alert('Select Employee');
   }
 });






 $('#refresh').click(function () {
   $('#employee').val('');


   $('#attendreporttable').html(response);
 });


});
</script>




@endsection