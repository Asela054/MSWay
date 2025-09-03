<?php $page_stitle = 'Report on Employee Attendance - Multi Offset'; ?>
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.reports_nav_bar')
               
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
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
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd"
                                    value="{{date('Y-m-d') }}"
                                           required
                                    >
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd"
                                    value="{{date('Y-m-d') }}"
                                           required
                                    >
                                </div>
                            </div>
                            <div class="col">
                                <br>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"> Filter</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="info_msg">
                        <div class="alert alert-info" role="alert">
                            <span><i class="fa fa-info-circle"></i>  Records for {{date('Y-m-d')}} showing by default </span>
                        </div>
                    </div>
                     <div class="response">
                     </div>
                    {{ csrf_field() }}
                </div>
            </div>
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

        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt('', '', '', from_date, to_date);

        // function load_dt(department, employee, location, from_date, to_date) {

        //     $('.response').html('');

        //     let element = $('.filter-btn');
        //     element.attr('disabled', true);
        //     element.html('<i class="fa fa-spinner fa-spin"></i>');

        //     //add loading to element button
        //     $(element).val('<i class="fa fa-spinner fa-spin"></i>');
        //     //disable
        //     $(element).prop('disabled', true);

        //     $.ajax({
        //         url: "{{ route('get_attendance_by_employee_data') }}",
        //         method: "POST",
        //         data: {
        //             department: department,
        //             employee: employee,
        //             location: location,
        //             from_date: from_date,
        //             to_date: to_date,
        //             _token: '{{csrf_token()}}'
        //         },
        //         success: function (res) {

        //             element.html('Filter');
        //             element.prop('disabled', false);

        //             $('.response').html(res);


        //         }
        //     });

        // }

        $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department').val();
        let employee = $('#employee').val();
        let location = $('#location').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        $('.info_msg').html('');
        load_dt(department, employee, location, from_date, to_date);
    });

    function load_dt(department, employee, location, from_date, to_date) {
        $('.response').html('');

        let element = $('.filter-btn');
        element.attr('disabled', true);
        element.html('<i class="fa fa-spinner fa-spin"></i>');

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
                element.html('Filter');
                element.prop('disabled', false);

                let html = '';

                html += `
            <div class="row mb-2">
                <div class="col-md-4">
                <button id="export_excel" class="btn btn-sm btn-success"><i class="fas fa-file-excel mr-2"></i>Export To Excel</button>
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
                        <th>ETF No</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Date Type</th>
                        <th>Check In Time</th>
                        <th>Check Out Time</th>
                        <th>Work Hours</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
            `;

                let currentDepartment = null;

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
                        if (currentDepartment !== emp_data.dept_name) {
                            currentDepartment = emp_data.dept_name;
                            html += `<tr class="font-weight-bold"><td colspan="9" class="font-weight-bold">${currentDepartment}</td></tr>`;
                        }

                        let tr = '<tr>';
                        if (emp_data.workhours === '00:00:00') {
                            tr = '<tr style="background-color: rgb(247, 200, 200)">';
                        } else if (emp_data.workhours === '-') {
                            tr = '<tr style="background-color: #ffeaea">';
                        }

                        const checkInTime = convertTo12HourFormat(emp_data.timestamp);
                        const checkOutTime = convertTo12HourFormat(emp_data.lasttimestamp);

                        html += tr;
                        html += `<td>${emp_data.emp_etfno}</td>`;
                        html += `<td>${emp_data.emp_name_with_initial}</td>`;
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
            }
        });

        // Use event delegation for the export button
        $(document).on('click', '#export_excel', function () {
            let wb = XLSX.utils.book_new();
            let ws_data = [];

            let tableRows = $('#attendance_report_table').find('tbody tr');
            let headers = ['ETF No', 'Name', 'Department', 'Date', 'Date Type', 'Check In Time', 'Check Out Time', 'Work Hours', 'Location'];
            ws_data.push(headers);

            tableRows.each(function () {
                let row = [];
                $(this).find('td').each(function () {
                    row.push($(this).text());
                });
                ws_data.push(row);
            });

            let ws = XLSX.utils.aoa_to_sheet(ws_data);
            XLSX.utils.book_append_sheet(wb, ws, "Attendance");
            XLSX.writeFile(wb, "Attendance_Report.xlsx");
        });
    }
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

@endsection

