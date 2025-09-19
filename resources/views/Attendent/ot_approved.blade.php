<?php $page_stitle = 'Report on Employee O.T. Hours - Multi Offset'; ?>
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
                         <span>Approved OT</span>
                     </h1>
                 </div>
             </div>
         </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Location</label>
                                <select name="location" id="location" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee" class="form-control form-control-sm">
                                </select>
                            </div>

                            <div class="col-md-3 mt-3 div_date_range">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd"
                                    >
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            <div class="col mt-3">
                                <br>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn float-right ml-2" id="btn-filter"><i class="fas fa-search mr-2"></i>Filter</button>
                                <button type="button" class="btn btn-danger btn-sm filter-btn float-right" id="btn-clear"><i class="far fa-trash-alt"></i>&nbsp;&nbsp;Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2">
                     <div class="col-12">
                         <div class="center-block fix-width scroll-inner">

                             <div class="daily_table">
                                 <table class="table table-striped table-bordered table-sm small nowrap w-100" id="ot_report_dt">
                                     <thead>
                                         <tr id="dt_head">
                                         </tr>
                                     </thead>
                                     <tbody>
                                     </tbody>
                                 </table>
                             </div>
                             <div class="month_table">
                                 <table class="table table-striped table-bordered table-sm small nowrap w-100"
                                     id="ot_report_monthly_dt">
                                     <thead>
                                         <tr id="dt_head_month">
                                         </tr>
                                     </thead>
                                     <tbody>
                                     </tbody>
                                 </table>
                             </div>

                         </div>
                     </div>
                    {{ csrf_field() }}
                </div>
            </div>
        </div>

    </main>

    <div class="modal fade" id="view_more_modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">OT Breakdown</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="viewRes"></div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Area End -->

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#attendantmaster').addClass('navbtnactive');

            let company = $('#company');
            let department = $('#department');
            let employee = $('#employee');
            let location = $('#location');
            let type = $('#type');

            var canDeleteEmployee = false;
            @can('ot-delete')
                canDeleteEmployee = true;
            @endcan

            company.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("company_list_sel2")}}',
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

            department.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("department_list_sel2")}}',
                    dataType: 'json',
                    data: function(params) {
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
                    url: '{{url("employee_list_sel2")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company.val(),
                            department: department.val()
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
                    url: '{{url("location_list_sel2")}}',
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

            type.on('change',function(e) {
                let type_val = $(this).val();
                if(type_val == 'Daily'){
                    $('.div_month').css('display','none');
                    $('.div_date_range').css('display','block');
                    $('#month').val('');
                }else{
                    $('.div_month').css('display','block');
                    $('.div_date_range').css('display','none');
                    $('#from_date').val('');
                    $('#to_date').val('');
                }
            });
            $('.div_month').css('display','none');

           let temp_department = '';
            let temp_employee = '';
            let temp_location = '';
            let temp_from_date = '';
            let temp_to_date = '';
            let temp_type = 'Daily';
            let temp_month = '';
            load_dt(temp_department,temp_employee,temp_location,temp_from_date,temp_to_date,temp_type,temp_month);
            
            function load_dt(department, employee, location, from_date, to_date, type = 'Daily', month){

                if(type == 'Daily'){

                    $('.month_table').css('display','none');
                    $('.daily_table').css('display','block');

                    $('#dt_head').html('<th>ETF NO</th> ' +
                        '<th>EMP NAME</th>' +
                        '<th>DATE</th> ' +
                        '<th>FROM</th> ' +
                        '<th>TO</th>' +
                        '<th>OT TIME</th>' +
                        '<th>D/OT TIME</th> ' +
                        '<th>T/OT TIME</th> ' +
                        '<th>IS HOLIDAY</th> ' +
                        '<th>LOCATION</th> ' +
                        '<th>DEPARTMENT</th> ' +
                        '<th>ACTION</th> ' );

                    $('#ot_report_dt').DataTable({
                        "columnDefs": [
                            {
                                "targets": -3,
                                "orderable": false
                            }
                        ],
                        // "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
                        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                        "buttons": [{
                                extend: 'csv',
                                className: 'btn btn-success btn-sm',
                                title: 'Approved OT  Information',
                                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                            },
                            { 
                                extend: 'pdf', 
                                className: 'btn btn-danger btn-sm', 
                                title: 'Approved OT Information', 
                                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                orientation: 'landscape', 
                                pageSize: 'legal', 
                                customize: function(doc) {
                                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Approved OT  Information',
                                className: 'btn btn-primary btn-sm',
                                text: '<i class="fas fa-print mr-2"></i> Print',
                                customize: function(win) {
                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');
                                },
                            },
                        ],
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: scripturl + '/ot_approved_list.php',
                            type: "POST",
                            data: {
                                department:department,
                                employee:employee,
                                location: location,
                                from_date: from_date,
                                to_date: to_date,
                                type: type,
                                month: month
                            }
                        },

                        columns: [
                            { data: 'emp_id' },
                            { data: 'employee_display' },
                            { data: 'date' },
                            { data: 'from' ,
                                render: function(data, type, row) {
                                    var date = new Date(data);
                                    var year = date.getFullYear();
                                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                                    var day = ('0' + date.getDate()).slice(-2);
                                    var hours = ('0' + date.getHours()).slice(-2);
                                    var minutes = ('0' + date.getMinutes()).slice(-2);
                                    return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
                                }
                            },
                            { data: 'to' ,
                                render: function(data, type, row) {
                                    var date = new Date(data);
                                    var year = date.getFullYear();
                                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                                    var day = ('0' + date.getDate()).slice(-2);
                                    var hours = ('0' + date.getHours()).slice(-2);
                                    var minutes = ('0' + date.getMinutes()).slice(-2);
                                    return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
                                }
                            },
                            {
                                data: 'hours',
                                render: function(data, type, row) {
                                    return parseFloat(data).toFixed(1);
                                }
                            },
                            { data: 'double_hours' ,
                                render: function(data, type, row) {
                                        return parseFloat(data).toFixed(1);
                                }

                            },
                            { data: 'triple_hours' ,
                                render: function(data, type, row) {
                                        return parseFloat(data).toFixed(1);
                                }
                            },
                            { data: 'is_holiday' ,
                                render: function(data, type, row) {
                                    if(data==1){
                                        return "Yes";
                                    }
                                    else{
                                        return "No";
                                    }
                                }
                            },
                            { data: 'b_location' },
                            { data: 'dept_name' },
                            // {data: 'action'}
                            {
                                "targets": -1,
                                "className": 'text-right',
                                "data": null,
                                "render": function (data, type, full) {

                                    var button = '';

                                    if (canDeleteEmployee) {
                                        button += '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' + full['id'] + '" data-original-title="Delete" class="delete_btn btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i> </a>';
                                    }

                                    return button;
                                }
                            }
                        ],
                        "bDestroy": true,
                        "order": [[ 2, "desc" ]],
                    });
                }
                else if(type == 'Monthly'){

                    $('.month_table').css('display','block');
                    $('.daily_table').css('display','none');

                    $('#dt_head_month').html('<th>Emp ID</th> ' +
                        '<th>Emp Name</th>' +
                        '<th>Month</th> ' +
                        '<th>Work Days</th> ' +
                        '<th>Leave Days</th>' +
                        '<th>No Pay Days</th>' +
                        '<th>O.T. Hours</th> ' +
                        '<th>Double O.T. Hours</th> ' +
                        '<th>Location</th> ' +
                        '<th>Department</th> ' );

                    $('#ot_report_monthly_dt').DataTable({
                        "columnDefs": [
                            {
                                "targets": -3,
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
                            "url": "{{url('/ot_report_list_month')}}",
                            "data": {
                                'department':department,
                                'employee':employee,
                                'location': location,
                                'from_date': from_date,
                                'to_date': to_date,
                                'type': type,
                                'month': month
                            }
                        },

                        columns: [
                            { data: 'emp_id' },
                            { data: 'emp_name_with_initial' },
                            { data: 'month' },
                            { data: 'work_days' },
                            { data: 'leave_days' },
                            { data: 'no_pay_days' },
                            { data: 'normal_rate_otwork_hrs' },
                            { data: 'double_rate_otwork_hrs' },
                            { data: 'b_location' },
                            { data: 'dept_name' }
                        ],
                        "bDestroy": true,
                        "order": [[ 2, "desc" ]],
                    });
                }


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
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                let type = $('#type').val();
                let month = $('#month').val();

                if (type == 'Monthly'){
                    if (month == ''){
                        alert('Please select month');
                        return false;
                    }
                }

                e.preventDefault();

                load_dt(department, employee, location, from_date, to_date, type, month);
            });

            document.getElementById('btn-clear').addEventListener('click', function() {
            document.getElementById('formFilter').reset();

                $('#company').val('').trigger('change');   
                $('#location').val('').trigger('change');
                $('#department').val('').trigger('change');
                $('#employee').val('').trigger('change');
                $('#from_date').val('');                     
                $('#to_date').val('');                       

                // load_dt('', '', '', '', '');
            });

            $(document).on('click','.delete_btn',async function(e){

                e.preventDefault();
                let id = $(this).data('id');
                let btn = $(this);
                var r = await Otherconfirmation("You want to remove this ? ");

                if(r == true){

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{url('/ot_approved_delete')}}",
                        type: "POST",
                        data: {
                            'id': id
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

                    });

                }
            });

        });
    </script>

@endsection

