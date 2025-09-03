@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header page-header-light bg-white shadow">
            <div class="container-fluid">
                @include('layouts.payroll_nav_bar')
               
            </div>
        </div>
        <div class="container-fluid mt-4">
            <div class="row">


                <div class="col-lg-12">
                    <div id="default">
                        <div class="card card-header-actions mb-4">
                            <div class="card-header">
                                Payment Schedules
                                <!--button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm">Add</button-->
                            </div>
                            <div class="card-body">
                                <div class="datatable table-responsive">
                                    <table class="table table-bordered table-hover" id="titletable" width="100%"
                                           cellspacing="0">
                                        <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th class="actlist_col">Action</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @foreach($payroll_process_type as $payrolltype)

                                            <tr id="row-{{$payrolltype->payroll_process_type_id}}">
                                                <td>{{$payrolltype->process_name}}</td>
                                                <td>{{$payrolltype->payment_period_fr}}</td>
                                                <td>{{$payrolltype->payment_period_to}}</td>
                                                <td class="actlist_col">
                                                    <button class="btn btn-primary btn-datatable btn-icon renew"
                                                            type="button"
                                                            data-refid="{{$payrolltype->payroll_process_type_id}}">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card card-header-actions mb-4">
                            <div class="card-header">
                                Salary Banking - Send salary details to bank accounts of employees
                                <div>
                                    <button type="button" name="payslips_record" id="payslips_record"
                                            class="btn btn-success btn-sm">Payslips
                                    </button>
                                    <button type="button" name="facilities_record" id="facilities_record"
                                            class="btn btn-success btn-sm">Facilities
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>

        <div id="formModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalLabel"></h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span
                                    class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                    </div>
                    <div class="modal-body">
                        <span id="form_result"></span>
                        <form id="frmInfo" class="" method="post">
                            {{ csrf_field() }}
                            <div class="sbp-preview">
                                <div class="sbp-preview-content" style="padding:15px 5px;">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="control-label col">Year</label>
                                            <div class="col">
                                                <select name="work_year" id="work_year" class="form-control">
                                                    <option value="" disabled="disabled" selected="selected">Select
                                                        Year
                                                    </option>
                                                    <?php
                                                    date_default_timezone_set("Asia/Kolkata");
                                                    $mydate = getdate(date("U"));
                                                    $yearfr = $mydate['year'] - 1;
                                                    for($optyear = $yearfr;$optyear < ($yearfr + 5);$optyear++){
                                                    ?>

                                                    <option value="<?php echo $optyear; ?>"><?php echo $optyear; ?></option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col">Month</label>
                                            <div class="col">
                                                <select name="work_month" id="work_month" class="form-control">
                                                    <option value="" disabled="disabled" selected="selected">Select
                                                        Month
                                                    </option>
                                                    <option value="0">Jan</option>
                                                    <option value="1">Feb</option>
                                                    <option value="2">Mar</option>
                                                    <option value="3">Apr</option>
                                                    <option value="4">May</option>
                                                    <option value="5">Jun</option>
                                                    <option value="6">Jul</option>
                                                    <option value="7">Aug</option>
                                                    <option value="8">Sep</option>
                                                    <option value="9">Oct</option>
                                                    <option value="10">Nov</option>
                                                    <option value="11">Dec</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="control-label col">From</label>
                                            <div class="col">
                                                <input type="date" name="payment_period_fr" id="payment_period_fr"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col">To</label>
                                            <div class="col">
                                                <input type="date" name="payment_period_to" id="payment_period_to"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="" align="right" style="padding:5px; border-top:none;">

                                    <input type="hidden" name="action" id="action" value="Edit"/>
                                    <input type="hidden" name="hidden_id" id="hidden_id"/>
                                    <input type="hidden" name="payroll_process_type_id" id="payroll_process_type_id"
                                           value=""/>
                                    <input type="submit" name="action_button" id="action_button" class="btn btn-warning"
                                           value="Edit"/>
                                    <!--input type="button" id="btn_next" value="More" class="btn btn-light" /-->

                                </div>
                            </div>
                        </form>


                    </div>


                </div>

            </div>
        </div>

        <div id="confirmModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
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
            /*
            var remunerationTable=$("#titletable").DataTable();
            */

            var criteriaTable = $("#titletable").DataTable({
                "searching": false,
                "info": false,
                "paging": false,
                "order": [],
                "columnDefs": [{
                    "targets": 3,
                    "className": 'actlist_col',
                    "orderable": false
                }]
            });

            Date.prototype.isValid = function () {
                // If the date object is invalid it
                // will return 'NaN' on getTime()
                // and NaN is never equal to itself.
                return this.getTime() === this.getTime();
            };

            $("#work_year, #work_month").on('change', function () {
                var yr = parseInt($("#work_year").find(":selected").val());
                var mm = parseInt($("#work_month").find(":selected").val());
                var firstDay = new Date(yr, mm, 1);

                if (firstDay.isValid()) {
                    $("#payment_period_fr").val(firstDay.toLocaleDateString('en-CA'));
                    var lastDay = new Date(yr, mm + 1, 0);
                    $("#payment_period_to").val(lastDay.toLocaleDateString('en-CA'));
                }
            });

            $(document).on('click', '#payslips_record, #facilities_record', function () {
                alert('todo - Salary file created successfully');
            });
            $(document).on("click", '.renew', function () {
                $('#formModalLabel').text('Renew Schedule');

                var par = $(this).parent().parent();
                $('#action_button').val('Add ' + par.children("td:nth-child(1)").html() + " schedule");

                $('#action').val('Add');
                $('#form_result').html('');

                $("#payroll_process_type_id").val($(this).data('refid'));

                $('#formModal').modal('show');
            });


            $('#frmInfo').on('submit', function (event) {
                event.preventDefault();
                var action_url = '';


                if ($('#action').val() == 'Add') {
                    action_url = "{{ route('addSchedule') }}";
                }

                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('PaymentPeriod.update') }}";
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
                            html = '<div class="alert alert-danger">';
                            for (var count = 0; count < data.errors.length; count++) {
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                        }
                        if (data.success) {
                            html = '<div class="alert alert-success">' + data.success + '</div>';
                            // $('#frmInfo')[0].reset();
                            // $('#titletable').DataTable().ajax.reload();
                            // location.reload()


                            var selected_tr = criteriaTable.row('#row-' + $("#payroll_process_type_id").val() + '');//remunerationTable.$('tr.classname');
                            /*
                            alert(JSON.stringify(selected_tr.data()));

                            var rowNode=selected_tr.node();
                            $( rowNode ).find('td').eq(0).html( data.alt_obj.remuneration_name );
                            $( rowNode ).find('td').eq(1).html( data.alt_obj.remuneration_type );
                            $( rowNode ).find('td').eq(2).html( data.alt_obj.epf_payable );
                            */
                            /*
                            var d=[data.alt_obj.remuneration_name, data.alt_obj.remuneration_type, data.alt_obj.epf_payable, data.alt_obj.alt_id];
                            */
                            var d = selected_tr.data();
                            d[1] = $("#payment_period_fr").val();//data.alt_obj.remuneration_type;
                            d[2] = $("#payment_period_to").val();//data.alt_obj.epf_payable;
                            criteriaTable.row(selected_tr).data(d).draw();


                        }
                        $('#form_result').html(html);
                    }
                });
            });

            $(document).on('click', '.edit', function () {
                var id = $(this).data('refid');
                $("#btn_next").prop("disabled", ($(this).parent().data('refopt') == 0) ? true : false);
                $('#form_result').html('');

                $("#frmInfo").removeClass('sect_bg');
                $("#frmMore").addClass('sect_bg');

                $.ajax({
                    url: "Remuneration/" + id + "/edit",
                    dataType: "json",
                    success: function (data) {
                        $('#remuneration_name').val(data.pre_obj.remuneration_name);
                        $('#remuneration_type').val(data.pre_obj.remuneration_type);
                        $('#epf_payable').val(data.pre_obj.epf_payable);
                        $('#hidden_id').val(id);
                        $('#formModalLabel').text('Edit Remuneration');
                        $('#action_button').val('Edit');
                        $('#action').val('Edit');
                        $('#formModal').modal('show');

                    }
                })/**/
            });


        });
    </script>

@endsection