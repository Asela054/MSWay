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
                                    <form id="frmExport" method="post" target="_blank" action="{{ url('DownloadSalarySheet') }}">
                                    {{ csrf_field() }}
                                        <div class="card card-header-actions mb-4">
                                            <div class="card-header">
                                                Employee Salary Sheets - Bank Slip
                                                <span id="lbl_duration" style="display:none; margin-right:auto; padding-left:10px;">
                                                    <span id="lbl_date_fr">&nbsp;</span> To <span id="lbl_date_to">&nbsp;</span>
                                                    (<span id="lbl_payroll_name">&nbsp;</span>)
                                                </span>
                                                <div>
                                                    <button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm">Search</button>
                                                    <button type="submit" name="print_record" id="print_record" disabled="disabled" class="btn btn-secondary btn-sm btn-light" style="display:none;">Download</button>
                                                    <a href="#" class="btn btn-primary btn-sm btn-light disabled" type="button" id="slip" > Bank Report</a>
                                                </div>
                                            </div>
                                            
                                            <div class="card-body">
                                                
                                                <div id="divPrint" class="datatable" style="margin-top:0px;">
                                                    <div id="tbl_all" class="table-responsive">
                                                        <table class="table table-bordered table-hover" id="emptable" width="100%" cellspacing="0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Ref No</th>
                                                                    <th>Name (Employee)</th>
                                                                    <th>Bank-Br-Code</th>
                                                                    <th>Account No</th>
                                                                    <th>TRX Code</th>
                                                                    <th>Amount (Salary)</th>
                                                                    <th>Date</th>
                                                                </tr>
                                                            </thead>
                                                         
                                                            <tbody class="">
                                                            </tbody>
                                                            
                                                        </table>
                                                    </div>
                                                    <div id="tbl_etf" class="table-responsive" style="display:none;">
                                                        <table class="table table-bordered table-hover" id="emp_etftable" width="100%" cellspacing="0">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:70px;">Code</th>
                                                                    <th>Employer No.</th>
                                                                    <th>Member No.</th>
                                                                    <th>Initials</th>
                                                                    <th>Last Name</th>
                                                                    <th>NIC No.</th>
                                                                    <th>Period From</th>
                                                                    <th>Period To</th>
                                                                    <th>ETF-3</th>
                                                                </tr>
                                                            </thead>
                                                         
                                                            <tbody class="">
                                                            </tbody>
                                                            
                                                        </table>
                                                    </div>
                                                    <div id="tbl_epf" class="table-responsive" style="display:none;">
                                                        <table class="table table-bordered table-hover" id="emp_epftable" width="100%" cellspacing="0">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:300px;">NIC No.</th>
                                                                    <th>Last Name</th>
                                                                    <th>Initials</th>
                                                                    <th>Member No.</th>
                                                                    <th>Total Cont.</th>
                                                                    <th>EPF-12</th>
                                                                    <th>EPF-8</th>
                                                                    <th>Total Earn</th>
                                                                    <th>Member Status</th>
                                                                    <th>Zone Code</th>
                                                                    <th>Employer No.</th>
                                                                    <th>Cont. Period</th>
                                                                    <th>Submit No.</th>
                                                                    <th>Days Worked</th>
                                                                    <th>Occu. Grade</th>
                                                                </tr>
                                                            </thead>
                                                         
                                                            <tbody class="">
                                                            </tbody>
                                                            
                                                        </table>
                                                    </div>
                                                </div>
                                                
                                                <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" value="" /><!-- edit loans -->
                                                <input type="hidden" name="payment_period_id" id="payment_period_id" value="" />
                                                <input type="hidden" name="payslip_process_type_id" id="payslip_process_type_id" value="" />
                                                
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
                                                <input type="hidden" name="rpt_no_of_transactions" id="rpt_no_of_transactions" value="" />
                                                
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
                    </div>
                    
                    <div id="formModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <form id="frmSearch" method="post">
                            {{ csrf_field() }}	
                                <div class="modal-content">
                                   <div class="modal-header">
                                       <h5 class="modal-title" id="formModalLabel"></h5>
                                       
                                       <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                                   </div>
                                   <div class="modal-body">
                                        <span id="search_result"></span>
                                        <div class="row">
                                            
                                            <div class="form-group col-md-6">
                                               <label class="control-label col">Branch</label>
                                               <div class="col">
                                                   <select name="location_filter_id" id="location_filter_id" class="custom-select shipClass nest_head" style="" data-findnest="deptnest" >
                                                        <option value="-1" selected="selected" data-regcode="">Please Select</option>
                                                        @foreach($branch as $branches)
                                                        
                                                        <option value="{{$branches->id}}" data-regcode="{{$branches->id}}">{{$branches->location}}</option>
                                                        @endforeach
                                                        
                                                   </select>
                                               </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                               <label class="control-label col">Department</label>
                                               <div class="col">
                                                   <select name="department_filter_id" id="department_filter_id" class="custom-select" style="" data-nestname="deptnest" >
                                                        <option value="" selected="selected">Please Select</option>
                                                        @foreach($department as $section)
                                                        
                                                        <option class="nestopt d-none" value="{{$section->id}}" data-nestcode="{{$section->company_id}}" data-sectcode="{{$section->id}}">{{$section->name}}</option>
                                                        @endforeach
                                                        
                                                   </select>
                                               </div>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                        	<div class="form-group col-md-6">
                                               <label class="control-label col" >Payroll type</label>
                                               <div class="col">
                                                 <select name="payroll_process_type_id" id="payroll_process_type_id" class="form-control" >
                                                    <option value="" disabled="disabled" selected="selected">Please select</option>
                                                    @foreach($payroll_process_type as $payroll)
                                                    
                                                    <option value="{{$payroll->id}}" data-totdays="{{$payroll->total_work_days}}">{{$payroll->process_name}}</option>
                                                    @endforeach
                                                    
                                                 </select>
                                               </div>
                                            </div>
                                            <div class="form-group col">
                                            	<label class="control-label col">Working Period</label>
                                                <div class="col">
                                                   <select name="period_filter_id" id="period_filter_id" class="custom-select" style="" >
                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                        @foreach($payment_period as $schedule)
                                                        
                                                        <option value="{{$schedule->id}}" disabled="disabled" data-payroll="{{$schedule->payroll_process_type_id}}" style="display:none;">{{$schedule->payment_period_fr}} to {{$schedule->payment_period_to}}</option>
                                                        @endforeach
                                                        
                                                   </select>
                                               </div>
                                            </div>
                                            <!--div class="form-group col-md-6">
                                            	<label class="control-label col">To</label>
                                                <div class="col">
                                                	<input type="date" class="form-control" name="work_date_to" id="work_date_to" value="" />
                                                </div>
                                            </div-->
                                        </div>
                                        <div class="row">
                                        	<div class="col-md-12" style="padding: 15px 24px;">
                                                <div class="form-group row">
                                                    <label class="control-label col-md-6" style="padding:3px 15px;">Salary Bank Date :</label>
                                                    <div class="col-md-6" style="padding-left:25px;">
                                                        <input type="date" class="form-control form-control-sm" name="salary_bank_date" id="salary_bank_date" value="" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                        	<div class="col-md-6" style="padding: 15px 24px;">
                                                <div class="form-group row">
                                                    <label class="control-label col-md-6 small" style="padding:3px 15px; white-space:nowrap;">Submission No :</label>
                                                    <div class="col-md-6"><!-- style="padding-left:25px;" -->
                                                        <input type="number" class="form-control form-control-sm" name="salary_submit_attemptno" id="salary_submit_attemptno" value="1" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6" style="padding: 15px 24px;">
                                                <div class="form-group row">
                                                    <label class="control-label col-md-6 small" style="padding:3px 15px; white-space:nowrap;">Work Days :</label>
                                                    <div class="col-md-6"><!-- style="padding-left:25px;" -->
                                                        <input type="number" class="form-control form-control-sm" name="pay_schedule_days" id="pay_schedule_days" value="26" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                   </div>
                                   <div class="modal-footer form-group d-flex align-items-center justify-content-between" align="">
                                       <div>
                                           <input type="radio" name="opt_rpt" id="opt_rpt_a" checked="checked" value="1" /><label for="opt_rpt_a">Salary</label>
                                           <input type="radio" name="opt_rpt" id="opt_rpt_b" value="2" /><label for="opt_rpt_b">EPF</label>
                                           <input type="radio" name="opt_rpt" id="opt_rpt_c" value="3" /><label for="opt_rpt_c">ETF</label>
                                       </div>
                                       <div>
                                           <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="View Payslips" />
                                           <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
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
                                       <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                                   </div>
                                   <div class="modal-body">
                                       <span id="loan_result"></span>
                                       <form id="frmInstallmentList" class="frm_link" method="post">
                                       {{ csrf_field() }}
                                           <div class="">
                                               <div class="" style="">
                                                   <div class="datatable table-responsive" style="margin-top:10px;">
                                                        <table class="table table-bordered table-hover" id="loantable" width="100%" cellspacing="0">
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
                                                           <label class="control-label col" >Installment</label>
                                                           <div class="col">
                                                             <input type="text" name="pre_installment_amount" id="pre_installment_amount" class="form-control" readonly="readonly" />
                                                           </div>
                                                       </div>
                                                       <div class="form-group col-md-6">
                                                           <label class="control-label col" >Payment</label>
                                                           <div class="col">
                                                             <input type="text" name="new_installment_amount" id="new_installment_amount" class="form-control" />
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="" align="right" style="padding:5px; border-top:none;">
                                               	   <input type="submit" name="setup_button" id="setup_button" class="btn btn-warning" value="Edit" />
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
$(document).ready(function(){
	$('#payrollmenu').addClass('active');
    $('#payrollmenu_icon').addClass('active');
    $('#payrollreport').addClass('navbtnactive');

	var empTable=$("#emptable").DataTable({
			"columns":[{data:'ref_no'}, {data:'emp_name'}, {data:'bank_br_code'},
				{data:'account_no'}, {data:'trx_code'},
				{data:'amount'}, {data:'date'} ],
			"iDisplayLength": -1,//100,
			"aLengthMenu": [[-1], ["All"]],
			"order":[],
			
			"createdRow": function( row, data, dataIndex ){
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$( row ).attr( 'id', 'row-'+data.id );//$( row ).data( 'refid', data[3] );
			}
		});
	
	var emp_epfTable=$("#emp_epftable").DataTable({
			"columns":[{data:'emp_nicnum'}, {data:'emp_last_name'}, {data:'emp_first_name'}, {data:'emp_epfno'}, {data:'tot_epf'}, {data:'EPF12'}, {data:'EPF8'}, {data:'tot_fortax'}, {data:'member_status'}, {data:'zone_code'}, {data:'employer_num'}, {data:'cont_period'}, {data:'submit_num'}, {data:'emp_workdays'}, {data:'occu_grade'}],
			"iDisplayLength": -1,//100,
			"aLengthMenu": [[-1], ["All"]],
			"order":[],
			
			"createdRow": function( row, data, dataIndex ){
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$( row ).attr( 'id', 'row-'+data.id );//$( row ).data( 'refid', data[3] );
			}
		});
	
	var emp_etfTable=$("#emp_etftable").DataTable({
			"columns":[{data:'etf_rec_code'}, {data:'employer_num'}, {data:'emp_etfno'}, {data:'emp_first_name'}, {data:'emp_last_name'}, {data:'emp_nicnum'}, {data:'cont_period_fr'}, {data:'cont_period_to'}, {data:'ETF3'}],
			"iDisplayLength": -1,//100,
			"aLengthMenu": [[-1], ["All"]],
			"order":[],
			
			"createdRow": function( row, data, dataIndex ){
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$( row ).attr( 'id', 'row-'+data.id );//$( row ).data( 'refid', data[3] );
			}
		});
	//var loanTable=$("#loantable").DataTable();
	
	var _token = $('#frmSearch input[name="_token"]').val();;
	
	function findEmployee(){
		$('#formModalLabel').text('Find Employee');
		//$('#action_button').val('Add');
		//$('#action').val('Add');
		$('#search_result').html('');
		
		$('#formModal').modal('show');
	}
	
	$('#find_employee').click(function(){
		findEmployee();
	});

    $('#slip').click(function(){
		if($("#tbl_all").is(':visible')){
			var fromdate = $('#rpt_info').val();
			var retContent = [];
			var retString = '';
		
			//add company name
			var txtaccname = $("#txt_acc_name").val();
			let company_name = txtaccname.substring(0, 20);//'Multi Offset (Pvt) Ltd'.substring(0, 20);//(0, 19);
			let amount = $('#rpt_total_amount').val();//console.log('amount='+amount);
			let acc_no = $("#txt_acc_number").val();//'000000000000';
			let date = $('#rpt_date_e').val();//console.log('date='+date);
			let hash_total = $('#rpt_hash_total').val();//console.log('hash_total='+hash_total);
			let no_of_transactions = $('#rpt_no_of_transactions').val();//console.log('no_of_transactions='+no_of_transactions);
			let br_code = $("#txt_acc_br").val();//'0000000';//'xxxxxxx';
			let trans_code = '223';//$('#rpt_trx_code').val();console.log('trans_code='+trans_code);
		
			retContent.push(company_name+amount+acc_no+date+hash_total+no_of_transactions+br_code+trans_code);
		
			$('#emptable tbody tr').each(function (e, elem) {
				var elemText = [];
				$(elem).children('td').each(function (childIdx, childElem) {
					elemText.push($(childElem).text());
				});
				retContent.push(`${elemText.join('')}`+'SALARY');
			});
		
			let counter = '';
			for(let a = 0; a< 80; a++){
				counter += '0';
			}
			retContent.push(counter);
		
			retString = retContent.join('\r\n');
		
			var file = new Blob([retString], {
				type: 'text/plain'
			});
			var btn = $('#slip');
			btn.attr("href", URL.createObjectURL(file));
			btn.prop("download", fromdate+' - Report.txt');
		}else if($("#tbl_epf").is(':visible')){
			var fromdate = $('#rpt_info').val();
			var retContent = [];
			var retString = '';
			
			$('#emp_epftable tbody tr').each(function (e, elem) {
				var elemText = [];
				$(elem).children('td').each(function (childIdx, childElem) {
					elemText.push($(childElem).text());
				});
				retContent.push(`${elemText.join('')}`);
			});
			
			retString = retContent.join('\r\n');
		
			var file = new Blob([retString], {
				type: 'text/plain'
			});
			var btn = $('#slip');
			btn.attr("href", URL.createObjectURL(file));
			btn.prop("download", fromdate+' - Epf.txt');
			
		}else if($("#tbl_etf").is(':visible')){
			var fromdate = $('#rpt_info').val();
			var retContent = [];
			var retString = '';
			
			$('#emp_etftable tbody tr').each(function (e, elem) {
				var elemText = [];
				$(elem).children('td').each(function (childIdx, childElem) {
					elemText.push($(childElem).text());
				});
				retContent.push(`${elemText.join('')}`);
			});
			
			retContent.push($('#etf_summary_str').val());
			
			retString = retContent.join('\r\n');
		
			var file = new Blob([retString], {
				type: 'text/plain'
			});
			var btn = $('#slip');
			btn.attr("href", URL.createObjectURL(file));
			btn.prop("download", fromdate+' - Etf.txt');
		}
	});
	
	$(".modal").on("shown.bs.modal", function(){
		var objinput=$(this).find('input[type="text"]:first-child');
		objinput.focus();
		objinput.select();
	});
	
	$("#payroll_process_type_id").on("change", function(){
		$('#period_filter_id').val('');
		$('#period_filter_id option').prop("disabled", true);
		$('#period_filter_id option:not(:first-child)').hide();
		$('#period_filter_id option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').prop("disabled", false);
		$('#period_filter_id option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').show();
	});
	
	$('.nest_head').change(function(){
		//prep_nest($(this).data('findnest'), $(this).find(":selected").val(), 0);
		prep_nest($(this).data('findnest'), $(this).find(":selected").data('regcode'), '-1');
	});
	
	function prep_nest(nestname, nestcode, selectedval){
		//console.log(nestname+'--'+nestcode+'--'+selectedval);
		
		var childobj=$('select[data-nestname="'+nestname+'"]')
		
		var blockobj=$(childobj).find('option.nestopt');
		$(blockobj).prop('disabled', true);
		$(blockobj).addClass('d-none');
		
		var allowobj=$(childobj).find('option[data-nestcode="'+(nestcode)+'"]');
		$(allowobj).prop('disabled', false);
		$(allowobj).removeClass('d-none');
		
		var selected_val=(selectedval!=='')?selectedval:'-1';
		//console.log(selectedval+'vs'+selected_val);
		var selected_pos=0;
		
		if(selected_val=='0'){
			var selected_opt=$(allowobj).index();
			//selected_val=(typeof($(allowobj).val())=="undefined")?$(childobj).children('option:first').val():$(allowobj).val();
			//console.log(typeof($(allowobj).val())=="undefined");//$(allowobj).length
			//console.log('0--'+$(allowobj).index());
			selected_pos=(selected_opt>0)?selected_opt:0;
		}else{
			var actobj=$(childobj).find('option[data-nestcode="'+(nestcode)+'"][data-sectcode="'+(selectedval)+'"]');
			//console.log('1--'+$(actobj).index());
			var selected_opt=$(actobj).index();
			selected_pos=(selected_opt>0)?selected_opt:0;
		}
		
		//$(childobj).val(selected_val);
		$(childobj).find('option').eq(selected_pos).prop("selected", true);
		
	}
	
	$("#frmSearch").on('submit', function(event){
	  event.preventDefault();
	  
	  $.ajax({
	   url:"checkPayslipListBankSlip",
	   method:'POST',
	   data:$(this).serialize(),
	   dataType:"JSON",
	   beforeSend:function(){
		//$('#find_employee').prop('disabled', true);
	   },
	   success:function(data){
		//alert(JSON.stringify(data));
		var html = '';
		empTable.clear();
		
		if(data.errors){
			html = '<div class="alert alert-danger">';
			for(var count = 0; count < data.errors.length; count++){
			  html += '<p>' + data.errors[count] + '</p>';
			}
			html += '</div>';
			$('#search_result').html(html);
		}else{
			var optrpt=$('input[name="opt_rpt"]:checked').val();
			
			if(optrpt==1){
				$("#tbl_all").show();
				$("#tbl_etf").hide();
				$("#tbl_epf").hide();
				empTable.clear();
				emp_epfTable.clear();
				emp_etfTable.clear();
				empTable.rows.add(data.employee_detail);
				empTable.draw();
				emp_epfTable.draw();
				emp_etfTable.draw();
				
				$('#rpt_total_amount').val(data.total_amount);
				$('#rpt_date_e').val(data.date_e);
				$('#rpt_hash_total').val(data.hash_total);
				$('#rpt_trx_code').val(data.trx_code);
				$('#rpt_no_of_transactions').val(data.no_of_transactions);
	
				$("#slip").removeClass('btn-light');
				$("#slip").removeClass('disabled');
			}else if(optrpt==2){
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
			}else{
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
			$("#payslip_process_type_id").val($("#payroll_process_type_id").find(":selected").val());
			$("#lbl_payroll_name").html($("#payroll_process_type_id").find(":selected").text());
			//$('#find_employee').prop('disabled', false);
			
			$("#rpt_payroll_id").val($("#payroll_process_type_id").find(":selected").val());
			$("#rpt_location_id").val($("#location_filter_id").find(":selected").val());
			$("#rpt_dept_id").val($("#department_filter_id").find(":selected").val());
			$("#rpt_dept_name").val($("#department_filter_id").find(":selected").text());
			$("#rpt_period_id").val($("#period_filter_id").find(":selected").val());
			$("#rpt_info").val(data.work_date_fr+" To "+data.work_date_to+" ("+$("#payroll_process_type_id").find(":selected").text()+")");
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
	
	
	$(".btn_back").on("click", function(){
		$(".show .frm_info").addClass('sect_bg');
		$(".show .frm_link").removeClass('sect_bg');
	});
	
	
	$(".modal").on("shown.bs.modal", function(e){
		if($(this).find(".frm_link")){
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