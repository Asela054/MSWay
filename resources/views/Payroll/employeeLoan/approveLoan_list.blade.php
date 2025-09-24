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
                    <span>Loan Approval</span>
                </h1>
            </div>
        </div>
    </div>
	<div class="container-fluid mt-2 p-0 p-2">
		<div class="card">
            <div class="card-body p-0 p-2">
				<div class="row">
					<div class="col-lg-12 text-right">
						<button type="button" name="approve_record" id="approve_record" class="btn btn-secondary btn-sm btn-light px-3" disabled="disabled">Approve All</button>
					</div>
					<div class="col-12">
						<hr>
						 <div class="center-block fix-width scroll-inner mt-3">
							<table class="table table-bordered table-striped table-sm small nowrap" id="emptable" cellspacing="0">
								<thead>
									<tr>
										<th>NAME</th>
										<th>OFFICE</th>
										<th>ACTIVE LOANS</th>
										<th>LOAN APPLICATIONS</th>
										<th class="">AMOUNT</th>
										<th>ACTIONS</th>
									</tr>
								</thead>
								<tbody class=""></tbody>
							</table>
						 </div>
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
							<div class="col">
								<label class="font-weight-bolder small">Loan Description</label>
								<input type="text" name="loan_name" id="loan_name" class="form-control form-control-sm" autocomplete="off" />
							</div>
							<div class="col">
								<label class="font-weight-bolder small">Date of Issue</label>
								<input type="date" name="loan_date" id="loan_date" class="form-control form-control-sm" />
							</div>
						</div>
						<div class="form-row mb-1">
							<div class="col">
								<label class="font-weight-bolder small">Loan Type</label>
								<select name="loan_type" id="loan_type" class="form-control form-control-sm">
									<option value="PL" data-ratekey="0">Personal</option>
									<option value="FL" data-ratekey="0">Festival</option>
									<option value="WL" data-ratekey="1">Welfare</option>
								</select>
							</div>
							<div class="col">
								<label class="font-weight-bolder small">Interest Rate (%)</label>
								<input type="text" name="interest_rate" id="interest_rate" class="form-control form-control-sm" readonly autocomplete="off" />
							</div>
						</div>
						<div class="form-row mb-1">
							<div class="col">
								<label class="font-weight-bolder small">Issue Amount</label>
								<input type="text" name="issue_amount" id="issue_amount" class="form-control form-control-sm" autocomplete="off" readonly="readonly" />
							</div>
							<div class="col">
								<label class="font-weight-bolder small">Loan Value</label>
								<input type="text" name="loan_amount" id="loan_amount" class="form-control form-control-sm" autocomplete="off" readonly />
							</div>
						</div>
						<div class="form-row mb-1">
							<div class="col">
								<label class="font-weight-bolder small">No. of Installments <!--Duration (Months)--></label>
								<input type="text" name="loan_duration" id="loan_duration" class="form-control form-control-sm" autocomplete="off" readonly="readonly" />
							</div>
							<div class="col">
								<label class="font-weight-bolder small">Installment Value</label>
								<input type="text" name="installment_value" id="installment_value" class="form-control form-control-sm" readonly autocomplete="off" />
							</div>
						</div>
						<div class="form-row mb-1">
							<div class="col">
								<label class="font-weight-bolder small">Primary Loan Guarantor</label>
								<label class="font-weight-bolder small" id="warinig_1"></label>
								<select name="employeegarentee" id="employee_f" class="form-control form-control-sm" readonly>
								</select>
							</div>
						</div>

						<div class="form-row mb-1">
							<div class="col">
								<label class="font-weight-bolder small">Secondary Loan Guarantor</label>
								<label class="font-weight-bolder small" id="warinig_2"></label>
								<select name="employee_secondgarentee" id="employee_ff" class="form-control form-control-sm" readonly>
								</select>
							</div>
						</div>
						<div class="form-row">
							<div class="col text-right">
								<hr>
								<input type="hidden" name="action" id="action" value="Edit" />
								<input type="hidden" name="employee_loan_id" id="employee_loan_id" />
								<input type="hidden" name="payroll_profile_id" id="payroll_profile_id" />
								<input type="hidden" name="hidden_id" id="hidden_id" />
								<button type="button" name="action_buttonv" id="action_button" class="btn btn-primary btn-sm px-3 mr-1" value="Approve">Approve</button>
								<button type="button" name="action_buttonv" id="reject_button" class="btn btn-danger btn-sm px-3 mr-1" value="Reject">Reject</button>
								<button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal">Cancel</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</main>

@endsection


@section('script')
<script>
	(function () {
		var cssMain = document.createElement('link');
		cssMain.href = "{{ asset('/public/payroll/css/approveLoan_list.css') }}";
		cssMain.rel = 'stylesheet';
		cssMain.type = 'text/css';
		document.getElementsByTagName('head')[0].appendChild(cssMain);
		/*
		
		*/
	})();
</script>

<script>
	function format(d) {
		var application_tr = '';
		// `d` is the original data object for the row
		$.each(d, function (index, obj) {
			application_tr += '<tr data-conf="0" class="loan_grp">' +
				'<th>Description</th>' + '<td>' + obj.loan_name + '</td>' +
				'<th>Amount</th>' + '<td>' + obj.loan_amount + '</td>' +
				'<th>Installments</th>' + '<td>' + obj.loan_duration + '</td>' +
				'<td class="actlist_col"><button class="btn btn-primary btn-sm edit" data-refid="' +
				obj.id + '"><i class="fa fa-edit"></i></button></td>' +
				'</tr>';
		});
		return '<table cellpadding="5" cellspacing="0" border="0" width="100%" align="right" style="padding-left:50px;">' +
			application_tr +
			'</table>';
	}

	$(document).ready(function () {
		$('#payrollmenu').addClass('active');
		$('#payrollmenu_icon').addClass('active');
		$('#policymanagement').addClass('navbtnactive');

		var _token = $('#frmConfirm input[name="_token"]').val();

		var empTable = $("#emptable").DataTable({
			"ajax": {
				url: "viewLoanApplicants",
				method: "POST",
				data: {
					_token: _token
				},
				dataSrc: "table_data"
			},
			"columns": [{
					data: 'emp_first_name'
				}, {
					data: 'location'
				}, {
					data: 'active_loans'
				},
				{
					data: 'loan_applications'
				}, {
					data: 'loan_amount'
				},
				{
					"className": 'actlist_col text-right',
					"orderable": false,
					data: null,
					"defaultContent": ''
				}
			],
			"order": [],
			"columnDefs": [{
                "targets": 4,
                "className":'text-right'
            },{
				"targets": 5,
				"className": '', // 'details-control ',
				render: function (data, type, row) {
					if (row.loan_approved == 1) {
						return '<span class="text-success"><i class="fas fa-check-circle mr-2"></i>Approved</span>';
					} else if (row.loan_rejected == 1) {
						return '<span class="text-danger"><i class="fas fa-times-circle mr-2"></i>Rejected</span>';
					}
					if (row.loan_applications == 1) {
						return '<button class="btn btn-primary btn-sm edit" data-refid="' +
							row.loan_id + '"><i class="fa fa-pencil-alt"></i></button>';
					}
				}
			}],
			"createdRow": function (row, data, dataIndex) {
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$(row).attr('id', 'row-' + data.profile_id); //$( row ).data( 'refid', data[3] );
				//$(row).addClass('myRow');
				$('td', row).eq(5).addClass(data.td_class); //'details-control'

				$(row).attr('data-conf', '0');
			}
		});

		// Add event listener for opening and closing details
		$('#emptable tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = empTable.row(tr);

			var d = row.data();
			var id = d.profile_id; //alert(d.location);

			if (row.child.isShown()) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			} else {
				// Open this row
				$.ajax({
					url: "LoanApplicationList/" + id + "/review",
					dataType: "json",
					success: function (data) {
						row.child(format(data.applications)).show();
						tr.addClass('shown');
					}
				})
			}
		});

		//var loanTable=$("#loantable").DataTable();

		$(document).on('click', '.edit', function () {
			var id = $(this).data('refid'); //row#
			//var pack_id = $(this).data('refpack');
			$("#confirm_result").html('');
			var par = $(this).parent().parent();

			$('tr').attr('data-conf', "0");
			$(par).attr('data-conf', "1");


			$.ajax({
				url: "EmployeeLoanApprove/" + id + "/edit",
				dataType: "json",
				success: function (data) {
					$('#action').val('Edit');
					$('#action_button').val('Approve');



					$('#loan_amount').val(data.loan_obj.loan_amount);
					$('#loan_duration').val(data.loan_obj.loan_duration);
					$('#loan_type').val(data.loan_obj.loan_type);
					$('#loan_type').prop('disabled', true);
					$('#interest_rate').val(data.loan_obj.interest_rate);
					$('#loan_name').val(data.loan_obj.loan_name);
					$('#issue_amount').val(data.loan_obj.issue_amount);
					$('#loan_date').val(data.loan_obj.loan_date);

					$('#installment_value').val(data.loan_obj.installment_value);
					$("#employee_loan_id").val(data.loan_obj.id); //$('#hidden_id')

					$('#employee_f').html('<option value="' + data.loan_obj.primery_guarantor +
						'">' + data.loan_obj.primary_emp_name + '</option>');

					$('#employee_ff').html('<option value="' + data.loan_obj
						.secondary_guarantor + '">' + data.loan_obj.secondary_emp_name +
						'</option>');


					if (data.primary_guarantor_result == 1) {
						$('#warinig_1')
							.text('This employee is already signed to another loan.')
							.css('color', 'red');
					}
					if (data.secondary_guarantor_result == 1) {
						$('#warinig_2')
							.text('This employee is already signed to another loan.')
							.css('color', 'red');
					}

					$('#confirmModal').modal('show');

				}
			}) /**/

		});

		$('#approve_record').click(function () {
			Swal.fire({
				icon: 'success',
				title: 'Success',
				text: 'todo - Listed loans approved successfully'
			});
		});

		$('#frmConfirm').on('submit', function (event) {
			event.preventDefault();
			var action_url = "{{ route('EmployeeLoanApprove.update') }}"; //EmployeeLoanApproval.update

			var param_interest = $('#interest_rate').val();
			$('#interest_rate').val(0); //(realRate()); // set-interest-rate-of-loan

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
						// html = '<div class="alert alert-success">' + data.success + '</div>';
						// $('#frmInfo')[0].reset();
						// $('#titletable').DataTable().ajax.reload();
						// location.reload()

						//if($('#hidden_id').val()==''){
						//$('#hidden_id').val(data.new_obj.id);
						//}

						var par = $('tr[data-conf="1"]');
						/*
						alert(JSON.stringify(selected_tr.data()));
						*/
						if (par.length == 1) {
							if ($(par).hasClass('loan_grp')) {
								if (data.loan_approved == 1) {
									par.children('td:last-child').html(
										'<span class="text-success"><i class="fa fa-check-square mr-2"></i>Approved</span>');
								} else if (data.loan_rejected == 1) {
									par.children('td:last-child').html(
										'<span class="text-danger"><i class="fa fa-window-close mr-2"></i>Rejected</span>');
								}
							} else {
								var selected_tr = empTable.row(par)
								var d = selected_tr.data();
								d.loan_approved = data.loan_approved;
								d.loan_rejected = data.loan_rejected;
								empTable.row(selected_tr).data(d).draw();
							}
						}

						$('#confirmModal').modal('hide');

					} else {
						$('#interest_rate').val(param_interest); // set-previous-value-on-error
					}

					// $('#confirm_result').html(html);

				}
			});
		});

		$(".modal").on("shown.bs.modal", function () {
			var objinput = $("#loan_name"); //$(this).find('input[type="text"]:first-child');//
			objinput.focus();
			objinput.select();
		});

		$('#action_button').on('click', async function() {
			var r = await Otherconfirmation("You want to approve this ? ");
        	if (r == true) {
				var btntxt = $(this).val();
				$("#frmConfirm").append("<input type='hidden' name='act_btn' value='" + btntxt + "' />");
				$('#frmConfirm').submit();
			}
		});
		$('#reject_button').on('click', async function() {
			var r = await Otherconfirmation("You want to reject this ? ");
        	if (r == true) {
				var btntxt = $(this).val();
				$("#frmConfirm").append("<input type='hidden' name='act_btn' value='" + btntxt + "' />");
				$('#frmConfirm').submit();
			}
		});
	});

	function buttonSubmitHandler() {
		var btntxt = $(document.activeElement).val();
		$("#frmConfirm").append("<input type='hidden' name='act_btn' value='" + btntxt + "' />");
		
		// Print the value of the button that was clicked
		//console.log($(document.activeElement).val());//$(document.activeElement).attr('id');
		/*Note that if the form is submitted by hitting the Enter key, then document.activeElement will be whichever form input that was focused at the time. If this wasn't a submit button then in this case it may be that there is no "button that was clicked."*/

	}
</script>

@endsection