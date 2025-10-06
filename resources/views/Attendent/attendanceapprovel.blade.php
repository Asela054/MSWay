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
                         <div class="page-header-icon"><i class="fa-light fa-calendar-check"></i></div>
                         <span>Attendance Approve</span>
                     </h1>
                 </div>
             </div>
         </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2 main_card">
                <div class="row">
                    <div class="col-md-12">

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
                                <div class="col-12 text-right">
                                    <button id="approve_att" class="btn btn-primary btn-sm px-3"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Approve All</button>
                                </div>
                            </div>
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap w-100" id="attendtable">
                                <thead>
                                <tr>
                                    <th>EMPLOYEE ID</th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>WORK MONTH</th>
                                    <th>DEPARTMENT</th>
                                    <th>COMPANY</th>
                                    <th>WORKING WEEK DAYS</th>
                                    <th>WORKING HOURS</th>
                                    <th>LEAVE DAYS</th>
                                    <th>NO PAY DAYS</th>
                                    {{-- <th>Last Time Stamp</th> --}}
                                    {{-- <th>Action</th> --}}
                                </tr>
                                </thead>
                                <tbody class="response"></tbody>
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
                            <li class="mb-3">
                                <div class="col-md-12">
                                   <label class="small font-weight-bolder text-dark">Company</label>
                                    <select name="company" id="company" class="form-control form-control-sm" required>
                                    </select>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="col-md-12">
                                     <label class="small font-weight-bolder text-dark">Department</label>
                                    <select name="department" id="department" class="form-control form-control-sm" required>
                                    </select>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">Month</label>
                                     <input type="month" id="month" name="month" class="form-control form-control-sm" placeholder="yyyy-mm" required>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="col-md-12">
                                   <label class="small font-weight-bolder text-dark">Close Date</label>
                                 <input type="date" id="closedate" name="closedate" class="form-control form-control-sm" required>
                                </div>
                            </li>
                            <li>
                                <div class="col-md-12 d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary btn-md filter-btn px-3"
                                        id="btn-filter">
                                        <i class="fas fa-search mr-2"></i>Search
                                    </button>
                                    <button type="button" class="btn btn-danger btn-md filter-btn px-3" id="btn-reset">
                                        <i class="fas fa-redo mr-1"></i> Reset
                                    </button>
                                </div>
                            </li>
                        </form>
                    </ul>
                </div>
            </div>


    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="AttendviewModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">View Attendence</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div id="message"></div>
                            <table id='attendTable' class="table table-striped table-bordered table-sm small">
                                <thead>

                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <div id="htmlbutton"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Area End -->
</main>
              
@endsection


@section('script')

<script>
$(document).ready(function () {

    $('#attendant_menu_link').addClass('active');
    $('#attendant_menu_link_icon').addClass('active');
    $('#attendantmaster').addClass('navbtnactive');


    let company = $('#company');
    let department = $('#department');

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

   showInitialMessage();

    function load_dt(company,department, month, closedate){
        
        $('#attendtable').DataTable({
           "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Attendance Approve Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Attendance Approve Information', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Attendance Approve  Information',
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
                [0, "desc"]
            ],
            ajax: {
                "url": "{{url('/attendance_list_for_approve')}}",
                "data": {'company':company, 'department':department, 'month':month, 'closedate':closedate},
            },

            columns: [
                { data: 'uid', name: 'at1.uid' },
                { data: 'emp_name_with_initial', name: 'employees.emp_name_with_initial' },
                { data: 'date', name: 'at1.date' },
                { data: 'dept_name', name: 'departments.name' },
                { data: 'location', name: 'branches.location' },
                { data: 'work_days', name: 'work_days' },
                { data: 'working_hours', name: 'working_hours' },
                { data: 'leave_days', name: 'leave_days' },
                { data: 'no_pay_days', name: 'no_pay_days' },
                // { data: 'uid' ,
                //     render : function ( data, type, row, meta ) {

                //         return type === 'display'  ?
                //             ' <a href="Attendentdetails/'+row['uid']+ "/"+ row['date'] +'"class="view_button btn btn-outline-dark btn-sm ml-1 "><i class="fas fa-eye"></i></a> '
                //             : data;
                //     }},
            ],
            "bDestroy": true,
            "order": [[ 0, "desc" ]],
        });
    }

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department').val();
        let company = $('#company').val();
        let month = $('#month').val();
        let closedate = $('#closedate').val();

        load_dt(company, department, month, closedate);
        closeOffcanvasSmoothly();
    });

    $(document).on('click', '#approve_att',async function (e) {
        e.preventDefault();
        let department = $('#department').val();
        let company = $('#company').val();
        let month = $('#month').val();
        let closedate = $('#closedate').val();

         var r = await Otherconfirmation("You want to Edit this ? ");

        if (r == true) {
            $('#approve_att').html('<i class="fa fa-spinner fa-spin mr-2"></i> Processing').prop('disabled', true);
            $.ajax({
                url: "AttendentAprovelBatch",
                method: "POST",
                data: {
                    department: department,
                    company: company,
                    month: month,
                    closedate: closedate,
                    _token: $('input[name=_token]').val(),
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
                            message:'Attendance Successfully  Approved',
                            url: '',
                            target: '_blank',
                            type: 'success'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        actionreload(actionJSON);
                    }
                }
            });

        }

    });
        // Offcanvas toggle functionality - UPDATED
        $('[data-toggle="offcanvas"]').on('click', function () {
            var target = $(this).data('target');
            $(target).addClass('show');
            $('body').addClass('offcanvas-open');

            // Add backdrop
            $('<div class="offcanvas-backdrop fade show"></div>').appendTo('body');
        });

        // Close offcanvas when clicking on backdrop
        $(document).on('click', '.offcanvas-backdrop', function () {
            closeOffcanvasSmoothly();
        });
        // Close offcanvas when clicking on close button
        $('[data-dismiss="offcanvas"]').on('click', function () {
            closeOffcanvasSmoothly();
        });

        $('#btn-reset').on('click', function () {
            $('#formFilter')[0].reset();
            $('#company').val(null).trigger('change');
            $('#department').val(null).trigger('change');
            $('#employee').val(null).trigger('change');
            $('#location').val(null).trigger('change');
        });
              
        $('#month').on('change', function() {
            updateClosingDateConstraints();
        });
});

$(document).on('click', '.view_button', function () {
    id = $(this).attr('uid');
    date = $(this).attr('data-date');
    emp_name_with_initial = $(this).attr('data-name');

    var formdata = {
        _token: $('input[name=_token]').val(),
        id: id,
        date: date
    };
    // alert(date);
    $('#form_result').html('');
    $.ajax({
        url: "getAttendanceApprovel",
        dataType: "json",
        data: formdata,
        success: function (data) {
            $('#AttendviewModal').modal('show');
            var htmlhead = '';
            htmlhead += '<tr><td>Emp ID :' + id + '</td><td >Name :' + emp_name_with_initial + '</td></tr>';
            htmlhead += '<tr><th>Date</th><th>Check in</th><th>Check out</th></tr>';
            var html = '';
            var htmlbutton = '';

            html += '<tr>';


            var errorcount = 0;
            for (var count = 0; count < data.length; count++) {
                html += '<tr>';
                if (data[count].firsttimestamp >= data[count].lasttimestamp) {
                    errorcount++

                    html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].date + '</td>';
                    html += '<td contenteditable class="timestamp " data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].firsttimestamp + '</td>';
                    html += '<td contenteditable class="timestamp text-danger" data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].lasttimestamp + '</td>';

                } else {
                    html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].date + '</td>';
                    html += '<td contenteditable class="timestamp " data-timestamp="' + data[count].id + '" data-id="' + data[count].id + '">' + data[count].firsttimestamp + '</td>';
                    html += '<td contenteditable class="timestamp " data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].lasttimestamp + '</td>';

                }

            }
            if (errorcount == 0) {
                htmlbutton += '<tr > <td > <button type="button" class="btn btn-success pull-left" id="approvel">Approval</button></td><tr >';
            }

            $('#attendTable thead').html(htmlhead);
            $('#attendTable tbody').html(html);
            $('#htmlbutton').html(htmlbutton);
        }
    })
});

$(document).on('click', '#approvel', function () {
    var _token = $('input[name="_token"]').val();
    var emp_id = $('#emp_id').text();

    if (emp_id != '') {
        $.ajax({
            url: "AttendentAprovel",
            method: "POST",
            data: {
                emp_id: emp_id,
                _token: _token
            },
            success: function (data) {
                $('#message').html(data);
                fetch_data();
            }
        });
    }
});

    function showInitialMessage() {
        $('.response').html(
            '<tr>' +
            '<td colspan="9" class="text-center py-5">' + // Changed colspan to 9 to match your columns
            '<div class="d-flex flex-column align-items-center">' +
            '<i class="fas fa-filter fa-3x text-muted mb-3"></i>' +
            '<h4 class="text-muted mb-2">No Records Found</h4>' +
            '<p class="text-muted">Use the filter options to get records</p>' +
            '</div>' +
            '</td>' +
            '</tr>'
        );
    }

    function closeOffcanvasSmoothly(offcanvasId = '#offcanvasRight') {
                const offcanvas = $(offcanvasId);
                const backdrop = $('.offcanvas-backdrop');

                // Add hiding class to trigger reverse animation
                offcanvas.addClass('hiding');
                backdrop.addClass('fading');

                // Remove elements after animation completes
                setTimeout(() => {
                    offcanvas.removeClass('show hiding');
                    backdrop.remove();
                    $('body').removeClass('offcanvas-open');
                }, 900); // Match this with your CSS transition duration
    }

    function updateClosingDateConstraints() {
        const monthInput = $('#month').val();
        const closeDateInput = $('#closedate');

        if (monthInput) {
            // Extract year and month from the month input
            const [year, month] = monthInput.split('-');

            // Calculate first and last day of the selected month
            const firstDay = `${year}-${month}-01`;
            const lastDay = new Date(year, month, 0).getDate(); // Last day of the month
            const lastDate = `${year}-${month}-${lastDay}`;

            // Set min and max attributes to restrict dates to the selected month
            closeDateInput.attr('min', firstDay);
            closeDateInput.attr('max', lastDate);

            // Update placeholder to show the valid date range
            closeDateInput.attr('placeholder', `${firstDay} to ${lastDate}`);

            // If current close date is outside the selected month, clear it
            const currentCloseDate = closeDateInput.val();
            if (currentCloseDate && (currentCloseDate < firstDay || currentCloseDate > lastDate)) {
                closeDateInput.val('');
            }

            // Enable the close date input
            closeDateInput.prop('disabled', false);

            // Auto-set close date to last day of month if not set
            if (!closeDateInput.val()) {
                closeDateInput.val(lastDate);
            }
        } else {
            // If no month selected, disable and clear close date
            closeDateInput.val('');
            closeDateInput.prop('disabled', true);
            closeDateInput.removeAttr('min');
            closeDateInput.removeAttr('max');
            closeDateInput.attr('placeholder', 'Select month first');
        }
    }
</script>

@endsection