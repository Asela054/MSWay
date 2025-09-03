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
                                                Employee Salary Sheets
                                                <span id="lbl_duration" style="display:none; margin-right:auto; padding-left:10px;">
                                                    <span id="lbl_date_fr">&nbsp;</span> To <span id="lbl_date_to">&nbsp;</span>
                                                    (<span id="lbl_payroll_name">&nbsp;</span>)
                                                </span>
                                                <div>
                                                    <button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm">Search</button>
                                                    <button type="submit" name="print_record" id="print_record" disabled="disabled" class="btn btn-secondary btn-sm btn-light">Download</button>
                                                </div>
                                            </div>
                                            
                                            <div class="card-body">
                                                
                                                <div id="divPrint" class="datatable table-responsive" style="margin-top:0px;">
                                                    <table class="table table-bordered table-hover" id="emptable" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Office</th>
                                                                <th>Basic</th>
                                                                <th>No-pay</th>
                                                                <th class="">OT</th>
                                                                <th class="">Facility</th>
                                                                <th>Loan</th>
                                                                <th>Additions</th>
                                                                <th>EPF-8</th>
                                                                <th>EPF-12</th>
                                                                <th>ETF-3</th>
                                                                <th>PAYE</th>
                                                            </tr>
                                                        </thead>
                                                     
                                                        <tbody class="">
                                                        </tbody>
                                                        
                                                    </table>
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
                                                        <option value="" disabled="disabled" selected="selected" data-regcode="">Please Select</option>
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
                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
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
                                            <div class="form-group col-md-6">
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
                                   </div>
                                   <div class="modal-footer" align="right">
                                       <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="View Payslips" />
                                       <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
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
			"columns":[{data:'emp_first_name'}, {data:'location'}, {data:'BASIC'}, 
				{data:'NOPAY'}, {data:'OTHRS'}, 
				{data:'FACILITY'}, {data:'LOAN'}, {data:'ADDITION'}, 
				{data:'EPF8'}, {data:'EPF12'}, {data:'ETF3'}, {data:'PAYE'}],
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
		prep_nest($(this).data('findnest'), $(this).find(":selected").data('regcode'), 0);
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
	   url:"checkPayslipListByDept",
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
			empTable.rows.add(data.employee_detail);
			empTable.draw();
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
			$("#print_record").prop('disabled', false);
			$("#print_record").removeClass('btn-light');
			
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