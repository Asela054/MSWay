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
                    <span>Salary Schedule</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-bordered table-striped table-sm small nowrap w-100" id="titletable" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>TYPE</th>
                                        <th>PAY DAY</th>
                                        <th>FROM</th>
                                        <th>TO</th>
                                        <th>DURATION</th>
                                        <th class="actlist_col">ACTIONS</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($payroll_process_type as $payrolltype)

                                    <tr id="row-{{$payrolltype->payroll_process_type_id}}">
                                        <td>{{$payrolltype->process_name}}</td>
                                        <td>{{$payrolltype->payday_name}}</td>
                                        <td>{{$payrolltype->payment_period_fr}}</td>
                                        <td>{{$payrolltype->payment_period_to}}</td>
                                        <td>{{$payrolltype->payment_total_days_hours}}</td>
                                        <td class="actlist_col">
                                            <button class="btn btn-primary btn-sm renew"
                                                type="button"
                                                data-refid="{{$payrolltype->payroll_process_type_id}}">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                            <span class="more_info_schedules details-control"
                                                    style="font-size:20px;top:4px; color:rgba(0,100,200,0.8)" 
                                                    data-refid="{{$payrolltype->payroll_process_type_id}}">
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12" style="display:none;">
                        <div class="card mt-3">
                            <div class="card-body p-3">
                                Salary Banking - Send salary details to bank accounts of employees
                                <hr>
                                <button type="button" name="payslips_record" id="payslips_record" class="btn btn-success btn-sm px-3">Payslips</button>
                                <button type="button" name="facilities_record" id="facilities_record" class="btn btn-success btn-sm px-3">Facilities </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="formModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <form id="frmInfo" class="" method="post">
                        {{ csrf_field() }}
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="font-weight-bolder small">Year</label>
                                <select name="work_year" id="work_year" class="form-control form-control-sm">
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
                            <div class="col">
                                <label class="font-weight-bolder small">Month</label>
                                <select name="work_month" id="work_month" class="form-control form-control-sm">
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
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="font-weight-bolder small">From</label>
                                <input type="date" name="payment_period_fr" id="payment_period_fr"
                                    class="form-control form-control-sm" />
                            </div>
                            <div class="col">
                                <label class="font-weight-bolder small">To</label>
                                <input type="date" name="payment_period_to" id="payment_period_to"
                                    class="form-control form-control-sm" />
                            </div>
                        </div>
                        <div class="row mb-1">
                        	<div class="form-group col-md-4">
                            	<label class="font-weight-bolder small">Pay day</label>
                                <div class="col">
                                    <select name="employee_payday_id" id="employee_payday_id" class="form-control form-control-sm" required>
                                        <option value="0" selected="selected">General</option>
                                        @foreach($paydays as $payday)
                                        
                                        <option value="{{$payday->id}}" disabled="disabled" data-payroll="{{$payday->payroll_process_type_id}}" style="display:none;" >{{$payday->payday_name}}</option>
                                        @endforeach
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                            	<label class="font-weight-bolder small">Total days</label>
                                <div class="col">
                                    <input type="number" name="work_period_total_days" id="work_period_total_days"
                                           step="0.1" class="form-control form-control-sm"/>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                            	<label class="font-weight-bolder small">Total hours</label>
                                <div class="col">
                                    <input type="number" name="work_period_total_hours" id="work_period_total_hours"
                                           step="0.1" class="form-control form-control-sm"/>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                        	<hr style="width: 100%;" />
                            <div class="form-group col">
                            	<label class="font-weight-bolder small">Advance Payment Date:</label>
                                <div class="col">
                                    <input type="date" name="advance_payment_date" id="advance_payment_date"
                                           class="form-control form-control-sm" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <hr>
                                <input type="hidden" name="action" id="action" value="Edit" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="payroll_process_type_id" id="payroll_process_type_id" value="" />
                                <input type="submit" name="action_button" id="action_button" class="btn btn-warning btn-sm px-3" value="Edit" />
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
(function() {
	var cssMain = document.createElement('link');
	cssMain.href = "{{ asset('/public/payroll/css/paymentPeriod_list.css') }}";
	cssMain.rel = 'stylesheet';
	cssMain.type = 'text/css';
	document.getElementsByTagName('head')[0].appendChild(cssMain);
	/*
	
	*/
})();
</script>
<script>
    function format(d){
		var application_tr = '<th>Pay day</th>'+
								'<th>From</th>'+'<th>To</th>'+
								'<th>Duration</th>';
		// `d` is the original data object for the row
		$.each(d, function(index, obj){
			application_tr += '<tr class="">'+
						'<td>'+obj.payday_name+'</td>'+
						'<td>'+obj.payment_period_fr+'</td>'+
						'<td>'+obj.payment_period_to+'</td>'+
						'<td>'+obj.payment_total_days_hours+'</td>'+
					'</tr>';
		});
		return '<table cellpadding="5" cellspacing="0" border="0" width="100%" align="right" style="padding-left:50px;">'+
			application_tr+
		'</table>';
	}
	
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
                "targets": 5,
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
            // alert('todo - Salary file created successfully');
            Swal.fire({
				icon: 'question',
				title: 'todo',
				text: 'Salary file created successfully'
			});
        });
        $(document).on("click", '.renew', function () {
            $('#formModalLabel').text('Renew Schedule');

            var par = $(this).parent().parent();
            $('#action_button').val('Add ' + par.children("td:nth-child(1)").html() + " schedule");

            $('#action').val('Add');
            $('#form_result').html('');

            $("#payroll_process_type_id").val($(this).data('refid'));
			
			$('#employee_payday_id').val('0');//select General option
			$('#employee_payday_id option:not(:first-child)').prop("disabled", true);
			$('#employee_payday_id option:not(:first-child)').hide();
			$('#employee_payday_id option[data-payroll="'+$(this).data("refid")+'"]').prop("disabled", false);
			$('#employee_payday_id option[data-payroll="'+$(this).data("refid")+'"]').show();
			
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
                        // html = '<div class="alert alert-success">' + data.success +
                        // '</div>';
                        // $('#frmInfo')[0].reset();
                        // $('#titletable').DataTable().ajax.reload();
                        // location.reload()


                        var selected_tr = criteriaTable.row('#row-' + $(
                            "#payroll_process_type_id").val() +
                        ''); //remunerationTable.$('tr.classname');
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
                        d[1] = $("#employee_payday_id").find(":selected").text();//'set-payday-txt';
                        d[2] = $("#payment_period_fr").val();//data.alt_obj.remuneration_type;
						d[3] = $("#payment_period_to").val();//data.alt_obj.epf_payable;
						var period_tot_days=($("#work_period_total_days").val()!='')?$("#work_period_total_days").val():0;
						var period_tot_hrs=($("#work_period_total_hours").val()!='')?$("#work_period_total_hours").val():0;
						d[4] = period_tot_days + 'days, ' + period_tot_hrs + 'Hrs';
                        criteriaTable.row(selected_tr).data(d).draw();


                    }
                    // $('#form_result').html(html);
                }
            });
        });
		
		$('#titletable tbody').on('click', 'td .details-control', function () {
			var tr = $(this).closest('tr');
			var row = criteriaTable.row( tr );
			
			//var d = row.data();
			//var id = d.profile_id;//alert(d.location);
			var id = $(this).data('refid');

			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				$.ajax({
				   url :"LatestPayPeriods/"+id+"/review",
				   dataType:"json",
				   success:function(data){
					row.child( format(data.paydays) ).show();
					tr.addClass('shown');
				   }
				})
			}
		} );

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
            }) /**/
        });
    });
</script>

@endsection