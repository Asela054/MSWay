<?php $page_stitle = 'Report on Employee Attendance - Multi Offset'; ?>
@extends('layouts.app')

@section('content')

    <main>
         <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
             @include('layouts.reports_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-file-contract"></i></div>
                    <span>Attendance Report</span>
                </h1>
            </div>
        </div>
    </div>

        <div class="container-fluid  mt-2 p-0 p-2">
            <div class="card ">
                <div class="card-body p-0 p-2">
                    <div class="col-md-12">
                        <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                            data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                            Options</button><br>
                    </div>
                    <div class="col-md-12">
                        <hr class="border-dark">
                    </div>
                    <div class="response">
                    </div>
                    {{ csrf_field() }}
                </div>
            </div>
            @include('layouts.filter_menu_offcanves')
        </div>

    </main>

@endsection

@section('script')

<script>
$(document).ready(function () {

        $('#report_menu_link').addClass('active');
        $('#report_menu_link_icon').addClass('active');
        $('#employeereportmaster').addClass('navbtnactive');

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
                url: '{{url("location_list_from_attendance_sel2")}}',
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


        let today = new Date().toISOString().split('T')[0];
        $('#from_date').val(today);
        $('#to_date').val(today);
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt('', '', '', from_date, to_date);

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department').val();
        let employee = $('#employee').val();
        let location = $('#location').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt(department, employee, location, from_date, to_date);
        closeOffcanvasSmoothly();

    });


    function load_dt(department, employee, location, from_date, to_date) {
    $('.response').html('');

    $.ajax({
        url: "{{ route('get_attendance_by_employee_data') }}",
        method: "POST",
        data: {
            department: department,
            employee: employee,
            location: location,
            from_date: from_date,
            to_date: to_date,
            _token: '{{csrf_token()}}'
        },
        success: function (res) {
            let html = '';

            html += `
        <div class="row mb-2">
            <div class="col-md-4">
            <button id="export_excel" class="btn btn-sm btn-success d-none"><i class="fas fa-file-excel mr-2"></i>Export To Excel</button>
            </div>
            <div class="col-md-4">
            <label class="mr-2">
                <badge class="badge badge-pill " style="border: solid 1px black"> &nbsp; </badge> : Present
            </label>
            <label class="mr-2">
                <badge class="badge badge-pill " style="background-color: #ffeaea"> &nbsp; </badge> : Absent
            </label>
            <label class="mr-2">
                <badge class="badge badge-pill " style="background-color: rgb(247, 200, 200)"> &nbsp; </badge> : Incomplete
            </label>
            </div>
        </div>
        <table class="table table-sm table-hover" id="attendance_report_table">
            <thead>
                <tr>
                    <th>EMP ID</th>
                    <th>NAME</th>
                    <th>DEPARTMENT</th>
                    <th>DATE</th>
                    <th>DATE TYPE</th>
                    <th>CHECK IN</th>
                    <th>CHECK OUT</th>
                    <th>WORK HOURS</th>
                    <th>LOCATION</th>
                </tr>
            </thead>
            <tbody>
        `;

            // Function to convert 24-hour format to 12-hour format
            function convertTo12HourFormat(time) {
                if (!time || time === '-') return time;
                const [hour, minute] = time.split(':');
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const formattedHour = hour % 12 || 12;
                return `${formattedHour}:${minute} ${ampm}`;
            }

            res.data.forEach(function (datalist) {
                datalist.attendanceinfo.forEach(function (emp_data) {
                    let tr = '<tr>';
                    if (emp_data.workhours === '00:00:00') {
                        tr = '<tr style="background-color: rgb(247, 200, 200)">';
                    } else if (emp_data.workhours === '-') {
                        tr = '<tr style="background-color: #ffeaea">';
                    }

                    const checkInTime = convertTo12HourFormat(emp_data.timestamp);
                    const checkOutTime = convertTo12HourFormat(emp_data.lasttimestamp);

                    html += tr;
                    html += `<td>${emp_data.emp_id}</td>`;
                    html += `<td>${emp_data.emp_name_with_initial} - ${emp_data.calling_name}</td>`;
                    html += `<td>${emp_data.dept_name}</td>`;
                    html += `<td>${emp_data.date}</td>`;
                    html += `<td>${emp_data.day_type}</td>`;
                    html += `<td>${checkInTime}</td>`;
                    html += `<td>${checkOutTime}</td>`;
                    html += `<td>${emp_data.workhours}</td>`;
                    html += `<td>${emp_data.location}</td>`;
                    html += '</tr>';
                });
            });
            html += `
            </tbody>
        </table>
        `;

            $('.response').html(html);

            // Check if DataTable already exists and destroy it first
            if ($.fn.DataTable.isDataTable('#attendance_report_table')) {
                $('#attendance_report_table').DataTable().destroy();
            }

            // Initialize DataTable with client-side processing
            $('#attendance_report_table').DataTable({
                "processing": false, 
                "serverSide": false, 
                "searching": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + 
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [
                    {
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Attendance Reports',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'Attendance Reports', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'landscape', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Attendance Reports',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-print mr-2"></i> Print',
                        customize: function(win) {
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        },
                    },
                ],
                "order": [[0, "asc"]]
            });
        }
    });
}
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

@endsection

