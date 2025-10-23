@extends('layouts.app')

@section('content')

<main> 
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.employee_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-users-gear"></i></div>
                    <span>Training Summary</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
    <div class="card">
        <div class="card-body p-2">
            <div class="row">
                <div class="col-12">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-12">
                            <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                data-toggle="offcanvas" data-target="#offcanvasRight"
                                aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                Records</button>
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>EMP ID</th>
                                        <th>EMPLOYEE</th>
                                        <th>TRAINING TYPE</th>
                                        <th>VENUE</th>
                                        <th>START TIME</th>
                                        <th>END TIME</th>
                                        <th>MARKS</th>
                                        <th>STATUS</th>
                                        <th style="display:none;">EMP NAME</th>
                                        <th style="display:none;">CALLING NAME</th>
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

    {{-- offcanvas menu --}}
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
                            <label class="small font-weight-bolder text-dark">Training Type</label>
                            <select name="type" id="type" class="form-control form-control-sm">
                            </select>
                        </div>
                    </li>
                    <li class="mb-2">
                        <div class="col-md-12">
                            <label class="small font-weight-bolder text-dark">Venue</label>
                            <select name="venue" id="venue" class="form-control form-control-sm">
                            </select>
                        </div>
                    </li>
                    <!-- <li class="mb-2">
                        <div class="col-md-12">
                            <label class="small font-weight-bolder text-dark">Department</label>
                            <select name="department" id="department" class="form-control form-control-sm">
                            </select>
                        </div>
                    </li> -->
                    <li class="mb-2">
                        <div class="col-md-12">
                            <label class="small font-weight-bolder text-dark">Employee</label>
                            <select name="employee" id="employee" class="form-control form-control-sm">
                            </select>
                        </div>
                    </li>
                    <li class="mb-2">
                        <div class="col-md-12">
                            <label class="small font-weight-bolder text-dark">From Date </label>

                            <input type="date" id="from_date" name="from_date"
                                class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                        </div>
                    </li>
                    <li class="mb-2">
                        <div class="col-md-12">
                            <label class="small font-weight-bolder text-dark">To Date</label>
                            <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                placeholder="yyyy-mm-dd">
                        </div>
                    </li>
                    <li class="mb-2">
                        <div class="col-md-12">
                            <label class="small font-weight-bolder text-dark">Status</label>
                            <select name="status" id="status" class="form-control form-control-sm">
                                <option value="">Select...</option>
                                <option value="attended">Attended</option>
                                <option value="not_attended">Not Attended</option>
                            </select>
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
</main>
              
@endsection

@section('script')
<script>
$(document).ready(function(){
    $('#employee_menu_link').addClass('active');
    $('#employee_menu_link_icon').addClass('active');
    $('#training').addClass('navbtnactive');

    let type = $('#type');
    let venue = $('#venue');
    let employee = $('#employee');

    type.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("trainType_list_sel2")}}',
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            cache: true
        }
    });

    venue.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("trainVenue_list_sel2")}}',
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    type: type.val()
                }
            },
            cache: true
        }
    });

    employee.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("trainEmp_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    venue: venue.val()
                }
            },
            cache: true
        }
    });

    load_dt('', '', '', '', '', '');

    function load_dt(type, venue, employee, from_date, to_date, status) {
        $('#dataTable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,  
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + 
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [
                {
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Training Summary Details',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Training Summary Details', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    },
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Training Summary Details',
                    className: 'btn btn-primary btn-sm',
                    text: '<i class="fas fa-print mr-2"></i> Print',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    },
                    customize: function(win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    },
                },
            ],
            "order": [[0, "desc"]],
            ajax: {
                "url": scripturl + "/training_Summery.php",
                "type": "POST",
                "data": function(d) {
                    d.type = type;
                    d.venue = venue;
                    d.employee = employee;
                    d.from_date = from_date;
                    d.to_date = to_date;
                    d.status = status;
                }
            },
            columns: [
                { data: 0, name: 'id' },
                { data: 1, name: 'emp_id' },
                { data: 2, name: 'employee_display' },
                { data: 3, name: 'training_type' },
                { data: 4, name: 'venue' },
                { data: 5, name: 'start_time' },
                { data: 6, name: 'end_time' },
                { data: 7, name: 'marks' },
                { data: 8, name: 'status' },
                { data: 9, name: "emp_name", visible: false },
                { data: 10, name: "calling_name", visible: false }
            ],
        });
    }

    // Filter form submission
    $('#formFilter').on('submit', function(e) {
        e.preventDefault();
        
        let typeVal = type.val();
        let venueVal = venue.val();
        let employeeVal = employee.val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        let status = $('#status').val();
        
        load_dt(typeVal, venueVal, employeeVal, from_date, to_date, status);
    });

    // Reset button
    $('#btn-reset').on('click', function() {
        $('#formFilter')[0].reset();
        type.val(null).trigger('change');
        venue.val(null).trigger('change');
        employee.val(null).trigger('change');
        load_dt('', '', '', '', '', '');
    });
});
</script>

@endsection