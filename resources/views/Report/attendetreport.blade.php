@extends('layouts.app')

@section('content')



<main>
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.reports_nav_bar')
           
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <form class="form" method="POST" action="{{ route('atenddatafilter') }}" method="POST">
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
                                    <select name="location" id="location" class="form-control form-control-sm">
                                        <option value="">Location</option>
                                        @foreach($branch  as $branchs)
                                        <option value="{{$branchs->id}}">{{$branchs->location}}</option>
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
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="attendreporttable">
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
                        </div>
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
    $('#report_menu_link').addClass('active');
    $('#report_menu_link_icon').addClass('active');
    $('#employeereportmaster').addClass('navbtnactive');

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

    function fetch_data() {
        var location = $('#location').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();


        $.ajax({
            url: "{{ route('employee.fetch_attend_data') }}",
            method: "POST",
            data: {
                location: location,
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
        var location = $('#location').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        if (location != '' || from_date != '' && to_date != '') {
            fetch_data();
        }

    });

    $('#refresh').click(function () {
        $('#from_date').val('');
        $('#employee').val('');
        $('#to_date').val('');

        $('#attendreporttable').html(response);
    });

});
</script>

@endsection