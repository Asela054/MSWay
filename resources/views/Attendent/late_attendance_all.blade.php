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
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="col-md-12">
                        <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                            data-toggle="offcanvas" data-target="#offcanvasRight"
                            aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                            Options</button>
                    </div><br><br>

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
            @include('layouts.filter_menu_offcanves')
        </div>

    </main>

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#attendantmaster').addClass('navbtnactive');


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

                                    button += '<button type="button"  name="delete_button" title="Delete" data-id="'  + id +'" class="view_button btn btn-danger btn-sm delete_button" data-toggle="tooltip" title="Remove">'+
                                       '<i class="fas fa-trash-alt" ></i></button>';

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
                 closeOffcanvasSmoothly();
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

