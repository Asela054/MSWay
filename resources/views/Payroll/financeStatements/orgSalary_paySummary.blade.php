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
                                    <form id="frmExport" method="post" action="{{ url('DownloadEpfEtf') }}">
                                    {{ csrf_field() }}
                                        <div class="card card-header-actions mb-4">
                                            <div class="card-header">
                                                Pay Summary
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
                                                    <table class="table table-bordered table-hover" id="emp_bank_table" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:300px;">&nbsp;</th>
                                                                <th style="text-align:right;">Amount</th>
                                                                <th style="text-align:center;">No. of Emp.</th>
                                                            </tr>
                                                        </thead>
                                                     
                                                        <tbody class="">
                                                        	<tr data-figid="BASIC">
                                                            	<td>Basic Salary</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="NOPAY">
                                                            	<td>Nopay</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="SAL_AFT_NOPAY">
                                                            	<td>Salary After Nopay</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="OTHRS">
                                                            	<td>Overtime</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="add_holiday_x">
                                                            	<td>Holiday</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            
                                                            <tr data-figid="add_transport_x">
                                                            	<td>Reimburse Traveling</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="INCNTV_EMP">
                                                            	<td>Incentive</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="INCNTV_DIR">
                                                            	<td>Directors Incentive</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            
                                                            <tr data-figid="tot_earn">
                                                            	<td>Total Earning</td>
                                                                <td data-cap="amt" style="border-top:1px double;"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            
                                                            <tr data-figid="EPF8">
                                                            	<td>EPF Contribution 8%</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="sal_adv">
                                                            	<td>Salary Advance</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="ded_fund_1">
                                                            	<td>Funeral Fund</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="ded_IOU">
                                                            	<td>I.O.U</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="PAYE">
                                                            	<td>PAYEE</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="add_transport">
                                                            	<td>Traveling</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="LOAN">
                                                            	<td>Loan</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="">
                                                            	<td>Loan-2</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="tot_ded">
                                                            	<td>Total Deduction</td>
                                                                <td data-cap="amt" style="border-top:1px double;"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            
                                                            <tr data-figid="bal_earn">
                                                            	<td>Balance</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            
                                                            <tr data-figid="EPF12">
                                                            	<td>Contribution EPF 12%</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            <tr data-figid="ETF3">
                                                            	<td>Contribution EPF 3%</td>
                                                                <td data-cap="amt"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            
                                                            <tr data-figid="bal_earn">
                                                            	<td>Total Payment:</td>
                                                                <td data-cap="amt" style="border-top:1px double; border-bottom:1px double;"></td>
                                                                <td data-cap="cnt"></td>
                                                            </tr>
                                                            
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
                                                
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                                
                                
                              
                            </div>
                           
                        </div>
                        
                        <div class="row" style="margin-top:10px; margin-left:5px;">
                        	<div class="col-xl-3 col-sm-6 mb-3">
                              <div class="card text-white bg-primary o-hidden h-100">
                                <div class="card-body">
                                  <div class="card-body-icon">
                                    <i class="fa fa-fw fa-comments"></i>
                                  </div>
                                  <div class="mr-5" data-accdesc="hjaela"><!-- Title 1 --></div>
                                </div>
                                <a class="card-footer text-white clearfix small z-1" href="#">
                                  <span class="float-left">HNB Ja-Ela</span>
                                  <span class="float-right">
                                    <i class="fa fa-angle-right"></i>
                                  </span>
                                </a>
                              </div>
                            </div>
                            
                            <div class="col-xl-3 col-sm-6 mb-3">
                              <div class="card text-white bg-primary o-hidden h-100">
                                <div class="card-body">
                                  <div class="card-body-icon">
                                    <i class="fa fa-fw fa-comments"></i>
                                  </div>
                                  <div class="mr-5" data-accdesc="hseeduwa"><!-- Title 1 --></div>
                                </div>
                                <a class="card-footer text-white clearfix small z-1" href="#">
                                  <span class="float-left">HNB Seeduwa</span>
                                  <span class="float-right">
                                    <i class="fa fa-angle-right"></i>
                                  </span>
                                </a>
                              </div>
                            </div>
                            
                            <div class="col-xl-3 col-sm-6 mb-3">
                              <div class="card text-white bg-success o-hidden h-100">
                                <div class="card-body">
                                  <div class="card-body-icon">
                                    <i class="fa fa-fw fa-comments"></i>
                                  </div>
                                  <div class="mr-5" data-accdesc="other"><!-- Title 2 --></div>
                                </div>
                                <a class="card-footer text-white clearfix small z-1" href="#">
                                  <span class="float-left">Other Banks</span>
                                  <span class="float-right">
                                    <i class="fa fa-angle-right"></i>
                                  </span>
                                </a>
                              </div>
                            </div>
                            
                            
                            <div class="col-xl-3 col-sm-6 mb-3">
                              <div class="card text-white bg-warning o-hidden h-100">
                                <div class="card-body">
                                  <div class="card-body-icon">
                                    <i class="fa fa-fw fa-comments"></i>
                                  </div>
                                  <div class="mr-5" data-accdesc="cash"><!-- Title 4 --></div>
                                </div>
                                <a class="card-footer text-white clearfix small z-1" href="#">
                                  <span class="float-left">Cash</span>
                                  <span class="float-right">
                                    <i class="fa fa-angle-right"></i>
                                  </span>
                                </a>
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
                                               <label class="control-label col">Location</label>
                                               <div class="col">
                                                   <select name="location_filter_id" id="location_filter_id" class="custom-select shipClass" style="" >
                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                        @foreach($branch as $branches)
                                                        
                                                        <option value="{{$branches->id}}">{{$branches->location}}</option>
                                                        @endforeach
                                                        
                                                   </select>
                                               </div>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
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
                                   </div>
                                   <div class="modal-footer" align="right">
                                       <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="View Report" />
                                       <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                   </div>
                                   
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    
                    
                    
                </main>
              
@endsection


@section('script')

<script>
$(document).ready(function(){
  $('#payrollmenu').addClass('active');
    $('#payrollmenu_icon').addClass('active');
    $('#payrollststement').addClass('navbtnactive');
	/*var empTable=$("#emp_bank_table").DataTable({
			"columns":[{data:'fig_name'}, {data:'fig_value'}, {data:'emp_cnt'}],
			"ordering":false, "order":[],
			"columnDefs": [{
					"targets":1, 
					"className":"text-right",
					render: function(data, type, row){
						return parseFloat(data).toFixed(2);
					}
				}],
			"createdRow": function( row, data, dataIndex ){
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				//$( row ).attr( 'id', 'row-'+data.id );//$( row ).data( 'refid', data[3] );
			}
		});*/
	
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
	
	$("#frmSearch").on('submit', function(event){
	  event.preventDefault();
	  
	  $.ajax({
	   url:"previewPaySummary",
	   method:'POST',
	   data:$(this).serialize(),
	   dataType:"JSON",
	   beforeSend:function(){
		//$('#find_employee').prop('disabled', true);
	   },
	   success:function(data){
		//alert(JSON.stringify(data));
		var html = '';
		//empTable.clear();
		
		if(data.errors){
			html = '<div class="alert alert-danger">';
			for(var count = 0; count < data.errors.length; count++){
			  html += '<p>' + data.errors[count] + '</p>';
			}
			html += '</div>';
			$('#search_result').html(html);
		}else{
			$("#emp_bank_table tbody tr").each(function(index, obj){
				//console.log($(this).data('figid'));
				if($(this).data('figid')!=''){
					var disp_amt = parseFloat(Math.abs(data.payment_detail[$(this).data('figid')].amt)).toFixed(2);
					$(this).children('td[data-cap="amt"]').html(disp_amt).addClass('text-right');
					$(this).children('td[data-cap="cnt"]').html(data.payment_detail[$(this).data('figid')].cnt).addClass('text-center');
				}
			});
			//empTable.rows.add(data.payment_detail);
			//empTable.draw();
			
			$('div[data-accdesc="hjaela"]').html(parseFloat(data.br_jaela).toFixed(2));
			$('div[data-accdesc="hseeduwa"]').html(parseFloat(data.br_seeduwa).toFixed(2));
			$('div[data-accdesc="other"]').html(parseFloat(data.br_other).toFixed(2));
			$('div[data-accdesc="cash"]').html(parseFloat(data.br_none).toFixed(2));
			
			$("#lbl_date_fr").html(data.work_date_fr);
			$("#lbl_date_to").html(data.work_date_to);
			$("#lbl_duration").show();
			$("#payment_period_id").val(data.payment_period_id);
			$("#payslip_process_type_id").val($("#payroll_process_type_id").find(":selected").val());
			$("#lbl_payroll_name").html($("#payroll_process_type_id").find(":selected").text());
			//$('#find_employee').prop('disabled', false);
			
			$("#rpt_payroll_id").val($("#payroll_process_type_id").find(":selected").val());
			$("#rpt_location_id").val($("#location_filter_id").find(":selected").val());
			$("#rpt_period_id").val($("#period_filter_id").find(":selected").val());
			$("#rpt_info").val(data.work_date_fr+" To "+data.work_date_to+" ("+$("#payroll_process_type_id").find(":selected").text()+")");
			
			//$("#print_record").prop('disabled', false);
			//$("#print_record").removeClass('btn-light');
			
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