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
                                    <span>Salary Reconciliation</span>
                                </h1>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid mt-2 p-0 p-2">
                        <div class="card">
                            <div class="card-body p-0 p-2">
                                    <form id="frmExport" method="post">
                                    {{ csrf_field() }}

                                    <div class="col-12 text-right">
                                        <button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm px-3"><i class="fal fa-search mr-2"></i>Search</button>
                                        <button type="submit" name="print_record" id="print_record" disabled="disabled" class="btn btn-secondary btn-sm btn-light d-none" onclick="this.form.action='{{ url('DownloadHeldSalaries') }}'" style="width:auto;" value="1"><i class="fal fa-file-pdf mr-2"></i>Download XLS</button>
                                        <button type="submit" name="print_record" id="print_record_pdf" disabled="disabled" class="btn btn-secondary btn-sm btn-light d-none" onclick="this.form.action='{{ url('DownloadHeldSalaries') }}'" style="width:auto;" value="2"><i class="fal fa-file-pdf mr-2"></i>Download PDF</button>

                                    </div>
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
                                                <div id="divPrint" class="center-block fix-width scroll-inner" style="margin-top:0px;">
                                                        <table class="table table-bordered table-striped table-sm small nowrap w-100" id="datatable_salaryRec" width="100%" cellspacing="0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Duration</th>
                                                                    <!--th>Location Name</th-->
                                                                    <th>Basic</th>
                                                                    <th>BRA I</th>
                                                                    <th>BRA II</th>
                                                                    <th class="">Total for Tax</th>
                                                                    <th>Attendance</th>
                                                                    <th>Transport</th>
                                                                    <!--th>Other Addition</th-->
                                                                    <th>Salary Arrears</th>
                                                                    <th>Normal</th>
                                                                    <th>Double</th>
                                                                    <th>EPF<!--8--></th>
                                                                    <th>Salary Advance</th>
                                                                    <th>Telephone</th>
                                                                    <th>Other<!-- Deductions--></th>
                                                                    <th>Loans</th>
                                                                    <th>Total Deductions</th>
                                                                    <th>Total Pay</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="">
                                                            </tbody>
                                                        </table>
                                                </div>
                                                
                                                <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" value="" /><!-- edit loans -->
                                                <input type="hidden" name="payment_period_id_fr" id="payment_period_id_fr" value="" />
                                                <input type="hidden" name="payment_period_id_to" id="payment_period_id_to" value="" />
                                                <input type="hidden" name="payslip_process_type_id" id="payslip_process_type_id" value="" />
                                                
                                                <input type="hidden" name="rpt_period_id" id="rpt_period_id" value="" />
                                                <input type="hidden" name="rpt_info" id="rpt_info" value="-" />
                                                <input type="hidden" name="rpt_payroll_id" id="rpt_payroll_id" value="" />
                                                <input type="hidden" name="rpt_location_id" id="rpt_location_id" value="" />
                                                <input type="hidden" name="rpt_dept_id" id="rpt_dept_id" value="" />
                                                <input type="hidden" name="rpt_dept_name" id="rpt_dept_name" value="" />
                                                <input type="hidden" name="rpt_tablename" id="rpt_tablename" value="{{ $rpt_table }}" />
                                                <!--input type="hidden" name="rpt_chk_held" id="rpt_chk_held" value="0" />
                                                <input type="hidden" name="rpt_chk_released" id="rpt_chk_released" value="0" /-->
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
                                                                <label class="font-weight-bolder small">Branch</label>
                                                                <div class="col">
                                                                    <select name="location_filter_id" id="location_filter_id" class="custom-select custom-select-sm shipClass nest_head" style="" data-findnest="deptnest" >
                                                                            <option value="" disabled="disabled" selected="selected" data-regcode="">Please Select</option>
                                                                            @foreach($branch as $branches)  
                                                                            <option value="{{$branches->id}}" data-regcode="{{$branches->id}}">{{$branches->location}}</option>
                                                                            @endforeach
                                                                            
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                            <label class="font-weight-bolder small">Department</label>
                                                            <div class="col">
                                                                <select name="department_filter_id" id="department_filter_id" class="custom-select custom-select-sm" style="" data-nestname="deptnest" >
                                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                                        @foreach($department as $section)
                                                                       <option class="nestopt d-none" value="{{$section->id}}" data-nestcode="{{$section->company_id}}" data-sectcode="{{$section->id}}">{{$section->name}}</option> 
                                                                        @endforeach
                                                                        
                                                                </select>
                                                            </div>
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                            <label class="font-weight-bolder small" >Payroll type</label>
                                                            <div class="col">
                                                                <select name="payroll_process_type_id" id="payroll_process_type_id" class="form-control form-control-sm" >
                                                                    <option value="" disabled="disabled" selected="selected">Please select</option>
                                                                    @foreach($payroll_process_type as $payroll)
                                                                    
                                                                    <option value="{{$payroll->id}}" data-totdays="{{$payroll->total_work_days}}">{{$payroll->process_name}}</option>
                                                                    @endforeach
                                                                    
                                                                </select>
                                                            </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group col-md-6">
                                                                <label class="font-weight-bolder small">Working Period From</label>
                                                                <div class="col">
                                                                <select name="period_filter_id_fr" id="period_filter_id_fr" class="custom-select custom-select-sm" style="" >
                                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                                        @for($i=(count($payment_period)-1);$i>=0;$i--)
                                                                        
                                                                        <option value="{{$payment_period[$i]->id}}" disabled="disabled" data-payroll="{{$payment_period[$i]->payroll_process_type_id}}" style="display:none;">{{$payment_period[$i]->payment_period_fr}} to {{$payment_period[$i]->payment_period_to}}</option>
                                                                        @endfor
                                                                        
                                                                </select>
                                                            </div>
                                                            </div>
                                                            <div class="form-group col-md-6">
                                                                <label class="font-weight-bolder small">Working Period To</label>
                                                                <div class="col">
                                                                <select name="period_filter_id_to" id="period_filter_id_to" class="custom-select custom-select-sm" style="" >
                                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                                        @foreach($payment_period as $schedule)
                                                                        
                                                                        <option value="{{$schedule->id}}" disabled="disabled" data-payroll="{{$schedule->payroll_process_type_id}}" style="display:none;">{{$schedule->payment_period_fr}} to {{$schedule->payment_period_to}}</option>
                                                                        @endforeach
                                                                        
                                                                </select>
                                                            </div>
                                                            </div>
                                                            
                                                        </div>
                                                
                                                    <div class="form-row">
                                                        <div class="col-12 text-right">
                                                            <hr>
                                                            <input type="submit" name="action_button" id="action_button" class="btn btn-success btn-sm px-3" value="View Payments" />
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
$(document).ready(function(){
    $('#payrollmenu').addClass('active');
    $('#payrollmenu_icon').addClass('active');
    $('#payrollreport').addClass('navbtnactive');

	
	var salaryRec=$("#datatable_salaryRec").DataTable({
			"columns":[{data:'pay_dura'}, {data:'BASIC'}, {data:'BRA_I'}, {data:'add_bra2'}, 
				{data:'tot_fortax'}, {data:'ATTBONUS'}, {data:'add_transport'}, 
				//{data:'add_other'}, 
				{data:'sal_arrears2'}, {data:'OTHRS1'}, {data:'OTHRS2'}, 
				{data:'EPF8'}, {data:'sal_adv'}, {data:'ded_tp'}, {data:'ded_other'}, {data:'LOAN'}, 
				{data:'tot_ded'}, {data:'NETSAL'}],
			"order":[]
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
	/*
	$(".modal").on("shown.bs.modal", function(){
		var objinput=$(this).find('input[type="text"]:first-child');
		objinput.focus();
		objinput.select();
	});
	*/
	$("#payroll_process_type_id").on("change", function(){
		$('#period_filter_id_fr').val('');
		$('#period_filter_id_fr option').prop("disabled", true);
		$('#period_filter_id_fr option:not(:first-child)').hide();
		$('#period_filter_id_fr option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').prop("disabled", false);
		$('#period_filter_id_fr option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').show();
		
		$('#period_filter_id_to').val('');
		$('#period_filter_id_to option').prop("disabled", true);
		$('#period_filter_id_to option:not(:first-child)').hide();
		$('#period_filter_id_to option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').prop("disabled", false);
		$('#period_filter_id_to option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').show();
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
	  
	  obj_table = null; var acturl = 'checkPaySummary';
	  var area_info_str = '&nbsp;';
	  
	  if($('#rpt_tablename').val()=='PAY_SUMMARY'){
		obj_table = paySummary;
		//acturl = "checkPaySummary";
	  }else if($('#rpt_tablename').val()=='MASTER_SUMMARY'){
		obj_table = masterSummary;
		//acturl = "checkMasterSummary";
	  }else{//if($('#rpt_tablename').val()=='SALARY_REC'){
		obj_table = salaryRec;
		//acturl = "checkSalaryRec";
		area_info_str=$("#department_filter_id").find(":selected").text() + 
						" of " + $("#location_filter_id").find(":selected").text();
	  }
		
	  $.ajax({
	   url:acturl,
	   method:'POST',
	   data:$(this).serialize(),
	   dataType:"JSON",
	   beforeSend:function(){
		//$('#find_employee').prop('disabled', true);
	   },
	   success:function(data){
		//alert(JSON.stringify(data));
		var html = '';
		
		obj_table.clear();
		
		if(data.errors){
			html = '<div class="alert alert-danger">';
			for(var count = 0; count < data.errors.length; count++){
			  html += '<p>' + data.errors[count] + '</p>';
			}
			html += '</div>';
			$('#search_result').html(html);
		}else{
			//var rpt_chk_held=$("#chk_held").is(":checked")?$("#chk_held").val():0;
			//var rpt_chk_released=$("#chk_released").is(":checked")?$("#chk_released").val():0;
			obj_table.rows.add(data.employee_detail);
			obj_table.draw();
			$("#lbl_date_fr").html(data.work_date_fr);
			$("#lbl_date_to").html(data.work_date_to);
			$("#lbl_duration").show();
			$("#payment_period_id_fr").val(data.payment_period_id_fr);//id_fr
			$("#payment_period_id_to").val(data.payment_period_id_to);//id_to
			$("#payslip_process_type_id").val($("#payroll_process_type_id").find(":selected").val());
			$("#lbl_payroll_name").html($("#payroll_process_type_id").find(":selected").text());
			//$('#find_employee').prop('disabled', false);
			
			$("#rpt_payroll_id").val($("#payroll_process_type_id").find(":selected").val());
			$("#rpt_location_id").val($("#location_filter_id").find(":selected").val());
			$("#rpt_dept_id").val($("#department_filter_id").find(":selected").val());
			$("#rpt_dept_name").val($("#department_filter_id").find(":selected").text());
			$("#rpt_period_id").val($("#period_filter_id").find(":selected").val());
			$("#rpt_info").val(data.work_date_fr+" To "+data.work_date_to+" ("+$("#payroll_process_type_id").find(":selected").text()+")");
			//$("#rpt_chk_held").val(rpt_chk_held);
			//$("#rpt_chk_released").val(rpt_chk_released);
			
			$("#print_record").prop('disabled', false);
			$("#print_record").removeClass('btn-light');
			$("#print_record_pdf").prop('disabled', false);
			$("#print_record_pdf").removeClass('btn-light');
			
			$("#lbl_area_info").html(area_info_str);
			$('#formModal').modal('hide');
		}
	   }
	  })
	});
	
	/*
	$(".modal").on("hide.bs.modal", function(e){
		$(this).removeClass('active');
	});
	*/
	
});
</script>

@endsection