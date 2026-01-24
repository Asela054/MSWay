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
                    <span>Salary Preperation</span>
                </h1>
            </div>
        </div>
    </div>
	<div class="container-fluid mt-2 p-0 p-2">
		<div class="card">
            <div class="card-body p-0 p-2">
				<div class="row">
					<div class="col-12 text-right">
						<div>
							<button type="button" name="find_employee" id="find_employee"
								class="btn btn-success btn-sm px-3"><i class="fa-light fa-search mr-2"></i>Search</button>
							<!--button type="button" name="create_record" id="create_record" class="btn btn-secondary btn-sm">Add</button-->
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<span id="lbl_duration" style="display:none; margin-right:auto; padding-left:10px;">
							<div class="alert alert-primary" role="alert">
								<span id="lbl_date_fr">&nbsp;</span> To <span id="lbl_date_to">&nbsp;</span>
								(<span id="lbl_payroll_name">&nbsp;</span>)
							</div>
						</span>
					</div>
					<div class="col-lg-12">
						<hr>
						<div class="center-block fix-width scroll-inner">
							<table class="table table-bordered table-striped table-sm small nowrap w-100" id="emptable" cellspacing="0">
								<thead>
									<tr>
										<th class="actlist_col">SELECT</th>
										<th>NAME</th>
										<th>OFFICE</th>
										<th>SALARY</th>
										<th>GROUP</th>
										<th class="actdrop_col">LOANS</th>
										<th class="actdrop_col">ADDITIONS</th>
										<th>WORK (W/O HOLIDAYS)</th>
										<th>WORK</th>
										<th>LEAVE</th>
										<th>NOPAY</th>
										<th>OT 1</th>
										<th>OT 2</th>
										<th>&nbsp;</th>
									</tr>
								</thead>
								<tbody class="">
								</tbody>
							</table>
						</div>

						<input type="hidden" name="payroll_profile_id" id="payroll_profile_id" value="" />
						<!-- edit loans -->
						<input type="hidden" name="payment_period_id" id="payment_period_id" value="" />
						<input type="hidden" name="payslip_process_type_id" id="payslip_process_type_id" value="" />
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="formModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<form id="frmSearch" method="post">
				{{ csrf_field() }}
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="formModalLabel"></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<span id="search_result"></span>
						<div class="form-row mb-1">
							<div class="col">
								<label class="font-weight-bolder small">Payroll type</label>
								<select name="payroll_process_type_id" id="payroll_process_type_id"
									class="form-control form-control-sm">
									<option value="" disabled="disabled" selected="selected">Please select</option>
									@foreach($payroll_process_type as $payroll)

									<option value="{{$payroll->id}}" data-totdays="{{$payroll->total_work_days}}">
										{{$payroll->process_name}}</option>
									@endforeach

								</select>
							</div>
							<div class="col">
								<label class="font-weight-bolder small">Location</label>
								<select name="location_filter_id" id="location_filter_id"
									class="custom-select custom-select-sm shipClass" style="">
									<option value="" disabled="disabled" selected="selected">Please Select</option>
									@foreach($branch as $branches)

									<option value="{{$branches->id}}">{{$branches->location}}</option>
									@endforeach

								</select>
							</div>
						</div>
                        <div class="form-row mb-1">
                        	<div class="col">
                            	<label class="font-weight-bolder small">Pay day</label>
                                <select name="employee_payday_id" id="employee_payday_id"
									class="custom-select custom-select-sm shipClass" style="" required >
									<option value="0" selected="selected">General</option>
									@foreach($paydays as $payday)

									<option value="{{$payday->id}}" disabled="disabled" data-payroll="{{$payday->payroll_process_type_id}}" style="display:none;" >{{$payday->payday_name}}</option>
									@endforeach

								</select>
                            </div>
                        </div>
						<!--div class="form-row mb-1">
								<div class="col">
									<label class="font-weight-bolder small">Working Period From</label>
									<input type="date" class="form-control form-control-sm" name="work_date_fr" id="work_date_fr" value="" />
								</div>
								<div class="col">
									<label class="font-weight-bolder small">To</label>
									<input type="date" class="form-control form-control-sm" name="work_date_to" id="work_date_to" value="" />
								</div>
						</div-->
						<div class="form-row">
							<div class="col-12 text-right">
								<hr>
								<input type="submit" name="action_button" id="action_button" class="btn btn-warning btn-sm px-3" value="Check Attendance" />
								<button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div id="loanModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="loanModalLabel">Loan Installments</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
				</div>
				<div class="modal-body">
					<span id="loan_result"></span>
					<div class="form-row">
						<div class="col">
							<label class="font-weight-bolder small">Employee</label>
							<input type="text" name="loan_modal_employee_name" id="loan_modal_employee_name" class="form-control form-control-sm" readonly="readonly" />
						</div>
					</div>
					<form id="frmInstallmentList" class="frm_link" method="post">
						{{ csrf_field() }}
						<div class="form-row mt-3">
							<div class="col-12" style="">
								<div class="center-block fix-width scroll-inner">
									<table class="table table-bordered table-striped table-sm small w-100 nowrap" id="loantable" width="100%"
										cellspacing="0">
										<thead>
											<tr>
												<th>LOAN NAME</th>
												<th>PAYMENTS</th>
												<th class="actlist_col">ACTIONS</th>
											</tr>
										</thead>
									</table>
								</div>
							</div>
							<div class="col-12 text-right mt-2">
								<button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal">Close</button>
							</div>
						</div>
					</form>
					<form id="frmInstallmentInfo" class="frm_info sect_bg" method="post">
						{{ csrf_field() }}
						<div class="form-row mb-1">
							<div class="col">
								<label class="font-weight-bolder small">Loan</label>
								<input type="text" name="loan_modal_loan_name" id="loan_modal_loan_name" class="form-control form-control-sm" readonly="readonly" />
							</div>
                            <div class="col">
                            	<label class="font-weight-bolder small">Collection Method</label>
                                <select name="loan_modal_loan_collect_opt" id="loan_modal_loan_collect_opt" class="form-control form-control-sm" >
                                    <option value="1">Salary</option>
                                    <option value="2">Prepaid</option>
                                </select>
                            </div>
						</div>
						<div class="form-row mb-1">
							<div class="col">
								<label class="font-weight-bolder small">Installment</label>
								<input type="text" name="pre_installment_amount" id="pre_installment_amount" class="form-control form-control-sm" readonly="readonly" />
							</div>
							<div class="col">
								<label class="font-weight-bolder small">Payment</label>
								<input type="text" name="new_installment_amount" id="new_installment_amount" class="form-control form-control-sm" />
							</div>
						</div>
                        <div class="form-row mb-1">
                        	<div class="col">
                            	<label class="font-weight-bolder small">Remarks</label>
                                <input type="text" name="loan_modal_collect_remarks" id="loan_modal_collect_remarks" class="form-control form-control-sm" maxlength="150" />
                            </div>
                        </div>
						<div class="form-row mt-3">
							<div class="col-12 text-right">
								<hr>
								<input type="submit" name="setup_button" id="setup_button" class="btn btn-warning btn-sm px-3" value="Edit" />
								<input type="button" id="" value="Back" class="btn btn-light btn_back btn-sm px-3" />
								<input type="hidden" name="hidden_loan_id" id="hidden_loan_id" value="" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div id="termModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="termModalLabel">Salary Additions</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
				</div>
				<div class="modal-body">
					<span id="term_result"></span>
					<div class="form-row">
						<div class="col">
							<label class="font-weight-bolder small">Employee</label>
							<input type="text" name="term_modal_employee_name" id="term_modal_employee_name" class="form-control form-control-sm" readonly="readonly" />
						</div>
					</div>
					<form id="frmAdditionList" class="frm_link" method="post">
						{{ csrf_field() }}
						<div class="form-row mt-3 mb-1">
							<div class="col-12" style="">
								<div class="center-block fix-width scroll-inner">
									<table class="table table-bordered table-striped table-sm small w-100 nowrap" id="termtable" cellspacing="0">
										<thead>
											<tr>
												<th>ADDITION NAME</th>
												<th>PAYMENT</th>
												<th class="actlist_col">ACTIONS</th>
											</tr>
										</thead>
									</table>
								</div>
							</div>
							<div class="col-12 text-right">
								<button type="button" class="btn btn-light btn-sm px-3 mt-3" data-dismiss="modal">Close</button>
							</div>
						</div>
					</form>
					<form id="frmAdditionInfo" class="frm_info sect_bg" method="post">
						{{ csrf_field() }}
						<div class="form-row mb-1">
							<div class="col">
								<label class="font-weight-bolder small">Addition name</label>
								<input type="text" name="term_modal_addition_name" id="term_modal_addition_name" class="form-control form-control-sm" readonly="readonly" />
							</div>
						</div>
						<div class="form-row mb-1">
							<div class="col">
								<label class="font-weight-bolder small">Previous Payment</label>
								<input type="text" name="pre_allocated_amount" id="pre_allocated_amount" class="form-control form-control-sm" readonly="readonly" />
							</div>
							<div class="col">
								<label class="font-weight-bolder small">New Payment</label>
								<input type="text" name="new_allocated_amount" id="new_allocated_amount" class="form-control form-control-sm" />
							</div>
						</div>
						<div class="form-row mt-3">
							<div class="col-12 text-right">
								<input type="submit" name="allocate_button" id="allocate_button" class="btn btn-warning btn-sm px-3" value="Edit" />
								<input type="button" id="btn_term_list" value="Back" class="btn btn-light btn_back btn-sm px-3" />
								<input type="hidden" name="hidden_term_id" id="hidden_term_id" value="" />
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
	function format(d) {
		// `d` is the original data object for the row
		return '<table cellpadding="5" cellspacing="0" border="0" width="100%" style="padding-left:50px;">' +
			'<tr>' +
			'<td width="150">Full name:</td>' +
			'<td>' + d.emp_first_name + '</td>' +
			'</tr>' +
			'<tr>' +
			'<td>Extension number:</td>' +
			'<td>' + d.location + '</td>' +
			'</tr>' +
			'</table>';
	}

	$(document).ready(function () {

		$('#payrollmenu').addClass('active');
		$('#payrollmenu_icon').addClass('active');
		$('#policymanagement').addClass('navbtnactive');

		var empTable = $("#emptable").DataTable({
			"columns": [{
					data: 'payslip_cancel'
				}, {
					data: 'emp_first_name'
				}, {
					data: 'location'
				}, {
					data: 'basic_salary'
				},
				{
					data: 'process_name'
				}, {
					data: 'loan_installments'
				},
				{
					data: 'term_payments'
				}, {
					data: 'opt_work'
				}, {
					data: 'emp_work'
				}, {
					data: 'emp_leave'
				}, {
					data: 'emp_nopay'
				},
				{
					data: 'emp_ot1'
				}, {
					data: 'emp_ot2'
				},
				{
					"className": 'details-control',
					"orderable": false,
					"data": null,
					"defaultContent": ''
				}
			],
			"order": [],
			"columnDefs": [{
				"targets": 0,
				"className": 'actlist_col',
				"orderable": false,
				render: function (data, type, row) {
					var check_str = ''; //(data==0)?' checked="checked"':'';
					var block_str =
					''; //($("#hidden_id").val()=='')?' disabled="disabled"':'';
					var delete_str = '';

					if (data == 0) {
						check_str = ' checked="checked"';
					}

					if (row.id != '') {
						delete_str =
							'<button type="button" class="btn btn-transparent-dark btn-sm opts_held actbtn_delete mt-1" data-payid="' +
							row.id + '" data-empid="' + row.payroll_profile_id +
							'"><i class="fal fa-trash-alt text-danger"></i></button>';
					}

					return '<div class="d-inline-flex align-items-center"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input freeze" id="check_' + row.payroll_profile_id + '" data-refid="' + row.id + '" data-refemp="' + row.payroll_profile_id + '"' + check_str + block_str + '><label class="custom-control-label m-0" for="check_' + row.payroll_profile_id + '">&nbsp;</label></div>' + delete_str + '</div>';
				}
			}, {
				"targets": 5,
				"className": 'actdrop_col',
				render: function (data, type, row) {
					return data +
						'<button class="btn btn-transparent-dark btn-sm loan_list" type="button" data-refemp="' +
						row.payroll_profile_id + '" data-refpay="' + row.emp_payslip_no +
						'">' + '<i class="fal fa-question-circle text-primary"></i>' + '</button>';
				}
			}, {
				"targets": 6,
				"className": 'actdrop_col',
				render: function (data, type, row) {
					return data +
						'<button class="btn btn-transparent-dark btn-sm term_list" type="button" data-refemp="' +
						row.payroll_profile_id + '" data-refpay="' + row.emp_payslip_no +
						'">' + '<i class="fal fa-question-circle text-primary"></i>' + '</button>';
				}
			}],
			"createdRow": function (row, data, dataIndex) {
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$(row).attr('id', 'row-' + data
				.payroll_profile_id); //$( row ).data( 'refid', data[3] );
			}
		});

		// Add event listener for opening and closing details
		$('#emptable tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = empTable.row(tr);

			if (row.child.isShown()) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			} else {
				// Open this row
				row.child(format(row.data())).show();
				tr.addClass('shown');
			}
		});

		var loanTable = $("#loantable").DataTable({
			"info": false,
			"searching": false,
			"paging": false,
			"columns": [{
				data: 'loan_name'
			}, {
				data: 'installment_value'
			}, {
				data: 'id'
			}],
			"columnDefs": [{
					"targets":1,
					render: function( data, type, row ){
						var pillstr=(row.collect_type_pill!='')?'<div class="badge badge-primary badge-pill" '+
								'style="margin-left:5px; line-height:14px; vertical-align:top;">'+row.collect_type_pill+'</div>':'';
						return data+pillstr;
					}
				},{
					"targets": 2,
					"orderable": false,
					"className": 'actlist_col text-right',
					render: function (data, type, row) {
						var btn_act = ' btn-primary loan_edit';
						var block_str = '';
	
						if (row.loan_complete == 1) {
							btn_act = ' btn-light';
							block_str = ' disabled="disabled"';
						}
	
						return '<button type="button" class="btn btn-primary btn-sm' +
							btn_act + '" data-refid="' + data + '"' + block_str +
							'><i class="fas fa-pencil-alt"></i></button>'+'&nbsp;'+
							'<button type="button" class="btn btn-success btn-sm inst_collect_type" data-refid="'+data+'" data-regval="1" title="Collect from Salary"><i class="fas fa-user-edit"></i></button>'+'&nbsp;'+
							'<button type="button" class="btn btn-success btn-sm inst_collect_type" data-refid="'+data+'" data-regval="2" title="Mark as Prepaid Installment"><i class="fas fa-comment-dollar"></i></button>';
				}
			}],
			"createdRow": function (row, data, dataIndex) {
				$(row).attr('id', 'row-' + data.id); //$( row ).data( 'refid', data[3] );
			}
		});

		var termTable = $("#termtable").DataTable({
			"info": false,
			"searching": false,
			"paging": false,
			"columns": [{
				data: 'term_name'
			}, {
				data: 'payment_value'
			}, {
				data: 'id'
			}],
			"columnDefs": [{
				"targets": 2,
				"orderable": false,
				"className": 'actlist_col text-right',
				render: function (data, type, row) {
					var btn_act = ' btn-primary term_edit';
					var block_str = '';

					if (row.value_group == -1) {
						btn_act = ' btn-light';
						block_str = ' disabled="disabled"';
					}

					return '<button type="button" class="btn btn-primary btn-sm' +
						btn_act + '" data-refid="' + data + '"' + block_str +
						'><i class="fas fa-pencil-alt"></i></button>';
				}
			}],
			"createdRow": function (row, data, dataIndex) {
				$(row).attr('id', 'row-' + data.id); //$( row ).data( 'refid', data[3] );
			}
		});

		var _token = $('#frmSearch input[name="_token"]').val();;

		function findEmployee() {
			$('#formModalLabel').text('Find Employee');
			//$('#action_button').val('Add');
			//$('#action').val('Add');
			$('#search_result').html('');

			$('#formModal').modal('show');
		}

		$('#find_employee').click(function () {
			findEmployee();
		});
		
		$("#payroll_process_type_id").on("change", function(){
			$('#employee_payday_id').val('0');//select General option
			$('#employee_payday_id option:not(:first-child)').prop("disabled", true);
			$('#employee_payday_id option:not(:first-child)').hide();
			$('#employee_payday_id option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').prop("disabled", false);
			$('#employee_payday_id option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').show();
		});

		$(".modal").on("shown.bs.modal", function () {
			var objinput = $(this).find('input[type="text"]:first-child');
			objinput.focus();
			objinput.select();
		});

		$("#frmSearch").on('submit', function (event) {
			event.preventDefault();

			$.ajax({
				url: "checkAttendance",
				method: 'POST',
				data: $(this).serialize(),
				dataType: "JSON",
				beforeSend: function () {
					//$('#find_employee').prop('disabled', true);
				},
				success: function (data) {
					//alert(JSON.stringify(data));
					var html = '';
					empTable.clear();

					if (data.errors) {
						html = '<div class="alert alert-danger">';
						for (var count = 0; count < data.errors.length; count++) {
							html += '<p>' + data.errors[count] + '</p>';
						}
						html += '</div>';
						$('#search_result').html(html);
					} else {
						empTable.rows.add(data.employee_detail);
						empTable.draw();
						$("#lbl_date_fr").html(data.work_date_fr);
						$("#lbl_date_to").html(data.work_date_to);
						$("#lbl_duration").show();
						$("#payment_period_id").val(data.payment_period_id);
						$("#payslip_process_type_id").val($("#payroll_process_type_id").find(
							":selected").val());
						$("#lbl_payroll_name").html($("#payroll_process_type_id").find(
							":selected").text());
						//$('#find_employee').prop('disabled', false);
						$('#formModal').modal('hide');
					}
				}
			})
		});

		$(document).on("click", ".loan_edit", function () {
			var id = $(this).data('refid');
			$('#loan_result').html('');

			$(".show .frm_info").removeClass('sect_bg');
			$(".show .frm_link").addClass('sect_bg');

			var par = $(this).parent().parent();
			$('#loan_modal_loan_name').val(par.children("td:nth-child(1)").html());

			var selected_tr = loanTable.row('#row-' + id);
			var d = selected_tr.data();

			$.ajax({
				url: "EmployeeLoanInstallment/" + id + "/edit",
				dataType: "json",
				success: function (data) {
					$('#pre_installment_amount').val(d
					.installment_value); //data.loan_obj.installment_value
					$('#new_installment_amount').val(data.loan_obj.installment_value);
					$('#hidden_loan_id').val(id); //update-loan-installment
					$('#loan_modal_loan_collect_opt').val(data.loan_obj.collect_opt);
					$('#loan_modal_collect_remarks').val(data.loan_obj.collect_remarks);
				}
			}) /**/
		});

		$(document).on("click", ".term_edit", function () {
			var id = $(this).data('refid');
			$('#term_result').html('');

			$(".show .frm_info").removeClass('sect_bg');
			$(".show .frm_link").addClass('sect_bg');

			var par = $(this).parent().parent();
			$('#term_modal_addition_name').val(par.children("td:nth-child(1)").html());

			$.ajax({
				url: "EmployeeTermPayment/" + id + "/edit",
				dataType: "json",
				success: function (data) {
					$('#pre_allocated_amount').val(data.term_obj.payment_amount);
					$('#new_allocated_amount').val(data.term_obj.payment_amount);
					$('#hidden_term_id').val(id); //update-loan-installment

				}
			}) /**/
		});

		$(document).on("click", ".loan_list", function () {
			var refemp = $(this).data('refemp'); //payroll-profile
			var refpay = $(this).data('refpay'); //payroll-payslip
			$("#payroll_profile_id").val(refemp); //update-employee-payslip-rows-td

			var selected_payslip = empTable.row('#row-' + $("#payroll_profile_id").val() + '');
			var d = selected_payslip.data();
			$('#loan_modal_employee_name').val(d.emp_first_name);

			loanList(refemp, refpay, selected_payslip, d);
		});
		
		$(document).on("click", ".inst_collect_type", function(){
			var id = $(this).data('refid');
			var regval = $(this).data('regval');
			
			$.ajax({
				url: "reviseCollectionMethod",
				method:"POST",
				data:{loan_regid:id, collect_type:regval, _token:_token},
				dataType:"json",
				success:function(data)
				{
					var html = '';
					if(data.errors){
						html = data.errors;
					}
					if(data.success){
						html = data.success;
						
						var selected_tr=loanTable.row('#row-'+id+'');//remunerationTable.$('tr.classname');
						
						var d=selected_tr.data();
						//alert(JSON.stringify(d));
						d.collect_type_pill=data.opt_pilltxt;
						loanTable.row(selected_tr).data(d).draw();
						
					}
					alert(html);
				}
			});
		});

		$(document).on("click", ".term_list", function () {
			var refemp = $(this).data('refemp'); //payroll-profile
			var refpay = $(this).data('refpay'); //payroll-payslip
			$("#payroll_profile_id").val(refemp); //update-employee-payslip-rows-td

			var selected_payslip = empTable.row('#row-' + $("#payroll_profile_id").val() + '');
			var d = selected_payslip.data();
			$('#term_modal_employee_name').val(d.emp_first_name);

			termList(refemp, refpay);
		});

		function loanList(refemp, refpay, paysliprow, payslipdata) {
			$.ajax({
				url: "checkLoanInstallment",
				method: 'POST',
				data: {
					payroll_profile_id: refemp,
					emp_payslip_no: refpay,
					_token: _token
				},
				dataType: "JSON",
				beforeSend: function () {
					//$('#find_employee').prop('disabled', true);
				},
				success: function (data) {
					//alert(JSON.stringify(data));
					loanTable.clear();
					loanTable.rows.add(data.loan_list);
					loanTable.draw();

					payslipdata.loan_installments = data.loan_sums;
					empTable.row(paysliprow).data(payslipdata).draw();
					//$('#find_employee').prop('disabled', false);
					$("#loanModal").modal("show");
				}
			})
		}

		function termList(refemp, refpay) {
			$.ajax({
				url: "checkTermPayment",
				method: 'POST',
				data: {
					payroll_profile_id: refemp,
					emp_payslip_no: refpay,
					filter_by: '1',
					_token: _token
				},
				dataType: "JSON",
				beforeSend: function () {
					//$('#find_employee').prop('disabled', true);
				},
				success: function (data) {
					//alert(JSON.stringify(data));
					termTable.clear();
					termTable.rows.add(data.employee_detail);
					termTable.draw();

					//$('#find_employee').prop('disabled', false);
					$("#termModal").modal("show");
				}
			})
		}

		$("#frmInstallmentInfo").on("submit", function (event) {
			event.preventDefault();
			var action_url = "{{ route('EmployeeLoanInstallment.update') }}";


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


						var selected_tr = loanTable.row('#row-' + $("#hidden_loan_id").val() +
							''); //remunerationTable.$('tr.classname');
						/*
						alert(JSON.stringify(selected_tr.data()));
						
						var rowNode=selected_tr.node();
						$( rowNode ).find('td').eq(0).html( data.alt_obj.remuneration_name );
						$( rowNode ).find('td').eq(1).html( data.alt_obj.remuneration_type );
						$( rowNode ).find('td').eq(2).html( data.alt_obj.epf_payable );
						*/
						var d = selected_tr.data();
						//alert(JSON.stringify(d));
						d.installment_value = $('#new_installment_amount').val();
						d.collect_type_pill = data.opt_pilltxt;
						loanTable.row(selected_tr).data(d).draw();

						var selected_payslip = empTable.row('#row-' + $("#payroll_profile_id")
							.val() + '');
						var d = selected_payslip.data();
						var diff = parseFloat($('#new_installment_amount').val()) - parseFloat(
							$('#pre_installment_amount').val()); //data.pre_installment_value;
						//console.log(diff);

						d.loan_installments = parseFloat(d.loan_installments) + diff;
						empTable.row(selected_payslip).data(d).draw();

						//going-back-to-loan-list
						$(".show .frm_info").addClass('sect_bg');
						$(".show .frm_link").removeClass('sect_bg');

					}
					$('#loan_result').html(html);
				}
			});
		});

		$("#frmAdditionInfo").on("submit", function (event) {
			event.preventDefault();
			var action_url = "{{ route('EmployeeTermPayment.update') }}";
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
						var selected_tr = termTable.row('#row-' + $("#hidden_term_id").val() +
							''); //remunerationTable.$('tr.classname');
						/*
						alert(JSON.stringify(selected_tr.data()));
						
						var rowNode=selected_tr.node();
						$( rowNode ).find('td').eq(0).html( data.alt_obj.remuneration_name );
						$( rowNode ).find('td').eq(1).html( data.alt_obj.remuneration_type );
						$( rowNode ).find('td').eq(2).html( data.alt_obj.epf_payable );
						*/
						var d = selected_tr.data();
						//alert(JSON.stringify(d));
						d.payment_value = $('#new_allocated_amount').val();
						termTable.row(selected_tr).data(d).draw();

						var selected_payslip = empTable.row('#row-' + $("#payroll_profile_id")
							.val() + '');
						var d = selected_payslip.data();
						var diff = parseFloat($('#new_allocated_amount').val()) - parseFloat($(
							'#pre_allocated_amount').val());
						d.term_payments = parseFloat(d.term_payments) + diff;
						empTable.row(selected_payslip).data(d).draw();

						//going-back-to-loan-list
						$(".show .frm_info").addClass('sect_bg');
						$(".show .frm_link").removeClass('sect_bg');

					}
					$('#term_result').html(html);
				}
			});
		});

		$(".btn_back").on("click", function () {
			$(".show .frm_info").addClass('sect_bg');
			$(".show .frm_link").removeClass('sect_bg');
		});


		$(".modal").on("shown.bs.modal", function (e) {
			if ($(this).find(".frm_link")) {
				$(".show .frm_info").addClass('sect_bg');
				$(".show .frm_link").removeClass('sect_bg');
			}
		});
		/*
		$(".modal").on("hide.bs.modal", function(e){
			$(this).removeClass('active');
		});
		*/

		$(document).on('click', '.freeze', function () {
			var _token = $('#frmSearch input[name="_token"]').val();
			var selected_tr = empTable.row('#row-' + $(this).data('refemp') +
			''); //alert(JSON.stringify(selected_tr.data()))
			freezePayslip($(this), selected_tr.data(), _token); //alert(selected_tr.data().emp_leave);//
		});

		function freezePayslip(payslip, workinfo, _token) {
			$.ajax({
				url: "freezePayslip",
				method: 'POST',
				data: {
					id: workinfo.id,
					payslip_cancel: ($(payslip).is(":checked") ? 0 : 1),
					payroll_profile_id: workinfo.payroll_profile_id,
					employee_id: workinfo.employee_id,
					emp_job_code: workinfo.emp_job_code,
					emp_status: workinfo.emp_status,
					emp_etfno: workinfo.emp_etfno,
					emp_payslip_no: workinfo.emp_payslip_no,
					pay_per_day: workinfo.pay_per_day,
					basic_salary: workinfo.basic_salary,
					day_salary: workinfo.day_salary,
					payment_period_id: $("#payment_period_id").val(),
					payment_period_fr: $("#lbl_date_fr").html(),
					payment_period_to: $("#lbl_date_to").html(),
					payroll_process_type_id: $("#payslip_process_type_id").val(),
					emp_work: (parseFloat(workinfo.emp_work) + parseFloat(workinfo.emp_leave)),
					opt_work: parseFloat(workinfo.opt_work),
					emp_nopay: parseFloat(workinfo.emp_nopay),
					_token: _token
				},
				dataType: "JSON",
				beforeSend: function () {
					$(payslip).prop('disabled', true);
				},
				success: function (data) {
					//alert(JSON.stringify(data));
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

						$(payslip).prop('checked', false);
						$(payslip).prop('disabled', false);
                    }
					else{
						if (data.result == 'error') {
							$(payslip).prop('checked', !$(payslip).prop('checked'));
							Swal.fire({
								icon: 'error',
								title: 'Something wrong.',
								text: 'Payslip cannot be processed at the moment. ' + data.msg
							});
							// alert('Something wrong. Payslip cannot be processed at the moment\r\n' + data.msg);
						} else {
							var selected_tr = empTable.row('#row-' + workinfo.payroll_profile_id + '');
							workinfo.id = data.employee_payslip_id;
							workinfo.payslip_cancel = ($(payslip).is(":checked") ? 0 : 1);
							/*
							$(payslip).prop('disabled', false);
							$(payslip).data('refid', data.employee_payslip_id);
							*/
							empTable.row(selected_tr).data(workinfo).draw(false);

							/*
							var selected_tr=empTable.row('#row-'+$(loanref).data('refloan')+'');
							var rowNode=selected_tr.node();
							var new_val=parseFloat($( rowNode ).find('td').eq(5).html())+data.payment_value;
							
							$( rowNode ).find('td').eq(5).html( new_val );
							*/
						}
					}
				}
			});
		}

		$(document).on('click', '.actbtn_delete', async function () {
			var _token = $('#frmSearch input[name="_token"]').val();
			var empid = $(this).data('empid'); //emp-id
			var delete_payslip = empTable.row('#row-' + empid + '');
			var paidinfo = delete_payslip.data();

			var r = await Otherconfirmation("You want to delete this "+paidinfo.emp_first_name+"? ");
            if (r == true) {
				// var confres = confirm("Are you sure you want to delete " + paidinfo.emp_first_name);

				// if (confres) {
					deletePayslip($(this), paidinfo, _token);
				// }
			}
		});

		function deletePayslip(payslip, paidinfo, _token) {
			$.ajax({
				url: "deletePayslip",
				method: 'POST',
				data: {
					id: paidinfo.id,
					emp_payslip_no: paidinfo.emp_payslip_no,
					_token: _token
				},
				dataType: "JSON",
				beforeSend: function () {
					$(payslip).prop('disabled', true);
				},
				success: function (data) {
					//alert(JSON.stringify(data));
					if (data.result == 'error') {
						// alert('Something wrong. Payslip cannot be processed at the moment\r\n' + data
						// 	.msg);
						Swal.fire({
							icon: 'error',
							title: 'Something wrong.',
							text: 'Payslip cannot be processed at the moment. ' + data.msg
						});
					} else {
						var selected_tr = empTable.row('#row-' + paidinfo.payroll_profile_id + '');
						paidinfo.id = data.employee_payslip_id; //''
						paidinfo.payslip_cancel = "1"; //($(payslip).is(":checked")?0:1);

						empTable.row(selected_tr).data(paidinfo).draw(false);


					}
				}
			});
		}
	});
</script>

@endsection