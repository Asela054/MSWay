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
        <div class="card mb-2">
            <div class="card-body">
                <form class="form-horizontal" id="formFilter">
                    <div class="form-row mb-1">
                        <div class="col-md-3">
                            <label class="small font-weight-bold text-dark">Company</label>
                            <select name="company" id="company" class="form-control form-control-sm" required>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department" class="form-control form-control-sm" required>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Month</label>
                            <input type="month" id="month" name="month" class="form-control form-control-sm" placeholder="yyyy-mm" required>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Close Date</label>
                            <input type="date" id="closedate" name="closedate" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-2">
                            <br>
                            <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"><i class="fas fa-search mr-2"></i>Filter</button>
                            <button type="button" class="btn btn-danger btn-sm filter-btn" id="btn-clear"><i class="far fa-trash-alt"></i>&nbsp;&nbsp;Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0 p-2 main_card">
                <div class="row">
                    <div class="col-12">
                        <div class="message"></div>
                        <div class="d-flex justify-content-end mb-2">
                            <button id="approve_att" class="btn btn-primary btn-sm"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Approve All</button>
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
                                
                            </table>
                        </div>
                    </div>
                </div>
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

    // $('.table_outer').css('display', 'none');
    // $('#approve_att').css('display', 'none');



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

    //load_dt('');
    function load_dt(company,department, month, closedate){

        $('.alert').remove();
        $('.table_outer').css('display', 'block');
        $('#approve_att').css('display', 'block');

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
    });

    document.getElementById('btn-clear').addEventListener('click', function() {
    document.getElementById('formFilter').reset();

                $('#company').val('').trigger('change');   
                $('#department').val('').trigger('change');
                $('#month').val('');
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

        }

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
    } else {
        $('#message').html("<div class='alert alert-danger'>Both Fields are required</div>");
    }
});
</script>

@endsection