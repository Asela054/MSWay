@extends('layouts.app')

@section('content')

<main>
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.payroll_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-money-check-dollar-pen"></i></div>
                    <span>Other Facilities</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row justify-content-end">
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 text-right">
                        <button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm px-3"><i class="fal fa-clipboard-check mr-2"></i>Allocate</button>
                        <button type="button" name="upload_record" id="upload_record" class="btn btn-secondary btn-sm px-3"><i class="fal fa-plus mr-2"></i>Upload</button>
                        <span class="nav-item dropdown" style="top:3px;">
                            <a class="nav-link dropdown-toggle mr-lg-2" id="facilityDropdown" href="#"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                title="<?php echo 'Add Facilities'; ?>">
                                New Facility&nbsp;
                                <!--i class="fa fa-fw fa-plus"></i-->
                                <!-- <span class="d-lg-none">Facilities
                                    <span
                                        class="badge badge-pill badge-warning"><?php echo 'Add New Facility'; ?></span>
                                </span> -->
                                <!--span class="indicator text-warning d-none d-lg-block">
                                            <!--i class="fa fa-fw fa-circle"></i-//-><?php //echo ''; ?>
                                            </span-->
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" id="addNewFacility" aria-labelledby="facilityDropdown"  style="">
                                <form id="frmFacility" method="post" class="px-4 py-3">
                                    {{ csrf_field() }}
                                    <div class="form-row">
                                        <div class="col-12">
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control" id="facility_name" name="facility_name" autocomplete="off" required />
                                                <span class="input-group-append">
                                                    <button type="submit" name="btn" id="btn" class="btn btn-primary"><i class="fa fa-save"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">
                                    <span id="form_result" class="text-success">
                                        &nbsp;
                                    </span>
                                </a>
                                <!-- -->
                                <!--div class="dropdown-divider"></div-->
                                <!--a class="dropdown-item small" href="event_overview.php">View all events</a-->
                            </div>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card mt-3">
                            <div class="card-body p-3">
                                @if (\Session::has('success'))
                                <div class="alert alert-primary" role="alert">
                                    {{ \Session::get('success') }}
                                </div>
                                @endif
                                <div class="form-row">
                                    <div class="col-sm-12 sol-md-6 col-lg-3 col-xl-3">
                                        <label class="font-weight-bolder small">Additions</label>
                                        <select name="remuneration_filter" id="remuneration_filter"
                                            class="form-control form-control-sm">
                                            <option value="" selected="selected">Select the Facility</option>
                                            <!--option value="0">Basic Salary</option-->
                                            @foreach($remuneration as $payment)

                                            <option value="{{$payment->id}}">{{$payment->facility_name}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-sm-12 sol-md-6 col-lg-2 col-xl-2">
                                        <label class="font-weight-bolder small">Payment Date</label>
                                        <input type="month" name="month_filter" id="month_filter"
                                            class="form-control form-control-sm" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="center-block fix-width scroll-inner mt-3">
                            <table class="table table-bordered table-striped table-sm small nowrap w-100" id="emptable" width="100%"
                                cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="actlist_col ">
                                            <!-- <label class="form-check-label"><span
                                                    class=""><input id="chk_approve" class="" type="checkbox"
                                                        style="" title="" disabled="disabled"></span> <span
                                                    style="display:block;">Approve</span></label> -->
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="chk_approve" disabled="disabled">
                                                <label class="custom-control-label m-0" for="chk_approve">APPROVE</label>
                                            </div>
                                        </th>
                                        <th>EMPLOYEE NAME</th>
                                        <th>ADDITION TYPE</th>
                                        <th>PAID VALUE</th>
                                        <th>DATE OF PAYMENT</th>
                                        <th>BASIC SALARY</th>
                                        <th class="actlist_col">ACTION</th>
                                    </tr>
                                </thead>

                                <!--tbody>
                                                
                                                
                                                    <tr>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                    </tr>
                                                
                                                
                                                </tbody-->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="incrementCancelModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="incrementCancelModalLabel">Confirmation</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span
                            class="btn-sm btn-danger" aria-hidden="true">X</span></button>

                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div id="incrementUploadModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">            
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel">Confirmation</h5>&nbsp;
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('uploadOtherFacilities') }}" method="post" target="_self" enctype="multipart/form-data" onsubmit="return colValidate();">
                    {{ csrf_field() }}
                        <div class="form-row">
                            <div class="col-12">
                                <label class="font-weight-bolder small">
                                    File Content :
                                    <a class="font-weight-normal" href="{{ url('/public/csvsample/other_facilities.csv') }}">
                                        CSV Format-Download Sample File
                                    </a>
                                </label>
                                <select name="remuneration_file" id="remuneration_file" class="form-control form-control-sm" required>
                                    <option value="" selected="selected">Select Facility</option>
                                    @foreach($remuneration as $payment)

                                    <option value="{{$payment->id}}">{{$payment->facility_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>  
                        <div class="form-row mb-1">
                            <div class="col-12">
                                <p id="lblstatus"></p>
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <div class="col-12">
                                <h6 class="title-style small"><span>Upload File</span></h6>
                                <div class="input-group input-group-sm">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="file" id="file" aria-describedby="inputGroupFileAddon04" required>
                                        <label class="custom-file-label" for="inputGroupFile04">Choose file</label>
                                    </div>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit" name="import_file" value="import" required="required">Upload</button>
                                    </div>
                                </div>
                                <!-- <input class="form-control col" type="file" name="file" id="file" style="padding-bottom:38px;" required>
                                <button type="submit" name="import_file" value="import" class="btn btn-primary" required="required">Upload</button> -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>            
        </div>
    </div>

    <div id="facilityAllocModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="facilityAllocModalLabel">Facility Allocation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="frmFacilityAlloc" method="post">
                        {{ csrf_field() }}
                        <span id="alloc_result"></span>
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="font-weight-bolder small">Employee*</label>
                                <select name="payroll_profile_id" id="payroll_profile_id" class="form-control form-control-sm" required>
                                    <option value="">Select employee</option>
                                    @foreach($employee_list as $employee)

                                    <option value="{{$employee->payroll_profile_id}}">{{$employee->emp_first_name}}
                                    </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="font-weight-bolder small">Additions*</label>
                                <select name="other_facility_id" id="other_facility_id" class="form-control form-control-sm" required>
                                    <option value="" selected="selected">Select the Facility</option>
                                    <!--option value="0">Basic Salary</option-->
                                    @foreach($remuneration as $payment)

                                    <option value="{{$payment->id}}">{{$payment->facility_name}}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="font-weight-bolder small">Payment Date*</label>
                                <input type="date" name="payment_date" id="payment_date" class="form-control form-control-sm" required />
                            </div>
                            <div class="col">
                                <label class="font-weight-bolder small">Amount*</label>
                                <input type="text" name="payment_amount" id="payment_amount" class="form-control form-control-sm" required autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <hr>
                                <button type="submit" name="add_amount" id="add_amount" class="btn btn-primary btn-sm px-3">Save</button>
                                <button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>            
        </div>
    </div>
</main>

@endsection


@section('script')

<script>
    $(document).ready(function () {

        $('#payrollmenu').addClass('active');
        $('#payrollmenu_icon').addClass('active');
        $('#policymanagement').addClass('navbtnactive');

        var empTable = $("#emptable").DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('facilitiesData.getData') }}",
            "order": [
                [1, 'asc'],
                [2, 'asc']
            ],
            "columns": [{
                    "data": 'payment_approved'
                },
                {
                    "data": "emp_name_with_initial"
                },
                {
                    "data": "increment_type"
                },
                {
                    "data": "increment_value"
                },
                {
                    "data": "effective_month"
                },
                {
                    "data": "basic_salary"
                },
                {
                    "data": "id"
                }
            ],
            "columnDefs": [{
                "targets": 0,
                "className": 'actlist_col',
                "orderable": false,
                render: function (data, type, row) {
                    var check_str = (data == 1) ? ' checked="checked"' : '';
                    var block_str =
                    ''; //($("#hidden_id").val()=='')?' disabled="disabled"':'';
                    return '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input freeze" id="check_' + row.id + '" data-refid="' + row.id +
                        '" data-refemp=""' + check_str + block_str + '><label class="custom-control-label m-0" for="check_' + row.id + '">&nbsp;</label></div>';
                }
            }, {
                "targets": 2,
                render: function (data, type, row) {
                    // return '<div class="badge badge-primary badge-pill">' + row
                    //     .increment_desc + '</div>';
                    return row.increment_desc;
                }
            }, {
                "targets": 3,
                "className": 'text-right'
            }, {
                "targets": 4,
                // render: function (data, type, row) {
                //     return '<div class="badge badge-primary badge-pill">' + row
                //         .effective_date + '</div>';
                // }
            }, {
                "targets": 5,
                "className": 'text-right',
                // render: function (data, type, row) {
                //     return '<div class="badge badge-primary badge-pill">' + data + '</div>';
                // }
            }, {
                "targets": 6,
                "orderable": false,
                "className": "actlist_col masked_col text-right",
                render: function (data, type, row) {
                    return '<button class="btn btn-danger btn-sm delete" data-refid="' +
                        data + '"><i class="fas fa-trash-alt"></i></button>';
                }
            }],
            "createdRow": function (row, data, dataIndex) {
                $('td', row).eq(6).removeClass('masked_col');
                $(row).attr('id', 'row-' + data.id); //data[5] //$( row ).data( 'refid', data[3] );
            },
            "drawCallback": function (settings) {
                var objs_visible = $('input.freeze[type=checkbox]').length;
                var chk_disabled = (objs_visible == 0); //?true:false;
                var chk_selected = ((objs_visible > 0) && ($('input.freeze[type=checkbox]:checked')
                    .length == objs_visible));
                $('#chk_approve').prop('disabled', chk_disabled);
                $('#chk_approve').prop('checked', chk_selected);

            }
        });
        $('#remuneration_filter').on('keyup change', function () {
            if (empTable.columns(2).search() !== this.value) {
                empTable.columns(2).search(this.value).draw();
            }
        });
        $('#month_filter').on('keyup change', function () {
            if (empTable.columns(4).search() !== this.value) {
                empTable.columns(4).search(this.value).draw();
            }
        });

        $('#upload_record').click(function () {
            //$('#formModalLabel').text('Find Employee');
            //$('#action_button').val('Add');
            //$('#action').val('Add');
            //$('#form_result').html('');

            $('#incrementUploadModal').modal('show');
        });

        $('#find_employee').click(function () {
            $('#facilityAllocModal').modal('show');
        });

        $('#facility_name').on('keydown', function () {
            $('#form_result').html('&nbsp;');
        });

        $('#frmFacility').on('submit', function (event) {
            event.preventDefault();
            var action_url = "{{ route('addOtherFacility') }}";

            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {
                    var html = '';
                    if (data.errors) {
                        // html = '<div class="alert alert-danger">';
                        // for (var count = 0; count < data.errors.length; count++) {
                        //     html += '<p>' + data.errors[count] + '</p>';
                        // }
                        // html += '</div>';
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: '',
                            message: data.errors,
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

                        // html = '<div class="alert alert-success">' + data.success + '';
                        var new_facility_name = data.new_obj.facility_name;
                        $('#other_facility_id').append('<option value="' + data.new_obj.id +
                            '">' + new_facility_name + '</option>');
                        $('#remuneration_filter').append('<option value="' + data.new_obj
                            .id + '">' + new_facility_name + '</option>');
                        $('#remuneration_file').append('<option value="' + data.new_obj.id +
                            '">' + new_facility_name + '</option>');
                    }
                    // $('#form_result').html(html);
                }
            });
        });

        $('#frmFacilityAlloc').on('submit', function (event) {
            event.preventDefault();
            var action_url = "{{ route('allocateOtherFacility') }}";

            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {
                    var html = '';
                    if (data.errors) {
                        // html = '<div class="alert alert-danger">';
                        // for (var count = 0; count < data.errors.length; count++) {
                        //     html += '<p>' + data.errors[count] + '</p>';
                        // }
                        // html += '</div>';
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: '',
                            message: data.errors,
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
                        action(actionJSON);
                        // html = '<div class="alert alert-success">' + data.success + '';

                        /*
                        var selected_tr=empTable.row('#row-'+data.new_obj.id+'');
                        
                        if(selected_tr.length==0){
                        	var rowNode = empTable.row.add({'id':data.new_obj.id,
                        		'emp_name_with_initial':$("#payroll_profile_id").find(":selected").text(),
                        		'effective_date':$('#payment_date').val(),
                        		'effective_month':'Now',
                        		'increment_type':$("#other_facility_id").find(":selected").val(),
                        		'increment_desc':$("#other_facility_id").find(":selected").text(),
                        		'increment_value':$("#payment_amount").val(),
                        		'basic_salary':'100'
                        		}).draw( true ).node();
                        }else{
                        	var d=selected_tr.data();
                        	d.payment_date='Now';
                        	d.payment_amount=$('#eligible_amount').val();
                        	
                        	empTable.row(selected_tr).data(d).draw();
                        }
                        */


                        setTimeout(function () {
                            empTable.draw();
                            $('#facilityAllocModal').modal('hide');
                        }, 1000);
                    }
                    // $('#alloc_result').html(html);
                }
            });
        });

        /* approve-payments-begin */
        var _token = $('#frmFacility input[name="_token"]').val();

        function invVal(batch_cnt) {
            return (batch_cnt > 0) ? 1 : 0;
        }

        function batchUpdate(par_checked, objs_cnt) {
            if (objs_cnt > 0) {
                //var par_checked=$('#chk_approve').is(':checked');
                //if(!(par_checked)&&(pos>0)){par_checked=!(par_checked)};
                var objs_list = (par_checked) ? $('input.freeze[type=checkbox]:not(:checked)') : $(
                    'input.freeze[type=checkbox]:checked');
                objs_cnt = $(objs_list).length;
                //prev_cnt=$(objs_list[0]).length;
                var batch_inv = invVal(1); //update-multiple-records
                //alert(objs_cnt+'>>'+prev_cnt);
                if (objs_cnt > 0) {
                    issuePayment(objs_list[0], objs_cnt, batch_inv, par_checked);
                }
            }
        }

        $('#chk_approve').on('click', function () {
            var par_checked = $(this).is(':checked');
            $('#chk_approve').parent().addClass('masked_obj');
            //var objs_list=(par_checked)?$('input.freeze[type=checkbox]:not(:checked)'):$('input.freeze[type=checkbox]:checked');
            //var objs_cnt=$(objs_list).length;
            //var batch_inv = invVal(1);//update-multiple-records
            batchUpdate(par_checked, 1); //set objs-cnt as 1 to begin
            /*
            $(objs_list).each(function(index, obj){
            	issuePayment($(obj), (objs_cnt-index), batch_inv);
            });
            */
        });

        $(document).on('click', '.freeze', function () {
            var batch_inv = invVal(0); //not-batch-update
            issuePayment($(this), 0, batch_inv, false);
        });

        function issuePayment(paymentref, batch_cnt, batch_inv, par_checked) {
            $.ajax({
                url: "freezeOtherFacilityPayment",
                method: 'POST',
                data: {
                    id: $(paymentref).data('refid'),
                    payment_approved: ($(paymentref).is(":checked") ? 1 - batch_inv : batch_inv),
                    _token: _token
                },
                dataType: "JSON",
                beforeSend: function () {
                    $(paymentref).prop('disabled', true);
                },
                success: function (data) {
                    //alert(JSON.stringify(data));

                    var act_finalize = false;
                    var head_obj = null;

                    if (data.errors) {
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: 'Record Error',
                            message: data.errors,
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        action(actionJSON);

                        $(paymentref).prop('checked', false);
                        $(paymentref).prop('disabled', false);
                    }
                    else{
                        if (data.result == 'error') {
                            if (batch_cnt == 0) {
                                $(paymentref).prop('checked', !$(paymentref).prop('checked'));
                                Swal.fire({
                                    icon: 'question',
                                    title: 'Something wrong.',
                                    text: 'Payment status cannot be approved at the moment'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'question',
                                    title: 'Payment update error.',
                                    text: 'Please reload the page to abort process.'
                                });
                                /*
                                $(paymentref).addClass('check_inactive');
                                */
                            }

                        } else {
                            $(paymentref).prop('disabled', false);
                            $(paymentref).data('refid', data.payment_id);

                            if (batch_cnt > 0) {
                                $(paymentref).prop('checked', !$(paymentref).prop('checked'));
                            }
                        }


                        if ((batch_cnt - batch_inv) == 0) {
                            act_finalize = true;
                            head_obj = $('#chk_approve').parent();
                        }

                        if (act_finalize) {
                            if ($(head_obj).hasClass('masked_obj')) {
                                $(head_obj).removeClass('masked_obj');
                            }
                            /*
                            var objs_visible=$('input.finalize[type=checkbox]').length;
                            var chk_selected=((objs_visible>0)&&($('input.finalize[type=checkbox]:checked').length==objs_visible));
                            $('#chk_approve').prop('checked', chk_selected);
                            */
                        }

                        empTable.draw(); //update-chk-approve-checked-value
                        batchUpdate(par_checked, (batch_cnt - 1));
                    }
                }
            });
        }
        /* approve-payments-close */

        var increment_id;

        $(document).on('click', '.delete', async function () {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (r == true) {
                increment_id = $(this).data('refid');

                $.ajax({
                    url: "OtherFacilities/destroy/" + increment_id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        //alert(JSON.stringify(data));
                        // setTimeout(function () {
                        //     $('#incrementCancelModal').modal('hide');
                        //     //$('#user_table').DataTable().ajax.reload();
                        //     //alert('Data Deleted');
                        // }, 2000);
                        //location.reload()
                        if (data.errors) {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: 'Record Error',
                                message: data.errors,
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                        if (data.result == 'success') {
                            const actionObj = {
                                icon: 'fas fa-trash-alt',
                                title: '',
                                message: 'Record Remove Successfully',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);

                            empTable.row('#row-' + increment_id + '').remove().draw();
                        }
                    }
                });
            }
        });
    });

    function colValidate() {
        var remuneration_file = $('#remuneration_file').find(":selected").val();

        if (remuneration_file == '') {
            Swal.fire({
				icon: 'question',
				title: '',
				text: 'Select file content'
			});
            return false;
        } else {
            return true;
        }

    }
</script>

@endsection