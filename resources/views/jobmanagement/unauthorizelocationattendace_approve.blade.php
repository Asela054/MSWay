@extends('layouts.app')

@section('content')

<main> 
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.attendant&leave_nav_bar')
           
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card mb-2">
                <div class="card-body">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Location</label>
                                <select name="location" id="location" class="form-control form-control-sm">
                                    <option value="">Select Location</option>
                                     @foreach($locations as $location)
                                        <option value="{{$location->id}}">{{$location->location}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm"> </span>
                                    </div>
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                          <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-sm filter-btn float-right" id="btn-filter" style="margin-top: 25px;"> Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <div class="row align-items-center mb-4">
                            <div class="col-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Select All Records</label>
                                </div>
                            </div>
                            <div class="col-6 text-right">
                                <button id="approve_att" class="btn btn-primary btn-sm">Approve All</button>
                            </div>
                        </div>
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%"
                                id="dataTable">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Emp ID</th>
                                        <th>Employee</th>
                                        <th>Location</th>
                                        <th>Date</th>
                                        <th>On Time</th>
                                        <th>Off Time</th>
                                        <th class="d-none">location id</th>
                                        <th>Reason</th>
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

    <div class="modal fade" id="approveconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Approve Location Attendance </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col text-center">
                            <h4 class="font-weight-normal">Are you sure you want to Approve this data?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="approve_button" id="approve_button"
                        class="btn btn-warning px-3 btn-sm">Approve</button>
                    <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

</main>
              
@endsection


@section('script')

<script>
$(document).ready(function(){

    $('#attendant_menu_link').addClass('active');
    $('#attendant_menu_link_icon').addClass('active');
    $('#jobmanegment').addClass('navbtnactive');

     let employee_f = $('#employee_f');

       employee_f.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("employee_list_production")}}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    }
                },
                cache: true
            }
       });

        function load_dt(location, employee, from_date, to_date){
           $('#dataTable').DataTable({
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                processing: true,
                serverSide: true,
                ajax: {
                    url: scripturl + '/unauthorizeattendance_approve_list.php',
                    type: 'POST',
                    data: {
                        location: location, 
                        employee: employee, 
                        from_date: from_date,
                        to_date: to_date
                    },
                },
                columns: [
                    {
                        data: null,
                        name: 'checkbox',
                        render: function(data, type, row) {
                            if (row.approve_status == 0) {
                                return '<input type="checkbox" class="approve-checkbox" data-id="' + row.id + '">';
                            } else {
                                return '<i class="fas fa-check-circle text-success"></i>';
                            }
                        },
                        orderable: false
                    },
                    { data: 'employee_id', name: 'employee_id' },
                    { data: 'employee_display', name: 'employee_display' },
                    { data: 'location', name: 'location' },
                    { data: 'date', name: 'date' },
                    { data: 'on_time', name: 'on_time' },
                    { data: 'off_time', name: 'off_time' },
                    { 
                        data: 'location_id', 
                        name: 'location_id',
                         visible: false
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                    }
                ],
                "bDestroy": true,
                "order": [
                    [1, "desc"]
                ]
            });
        }

        load_dt('', '',  '', '');

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let location = $('#location').val();
            let employee = $('#employee_f').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            load_dt(location, employee, from_date, to_date);
        });


           var selectedRowIdsapprove = [];

    $('#approve_att').click(function () {
        selectedRowIdsapprove = [];
        $('#dataTable tbody .approve-checkbox:checked').each(function () {
            var rowData = $('#dataTable').DataTable().row($(this).closest('tr')).data();
            
            if (rowData) {
                selectedRowIdsapprove.push({
                    id: rowData.id, // Using the ID from the first column
                    empid: rowData.employee_id, // From column 2
                    emp_name: rowData.employee_display, // From column 3
                    date: rowData.date, // From column 5
                    on_time: rowData.on_time, // From column 6
                    off_time: rowData.off_time, // From column 7
                    location_id: rowData.location_id,
                    reason: rowData.reason // From column 8 (if visible)
                });
            }
        });

        if (selectedRowIdsapprove.length > 0) {
            console.log(selectedRowIdsapprove);
            $('#approveconfirmModal').modal('show');
        } else {
            alert('Please select rows to approve!');
        }
    });

$('#approve_button').click(function () {
    $('#approve_button').html('<i class="fa fa-spinner fa-spin mr-2"></i> Processing').prop('disabled', true);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var location = $('#location').val();
    var attendace_type = $('#attendace_type').val();
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();

    $.ajax({
        url: '{!! route("jobattendanceapprovesave") !!}',
        type: 'POST',
        dataType: "json",
        data: {
            records: selectedRowIdsapprove,
            location: location,
            attendace_type: attendace_type,
            from_date: from_date,
            to_date: to_date
        },
        success: function (data) {
            $('#approve_button').html('Approve').prop('disabled', false);
            
            if (data.success) {
                setTimeout(function () {
                    $('#approveconfirmModal').modal('hide');
                    // Refresh the DataTable instead of full page reload
                    $('#dataTable').DataTable().ajax.reload(null, false);
                }, 500);
            } else {
                alert('Error: ' + data.message);
            }
        },
        error: function (xhr, status, error) {
            $('#approve_button').html('Approve').prop('disabled', false);
            alert('Error: ' + error);
        }
    });
});

    $('#selectAll').click(function (e) {
        $('#dataTable').closest('table').find('td input:checkbox').prop('checked', this.checked);
    });
});
</script>


@endsection