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
                    <span>Salary Sheet - Bank Slip</span>
                </h1>
            </div>
        </div>
    </div>
	<div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
				<form id="frmExport" method="post" target="_blank" action="{{ url('DownloadSalarySheet') }}">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-12 text-right">
							<button type="button" name="find_employee" id="find_employee" class="btn btn-warning btn-sm px-3"><i class="fal fa-search mr-2"></i>Search</button>
							<button type="submit" name="print_record" id="print_record" disabled="disabled" class="btn btn-secondary btn-sm btn-light" style="display:none;">Download</button>
							<a href="#" class="btn btn-danger btn-sm btn-light disabled px-3" type="button" id="slip"><i class="fal fa-file-pdf mr-2"></i> Bank Report</a>
						</div>
						<div class="col-12">
                            <span id="lbl_duration" style="display:none; margin-right:auto; padding-left:10px;">
                                <div class="alert alert-primary" role="alert">
                                    <span id="lbl_date_fr">&nbsp;</span> To <span id="lbl_date_to">&nbsp;</span>
									(<span id="lbl_payroll_name">&nbsp;</span>)
                                </div>
                            </span>
                        </div>
						<div class="col-12">
							<hr>
							<div id="divPrint" class="center-block fix-width scroll-inner">
								<div id="tbl_all" class="table-responsive">
									<table class="table table-bordered table-striped table-sm small w-100 nowrap" id="emptable" cellspacing="0">
										<thead>
											<tr>
												<th>REF NO</th>
												<th>NAME (EMPLOYEE)</th>
												<th>BANK-BR-CODE</th>
												<th>ACCOUNT NO</th>
												<th>TRX CODE</th>
												<th>AMOUNT (SALARY)</th>
												<th>DATE</th>
											</tr>
										</thead>

										<tbody class="">
										</tbody>

									</table>
								</div>
								<div id="tbl_etf" class="table-responsive" style="display:none;">
									<table class="table table-bordered table-striped table-sm small w-100 nowrap" id="emp_etftable" cellspacing="0">
										<thead>
											<tr>
												<th style="width:70px;">CODE</th>
												<th>EMPLOYER NO.</th>
												<th>MEMBER NO.</th>
												<th>INITIALS</th>
												<th>LAST NAME</th>
												<th>NIC NO.</th>
												<th>PERIOD FROM</th>
												<th>PERIOD TO</th>
												<th>ETF-3</th>
											</tr>
										</thead>

										<tbody class="">
										</tbody>

									</table>
								</div>
								<div id="tbl_epf" class="table-responsive" style="display:none;">
									<table class="table table-bordered table-striped table-sm small w-100 nowrap" id="emp_epftable" cellspacing="0">
										<thead>
											<tr>
												<th style="width:300px;">NIC NO.</th>
												<th>LAST NAME</th>
												<th>INITIALS</th>
												<th>MEMBER NO.</th>
												<th>TOTAL CONT.</th>
												<th>EPF-12</th>
												<th>EPF-8</th>
												<th>TOTAL EARN</th>
												<th>MEMBER STATUS</th>
												<th>ZONE CODE</th>
												<th>EMPLOYER NO.</th>
												<th>CONT. PERIOD</th>
												<th>SUBMIT NO.</th>
												<th>DAYS WORKED</th>
												<th>OCCU. GRADE</th>
											</tr>
										</thead>

										<tbody class="">
										</tbody>

									</table>
								</div>
							</div>

							<input type="hidden" name="payroll_profile_id" id="payroll_profile_id" value="" />
							<!-- edit loans -->
							<input type="hidden" name="payment_period_id" id="payment_period_id" value="" />
							<input type="hidden" name="payslip_process_type_id" id="payslip_process_type_id"
								value="" />

							<input type="hidden" name="rpt_period_id" id="rpt_period_id" value="" />
							<input type="hidden" name="rpt_info" id="rpt_info" value="-" />
							<input type="hidden" name="rpt_payroll_id" id="rpt_payroll_id" value="" />
							<input type="hidden" name="rpt_location_id" id="rpt_location_id" value="" />
							<input type="hidden" name="rpt_dept_id" id="rpt_dept_id" value="" />
							<input type="hidden" name="rpt_dept_name" id="rpt_dept_name" value="" />

							<input type="hidden" name="rpt_total_amount" id="rpt_total_amount" value="" />
							<input type="hidden" name="rpt_date_e" id="rpt_date_e" value="" />
							<input type="hidden" name="rpt_hash_total" id="rpt_hash_total" value="" />
							<input type="hidden" name="rpt_trx_code" id="rpt_trx_code" value="" />
							<input type="hidden" name="rpt_no_of_transactions" id="rpt_no_of_transactions"
								value="" />

							<input type="hidden" name="txt_acc_name" id="txt_acc_name" value="" />
							<input type="hidden" name="txt_acc_number" id="txt_acc_number" value="" />
							<input type="hidden" name="txt_acc_br" id="txt_acc_br" value="" />

							<input type="hidden" name="etf_summary_str" id="etf_summary_str" value="" />
						</div>
					</div>
				</form>
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
								<label class="font-weight-bolder small">Branch</label>
								<select name="location_filter_id" id="location_filter_id"
									class="custom-select custom-select-sm shipClass nest_head" style="" data-findnest="deptnest">
									<option value="-1" selected="selected" data-regcode="">Please Select</option>
									@foreach($branch as $branches)

									<option value="{{$branches->id}}" data-regcode="{{$branches->id}}">{{$branches->location}}</option>
									@endforeach
								</select>
							</div>
							<div class="col">
								<label class="font-weight-bolder small">Department</label>
								<select name="department_filter_id" id="department_filter_id" class="custom-select custom-select-sm"
									style="" data-nestname="deptnest">
									<option value="" selected="selected">Please Select</option>
									@foreach($department as $section)

									<option class="nestopt d-none" value="{{$section->id}}"
										data-nestcode="{{$section->company_id}}" data-sectcode="{{$section->id}}">
										{{$section->name}}</option>
									@endforeach

								</select>
							</div>
						</div>
						<div class="form-row mb-1" id="opt_payslip">
							<div class="col">
								<label class="font-weight-bolder small">Payroll type</label>
								<select name="payroll_process_type_id" id="payroll_process_type_id"
									class="form-control form-control-sm">
									<option value="" disabled="disabled" selected="selected">Please select</option>
									@foreach($payroll_process_type as $payroll)

									<option value="{{$payroll->id}}" data-totdays="{{$payroll->total_work_days}}">{{$payroll->process_name}}</option>
									@endforeach

								</select>
							</div>
							<div class="col">
								<label class="font-weight-bolder small">Working Period</label>
								<select name="period_filter_id" id="period_filter_id" class="custom-select custom-select-sm"
									style="">
									<option value="" disabled="disabled" selected="selected">Please Select</option>
									@foreach($payment_period as $schedule)

									<option value="{{$schedule->id}}" disabled="disabled"
										data-payroll="{{$schedule->payroll_process_type_id}}" style="display:none;">
										{{$schedule->payment_period_fr}} to {{$schedule->payment_period_to}}
									</option>
									@endforeach

								</select>
							</div>
							<!--div class="form-group col-md-6">
								<label class="control-label col">To</label>
								<div class="col">
									<input type="date" class="form-control" name="work_date_to" id="work_date_to" value="" />
								</div>
							</div-->
						</div>
						<div class="form-row mb-1">
							<div class="col">
								<label class="font-weight-bolder small">Salary Bank Date:</label>
								<input type="date" class="form-control form-control-sm" name="salary_bank_date" id="salary_bank_date" value="" />
							</div>
							<div class="col">
								<label class="font-weight-bolder small">Submission No :</label>
								<!-- style="padding-left:25px;" -->
								<input type="number" class="form-control form-control-sm" name="salary_submit_attemptno" id="salary_submit_attemptno" value="1" />
							</div>
							<div class="col">
								<label class="font-weight-bolder small">Work Days :</label>
								<!-- style="padding-left:25px;" -->
								<input type="number" class="form-control form-control-sm" name="pay_schedule_days" id="pay_schedule_days" value="26" />
							</div>
						</div>
						<div class="form-row">
							<div class="col-12">
								<label class="font-weight-bolder small">Slip Type</label><br>
								<div class="custom-control custom-radio custom-control-inline">
									<input type="radio" id="opt_rpt_d" name="opt_rpt" class="custom-control-input" value="4">
									<label class="custom-control-label small" for="opt_rpt_d" style="padding-right:15px;">Advance</label>
								</div>
                                <div class="custom-control custom-radio custom-control-inline">
									<input type="radio" id="opt_rpt_a" name="opt_rpt" checked="checked" class="custom-control-input" value="1">
									<label class="custom-control-label small" for="opt_rpt_a">Salary</label>
								</div>
								<div class="custom-control custom-radio custom-control-inline">
									<input type="radio" id="opt_rpt_b" name="opt_rpt" class="custom-control-input" value="2">
									<label class="custom-control-label small" for="opt_rpt_b">EPF</label>
								</div>
								<div class="custom-control custom-radio custom-control-inline">
									<input type="radio" id="opt_rpt_c" name="opt_rpt" class="custom-control-input" value="3">
									<label class="custom-control-label small" for="opt_rpt_c">ETF</label>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-12 text-right">
								<hr>
								<input type="submit" name="action_button" id="action_button" class="btn btn-warning btn-sm px-3" value="View Payslips" />
								<button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>


	<div id="loanModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">

			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="loanModalLabel">Loan Installments</h5>
					<button class="close" type="button" data-dismiss="modal" aria-label="Close"><span
							class="btn-sm btn-danger" aria-hidden="true">X</span></button>
				</div>
				<div class="modal-body">
					<span id="loan_result"></span>
					<form id="frmInstallmentList" class="frm_link" method="post">
						{{ csrf_field() }}
						<div class="">
							<div class="" style="">
								<div class="datatable table-responsive" style="margin-top:10px;">
									<table class="table table-bordered table-hover" id="loantable" width="100%"
										cellspacing="0">
										<thead>
											<tr>
												<th>Loan Name</th>
												<th>Payment</th>
												<th class="actlist_col">Actions</th>
											</tr>
										</thead>


									</table>
								</div>

							</div>
							<div class="" align="right" style="padding:5px; border-top:none;">
								<button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
							</div>
						</div>
					</form>
					<form id="frmInstallmentInfo" class="frm_info sect_bg" method="post">
						{{ csrf_field() }}
						<div class="">
							<div class="" style="">
								<div class="row">
									<div class="form-group col-md-6">
										<label class="control-label col">Installment</label>
										<div class="col">
											<input type="text" name="pre_installment_amount" id="pre_installment_amount"
												class="form-control" readonly="readonly" />
										</div>
									</div>
									<div class="form-group col-md-6">
										<label class="control-label col">Payment</label>
										<div class="col">
											<input type="text" name="new_installment_amount" id="new_installment_amount"
												class="form-control" />
										</div>
									</div>
								</div>
							</div>
							<div class="" align="right" style="padding:5px; border-top:none;">
								<input type="submit" name="setup_button" id="setup_button" class="btn btn-warning"
									value="Edit" />
								<input type="button" id="" value="Back" class="btn btn-light btn_back" />
								<input type="hidden" name="hidden_loan_id" id="hidden_loan_id" value="" />
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
		$('#payrollreport').addClass('navbtnactive');
		var currentBankFormat = '';

		var empTable = $("#emptable").DataTable({
			"columns": [{
					data: 'ref_no'
				}, {
					data: 'emp_name'
				}, {
					data: 'bank_br_code'
				},
				{
					data: 'account_no'
				}, {
					data: 'trx_code'
				},
				{
					data: 'amount'
				}, {
					data: 'date'
				}
			],
			"iDisplayLength": -1, //100,
			"aLengthMenu": [
				[-1],
				["All"]
			],
			"order": [],

			"createdRow": function (row, data, dataIndex) {
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$(row).attr('id', 'row-' + data.id); //$( row ).data( 'refid', data[3] );
			}
		});

		var emp_epfTable = $("#emp_epftable").DataTable({
			"columns": [{
				data: 'emp_nicnum'
			}, {
				data: 'emp_last_name'
			}, {
				data: 'emp_first_name'
			}, {
				data: 'emp_epfno'
			}, {
				data: 'tot_epf'
			}, {
				data: 'EPF12'
			}, {
				data: 'EPF8'
			}, {
				data: 'tot_fortax'
			}, {
				data: 'member_status'
			}, {
				data: 'zone_code'
			}, {
				data: 'employer_num'
			}, {
				data: 'cont_period'
			}, {
				data: 'submit_num'
			}, {
				data: 'emp_workdays'
			}, {
				data: 'occu_grade'
			}],
			"iDisplayLength": -1, //100,
			"aLengthMenu": [
				[-1],
				["All"]
			],
			"order": [],

			"createdRow": function (row, data, dataIndex) {
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$(row).attr('id', 'row-' + data.id); //$( row ).data( 'refid', data[3] );
			}
		});

		var emp_etfTable = $("#emp_etftable").DataTable({
			"columns": [{
				data: 'etf_rec_code'
			}, {
				data: 'employer_num'
			}, {
				data: 'emp_etfno'
			}, {
				data: 'emp_first_name'
			}, {
				data: 'emp_last_name'
			}, {
				data: 'emp_nicnum'
			}, {
				data: 'cont_period_fr'
			}, {
				data: 'cont_period_to'
			}, {
				data: 'ETF3'
			}],
			"iDisplayLength": -1, //100,
			"aLengthMenu": [
				[-1],
				["All"]
			],
			"order": [],

			"createdRow": function (row, data, dataIndex) {
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$(row).attr('id', 'row-' + data.id); //$( row ).data( 'refid', data[3] );
			}
		});
		//var loanTable=$("#loantable").DataTable();

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

		// $('#slip').click(function () {
		// 	if ($("#tbl_all").is(':visible')) {
		// 		var fromdate = $('#rpt_info').val();
		// 		var retContent = [];
		// 		var retString = '';

		// 		//add company name
		// 		var txtaccname = $("#txt_acc_name").val();
		// 		let company_name = txtaccname.substring(0,
		// 		20); //'Multi Offset (Pvt) Ltd'.substring(0, 20);//(0, 19);
		// 		let amount = $('#rpt_total_amount').val(); //console.log('amount='+amount);
		// 		let acc_no = $("#txt_acc_number").val(); //'000000000000';
		// 		let date = $('#rpt_date_e').val(); //console.log('date='+date);
		// 		let hash_total = $('#rpt_hash_total').val(); //console.log('hash_total='+hash_total);
		// 		let no_of_transactions = $('#rpt_no_of_transactions').val(); //console.log('no_of_transactions='+no_of_transactions);
		// 		let br_code = $("#txt_acc_br").val(); //'0000000';//'xxxxxxx';
		// 		let trans_code = '223'; //$('#rpt_trx_code').val();console.log('trans_code='+trans_code);

		// 		retContent.push(company_name + amount + acc_no + date + hash_total + no_of_transactions + br_code + trans_code);

		// 		$('#emptable tbody tr').each(function (e, elem) {
		// 			var elemText = [];
		// 			$(elem).children('td').each(function (childIdx, childElem) {
		// 				elemText.push($(childElem).text());
		// 			});
		// 			retContent.push(`${elemText.join('')}` + 'SALARY');
		// 		});

		// 		let counter = '';
		// 		for (let a = 0; a < 80; a++) {
		// 			counter += '0';
		// 		}
		// 		retContent.push(counter);

		// 		retString = retContent.join('\r\n');

		// 		var file = new Blob([retString], {
		// 			type: 'text/plain'
		// 		});
		// 		var btn = $('#slip');
		// 		btn.attr("href", URL.createObjectURL(file));
		// 		btn.prop("download", fromdate + ' - Report.txt');
		// 	} else if ($("#tbl_epf").is(':visible')) {
		// 		var fromdate = $('#rpt_info').val();
		// 		var retContent = [];
		// 		var retString = '';

		// 		$('#emp_epftable tbody tr').each(function (e, elem) {
		// 			var elemText = [];
		// 			$(elem).children('td').each(function (childIdx, childElem) {
		// 				elemText.push($(childElem).text());
		// 			});
		// 			retContent.push(`${elemText.join('')}`);
		// 		});

		// 		retString = retContent.join('\r\n');

		// 		var file = new Blob([retString], {
		// 			type: 'text/plain'
		// 		});
		// 		var btn = $('#slip');
		// 		btn.attr("href", URL.createObjectURL(file));
		// 		btn.prop("download", fromdate + ' - Epf.txt');

		// 	} else if ($("#tbl_etf").is(':visible')) {
		// 		var fromdate = $('#rpt_info').val();
		// 		var retContent = [];
		// 		var retString = '';

		// 		$('#emp_etftable tbody tr').each(function (e, elem) {
		// 			var elemText = [];
		// 			$(elem).children('td').each(function (childIdx, childElem) {
		// 				elemText.push($(childElem).text());
		// 			});
		// 			retContent.push(`${elemText.join('')}`);
		// 		});

		// 		retContent.push($('#etf_summary_str').val());

		// 		retString = retContent.join('\r\n');

		// 		var file = new Blob([retString], {
		// 			type: 'text/plain'
		// 		});
		// 		var btn = $('#slip');
		// 		btn.attr("href", URL.createObjectURL(file));
		// 		btn.prop("download", fromdate + ' - Etf.txt');
		// 	}
		// });
		$('#slip').click(function () {
			var fromdate   = $('#rpt_info').val();
			var retContent = [];
			var retString  = '';
			var btn        = $('#slip');

			if ($("#tbl_all").is(':visible')) {
				// ── Salary / Advance file ─────────────────────────────

				switch (currentBankFormat) {

					// ══════════════════════════════════════════════════
					case 'BOC':
					// ══════════════════════════════════════════════════
					// BOC format: one plain-text row per employee.
					// Fields: RecType BankCode AccNo Name TrxCode Flag Amount
					//         Currency PayBank PayBranch EmpBranch Company Ref Desc Date EndFlag
					// (No header record, no footer record — BOC processes the list directly)
					empTable.rows().every(function () {
						var d = this.data();
						if (!d || d.bank_format !== 'BOC') return;

						var line =
							d.boc_rec_type   +   // 0
							d.boc_bank_code  +   // employee bank code (4 digits)
							d.boc_acc_no     +   // account no (variable, no leading zeros)
							'\t'             +   // TAB separator (as seen in BOC sample)
							d.boc_emp_name   +   // 20-char padded name
							d.boc_trx_code   +   // 23
							d.boc_flag       +   // 0
							d.boc_amount     +   // integer rupees
							d.boc_currency   +   // SLR
							d.boc_pay_bank   +   // 7010 (company debit bank)
							d.boc_pay_branch +   // company branch code (3)
							d.boc_other_br   +   // employee branch code (3)
							d.boc_company    +   // company name (13 chars)
							d.boc_ref        +   // reference (blank or custom)
							d.boc_desc       +   // SAL MMM YY
							d.boc_date       +   // YYMMDD
							d.boc_end_flag;      // 0

						retContent.push(line);
					});

					retString = retContent.join('\r\n');
					btn.attr("href", URL.createObjectURL(new Blob([retString], { type: 'text/plain' })));
					btn.prop("download", fromdate + ' - BOC_Salary.txt');
					break;

					// ══════════════════════════════════════════════════
					case 'HNB':
					// ══════════════════════════════════════════════════
					// HNB SLIPS format (80-byte fixed records):
					// 1 control record + N detail records + 1 all-zero trailer

					// Control record (80 bytes):
					// AccountName(20) Amount(11) AccNo(12) Date(6) HashTotal(14)
					// NoOfTrans(5) BankBrCode(7) TrxCode(3=223) CtrlInfo(2 spaces)
					var company_name = $("#txt_acc_name").val().substring(0, 20);
					// pad to exactly 20 if shorter
					while (company_name.length < 20) company_name += ' ';

					retContent.push(
						company_name +
						$('#rpt_total_amount').val()       +  // 11 digits
						$("#txt_acc_number").val()         +  // 12 digits
						$('#rpt_date_e').val()             +  // 6 digits YYMMDD
						$('#rpt_hash_total').val()         +  // 14 digits
						$('#rpt_no_of_transactions').val() +  // 5 digits
						$("#txt_acc_br").val()             +  // 7 digits bank+branch
						'223'                             +  // control trx code
						'  '                                 // 2 blank control info chars
					);

					// Detail records (80 bytes each):
					// RefNo(8) AccName(20) BankBrCode(7) AccNo(12) TrxCode(3=023) Amount(11) Date(6) Comments(13)
					empTable.rows().every(function () {
						var d = this.data();
						if (!d || d.bank_format !== 'HNB') return;

						var comments = 'SALARY       '; // 13 chars (pad to 13)
						comments = (comments + '             ').substring(0, 13);

						var line =
							d.ref_no       +  // 8
							d.emp_name     +  // 20
							d.bank_br_code +  // 7
							d.account_no   +  // 12
							d.trx_code     +  // 3  (023)
							d.amount       +  // 11
							d.date         +  // 6
							comments;         // 13

						retContent.push(line);
					});

					// Trailer record – 80 zeros
					retContent.push('0'.repeat(80));

					retString = retContent.join('\r\n');
					btn.attr("href", URL.createObjectURL(new Blob([retString], { type: 'text/plain' })));
					btn.prop("download", fromdate + ' - HNB_SLIPS_Salary.txt');
					break;

					// ══════════════════════════════════════════════════
					case 'SAMPATH':
					// ══════════════════════════════════════════════════
					// TODO: build Sampath file format once spec is received.
					// empTable.rows().every(function () { var d = this.data(); ... });
					alert('Sampath bank file format not yet implemented. Please contact IT.');
					return; // prevent download

					// ══════════════════════════════════════════════════
					case 'NTB':
					// ══════════════════════════════════════════════════
					// TODO: build NTB file format once spec is received.
					alert('NTB bank file format not yet implemented. Please contact IT.');
					return;

					// ══════════════════════════════════════════════════
					default:
					// ══════════════════════════════════════════════════
					alert('Unknown bank format (' + currentBankFormat + '). Cannot generate bank file.');
					return;
				}

			} else if ($("#tbl_epf").is(':visible')) {
				// ── EPF file (bank-agnostic, format is the same for all banks) ──
				emp_epfTable.rows().every(function () {
					var d = this.data();
					retContent.push(
						d.emp_nicnum    +
						d.emp_last_name +
						d.emp_first_name+
						d.emp_epfno     +
						d.tot_epf       +
						d.EPF12         +
						d.EPF8          +
						d.tot_fortax    +
						d.member_status +
						d.zone_code     +
						d.employer_num  +
						d.cont_period   +
						d.submit_num    +
						d.emp_workdays  +
						d.occu_grade
					);
				});
				retString = retContent.join('\r\n');
				btn.attr("href", URL.createObjectURL(new Blob([retString], { type: 'text/plain' })));
				btn.prop("download", fromdate + ' - Epf.txt');

			} else if ($("#tbl_etf").is(':visible')) {
				// ── ETF file (bank-agnostic) ─────────────────────────
				emp_etfTable.rows().every(function () {
					var d = this.data();
					retContent.push(
						d.etf_rec_code   +
						d.employer_num   +
						d.emp_etfno      +
						d.emp_first_name +
						d.emp_last_name  +
						d.emp_nicnum     +
						d.ETF3           +
						d.cont_period_fr +
						d.cont_period_to
					);
				});
				retContent.push($('#etf_summary_str').val());
				retString = retContent.join('\r\n');
				btn.attr("href", URL.createObjectURL(new Blob([retString], { type: 'text/plain' })));
				btn.prop("download", fromdate + ' - Etf.txt');
			}
		});

		$(".modal").on("shown.bs.modal", function () {
			var objinput = $(this).find('input[type="text"]:first-child');
			objinput.focus();
			objinput.select();
		});

		$("#payroll_process_type_id").on("change", function () {
			$('#period_filter_id').val('');
			$('#period_filter_id option').prop("disabled", true);
			$('#period_filter_id option:not(:first-child)').hide();
			$('#period_filter_id option[data-payroll="' + $("#payroll_process_type_id").find(":selected")
				.val() + '"]').prop("disabled", false);
			$('#period_filter_id option[data-payroll="' + $("#payroll_process_type_id").find(":selected")
				.val() + '"]').show();
		});

		$('.nest_head').change(function () {
			//prep_nest($(this).data('findnest'), $(this).find(":selected").val(), 0);
			prep_nest($(this).data('findnest'), $(this).find(":selected").data('regcode'), '-1');
		});

		function prep_nest(nestname, nestcode, selectedval) {
			//console.log(nestname+'--'+nestcode+'--'+selectedval);

			var childobj = $('select[data-nestname="' + nestname + '"]')

			var blockobj = $(childobj).find('option.nestopt');
			$(blockobj).prop('disabled', true);
			$(blockobj).addClass('d-none');

			var allowobj = $(childobj).find('option[data-nestcode="' + (nestcode) + '"]');
			$(allowobj).prop('disabled', false);
			$(allowobj).removeClass('d-none');

			var selected_val = (selectedval !== '') ? selectedval : '-1';
			//console.log(selectedval+'vs'+selected_val);
			var selected_pos = 0;

			if (selected_val == '0') {
				var selected_opt = $(allowobj).index();
				//selected_val=(typeof($(allowobj).val())=="undefined")?$(childobj).children('option:first').val():$(allowobj).val();
				//console.log(typeof($(allowobj).val())=="undefined");//$(allowobj).length
				//console.log('0--'+$(allowobj).index());
				selected_pos = (selected_opt > 0) ? selected_opt : 0;
			} else {
				var actobj = $(childobj).find('option[data-nestcode="' + (nestcode) + '"][data-sectcode="' + (
					selectedval) + '"]');
				//console.log('1--'+$(actobj).index());
				var selected_opt = $(actobj).index();
				selected_pos = (selected_opt > 0) ? selected_opt : 0;
			}

			//$(childobj).val(selected_val);
			$(childobj).find('option').eq(selected_pos).prop("selected", true);

		}

		$("#frmSearch").on('submit', function (event) {
			event.preventDefault();

			$.ajax({
				url: "checkPayslipListBankSlip",
				method: 'POST',
				data: $(this).serialize(),
				dataType: "JSON",
				beforeSend: function () {
					//$('#find_employee').prop('disabled', true);
				},
				success: function (data) {
					console.log(data);
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
						var optrpt = $('input[name="opt_rpt"]:checked').val();

						if ((optrpt == 1)||(optrpt == 4)) {
							const columnMap = {
								'7010': [ // BOC
									{ title: "REC TYPE", data: 'boc_rec_type' },
									{ title: "BANK CODE", data: 'boc_bank_code' },
									{ title: "BRANCH CODE", data: 'boc_branch_code' },
									{ title: "ACCOUNT NO", data: 'boc_acc_no' },
									{ title: "NAME", data: 'boc_emp_name' },
									{ title: "TRX CODE", data: 'boc_trx_code' },
									{ title: "FLAG", data: 'boc_flag' },
									{ title: "AMOUNT", data: 'boc_amount' },
									{ title: "CURRENCY", data: 'boc_currency' },
									{ title: "PAY BANK", data: 'boc_pay_bank' },
									{ title: "PAY BRANCH", data: 'boc_pay_branch' },
									// { title: "OTHER BRANCH", data: 'boc_other_br' },
									{ title: "COMPANY", data: 'boc_company' },
									{ title: "REFERENCE", data: 'boc_ref' },
									{ title: "DESCRIPTION", data: 'boc_desc' },
									{ title: "DATE", data: 'boc_date' },
									{ title: "END FLAG", data: 'boc_end_flag' }
								],
								'7083': [ // HNB (Standard)
									{ title: "REF NO", data: 'ref_no' },
									{ title: "NAME (EMPLOYEE)", data: 'emp_name' },
									{ title: "BANK-BR-CODE", data: 'bank_br_code' },
									{ title: "ACCOUNT NO", data: 'account_no' },
									{ title: "TRX CODE", data: 'trx_code' },
									{ title: "AMOUNT", data: 'amount' },
									{ title: "DATE", data: 'date' }
								]
							};

							$("#tbl_all").show();
							$("#tbl_etf").hide();
							$("#tbl_epf").hide();
							empTable.clear();
							emp_epfTable.clear();
							emp_etfTable.clear();
							// empTable.rows.add(data.employee_detail);
							var bankID = data.bank_id; // Ensure your PHP returns this
							var configs = columnMap[bankID] || columnMap['7083']; // Default to HNB/Standard
							currentBankFormat = data.bank_format;

							// Initialize/Re-initialize
							empTable = initializeEmpTable(configs);

							// Add data
							empTable.rows.add(data.employee_detail);
							empTable.draw();
							// empTable.draw();
							emp_epfTable.draw();
							emp_etfTable.draw();

							$('#rpt_total_amount').val(data.total_amount);
							$('#rpt_date_e').val(data.date_e);
							$('#rpt_hash_total').val(data.hash_total);
							$('#rpt_trx_code').val(data.trx_code);
							$('#rpt_no_of_transactions').val(data.no_of_transactions);

							$("#slip").removeClass('btn-light');
							$("#slip").removeClass('disabled');
						} else if (optrpt == 2) {
							$("#tbl_epf").show();
							$("#tbl_all").hide();
							$("#tbl_etf").hide();
							empTable.clear();
							emp_epfTable.clear();
							emp_etfTable.clear();
							emp_epfTable.rows.add(data.employee_epf_detail);
							emp_epfTable.draw();
							empTable.draw();
							emp_etfTable.draw();

							$("#slip").removeClass('btn-light');
							$("#slip").removeClass('disabled');
						} else {
							$("#tbl_etf").show();
							$("#tbl_all").hide();
							$("#tbl_epf").hide();
							empTable.clear();
							emp_epfTable.clear();
							emp_etfTable.clear();
							emp_etfTable.rows.add(data.employee_etf_detail);
							emp_etfTable.draw();
							empTable.draw();
							emp_epfTable.draw();

							$("#etf_summary_str").val(data.etf_summary);

							$("#slip").removeClass('btn-light');
							$("#slip").removeClass('disabled');
						}

						$("#lbl_date_fr").html(data.work_date_fr);
						$("#lbl_date_to").html(data.work_date_to);
						$("#lbl_duration").show();
						$("#payment_period_id").val(data.payment_period_id);
						$("#payslip_process_type_id").val($("#payroll_process_type_id").find(
							":selected").val());
						$("#lbl_payroll_name").html($("#payroll_process_type_id").find(
							":selected").text());
						//$('#find_employee').prop('disabled', false);

						$("#rpt_payroll_id").val($("#payroll_process_type_id").find(
							":selected").val());
						$("#rpt_location_id").val($("#location_filter_id").find(":selected")
							.val());
						$("#rpt_dept_id").val($("#department_filter_id").find(":selected")
						.val());
						$("#rpt_dept_name").val($("#department_filter_id").find(":selected")
							.text());
						$("#rpt_period_id").val($("#period_filter_id").find(":selected")
					.val());
						
						var payperiodstr = data.work_date_fr + " To " + data.work_date_to;
						var filenamestr = (optrpt=='4')?'Advance':payperiodstr + " (" + $("#payroll_process_type_id").find(":selected").text() + ")";
						$("#rpt_info").val(filenamestr);
						/*
						$("#print_record").prop('disabled', false);
						$("#print_record").removeClass('btn-light');
						*/

						$("#txt_acc_name").val(data.acc_name);
						$("#txt_acc_number").val(data.acc_number);
						$("#txt_acc_br").val(data.acc_br_code);

						$('#formModal').modal('hide');
					}
				}
			})
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
		$('input[name="opt_rpt"]').change(function(){
			var opt_payslip_disp=($('input[name="opt_rpt"]:checked').val()==4)?false:true;
			if(opt_payslip_disp){
				$("#opt_payslip").show();
			}else{
				$("#opt_payslip").hide();
			}
		});
	});

	function initializeEmpTable(columnConfigs) {
		// 1. Destroy existing table if it exists
		if ($.fn.DataTable.isDataTable('#emptable')) {
			$('#emptable').DataTable().destroy();
			$('#emptable').empty(); // Clear the header/footer
		}

		// 2. Re-initialize
		return $("#emptable").DataTable({
			"columns": columnConfigs,
			"iDisplayLength": -1,
			"aLengthMenu": [[-1], ["All"]],
			"order": [],
			"createdRow": function (row, data, dataIndex) {
				$(row).attr('id', 'row-' + (data.id || dataIndex));
			}
		});
	}
</script>

@endsection