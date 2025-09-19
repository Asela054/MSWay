@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.payroll_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-money-check-dollar-pen"></i></div>
                    <span>Loans</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12 text-right">
                        <button type="button" name="find_employee" id="find_employee"  class="btn btn-success btn-sm px-3"><i class="fas fa-search mr-2"></i>Search</button>
                        <button type="button" name="create_record" id="create_record" class="btn btn-primary btn-sm px-3"><i class="fas fa-plus mr-2"></i>Add</button>
                    </div>
                    <div class="col-12">
                        <div class="card mt-3">
                            <div class="card-body p-3">
                                <form id="frmInfo" method="post">
                                    {{ csrf_field() }}
                                    <span id="form_result"></span>
                                    <div class="form-row">
                                        <div class="col">
                                            <label class="font-weight-bolder small">Employee Name</label>
                                            <input type="text" name="emp_name" id="emp_name" class="form-control form-control-sm" readonly="readonly" />
                                        </div>
                                        <div class="col">
                                            <label class="font-weight-bolder small">Basic Salary</label>
                                            <input type="text" name="basic_salary" id="basic_salary" class="form-control form-control-sm" readonly="readonly" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner mt-3">
                            <table class="table table-bordered table-striped table-sm small w-100 nowrap" id="titletable" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="actlist_col">ACTIVE</th>
                                        <th>TYPE</th>
                                        <th>DATE</th>
                                        <th>VALUE</th>
                                        <th>PAID</th>
                                        <th>BALANCE</th>
                                        <th>DURATION</th>
                                        <th class="actlist_col">ACTION</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="formModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Find Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-end">
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                            <select name="location_filter" id="location_filter" class="shipClass form-control form-control-sm">
                                <option value="">Please Select</option>
                                @foreach($branch as $branches)
                                <option value="{{$branches->location}}">{{$branches->location}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <hr>
                        </div>
                    </div>
                    <div class="center-block fix-width scroll-inner">
                        <table class="table table-bordered table-striped table-sm small w-100 nowrap" id="emptable" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>REG. NO</th>
                                    <th>NAME</th>
                                    <th>OFFICE</th>
                                    <th>SALARY</th>
                                    <th>GROUP</th>
                                    <th class="actlist_col">ACTIONS</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="frmConfirm" method="post">
                {{ csrf_field() }}
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span id="confirm_result"></span>
                        <!--h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4-->
                        <div class="form-row mb-1">
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <label class="font-weight-bolder small">Loan Description*</label>
                                <input type="text" name="loan_name" id="loan_name" class="form-control form-control-sm" autocomplete="off" required />
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <label class="font-weight-bolder small">Date of Issue*</label>
                                <input type="date" name="loan_date" id="loan_date" class="form-control form-control-sm" required />
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <label class="font-weight-bolder small">Loan Type*</label>
                                <select name="loan_type" id="loan_type" class="form-control form-control-sm" required>
                                    <option value="PL" data-ratekey="0">Personal</option>
                                    <option value="FL" data-ratekey="0">Festival</option>
                                    <option value="WL" data-ratekey="1">Welfare</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <label class="font-weight-bolder small">Interest Rate (%)</label>
                                <input type="text" name="interest_rate" id="interest_rate" class="form-control form-control-sm" readonly autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <label class="font-weight-bolder small">Issue Amount*</label>
                                <input type="text" name="issue_amount" id="issue_amount" class="form-control form-control-sm" autocomplete="off" required />
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <label class="font-weight-bolder small">Loan Value</label>
                                <input type="text" name="loan_amount" id="loan_amount" class="form-control form-control-sm" autocomplete="off" readonly />
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <label class="font-weight-bolder small">No. of Installments*</label>
                                <input type="text" name="loan_duration" id="loan_duration" class="form-control form-control-sm" autocomplete="off" required />
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <label class="font-weight-bolder small">Installment Value</label>
                                <input type="text" name="installment_value" id="installment_value" class="form-control form-control-sm" readonly autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col-12">
                                <label class="font-weight-bolder small">Primary Loan Guarantor*</label>
                                <label class="font-weight-bolder small" id="warinig_1"></label>
                                <select name="employeegarentee" id="employee_f" class="form-control form-control-sm" required>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col-12">
                                <label class="font-weight-bolder small">Secondary Loan Guarantor*</label>
                                <label class="font-weight-bolder small" id="warinig_2"></label>
                                <select name="employee_secondgarentee" id="employee_ff" class="form-control form-control-sm" required>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <div class="col-12 text-right">
                                <hr>
                                <input type="hidden" name="action" id="action" value="Edit" />
                                <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm px-3" value="Edit" />
                                <button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="loanCancelModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loanCancelModalLabel">Confirmation</h5>
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
</main>

@endsection


@section('script')

<script>
    $(document).ready(function () {

        $('#payrollmenu').addClass('active');
        $('#payrollmenu_icon').addClass('active');
        $('#policymanagement').addClass('navbtnactive');

        let employee_f = $('#employee_f');
        let employee_ff = $('#employee_ff');

        employee_f.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("employee_list_sel2")}}',
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

        employee_ff.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("employee_list_sel2")}}',
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

        var empTable = $("#emptable").DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": scripturl + "/get_conf_employee_data.php", //
            "columns": [{
                    "data": "emp_etfno"
                },
                {
                    "data": "emp_name_with_initial"
                },
                {
                    "data": "location"
                },
                {
                    "data": "basic_salary"
                },
                {
                    "data": "process_name"
                },
                {
                    "data": "payroll_profile_id"
                }
            ],
            "columnDefs": [{
                "targets": 3,
                "orderable": false,
                "className": "text-right",
            }, {
                "targets": 5,
                "className": 'text-center',
                "orderable": false,
                render: function (data, type, row) {
                    return '<button class="btn btn-primary btn-sm review" data-refid="' +
                        data + '" data-toggle="tooltip" title="Department"><i class="fas fa-eye"></i></button>';
                }
            }],
            "createdRow": function (row, data, dataIndex) {
                $('td', row).eq(5).removeClass('masked_col');
                $(row).attr('id', 'row-' + data
                .payroll_profile_id); //+data[5]//$( row ).data( 'refid', data[3] );
            }
        });
        $('#location_filter').on('keyup change', function () {
            if (empTable.columns(2).search() !== this.value) {
                empTable.columns(2).search(this.value).draw();
            }
        });

        var remunerationTable = $("#titletable").DataTable({
            "columns": [{
                    data: 'id'
                }, {
                    data: 'loan_type'
                }, {
                    data: 'loan_date'
                }, {
                    data: 'loan_amount'
                },
                {
                    data: 'loan_paid'
                }, {
                    data: 'loan_amount'
                }, {
                    data: 'loan_duration'
                }, {
                    data: 'loan_freeze'
                }
            ],
            "order": [],
            "columnDefs": [{
                "targets": 0,
                "orderable": false,
                "className": 'actlist_col',
                render: function (data, type, row) {
                    if (row.loan_complete == 0) {
                        var check_str = (row.loan_freeze == 0) ? ' checked="checked"' : '';
                        return '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input freeze" data-refid="' + row
                            .id + '"' + check_str + ' /><label class="custom-control-label mt-0" for="customCheck1"></label></div>';
                    } else {
                        return '<span><i class="fa fa-check-square text-success"></i></span>';
                    }
                }
            }, {
                "targets": 3,
                "className":'text-right'
            }, {
                "targets": 4,
                "className":'text-right'
            }, {
                "targets": 5,
                //"orderable":false,
                "className":'text-right',
                render: function (data, type, row) {
                    var loan_bal = row.loan_amount - row.loan_paid;
                    return loan_bal;
                }
            }, {
                "targets": 7,
                "orderable": false,
                "className": 'actlist_col text-right',
                render: function (data, type, row) {
                    /*
                    return '<button type="button" class="edit btn btn-datatable btn-icon btn-primary" data-refid="'+row.id+'" ><i class="fas fa-edit"></i></button><button type="button" class="delete btn btn-datatable btn-icon btn-danger" data-refid="'+row.id+'" ><i class="fas fa-trash"></i></button>';
                    */
                    if (row.loan_complete == 0) {
                        return '<button type="button" class="delete btn btn-danger btn-sm" data-refid="' +
                            row.id + '" ><i class="fas fa-trash-alt"></i></button>';
                    } else {
                        return '<span class="badge badge-success badge-pill">Completed</span>';
                    }
                }
            }],
            "createdRow": function (row, data, dataIndex) {
                //$('td', row).eq(0).removeClass('masked_col');
                //$('td', row).eq(5).removeClass('masked_col');
                $(row).attr('id', 'row-' + data.id);
            }
        });

        function calcInstallment() {
            var loan_dura = parseFloat(realDuration());
            var issue_amount = parseFloat(realAmount());
            var loan_rate = parseFloat(realRate()) / 100;
            var installment_value = (loan_dura > 0) ? (issue_amount + (issue_amount * loan_rate)) / loan_dura :
                0;
            $("#installment_value").val(isNaN(installment_value) ? '0.00' : installment_value.toFixed(2));
        }

        function calcLoan() {
            var part_value = parseFloat(realInstallment());
            var loan_dura = parseFloat(realDuration());
            var checkrate = $('#loan_type').find(":selected").data('ratekey');
            var loan_value = (checkrate == 0) ? parseFloat($("#issue_amount").val()) : part_value * loan_dura;
            $("#loan_amount").val(isNaN(loan_value) ? '0.00' : loan_value.toFixed(2));
        }

        function realAmount() {
            var loanamt = 0;

            if ($('#issue_amount').val() != '') {
                loanamt = isNaN($('#issue_amount').val()) ? 0 : $('#issue_amount').val();
            }

            return loanamt;
        }

        function realDuration() {
            var loandura = 0;

            if ($('#loan_duration').val() != '') {
                loandura = isNaN($('#loan_duration').val()) ? 0 : $('#loan_duration').val();
            }

            return loandura;
        }

        function realInstallment() {
            var loaninstallment = 0;

            if ($('#installment_value').val() != '') {
                loaninstallment = isNaN($('#installment_value').val()) ? 0 : $('#installment_value').val();
            }

            return loaninstallment;
        }

        function realRate() {
            var loanrate = 0;
            var checkrate = $('#loan_type').find(":selected").data('ratekey');

            if ($('#interest_rate').val() != '') {
                loanrate = isNaN($('#interest_rate').val()) ? 0 : $('#interest_rate').val();
            }

            return loanrate * checkrate;
        }

        $("#issue_amount, #interest_rate, #loan_duration").on('keyup', function () {
            calcInstallment();
            calcLoan();
        });
        /*
        $("#loan_duration, #installment_value").on('keyup', function(){
        	calcLoan();
        });
        */
        $('#loan_type').on('keyup change', function () {
            var check_rate = ($(this).find(":selected").data('ratekey') == 1) ? false : true;
            $('#interest_rate').prop('readonly', check_rate);

            calcInstallment();
            calcLoan();
        });

        function findEmployee() {
            //   $('#formModalLabel').text('Find Employee');
            //$('#action_button').val('Add');
            //$('#action').val('Add');
            $('#form_result').html('');

            $('#formModal').modal('show');
        }

        $('#find_employee').click(function () {
            findEmployee();
        });

        $('#create_record').click(async function () {
            if ($("#payroll_profile_id").val() == "") {
                var r = await Otherconfirmation("You haven't selected the employee. Search now ?");
                if (r == true) {
                    findEmployee();
                }
            } else {
                $('#action_button').val('Add Loan');
                $('#action').val('Add');
                $('#confirm_result').html('');

                $('#loan_name').val('');
                $('#loan_date').val('');
                $('#loan_amount').val('');
                $('#loan_duration').val('');
                $('#loan_type').prop("disabled", false);
                $('#interest_rate').val('');
                $('#installment_value').val('');
                $('#issue_amount').val('');

                $('#hidden_id').val('');

                $('#confirmModal').modal('show');
            }
        });

        var remuneration_id;

        $(document).on('click', '.edit', async function () {
            var r = await Otherconfirmation("You want to Edit this ? ");
            if (r == true) {
                var id = $(this).data('refid'); //row#
                //var pack_id = $(this).data('refpack');
                $("#confirm_result").html('');
                $.ajax({
                    url: "EmployeeLoan/" + id + "/edit",
                    dataType: "json",
                    success: function (data) {
                        $('#action').val('Edit');
                        $('#action_button').val('Edit Loan');
                        remuneration_id = id;
                        $('#loan_amount').val(data.loan_obj.loan_amount);
                        $('#loan_duration').val(data.loan_obj.loan_duration);
                        $('#loan_type').val(data.loan_obj.loan_type);
                        $('#loan_type').prop('disabled', true);
                        $('#interest_rate').val(data.loan_obj.interest_rate);
                        $('#loan_name').val(data.loan_obj.loan_name);
                        $('#issue_amount').val(data.loan_obj.issue_amount);
                        $('#loan_date').val(data.loan_obj.loan_date);
                        //$('#formModalLabel').text('Edit Remuneration');
                        //$('#action_button').val('Edit');
                        //$('#action').val('Edit');
                        $('#hidden_id').val(data.loan_obj.id);

                        $('#confirmModal').modal('show');

                    }
                }) /**/
            }
        });

        $('#frmConfirm').on('submit', function (event) {
            event.preventDefault();
            var action_url = '';

            var param_interest = $('#interest_rate').val();
            $('#interest_rate').val(realRate()); // set-interest-rate-of-loan

            if ($('#hidden_id').val() == '') {
                action_url = "{{ route('addEmployeeLoan') }}";
            } else {
                action_url = "{{ route('EmployeeLoan.update') }}";
            }
            /*
            alert(action_url);
            */
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

                        action(actionJSON);
                        // html = '<div class="alert alert-success">' + data.success + '</div>';
                        // $('#frmInfo')[0].reset();
                        // $('#titletable').DataTable().ajax.reload();
                        // location.reload()

                        //if($('#hidden_id').val()==''){
                        //$('#hidden_id').val(data.new_obj.id);
                        //}

                        var selected_tr = remunerationTable.row('#row-' + data.new_id +
                        ''); //remunerationTable.$('tr.classname');
                        /*
                        alert(JSON.stringify(selected_tr.data()));
                        */
                        if (selected_tr.length == 0) {
                            var rowNode = remunerationTable.row.add({
                                'id': data.new_id,
                                'loan_type': $("#loan_type").val(),
                                'loan_date': $("#loan_date").val(),
                                'loan_amount': $("#loan_amount").val(),
                                'loan_paid': 0,
                                'loan_duration': $("#loan_duration").val(),
                                'loan_freeze': 0,
                                'loan_complete': 0
                            }).draw(false).node();
                        } else {
                            var d = selected_tr.data();
                            d.loan_date = $('#loan_date').val();
                            d.loan_duration = $('#loan_duration').val();
                            d.loan_amount = $('#loan_amount').val();
                            remunerationTable.row(selected_tr).data(d).draw();
                        }

                        $('#confirmModal').modal('hide');

                    } else {
                        $('#interest_rate').val(
                        param_interest); // set-previous-value-on-error
                    }

                    // $('#confirm_result').html(html);
                }
            });
        });

        $(document).on('click', '.review', function () {
            var id = $(this).data('refid');
            $('#form_result').html('');
            $.ajax({
                url: "EmployeeLoan/" + id + "/review",
                dataType: "json",
                success: function (data) {
                    $('#emp_name').val(data.pre_obj.emp_name_with_initial);
                    $('#basic_salary').val(data.pre_obj.basic_salary);
                    $('#payroll_profile_id').val(id);

                    //$('#formModalLabel').text('Edit Remuneration');
                    //$('#action_button').val('Edit');
                    //$('#action').val('Edit');

                    remunerationTable.clear();
                    remunerationTable.rows.add(data.package);
                    remunerationTable.draw();

                    $('#formModal').modal('hide');

                }
            }) /**/
        });

        $(document).on('click', '.delete', async function() {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (r == true) {
                remuneration_id = $(this).data('refid');

                $.ajax({
                    url: "EmployeeLoan/destroy/" + remuneration_id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        //alert(JSON.stringify(data));
                        // setTimeout(function () {
                        //     $('#loanCancelModal').modal('hide');
                        //     //$('#user_table').DataTable().ajax.reload();
                        //     //alert('Data Deleted');
                        // }, 2000);
                        //location.reload()
                        if (data.result == 'success') {
                            remunerationTable.row('#row-' + remuneration_id + '').remove().draw();
                            
                            const actionObj = {
                                icon: 'fas fa-trash-alt',
                                title: '',
                                message: 'Record Remove Successfully',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            $('#formTitle')[0].reset();
                            actionreload(actionJSON);
                        } else {
                            var msg = "Loan cannot be deleted at the moment." + data.more_info;

                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: 'Record Error',
                                message: msg,
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                    }
                })
            }
        });

        $(document).on('click', '.freeze', function () {
            var _token = $('#frmInfo input[name="_token"]').val();
            //alert(_token);
            freezeLoan($(this), _token);
        });

        function freezeLoan(loanref, _token) {
            $.ajax({
                url: "freezeEmployeeLoan",
                method: 'POST',
                data: {
                    id: $(loanref).data('refid'),
                    loan_freeze: ($(loanref).is(":checked") ? 0 : 1),
                    _token: _token
                },
                dataType: "JSON",
                beforeSend: function () {
                    $(loanref).prop('disabled', true);
                },
                success: function (data) {
                    //alert(JSON.stringify(data));
                    if (data.result == 'error') {
                        $(loanref).prop('checked', !$(loanref).prop('checked'));
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something wrong. Loan status cannot be changed at the moment'
                        });
                    } else {
                        $(loanref).prop('disabled', false);
                    }
                }
            })
        }

        // $(document).on('click', '#ok_button', function () {
        //     $.ajax({
        //         url: "EmployeeLoan/destroy/" + remuneration_id,
        //         beforeSend: function () {
        //             $('#ok_button').text('Deleting...');
        //         },
        //         success: function (data) {
        //             //alert(JSON.stringify(data));
        //             setTimeout(function () {
        //                 $('#loanCancelModal').modal('hide');
        //                 //$('#user_table').DataTable().ajax.reload();
        //                 //alert('Data Deleted');
        //             }, 2000);
        //             //location.reload()
        //             if (data.result == 'success') {
        //                 remunerationTable.row('#row-' + remuneration_id + '').remove()
        //                 .draw();

        //             } else {
        //                 alert("Loan cannot be deleted at the moment.\r\n\r\n" + data
        //                     .more_info);
        //             }
        //         }
        //     })
        // });


        $('#employee_f').on('keyup change', function () {
            var employeeId = $(this).val();

            $.ajax({
                url: '{{ route("checkloanguranteemployee") }}',
                method: 'GET',
                data: {
                    employee_id: employeeId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response === 1) {
                        $('#warinig_1')
                            .text('This employee is already signed to another loan.')
                            .css('color', 'red');
                    } else {
                        $('#warinig_1').text('');
                    }
                }
            });
        });

        $('#employee_ff').on('keyup change', function () {
            var employeeId = $(this).val();

            $.ajax({
                url: '{{ route("checkloanguranteemployee") }}',
                method: 'GET',
                data: {
                    employee_id: employeeId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response === 1) {
                        $('#warinig_2')
                            .text('This employee is already signed to another loan.')
                            .css('color', 'red');
                    } else {
                        $('#warinig_2').text('');
                    }
                }
            });
        });

    });
</script>

@endsection