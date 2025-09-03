@extends('layouts.app')

@section('content')

<main>
    <div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            <div class="page-header-content py-3">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="fas fa-calendar-times"></i></div>
                    <span>Leave Report By Employee</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <form class="form" method="POST" action="{{ route('leavedatafilter') }}" method="POST">
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
                                    <select name="employee" id="employee" class="form-control form-control-sm col-md-12">
                                        <option value="">Select</option>
                                        @foreach($employee as $employees)
                                        <option value="{{$employees->emp_id}}">{{$employees->emp_id}} -
                                          {{$employees->emp_name_with_initial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="text" id="from_date" name="from_date" class="form-control border-right-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="far fa-calendar"></i></span>
                                        </div>
                                        <input type="text" id="to_date" name="to_date" class="form-control">
                                    </div>
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
                                <th>Leave From</th>
                                <th>Leave To</th>
                                <th>Leave Type</th>
                                <th>Status</th>
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
$(document).ready(function () {

  var date = new Date();

  $("#from_date").datetimepicker({
    pickTime: false,
    minView: 2,
    format: 'yyyy-mm-dd',
    autoclose: true,
  });

  $("#to_date").datetimepicker({
    pickTime: false,
    minView: 2,
    format: 'yyyy-mm-dd',
    autoclose: true,
  });

  var _token = $('input[name="_token"]').val();

    load_dt('');
    function load_dt(department){
        $('#attendreporttable').DataTable({
            "columnDefs": [
                {
                    "targets": -1,
                    "orderable": false
                }
            ],
            "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-default btn-sm',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Print',
                    className: 'btn btn-default btn-sm',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                }
            ],
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{url('/leave_report_list')}}",
                "data": {'department':department},
            },

            columns: [
                { data: 'id' },
                { data: 'emp_name_with_initial' },
                { data: 'dept_name' },
                { data: 'leave_from' },
                { data: 'leave_to' },
                { data: 'leave_type' },
                { data: 'status' }
            ],
            "bDestroy": true,
            "order": [[ 0, "desc" ]],
        });
    }



  //fetch_data();

  function fetch_data(employee = '') {
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();

    $.ajax({
      url: "{{ route('employee.fetch_leave_data') }}",
      method: "POST",
      data: {
        employee: employee,
        from_date: from_date,
        to_date: to_date,
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
          output += '<td>' + data[count].id + '</td>';
          output += '<td>' + data[count].emp_name_with_initial + '</td>';
          output += '<td>' + data[count].leave_from + '</td>';
          output += '<td>' + data[count].leave_to + '</td>';
          output += '<td>' + data[count].leave_type + '</td>';
          output += '<td>' + data[count].status + '</td></tr>';
        }
        $('tbody').html(output);
      }
    })
  }


  $('#filter').click(function () {
    var employee = $('#employee').val();
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    if (employee != '' || from_date != '' && to_date != '') {
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