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
                    <span>Six Month Report</span>
                </h1>
            </div>
        </div>
    </div>
	<div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
				<form id="frmExport" method="post" action="{{ url('DownloadEpfEtf') }}">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-12 text-right">
							<button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm px-3"><i class="fal fa-search mr-2"></i>Search</button>
							<button type="submit" name="print_record" id="print_record" disabled="disabled" class="btn btn-secondary btn-sm btn-light px-3"><i class="fal fa-file-pdf mr-2"></i>Download</button>
						</div>
					</div>
					<div class="col-12">
						<span id="lbl_duration" style="display:none; margin-right:auto; padding-left:10px;">
							<div class="alert alert-primary" role="alert">
								<span id="lbl_date_fr">&nbsp;</span> To <span id="lbl_date_to">&nbsp;</span>
								(<span id="lbl_payroll_name">&nbsp;</span>)
							</div>
						</span>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<hr>
							<div id="divPrint" class="center-block fix-width scroll-inner">
								<table class="table table-bordered table-striped table-sm small w-100 nowrap" id="emptable" cellspacing="0">
									<thead>
										<tr>
											<th nowrap style="width:300px;">NAME</th>
											<th nowrap>LOCATION</th>
											@foreach($payroll_months as $payroll_month)
											<th nowrap class="text-capitalize">{{$payroll_month}}</th>
											@endforeach
										</tr>
									</thead>

									<tbody class="">
									</tbody>

								</table>
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
						</div>
					</div>
				</form>
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
					<form id="frmSearch" method="post">
						{{ csrf_field() }}
						<span id="search_result"></span>
						<div class="form-row mb-1">
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
								<label class="font-weight-bolder small">Working Period</label>
								<select name="period_filter_id" id="period_filter_id" class="custom-select custom-select-sm"
									style="">
									<option value="" disabled="disabled" selected="selected">Please Select</option>
									@foreach($payment_period as $schedule)

									<option value="{{$schedule->id}}" disabled="disabled" data-payroll="{{$schedule->payroll_process_type_id}}" style="display:none;">{{$schedule->payment_period_fr}} to {{$schedule->payment_period_to}}</option>
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
						<div class="form-row">
							<div class="col-12 text-right">
								<hr>
								<input type="submit" name="action_button" id="action_button" class="btn btn-warning btn-sm px-3" value="View Payslips" />
								<button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal">Close</button>
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

		var empTable = $("#emptable").DataTable({
			"columns": [{
					data: 'emp_first_name'
				}, {
					data: 'location'
				},
				{
					data: 'Col1'
				}, {
					data: 'Col2'
				}, {
					data: 'Col3'
				},
				{
					data: 'Col4'
				}, {
					data: 'Col5'
				}, {
					data: 'Col6'
				}
			],
			"order": [],
			"columnDefs": [{
				"targets": 2,
				render: function (data, type, row) {
					return month_salary(data);
				}
			}, {
				"targets": 3,
				render: function (data, type, row) {
					return month_salary(data);
				}
			}, {
				"targets": 4,
				render: function (data, type, row) {
					return month_salary(data);
				}
			}, {
				"targets": 5,
				render: function (data, type, row) {
					return month_salary(data);
				}
			}, {
				"targets": 6,
				render: function (data, type, row) {
					return month_salary(data);
				}
			}, {
				"targets": 7,
				render: function (data, type, row) {
					return month_salary(data);
				}
			}],
			"createdRow": function (row, data, dataIndex) {
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$(row).attr('id', 'row-' + data.id); //$( row ).data( 'refid', data[3] );
			}
		});

		//var loanTable=$("#loantable").DataTable();

		var _token = $('#frmSearch input[name="_token"]').val();;

		function month_salary(data) {
			//console.log(JSON.stringify(data));
			return 'Basic: ' + data.BASIC + '<br />OT: ' + data.OTHRS; //0;
		}

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

		$("#frmSearch").on('submit', function (event) {
			event.preventDefault();

			$.ajax({
				url: "previewSixMonth",
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
						//alert(JSON.stringify(empTable.columns().header()));
						$.each(data.colslist, function (index, value) {
							empTable.columns(index + 2).header().to$().text(value);
						});

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

						$("#rpt_payroll_id").val($("#payroll_process_type_id").find(
							":selected").val());
						$("#rpt_location_id").val($("#location_filter_id").find(":selected")
							.val());
						$("#rpt_period_id").val($("#period_filter_id").find(":selected")
					.val());
						$("#rpt_info").val(data.work_date_fr + " To " + data.work_date_to +
							" (" + $("#payroll_process_type_id").find(":selected").text() +
							")");

						//$("#print_record").prop('disabled', false);
						//$("#print_record").removeClass('btn-light');

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

	});
</script>

@endsection