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
                    <span>Attendance Sync</span>
                </h1>
            </div>
        </div>
    </div>        
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div id="message"></div>
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <form class="form" method="POST">
                            {{ csrf_field() }}
                            <div class="form-row mb-1">
                                <div class="col-sm-4 col-md-2">
                                    <label class="small font-weight-bolder">Location*</label>
                                    <select name="device" id="device" class="form-control form-control-sm" required>
                                        <option value="">Location</option>
                                        @foreach($device as $devices)
                                        <option data-fname="{{$devices->name}}" value="{{$devices->ip}}">{{$devices->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-8 col-md-8">
                                     <label class="small font-weight-bold text-dark">&nbsp;</label><br>
                                    <button type="button" name="getdata" id="getdata" class="btn btn-primary btn-sm getdata px-3"><i class="fas fa-search mr-2"></i>Getdata</button>
                                    
                                        {{-- <a href="#" id="clear_data" class="btn btn-danger btn-sm pl-2 "><i class="fas fa-trash mr-2"></i>Clear Data</a> --}}
                                  
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                                <div class="col-md-12">
                                    <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                        data-toggle="offcanvas" data-target="#offcanvasRight"
                                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                        Options</button>
                                </div><br><br>

                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap w-100" id="attendtable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>EMPLOYEE ID</th>
                                    <th>DATE</th>
                                    <th>NAME</th>
                                    <th>CHECK IN</th>
                                    <th>CHECK OUT</th>
                                    <th>LOCATION</th>
                                    <th>DEPARTMENT</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      @include('layouts.filter_menu_offcanves')

    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="AttendviewModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
       aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Attendant Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div id="message"></div>
                            <table id='attendTable' class="table table-bordered table-hover" width="100%"
                                cellspacing="0">
                                <thead>
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

    <div class="modal fade" id="getdataModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
       aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                     <h5 class="modal-title" id="staticBackdropLabel">If you need to download data, please confirm?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row1">
                         <div class="form-row mb-1">
                            @if($companytype == 0)
                                <div class="col-sm-12 col-md-6">
                                    <label class="small font-weight-bold text-dark">From Date*</label>
                                    <input required type="date" id="date_from" name="date_from"
                                        class="form-control form-control-sm" value="{{Date('Y-m-d')}}"/>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <label class="small font-weight-bold text-dark">To Date*</label>
                                    <input required type="date" id="date_to" name="date_to"
                                        class="form-control form-control-sm" value="{{Date('Y-m-d')}}" />
                                </div>
                            @else
                                <div class="col-sm-12 col-md-12">
                                    <label class="small font-weight-bold text-dark">Date*</label>
                                    <input required type="date" id="sync_date" name="sync_date"
                                        class="form-control form-control-sm" value="{{Date('Y-m-d')}}" />
                                </div>
                            @endif
                           <input type="hidden" name="companytype" id="companytype" value="<?php echo $companytype; ?>" />
                         </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="comfirm_button" id="comfirm_button" class="btn btn-primary px-3 btn-sm">Confirm</button>
                    <button type="button" class="btn btn-danger px-3 btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Area End -->			


</main>
              
@endsection


@section('script')

<script>

$(document).ready(function() {

    $('#attendant_menu_link').addClass('active');
    $('#attendant_menu_link_icon').addClass('active');
    $('#attendantmaster').addClass('navbtnactive');

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
               url: '{{url("employee_list_sel2")}}',
               dataType: 'json',
               data: function (params) {
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
               data: function (params) {
                   return {
                       term: params.term || '',
                       page: params.page || 1
                   }
               },
               cache: true
           }
       });


     function load_dt(department, employee, location, from_date, to_date,company){
        $('#attendtable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Attendance Sync Details',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Attendance Sync Details', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Attendance Sync Details',
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

                url: scripturl + '/attendace_sync_list.php',
                        type: 'POST',
                        data : 
                        {department :department, 
                        company :company,
                        employee :employee, 
                        location : location,
                        from_date: from_date,
                        to_date: to_date},
            },
            columns: [
                { data: 'at_id' },
                { data: 'uid' },
                { data: 'date'},
                { data: 'employee_display' },
                { data: 'firsttimestamp' ,
                    render : function ( data, type, row, meta ) {
                        if(row['btn_in']){
                            return type === 'display'  ?
                                ' <button class="btn btn-outline-default btn-sm edit_button text-primary" ' +
                                'uid="'+row['uid'] +'" ' +
                                'data-date="'+row['date_row'] +'" ' +
                                'data-type="in time" ' +
                                'data-name="'+row['emp_name_with_initial'] +'" '  +
                                '>In Time</button> '
                                : data;
                        }else{
                            return type === 'display'  ?
                                row['firsttimestamp']:data;
                        }
                    }
                },
                { data: 'lasttimestamp' ,
                    render : function ( data, type, row, meta ) {
                            return type === 'display'  ?
                                ' <button class="btn btn-outline-default btn-sm edit_button text-primary" ' +
                                'uid="'+row['uid'] +'" ' +
                                'data-date="'+row['date_row'] +'" ' +
                                'data-type="out time" ' +
                                'data-name="'+row['emp_name_with_initial'] +'" '  +
                                '>Out Time</button> '
                                : data;
                    }
                },
                { data: 'location' },
                { data: 'dep_name' },
                { data: 'uid',
                  name: 'action',
                  className: 'text-right',
                  orderable: false,
                   searchable: false,
                    render : function ( data, type, row, meta ) {

                        
                            return type === 'display'  ?
                                ' <button class="btn btn-danger btn-sm delete_button" ' +
                                'data-uid="'+row['uid'] +'" ' +
                                'data-date="'+row['date_row'] +'" ' +
                                'data-type="delete" ' +
                                ' data-toggle="tooltip" title="Remove" name="delete"> <i class="far fa-trash-alt"> </i> </button> '
                                : data;
                    }
                }
            ],
            "bDestroy": true,
            "order": [
                [2, "desc"]
            ], drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
        });
     }

    load_dt('', '', '', '', '');

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


    $("#form_datetime").datetimepicker({
        format: "yyyy-mm-dd hh:ii"
    });

    $('#create_record').click(function () {
        $('.modal-title').text('Add New Attendance');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');

        $('#formModaladd').modal('show');
    });
    $('#formModaladd #uid').change(function () {
        var id = $(this).val();
        // alert(id);
        $('#formModaladd #id').val(id);
    });

    $(document).on('click', '.edit_button', async function () {
          var r = await Otherconfirmation("You want to Edit this ? ");
        if (r == true) {

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
                url: "AttendentUpdate",
                dataType: "json",
                data: formdata,
                success: function (data) {
                    $('.modal-title').text('Add Attendent');
                    $('#AttendviewModal').modal('show');
                    var htmlhead = '';
                    htmlhead += '<tr><td>Emp ID :' + id + '</td><td >Name :' + emp_name_with_initial + '</td><td></td></tr>';
                    htmlhead += '<tr><th></th><th>Timestamp</th><th>Action</th>';
                    var html = '';

                    html += '<tr>';
                    html += '<td id="aduserid"> <span style="display: none;">' + id + '</span></td>';
                    //html += '<td ><input size="16" id="formdate" type="date" ><input size="16" id="formtime" type="time" ></td>';
                    html += '<td ><input type="text" id="form_date_time"/> </td>';
                    html += '<td><button type="button" class="btn btn-success btn-xs" id="add">Add</button></td></tr>';
                    for (var count = 0; count < data.length; count++) {
                        html += '<tr>';
                        const timestamp = new Date(data[count].timestamp);
                        const date = data[count].date;
                        const begining_checkout = data[count].begining_checkout;
                        const ending_checkin = data[count].ending_checkin;
                        const checkdate = date.slice(0, -8)

                        var checkbegining_checkout = checkdate + begining_checkout + ':00';
                        var checkending_checkin = checkdate + ending_checkin + ':00';

                        var setbegining_checkout = new Date(checkbegining_checkout).getTime();
                        var setcheckending_checkin = new Date(checkending_checkin).getTime();
                        var settimestamp = timestamp.getTime();

                        html += '<tr>';
                        if (settimestamp < setbegining_checkout) {
                            html += '<td> Checkin</td>';
                        }
                        if (settimestamp > setbegining_checkout) {
                            html += '<td> Checkout</td>';
                        }

                        html += '<td  class="timestamp" data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].timestamp + '</td>';

                    }
                    $('#attendTable thead').html(htmlhead);
                    $('#attendTable tbody').html(html);

                    $('#form_date_time').datetimepicker({
                        format:'Y-m-d H:i',
                        mask:true,
                    });

                }
            })
        }
    });

    $(document).on('click', '#add', function () {
        var _token = $('input[name="_token"]').val();
        var userid = $('#aduserid').text();
        var timestamp = $('#form_date_time').val();

        if (timestamp != '') {
            $.ajax({
                url: "AttendentInsertLive",
                method: "POST",
                data: {
                    userid: userid,
                    timestamp: timestamp,
                    _token: _token
                },
                success: function (data) {
                    $('#message').html(data);
                    $('#AttendviewModal').modal('hide');

                    load_dt();
                    
                }
            });
        } else {
            $('#message').html("<div class='alert alert-danger'>Please Select Date and Time</div>");
        }
    });

    $(document).on('blur', '.timestamp', function () {
        var _token = $('input[name="_token"]').val();
        var timestamp = $(this).data("timestamp");
        var timestamp = $(this).text();
        var id = $(this).data("id");

        if (timestamp != '') {


            $.ajax({
                url: "AttendentUpdateLive",
                method: "POST",
                data: {
                    id: id,
                    timestamp: timestamp,
                    _token: _token
                },
                success: function (data) {
                    $('#message').html(data);
                    $('#AttendviewModal').modal('hide');
                    location.reload();
                }
            })
        } else {
            $('#message').html("<div class='alert alert-danger'>Enter some value</div>");
        }
    });

    $(document).on('click', '.getdata', function () {

        var device = $('#device').val();
        if (device != '') {
            $('#getdataModal').modal('show');
        } else {
            Swal.fire({
            position: "top-end",
            icon: 'warning',
            title: 'Please select a Location First',
            showConfirmButton: false,
            timer: 2500
              });
        }

    });

    $(document).on('click', '#clear_data', function (e) {
        e.preventDefault();
        let btn = $(this);
        var device = $('#device').val();
        if (device != '') {
            if (confirm('Are you sure?')) {
                btn.text('Deleting...');
                btn.attr('disabled', true);
                $.ajax({
                    url: "{{ route('Attendance.cleardevicedata') }}",
                    type: 'POST',
                    data: {
                        device: device,
                        _token: '{{csrf_token()}}',
                        sync_date: $('#sync_date').val(),
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val(),
                        companytype: $('#companytype').val(),
                    },
                    success: function(data) {
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
                    $('#formTitle')[0].reset();
                    actionreload(actionJSON);
                }
                    },
                });

            }
        }
    });

    $('#comfirm_button').click(function () {

        let btn = $(this);
        btn.html('<i class="fa fa-spinner fa-spin"></i> &nbsp; Processing...');

        var device = $('#device').val();

        $.ajax({
            url: "{{ route('Attendance.getdevicedata') }}",
            type: 'POST',
            data: {
                device: device,
                _token: '{{csrf_token()}}',
                sync_date: $('#sync_date').val(),
            },
            success: function(res) {
                var html = '';
                if (res.errors) {
                    html = '<div class="alert alert-danger">Error Occurred</div>';
                    $('#comfirm_users').text('confirm');
                }
                if (res.status) {
                    html = '<div class="alert alert-success">Success</div>';
                    load_dt('', '', '', '', '');
                    $('#getdataModal').modal('hide');
                    btn.html('Confirm');
                }
                $('#msg').html(html);
            },
            error: function(data) {
                //alert(data);
                let html = '<div class="alert alert-danger">Attendance Sync Time Out</div>';
                load_dt('', '', '', '', '');
                $('#getdataModal').modal('hide');
                btn.html('Confirm');
                $('#msg').html(html);
            }
        });

    });

    //documents .delete_button click event
    $(document).on('click', '.delete_button', async function () {
        let uid = $(this).data("uid");
        let date = $(this).data("date");

        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
             $.ajax({
                url: "{{ route('Attendance.delete') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    uid: uid,
                    date: date
                },
                success: function (data) {
                   $('#attendtable').DataTable().ajax.reload(null, false);
                   $('#msg').html('<div class="alert alert-success">' + data.msg + '</div>');
                }
            });
        }
       
        
    });

});

</script>
@endsection