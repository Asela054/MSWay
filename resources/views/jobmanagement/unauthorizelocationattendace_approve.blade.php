@extends('layouts.app')

@section('content')

<main> 
    <div class="page-header shadow">
            <div class="container-fluid d-none d-sm-block shadow">
                @include('layouts.attendant&leave_nav_bar')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-calendar-pen"></i></div>
                        <span>Unauthorized Location Attendance Approve</span>
                    </h1>
                </div>
            </div>
        </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card mb-2">
                <div class="card-body p-0 p-2">
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
                                        <th>EMP ID</th>
                                        <th>EMPLOYEE</th>
                                        <th>LOCATION</th>
                                        <th>DATE</th>
                                        <th>ON TIME</th>
                                        <th>OFF TIME</th>
                                        <th class="d-none">LOCATION ID</th>
                                        <th>REASON</th>
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
               "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Unauthorized Location Attendance Approve Information',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Unauthorized Location Attendance Approve Information', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Unauthorized Location Attendance Approve Information',
                            className: 'btn btn-primary btn-sm',
                            text: '<i class="fas fa-print mr-2"></i> Print',
                            customize: function(win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                    ],
                    "order": [
                        [1, "desc"]
                    ],
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

        $('#approve_att').click(async function () {
            var r = await Otherconfirmation("You want to Edit this ? ");
            if (r == true) {
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

                            if (data.errors) {
                                const actionObj = {
                                    icon: 'fas fa-warning',
                                    title: '',
                                    message: 'Record Error',
                                    url: '',
                                    target: '_blank',
                                    type: 'danger'
                                };
                                const actionJSON = JSON.stringify(actionObj, null, 2);
                                action(actionJSON);
                            }
                            if (data.success) {
                                const actionObj = {
                                    icon: 'fas fa-save',
                                    title: '',
                                    message: data.success,
                                    url: '',
                                    target: '_blank',
                                    type: 'success'
                                };
                                const actionJSON = JSON.stringify(actionObj, null, 2);
                                actionreload(actionJSON);
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        position: "top-end",
                        icon: 'warning',
                        title: 'Select Rows to Final Approve!',
                        showConfirmButton: false,
                        timer: 2500
                    });
                }

            }
        });

    $('#selectAll').click(function (e) {
        $('#dataTable').closest('table').find('td input:checkbox').prop('checked', this.checked);
    });
});
</script>


@endsection