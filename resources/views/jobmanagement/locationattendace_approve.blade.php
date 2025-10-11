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
                        <span>Location Attendance Approve</span>
                    </h1>
                </div>
            </div>
        </div>
    <div class="container-fluid mt-2 p-0 p-2">
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
                                <button id="approve_att" class="btn btn-primary btn-sm px-3"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;&nbsp;Approve All</button>
                            </div>
                            <div class="col-12">
                                    <hr class="border-dark">
                                </div>
                             <div class="col-md-12">
                                    <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                        data-toggle="offcanvas" data-target="#offcanvasRight"
                                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                        Records</button>
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
                                        <th class="d-none" >REASON</th>
                                        <th class="d-none">EMPNAME</th>
                                        <th class="d-none">CALLING</th>
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

         <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
                aria-labelledby="offcanvasRightLabel">
                <div class="offcanvas-header">
                    <h2 class="offcanvas-title font-weight-bolder" id="offcanvasRightLabel">Records Filter Options</h2>
                    <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                        <span aria-hidden="true" class="h1 font-weight-bolder">&times;</span>
                    </button>
                </div>
                <div class="offcanvas-body">
                    <ul class="list-unstyled">
                        <form class="form-horizontal" id="formFilter">
                            <li class="mb-2">
                                <div class="col-md-12">
                                   <label class="small font-weight-bolder text-dark">Location</label>
                                    <select name="location" id="location" class="form-control form-control-sm">
                                        <option value="">Select Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{$location->location}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">Employee</label>
                                    <select name="employee" id="employee_f" class="form-control form-control-sm">
                                    </select>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark"> From Date* </label>
                                    <input type="date" id="from_date" name="from_date"
                                        class="form-control form-control-sm" placeholder="yyyy-mm-dd"  value="{{date('Y-m-d') }}"
                                           required>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark"> To Date*</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                        placeholder="yyyy-mm-dd"  value="{{date('Y-m-d') }}" required>
                                </div>
                            </li>
                            <li>
                                <div class="col-md-12 d-flex justify-content-between">
                                    <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
                                        <i class="fas fa-redo mr-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm filter-btn px-3"
                                        id="btn-filter">
                                        <i class="fas fa-search mr-2"></i>Search
                                    </button>
                                </div>
                            </li>
                        </form>
                    </ul>
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

        function load_dt(location, employee,from_date, to_date){
           $('#dataTable').DataTable({
                  "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Location Attendance Approve  Information',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Location Attendance Approve Information', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Location Attendance Approve  Information',
                            className: 'btn btn-primary btn-sm',
                            text: '<i class="fas fa-print mr-2"></i> Print',
                            customize: function(win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                        // 'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    "order": [
                        [1, "desc"]
                    ],
                ajax: {
                    url: scripturl + '/attendance_approve_list.php',
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
                          visible: false
                    },
                     { data: "employee_name", 
                        name: "employee_name", 
                        visible: false
                        },
                        {   data: "calling_name",
                            name: "calling_name", 
                            visible: false
                        }

                ],
            });
        }

        load_dt('', '', '', '', '');

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let location = $('#location').val();
            let employee = $('#employee_f').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            load_dt(location, employee, from_date, to_date);
             closeOffcanvasSmoothly();
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

     $('#btn-reset').on('click', function () {
                 $('#formFilter')[0].reset();
                 $('#company').val(null).trigger('change');
                 $('#department').val(null).trigger('change');
                 $('#employee_f').val(null).trigger('change');
                 $('#location').val(null).trigger('change');
             });
    
});
</script>


@endsection