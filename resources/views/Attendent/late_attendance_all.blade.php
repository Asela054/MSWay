<?php $page_stitle = 'Report on Employee Attendance - Multi Offset'; ?>
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
                         <span>Approved Late Attendance</span>
                     </h1>
                 </div>
             </div>
         </div>

        <div class="container-fluid mt-2  p-0 p-2">
            <div class="card mb-2">
                <div class="card-body  p-0 p-2">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Location</label>
                                <select name="location" id="location" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm"> </span>
                                    </div>
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <br>
                                <div class="d-flex flex-wrap justify-content-end mb-2">
                                    <button type="submit" class="btn btn-primary btn-sm filter-btn float-right ml-2"
                                        id="btn-filter"><i class="fas fa-search mr-2"></i>Filter</button>&nbsp;
                                    <button type="button" class="btn btn-danger btn-sm filter-btn float-right"
                                        id="btn-clear"><i class="far fa-trash-alt"></i>&nbsp;&nbsp;Clear</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="center-block fix-width scroll-inner">
                    <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="attendtable">
                        <thead>
                        <tr>
                            <th>EMP ID</th>
                            <th>EMPLOYEE NAME</th>
                            <th>DATE</th>
                            <th>CHECK IN TIME</th>
                            <th>CHECK OUT TIME</th>
                            <th>WORKING HOURS</th>
                            <th>LOCATION</th>
                            <th>DEPARTMENT</th>
                            <th>ACTION</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

    </main>

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#attendantmaster').addClass('navbtnactive');

             var canDeletelateattendance = false;
            @can('late-attendance-delete')
                canDeletelateattendance = true;
            @endcan

            let late_id = 0;

            let company = $('#company');
            let department = $('#department');
            let employee = $('#employee');
            let location = $('#location');

            company.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("company_list_sel2")}}',
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

            department.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("department_list_sel2")}}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company.val()
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
                    url: '{{url("employee_list_from_attendance_sel2")}}',
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

            location.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("location_list_from_attendance_sel2")}}',
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



            load_dt('','','','','','');

            function load_dt(department, company, location, employee, from_date, to_date) {

                $('#attendtable').DataTable({
                     "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Late Attendance  Information',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Late Attendance Information', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Late Attendance   Information',
                            className: 'btn btn-primary btn-sm',
                            text: '<i class="fas fa-print mr-2"></i> Print',
                            customize: function(win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                    ],
                    ajax: {
                         url: scripturl +"/late_attendance_list.php", 
                          type: "POST",
                           data : function(d) {
                                d.department = $('#department').val();
                                d.employee = $('#employee').val();
                                d.location = $('#location').val();
                                d.from_date = $('#from_date').val();
                                d.to_date = $('#to_date').val();
                            }
                    },
                    columns: [
                        { data: 'emp_id', name: 'emp_id' },
                        { data: 'employee_display', name: 'employee_display' },
                        { data: 'date', name: 'date' },
                        { data: 'check_in_time', name: 'check_in_time' },
                        { data: 'check_out_time', name: 'check_out_time' },
                        { data: 'working_hours', name: 'working_hours' },
                        { data: 'location', name: 'location' },
                        { data: 'dep_name', name: 'dep_name' },
                         {
                            "data": "id",
                            "name": "action",
                            "className": 'text-right',
                            "orderable": false,
                            "searchable": false,
                            "render": function(data, type, full) {
                                var id = full['id'];
                                var button = '';

                                if (canDeletelateattendance) {
                                    button += '<button type="button"  name="delete_button" title="Delete" data-id="'  + id +'" class="view_button btn btn-danger btn-sm delete_button" data-toggle="tooltip" title="Remove">'+
                                       '<i class="fas fa-trash-alt" ></i></button>';
                                }

                                return button;
                            }
                        }
                    ],
                    "bDestroy": true,
                    "order": [
                        [2, "desc"]
                    ]
                });

            }

            $('#from_date').on('change', function() {
                let fromDate = $(this).val();
                $('#to_date').attr('min', fromDate); 
            });

            $('#to_date').on('change', function() {
                let toDate = $(this).val();
                $('#from_date').attr('max', toDate); 
            });

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let company = $('#company').val();
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                load_dt(department, company, location, employee, from_date, to_date);
            });

            document.getElementById('btn-clear').addEventListener('click', function() {
            document.getElementById('formFilter').reset();

                $('#company').val('').trigger('change');   
                $('#location').val('').trigger('change');
                $('#department').val('').trigger('change');
                $('#from_date').val('');                     
                $('#to_date').val('');                       

                // load_dt('', '', '', '', '');
            });

            $(document).on('click', '.delete_button', async function () {
                var r = await Otherconfirmation("You want to remove this ? ");
                if (r == true) {
                    late_id = $(this).data('id');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    $.ajax({
                        url: '{!! route("late_attendancedestroy") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {
                            id: late_id
                        },
                        beforeSend: function () {
                            $('#ok_button').text('Deleting...');
                        },
                        success: function (data) {
                             const actionObj = {
                                icon: 'fas fa-trash-alt',
                                title: '',
                                message: 'Record Remove Successfully',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            actionreload(actionJSON);
                        }
                    })
                }
            });


        });
    </script>

@endsection

