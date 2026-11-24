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
                                    <span>Gratuity Provision</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid mt-2 p-0 p-2">
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="default">
                                    <form id="frmExport" method="post">
                                    {{ csrf_field() }}
                                        <div class="card card-header-actions mb-4">
                                            <div class="card-header">
                                                {{ $rpt_title }}
                                                <span id="lbl_duration" style="display:none; margin-right:auto; padding-left:10px;">
                                                    <span id="lbl_date_fr">&nbsp;</span><!-- To --><span id="lbl_date_to">&nbsp;</span>
                                                    (<span id="lbl_payroll_name">&nbsp;</span>)
                                                </span>
                                                <div>
                                                    <button type="button" name="find_employee" id="find_employee" class="btn btn-primary btn-sm">  <i class="fas fa-search mr-2"></i>Search</button>
                                                    <button type="submit" name="print_record" id="print_record" disabled="disabled" class="btn btn-secondary btn-sm btn-light d-none" onclick="this.form.action='{{ url('DownloadHeldSalaries') }}'" style="width:auto;" value="1">Download XLS</button>
                                                    <button type="submit" name="print_record" id="print_record_pdf" disabled="disabled" class="btn btn-secondary btn-sm btn-light d-none" onclick="this.form.action='{{ url('DownloadHeldSalaries') }}'" style="width:auto;" value="2">Download PDF</button>
                                                </div>
                                            </div>
                                            
                                            <div class="card-body">
                                                
                                                <div id="divPrint" class="center-block fix-width scroll-inner" style="margin-top:0px;">
                                                    <div class="{{ ($rpt_table!='EMP_GRATUITY')?'d-none':'' }}">
                                                        <table class="table table-striped table-bordered table-sm small nowrap display" id="datatable_empGratuity" width="100%" cellspacing="0">
                                                            <thead>
                                                                <tr>
                                                                    <th>EPF No.</th>
                                                                    <th style="width:300px;" class="empinfo_dispname">Name</th>
                                                                    <th>Join Date</th>
                                                                    <th>Work Years</th>
                                                                    <th>Basic Salary</th>
                                                                    <th>Eligible Gratuity</th>
                                                                </tr>
                                                            </thead>
                                                         
                                                            <tbody class="">
                                                            </tbody>
                                                            
                                                        </table>
                                                    </div>
                                                    <div class="{{ ($rpt_table!='PRO_GRATUITY')?'d-none':'' }}">
                                                        <table class="table table-striped table-bordered table-sm small nowrap display" id="datatable_proGratuity" width="100%" cellspacing="0">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:300px;" class="empinfo_dispname">Name</th>
                                                                    <th>EPF No.</th>
                                                                    <th>Join Date</th>
                                                                    <th>Work Years</th>
                                                                    <th>Basic Salary</th>
                                                                    <th>Gratuity Provision</th>
                                                                </tr>
                                                            </thead>
                                                         
                                                            <tbody class="">
                                                            </tbody>
                                                            
                                                        </table>
                                                    </div>
                                                    
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
                                                   <select name="location_filter_id" id="location_filter_id" class="form-control shipClass chosen-select nest_head form-control-sm" style="" data-findnest="deptnest" >
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
                                                   <select name="department_filter_id" id="department_filter_id" class="form-control chosen-select form-control-sm" style="" data-nestname="deptnest" >
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
                                               <label class="control-label col" >Payroll type</label>
                                               <div class="col">
                                                 <select name="payroll_process_type_id" id="payroll_process_type_id" class="form-control chosen-select form-control-sm" >
                                                    <option value="" disabled="disabled" selected="selected">Please select</option>
                                                    @foreach($payroll_process_type as $payroll)
                                                    
                                                    <option value="{{$payroll->id}}" data-totdays="{{$payroll->total_work_days}}">{{$payroll->process_name}}</option>
                                                    @endforeach
                                                    
                                                 </select>
                                               </div>
                                            </div>
                                        </div>
                                        <div class="row d-none">
                                        	<div class="form-group col-md-6">
                                            	<label class="control-label col">Working Period From</label>
                                                <div class="col">
                                                   <select name="period_filter_id_fr" id="period_filter_id_fr" class="form-control chosen-select form-control-sm" style="" >
                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                        @for($i=(count($payment_period)-1);$i>=0;$i--)
                                                        
                                                        <option value="{{$payment_period[$i]->id}}" disabled="disabled" data-payroll="{{$payment_period[$i]->payroll_process_type_id}}" style="display:none;">{{$payment_period[$i]->payment_period_fr}} to {{$payment_period[$i]->payment_period_to}}</option>
                                                        @endfor
                                                        
                                                   </select>
                                               </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                            	<label class="control-label col">Working Period To</label>
                                                <div class="col">
                                                   <select name="period_filter_id_to" id="period_filter_id_to" class="form-control chosen-select form-control-sm" style="" >
                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                        @foreach($payment_period as $schedule)
                                                        
                                                        <option value="{{$schedule->id}}" disabled="disabled" data-payroll="{{$schedule->payroll_process_type_id}}" style="display:none;">{{$schedule->payment_period_fr}} to {{$schedule->payment_period_to}}</option>
                                                        @endforeach
                                                        
                                                   </select>
                                               </div>
                                            </div>
                                            
                                        </div>
                                   </div>
                                   <div class="modal-footer form-group d-flex align-items-center justify-content-between" align="right">
                                       <div>
                                       		<!--input type="checkbox" checked="checked" id="chk_held" name="chk_held" value="held" />
                                            <label for="chk_held">Held</label>
                                            
                                            <input type="checkbox" checked="checked" id="chk_released" name="chk_released" value="released" />
                                            <label for="chk_released">Released</label-->
                                       </div>
                                       <div>
                                           <input type="submit" name="action_button" id="action_button" class="btn btn-primary" value="View Gratuity" />
                                           <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
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
(function() {
	var cssMain = document.createElement('link');
	cssMain.href = "{{ asset('/public/payroll/css/locationGratuity.css') }}";
	cssMain.rel = 'stylesheet';
	cssMain.type = 'text/css';
	document.getElementsByTagName('head')[0].appendChild(cssMain);
	/*
	
	*/
})();
</script>

<script>
$(document).ready(function(){
	$('#payroll_link').addClass('active');
    $("#payrollreport").addClass('navbtnactive');
	
	var empGratuity=$("#datatable_empGratuity").DataTable({
			"columns":[{data:'emp_epfno'}, {data:'emp_name'}, 
				{data:'join_date'}, 
				{data:'tot_years'},
				{data:'basic_sal'}, 
				{data:'eligible_gratuity'}
			],
			"order":[],
			
			"createdRow": function( row, data, dataIndex ){
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$( row ).attr( 'id', 'row_emp-'+data.id );//$( row ).data( 'refid', data[3] );
			}
		});
	
	var proGratuity=$("#datatable_proGratuity").DataTable({
			"columns":[{data:'emp_name'}, {data:'emp_epfno'}, 
				{data:'join_date'}, 
				{data:'tot_years'},
				{data:'basic_sal'}, 
				{data:'pro_gratuity'}
			],
			"order":[],
			
			"createdRow": function( row, data, dataIndex ){
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$( row ).attr( 'id', 'row_pro-'+data.id );//$( row ).data( 'refid', data[3] );
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
		
		$('#period_filter_id_fr').trigger('chosen:updated');
		
		$('#period_filter_id_to').val('');
		$('#period_filter_id_to option').prop("disabled", true);
		$('#period_filter_id_to option:not(:first-child)').hide();
		$('#period_filter_id_to option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').prop("disabled", false);
		$('#period_filter_id_to option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').show();
		
		$('#period_filter_id_to').trigger('chosen:updated');
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
		
		$(childobj).trigger("chosen:updated");//$('select[data-nestname="'+nestname+'"]')
	}
	
	$("#frmSearch").on('submit', function(event){
	  event.preventDefault();
	  
	  obj_table = null; var acturl = 'checkEmpGratuity';
	  
	  if($('#rpt_tablename').val()=='EMP_GRATUITY'){
		obj_table = empGratuity;//acturl = "checkPaySummary";
	  }else{//if($('#rpt_tablename').val()=='PRO_GRATUITY'){
		obj_table = proGratuity;//acturl = "checkSalaryRec";
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
			$("#lbl_date_fr").html('');//(data.work_date_fr);
			$("#lbl_date_to").html('');//(data.work_date_to);
			$("#lbl_duration").show();
			$("#payment_period_id_fr").val('');//(data.payment_period_id_fr);//id_fr
			$("#payment_period_id_to").val('');//(data.payment_period_id_to);//id_to
			$("#payslip_process_type_id").val($("#payroll_process_type_id").find(":selected").val());
			$("#lbl_payroll_name").html($("#payroll_process_type_id").find(":selected").text());
			//$('#find_employee').prop('disabled', false);
			
			$("#rpt_payroll_id").val($("#payroll_process_type_id").find(":selected").val());
			$("#rpt_location_id").val($("#location_filter_id").find(":selected").val());
			$("#rpt_dept_id").val($("#department_filter_id").find(":selected").val());
			$("#rpt_dept_name").val($("#department_filter_id").find(":selected").text());
			$("#rpt_period_id").val('');//($("#period_filter_id").find(":selected").val());
			var rptInfo = $("#payroll_process_type_id").find(":selected").text();//+" ("+data.work_date_fr+" To "+data.work_date_to+")";
			$("#rpt_info").val(rptInfo);
			//$("#rpt_chk_held").val(rpt_chk_held);
			//$("#rpt_chk_released").val(rpt_chk_released);
			
			$("#print_record").prop('disabled', false);
			$("#print_record").removeClass('btn-light');
			$("#print_record_pdf").prop('disabled', false);
			$("#print_record_pdf").removeClass('btn-light');
			
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