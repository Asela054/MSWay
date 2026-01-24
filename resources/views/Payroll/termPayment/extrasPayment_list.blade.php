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
                                    <span>Salary Advance / Bonus Payments</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid mt-2 p-0 p-2">
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="default">
                                    <div class="card">
                                        <!-- ----
                                        <div class="card-header">
                                            Payment Details&nbsp;<span id="employee_payday_name"></span>
                                            <div>
                                                <div style="display:inline;">
                                                   select locations
                                                </div>
                                                allocate/upload html buttons
                                            </div>
                                            
                                        </div>
                                        ---- -->
                                        <div class="card-body p-0 p-2">
                                        	<div class="row justify-content-end">
                                            	<div class="col-12 text-right">
                                                	<select name="location_filter" id="location_filter" class="shipClass form-control form-control-sm" style="display:inline; width:300px;" >
                                                        <option value="">Please Select</option>
                                                        @foreach($branch as $branches)
                                                        
                                                        <option value="{{$branches->location}}">{{$branches->location}}</option>
                                                        @endforeach
                                                        
                                                   </select>
                                                <!--/div>
                                                <div class="text-right"--><!-- class="col-sm-12 col-md-6 col-lg-3 col-xl-3 text-right" -->
                                                	<button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm">Allocate</button>
                                                    <button type="button" name="create_record" id="create_record" class="btn btn-secondary btn-sm" disabled="disabled" style="display:none;">Add</button>
                                                    <button type="button" name="upload_record" id="upload_record" class="btn btn-secondary btn-sm" disabled="disabled" style="display:none;">Upload</button>
                                                </div>
                                            </div>
                                            <div class="row">
                                            	<div class="col-12">
                                                	<span id="" style="margin-right:auto; padding-left:10px;">
                                                        <div class="alert alert-primary" role="alert">
                                                            Payment Details:&nbsp;<span id="employee_payday_name">General</span>
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                            <form id="frmInfo" method="post">
                                                {{ csrf_field() }}	
                                                <div class="sbp-preview">
                                                    <div class="sbp-preview-content">
                                                       <span id="form_result" class="col d-none"></span>
                                                       @if (\Session::has('success'))
                                                       <span id="row_heading" style="color:blue;" class="col">
                                                       	{{ \Session::get('success') }}
                                                       </span>
                                                       @endif
                                                       <!--div class="row">
                                                       	   <div class="form-group col-md-12">
                                                        		<label class="control-label col">Term Remuneration Name</label>
                                                           </div>
                                                       </div-->
                                                       <div class="row">
                                                           <div class="form-group col-md-6">
                                                               <label class="control-label col" >Payment Name</label>
                                                               <div class="col">
                                                                 <input type="text" name="payment_name" id="payment_name" class=" form-control form-control-sm" readonly="readonly" />
                                                               </div>
                                                           </div>
                                                           <div class="form-group col-md-6">
                                                               <label class="control-label col" >Amount</label>
                                                               <div class="col">
                                                                 <input type="text" name="employees_extra_entitle_amount" id="employees_extra_entitle_amount" class=" form-control form-control-sm" readonly="readonly" />
                                                               </div>
                                                           </div>
                                                       </div>
                                                       
                                                    </div>
                                                    <!--div class="sbp-preview-text" style="text-align:right; padding:10px;">
                                                        
                                                    </div-->
                                                </div>
                                            </form>
                                            
                                            <div class="datatable table-responsive" style="margin-top:10px;">
                                                <table class="table table-bordered table-hover" id="emptable" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th class="actlist_col "><label class="form-check-label"><span class=""><input id="chk_approve" class="" type="checkbox" style="" title="" disabled="disabled"></span> <span style="display:block;">Select</span></label></th>
                                                            <th>Name</th>
                                                            <th>Office</th>
                                                            <th>Salary</th>
                                                            <th>Group</th>
                                                            <th class="actlist_col">Actions</th>
                                                        </tr>
                                                    </thead>
                                                 
                                                    <tbody class="">
                                                    </tbody>
                                                    
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="formModal" class="modal fade" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                               <div class="modal-header">
                                   <h5 class="modal-title" id="formModalLabel">Employee Salary Additions</h5>
                                   
                                   <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                               </div>
                               <div class="modal-body">
                                   <form id="frmMore" class="" method="post">
                                   {{ csrf_field() }}
                                   		<div class="sbp-preview">
                                           <div class="sbp-preview-content" style="padding:15px 5px;">
                                               <span id="allocate_result">&nbsp;</span>
                                               <div id="employee_info" class="row">
                                               		<div class="form-group col">
                                                       <label class="control-label col" >Employee</label>
                                                       <div class="col">
                                                         <input type="text" name="form_modal_employee" id="form_modal_employee" class=" form-control form-control-sm" readonly="readonly" />
                                                       </div>
                                                    </div>
                                               </div>
                                               <div id="payday_info" class="row">
                                               		<div class="form-group col">
                                                       <label class="control-label col" >Pay day</label>
                                                       <div class="col">
                                                         <select name="employee_paydays" id="employee_paydays" class="form-control form-control-sm" required>
                                                            <option value="0" selected="selected">General</option>
                                                            @php
                                                            $payroll_process = array('1'=>'(Monthly)',
                                                            							'2'=>'(Weekly)',
                                                                                    	'3'=>'(Bi-weekly)',
                                                                                    	'4'=>'(Daily)'
                                                                                 	);
                                                            @endphp
                                                            @foreach($paydays as $payday)
                                                            
                                                            <option value="{{$payday->id}}" data-payroll="{{$payday->payroll_process_type_id}}" style="" >{{$payday->payday_name}} {{$payroll_process[$payday->payroll_process_type_id]}}</option>
                                                            @endforeach
                                                            
                                                         </select>
                                                       </div>
                                                    </div>
                                               </div>
                                               <div class="row">
                                                   <div class="form-group col-md-6">
                                                       <label class="control-label col" >Type</label>
                                                       <div class="col">
                                                         <select name="remuneration_extra_id" id="remuneration_extra_id" class=" form-control form-control-sm" >
                                                            <option value="" disabled="disabled" selected="selected" data-optentitle="0">Select Payment</option>
                                                            @foreach($remuneration_extras as $payment)
                                                            
                                                            <option value="{{$payment->id}}" data-optentitle="{{$payment->extra_entitlement}}">{{$payment->extras_label}}</option>
                                                            @endforeach
                                                            
                                                         </select>
                                                       </div>
                                                   </div>
                                                   <div class="form-group col-md-6">
                                                       <label class="control-label col" >Payment</label>
                                                       <div class="col">
                                                         <input type="text" name="employee_extra_entitle_amount" id="employee_extra_entitle_amount" class=" form-control form-control-sm" autocomplete="off" />
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>
                                           <div class="" align="right" style="padding:5px; border-top:none;">
                                               <input type="submit" name="setup_button" id="setup_button" class="btn btn-warning" value="Allocate Payment" />
                                               <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                               <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" value="" />
                                               <input type="hidden" name="hidden_id" id="hidden_id" /><!-- newly created extras payment -->
                                               <input type="hidden" name="hidden_optentitle" id="hidden_optentitle" /><!-- opt-entitlement -->
                                               <input type="hidden" name="employee_work_rate_id" id="employee_work_rate_id" />
                                               <input type="hidden" name="payment_period_id" id="payment_period_id" />
                                               
                                               <input type="hidden" name="employee_payday_id" id="employee_payday_id" value="" />
                                               
                                               <!--input type="hidden" name="employee_term_payment_id" id="employee_term_payment_id" value="0" />
                                               <input type="hidden" name="employee_term_payment_total" id="employee_term_payment_total" /-->
                                               
                                               <!--input type="hidden" name="employee_term_payment_cancel" id="employee_term_payment_cancel" /-->
                                               
                                               <!--input type="hidden" name="remuneration_id" id="remuneration_id" /-->
                                           </div>
                                        </div>
                                   </form>
                                   <div class="datatable table-responsive" style="margin-top:10px;">
                                        <table class="table table-bordered table-hover" id="titletable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr> 
                                                    <th>Payment Description</th>
                                                    <th>Date</th>
                                                    <th>Value</th>
                                                    <th class="actlist_col">Action</th>   
                                                </tr>
                                            </thead>
                                          
                                            <!--tbody>
                                            
                                            
                                                <tr>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                </tr>
                                               
                                             
                                            </tbody-->
                                            
                                            <tfoot>
                                            	@foreach($remuneration_list as $footer_row)
                                                <tr id="adv_{{$footer_row->id}}">
                                                	<td colspan="2">{{$footer_row->remuneration_name}} Total</td>
                                                    <td>0.00</td>
                                                    <td class="actlist_col"><input type="checkbox" class="chk_term_approve" data-refgrp="{{$footer_row->id}}" data-regadv="0" /></td>
                                                </tr>
                                                @endforeach
                                            </tfoot>
                                        </table>
                                    </div> 
                                   
                               </div>
                               <!--div class="modal-footer" align="right">
                                   
                               </div-->
                               
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
    $('#policymanagement').addClass('navbtnactive');
    
 var empTable=$("#emptable").DataTable({
		"columns":[{data:'payment_cancel'}, {data:'emp_first_name'}, {data:'location'}, {data:'basic_salary'}, 
				{data:'process_name'}, {data:'id'}],
		"order":[],
		"columnDefs":[{
				"targets":0, 
				"className":'actlist_col',
				"orderable":false,
				render:function( data, type, row ){
					var check_str=(data==0)?' checked="checked"':'';
					var block_str=($("#hidden_id").val()=='')?' disabled="disabled"':'';
					return '<input type="checkbox" class="freeze" data-refid="'+row.id+'" data-refemp="'+row.payroll_profile_id+'"'+check_str+block_str+' data-entval="'+row.ent_amount+'" />';
				}
			},{
				"targets":4,
				render:function( data, type, row ){
					return '<div class="badge badge-primary badge-pill">'+data+'</div>';
				}
			},{
				"targets":5,
				"className":'actlist_col',
				"orderable":false,
				render:function( data, type, row ){
					return '<button class="btn btn-datatable btn-icon btn-primary review" data-refid="'+row.payroll_profile_id+'"><i class="fas fa-list"></i></button>';
				}
			}],
		"createdRow": function( row, data, dataIndex ){
			//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
			$( row ).attr( 'id', 'row-'+data.payroll_profile_id );//$( row ).data( 'refid', data[3] );
		},
		"drawCallback":function( settings ){
			var objs_visible=$('input.freeze[type=checkbox]').length;
			var chk_disabled=(objs_visible==0);//?true:false;
			var chk_selected=((objs_visible>0)&&($('input.freeze[type=checkbox]:checked').length==objs_visible));
			$('#chk_approve').prop('disabled', chk_disabled);
			$('#chk_approve').prop('checked', chk_selected);
			
		}
	});
 $('#location_filter').on('keyup change', function () {
		if (empTable.columns(2).search() !== this.value) {
			empTable.columns(2).search(this.value).draw();
		}
  });
 
 var remunerationTable=$("#titletable").DataTable({
		"info":false,
		"paging":false,
		"searching":false,
		"columns":[{data:'remuneration_name'}, {data:'payment_date'}, {data:'payment_amount'}, 
						   {data:'payment_cancel'}],
		"columnDefs":[{
				"targets":3, 
				"className":'actlist_col',
				"orderable":false,
				render:function( data, type, row ){
					return '<button type="button" class="delete btn btn-datatable btn-icon btn-danger" data-refid="'+row.id+'" ><i class="fas fa-trash"></i></button>';
				}
			}],
		"createdRow": function( row, data, dataIndex ){
			$( row ).attr( 'id', 'row-'+data.id);
		}
	});
 
 var _token = $('#frmMore input[name="_token"]').val();//var _token = $('#frmInfo input[name="_token"]').val();
 var remuneration_id, remuneration_extra_id, pres_val;
 
 $('#find_employee').click(function(){
	remunerationTable.clear();
	
	$('#form_result').html('');
	$('#allocate_result').html('');
	
	var txtlock=($("#remuneration_extra_id").find(":selected").data('optentitle')=='0')?false:true;
	$("#employee_extra_entitle_amount").prop("readonly", txtlock);
	
	$('#payroll_profile_id').val('');
	$('#employee_extra_entitle_amount').val('');
	
	$('#formModal div.datatable, #employee_info').addClass('sect_bg');//hide-table
	
	$('#payday_info').show();
	
	$('#formModal').modal('show');
 });
 
 $("#remuneration_extra_id").change(function(){
	if($("#employee_extra_entitle_amount").val()!=""){
		pres_val = $("#employee_extra_entitle_amount").val();
	}
	
	var opt_entitlement=$("#remuneration_extra_id").find(":selected").data('optentitle');
	$("#hidden_optentitle").val(opt_entitlement);
	
	if($("#payroll_profile_id").val()!=""){
		$("#employee_extra_entitle_amount").prop("readonly", false);
	}else{
		if(opt_entitlement=='0'){
			$("#employee_extra_entitle_amount").prop("readonly", false);
			$("#employee_extra_entitle_amount").val(pres_val);
		}else{
			$("#employee_extra_entitle_amount").prop("readonly", true);
			$("#employee_extra_entitle_amount").val("");
		}
	}
 });
 
 function viewEmployees(payment_id, payday_id, payment_opt){
  $.ajax({
   url:"checkExtraPayment", //"checkTermPayment",
   method:'POST',
   data:{id:payment_id, payment_group:payday_id, entopt:payment_opt, _token:_token},
   dataType:"JSON",
   beforeSend:function(){
    $('#find_employee').prop('disabled', true);
   },
   success:function(data){
	//alert(JSON.stringify(data));
    empTable.clear();
	empTable.rows.add(data.employee_detail);
	empTable.draw();
	
	$('#find_employee').prop('disabled', false);
	
   }
  })
 }
 
 

 $('#frmMore').on('submit', function(event){
  event.preventDefault();
  var action_url = "{{ route('addExtraPayment') }}"; //addTermPayment
  
 
  /*
  alert(action_url);
  */
  
  if($("#payroll_profile_id").val()!=''){
	  $.ajax({
	   url: action_url,
	   method:"POST",
	   data:$(this).serialize(),
	   dataType:"json",
	   success:function(data)
	   {//alert(JSON.stringify(data));
		   
		var html = '';
		if(data.errors){
			html = '<div class="alert alert-danger">';
			for(var count = 0; count < data.errors.length; count++){
			  html += '<p>' + data.errors[count] + '</p>';
			}
			html += '</div>';
		}
		if(data.success){
		 html = '<div class="alert alert-success">' + data.success + '</div>';
		 // $('#frmInfo')[0].reset();
		 // $('#titletable').DataTable().ajax.reload();
		 // location.reload()
		 
		 $.each(data.new_rows, function(index, obj){
			 var selected_tr=remunerationTable.row('#row-'+obj.id+'')
			 
			 if(selected_tr.length==0){
				var rowNode = remunerationTable.row.add({'id':obj.id,
					'remuneration_name':obj.remuneration_name,
					'payment_date':'Now',
					'payment_amount':obj.payment_amount,
					'payment_cancel':0}).draw( false ).node();
			 }else{
				 var d=selected_tr.data();
				 d.payment_date='Now';
				 d.payment_amount=obj.payment_amount;
				 d.payment_cancel=0;
				 
				 remunerationTable.row(selected_tr).data(d).draw();
			 }
		 });
		 
		 
		 if($("#remuneration_extra_id").find(":selected").val()==$("#hidden_id").val()){
			 var empterm_tr=empTable.row('#row-'+$("#payroll_profile_id").val()+'')
			 var d=empterm_tr.data();
			 d.id=data.new_obj.id;
			 d.payment_cancel=0;
			 empTable.row(empterm_tr).data(d).draw();
		 }
		 
		 //$("#employee_term_payment_id").val(data.term_id);
		 //$("#employee_term_payment_total").val(data.term_total);
		 //$("#remuneration_id").val(data.term_grpid);//$("#employee_term_payment_cancel").val(0);
		 
		 //checkbox code
		 //var summary_tr = $("#");//#titletable_summary
		 //summary_tr.children('td:nth-child(2)').html(parseFloat(data.term_total).toFixed(2));
		 //$("#chk_term_approve").prop('disabled', false);
		 //$("#chk_term_approve").prop('checked', true);//data.term_cancel
		 //checkbox code 2025-01-17
		 //select summary_tr from footer where footer-row-id mapped to remuneration-id to update term-id, term-total
		 $('.chk_term_approve[data-refgrp="'+data.term_grpid+'"]').prop('disabled', false);
		 $('.chk_term_approve[data-refgrp="'+data.term_grpid+'"]').prop('checked', true);//data.term_cancel
		 $('.chk_term_approve[data-refgrp="'+data.term_grpid+'"]').attr('data-regadv', data.term_id);
		 var summary_tr = $("#adv_"+data.term_grpid);
		 summary_tr.children('td:nth-child(2)').html(parseFloat(data.term_total).toFixed(2));
		 //.
		 
		 
		}
		$('#allocate_result').html(html);
	   }
	  });
  }else{
	  var err_desc='';
	  var opt_entitlement=$("#remuneration_extra_id").find(":selected").data('optentitle');
	  if($("#remuneration_extra_id").find(":selected").val()==''){
		  err_desc='Select the payment name';
	  }else if(($("#employee_extra_entitle_amount").val()=='')&&(opt_entitlement=='0')){
		  err_desc='Payment value is required';
	  }
	  if(err_desc==''){
		  $("#hidden_id").val($("#remuneration_extra_id").find(":selected").val());
		  //$("#hidden_optentitle").val(opt_entitlement);
		  $("#payment_name").val($("#remuneration_extra_id").find(":selected").text());
		  $("#employees_extra_entitle_amount").val($("#employee_extra_entitle_amount").val());
		  
		  $("#employee_payday_id").val($("#employee_paydays").find(":selected").val());//set hidden payday-id
		  $("#employee_payday_name").html($("#employee_paydays").find(":selected").text());
		  
		  viewEmployees($('#hidden_id').val(), $("#employee_payday_id").val(), opt_entitlement);//$("#hidden_optentitle").val()
		  $("#formModal").modal('hide');
	  }else{
		  $('#allocate_result').html('<div class="alert alert-danger">'+err_desc+'</div>');
	  }
  }
 });

 $(document).on('click', '.review', function(){
  var id = $(this).data('refid');
  $('#form_result').html('');
  $('#allocate_result').html('');
  
  var par=$(this).parent().parent();
  $("#form_modal_employee").val(par.children("td:nth-child(2)").html());
  
  $.ajax({
   url :"EmployeeTermPaymentExtras/review/"+id+"", //"EmployeeTermPayment/"+id+"/review",
   dataType:"json",
   success:function(data){
    remunerationTable.clear();
	remunerationTable.rows.add(data.package);
	remunerationTable.draw();
	
	$("#employee_extra_entitle_amount").prop("readonly", false);//always-editable
	
	$('#employee_work_rate_id').val(data.work_id);//to be verified on insert/update
	$('#payment_period_id').val(data.period_id);
	
	$('#payroll_profile_id').val(id); // emp-payroll-id
	$('#employee_extra_entitle_amount').val('');
	
	if($('#formModal div.datatable, #employee-info').hasClass('sect_bg')){
		$('#formModal div.datatable, #employee_info').removeClass('sect_bg');
	}
	
	$('#payday_info').hide();
	
	//$("#employee_term_payment_id").val(data.term_id);
	//$("#employee_term_payment_total").val(data.term_total);
	//$("#remuneration_id").val(data.term_grpid);//$("#employee_term_payment_cancel").val(data.term_cancel);
	
	//checkbox code
	//var summary_tr = $("#");#titletable_summary
	//summary_tr.children('td:nth-child(2)').html(parseFloat(data.term_total).toFixed(2));
	//$("#chk_term_approve").prop('disabled', false);
	//$("#chk_term_approve").prop('checked', (data.term_cancel==0)?true:false);
	//checkbox code 2025-01-17
	//iterate remuneration check-boxes to set term-id, term-total
	$(".chk_term_approve").each(function(elem_index, obj){
		$(this).prop('disabled', false);
		
		var k = $(this).data("refgrp");
		var summary_tr = $("#adv_"+k);
		
		if(!(typeof data.footer_data[k] === "undefined")){
			$(this).attr('data-regadv', data.footer_data[k].term_id);
			summary_tr.children('td:nth-child(2)').html(parseFloat(data.footer_data[k].term_total).toFixed(2));
			$(this).prop("checked", (data.footer_data[k].term_cancel=="0"));
		}else{
			$(this).attr('data-regadv', '0');
			summary_tr.children('td:nth-child(2)').html('0.00');
			$(this).prop("checked", false);
		}
	});
	//.
	
	
    $('#formModal').modal('show');
    
   }
  })/**/
 });

 
 $(document).on('click', '.delete', function(){
  alert('to-be done: Delete employee salary addition');
 });
 
 function invVal(batch_cnt){
	return (batch_cnt>0)?1:0;
 }
 
 $('#chk_approve_dev').on('click', function(){
 	alert('to do optimizations');
 });
 $('#chk_approve').on('click', function(){
	var par_checked=$(this).is(':checked');
	$('#chk_approve').parent().addClass('masked_obj');
	//var objs_list=(par_checked)?$('input.freeze[type=checkbox]:not(:checked)'):$('input.freeze[type=checkbox]:checked');
	//var objs_cnt=$(objs_list).length;
	//var batch_inv = invVal(1);//update-multiple-records
	batchUpdate(par_checked, 1);//set objs-cnt as 1 to begin
	/*
	$(objs_list).each(function(index, obj){
		issuePayment($(obj), (objs_cnt-index), batch_inv);
	});
	*/
 });
 
 function batchUpdate(par_checked, objs_cnt){
	if(objs_cnt>0){
		//var par_checked=$('#chk_approve').is(':checked');
		//if(!(par_checked)&&(pos>0)){par_checked=!(par_checked)};
		var objs_list=(par_checked)?$('input.freeze[type=checkbox]:not(:checked)'):$('input.freeze[type=checkbox]:checked');
		objs_cnt=$(objs_list).length;
		//prev_cnt=$(objs_list[0]).length;
		var batch_inv = invVal(1);//update-multiple-records
		//alert(objs_cnt+'>>'+prev_cnt);
		if(objs_cnt>0){
			var emp_profile=$(objs_list[0]).data('refemp');
			var empterm_tr=empTable.row('#row-'+emp_profile+'')
			var d=empterm_tr.data();
			issuePayment(objs_list[0], objs_cnt, batch_inv, par_checked, d.work_id, d.period_id);
		}
	}
 }
 
 $(document).on('click', '.freeze', function(){
 	var batch_inv=invVal(0);//not-batch-update
	var emp_profile=$(this).data('refemp');
	var empterm_tr=empTable.row('#row-'+emp_profile+'')
	var d=empterm_tr.data();
	issuePayment($(this), 0, batch_inv, false, d.work_id, d.period_id);
 });
 
 function issuePayment(paymentref, batch_cnt, batch_inv, par_checked, work_id, period_id){
  $.ajax({
   url:"freezeExtrasPayment",
   method:'POST',
   data:{id:$(paymentref).data('refid'), payment_cancel:($(paymentref).is(":checked")?batch_inv:1-batch_inv), remuneration_extra_id:$('#hidden_id').val(), payroll_profile_id:$(paymentref).data('refemp'), payment_amount:(($(paymentref).data('entval')=='0')?$("#employees_extra_entitle_amount").val():$(paymentref).data('entval')), employee_work_rate_id:work_id, payment_period_id:period_id, _token:_token},
   dataType:"JSON",
   beforeSend:function(){
    $(paymentref).prop('disabled', true);
   },
   success:function(data){
	//alert(JSON.stringify(data));
	
	var act_finalize=false;
	var head_obj=null;
	
    if(data.result=='error'){
		if(batch_cnt==0){
			$(paymentref).prop('checked', !$(paymentref).prop('checked'));
			alert('Something wrong. Payment status cannot be changed at the moment\r\n'+data.msg);
		}
		
		else{
			alert('Payment update error. Please reload the page to abort process.');
			/*
			$(paymentref).addClass('check_inactive');
			*/
		}
		
	}else{
		$(paymentref).prop('disabled', false);
		$(paymentref).data('refid', data.payment_id);
		
		if(batch_cnt>0){
			$(paymentref).prop('checked', !$(paymentref).prop('checked'));
		}
	}
	
	
	if((batch_cnt-batch_inv)==0){
		act_finalize = true;
		head_obj = $('#chk_approve').parent();
	}
	
	if(act_finalize){
		if($(head_obj).hasClass('masked_obj')){
			$(head_obj).removeClass('masked_obj');
		}
		/*
		var objs_visible=$('input.finalize[type=checkbox]').length;
		var chk_selected=((objs_visible>0)&&($('input.finalize[type=checkbox]:checked').length==objs_visible));
		$('#chk_approve').prop('checked', chk_selected);
		*/
	}
	
	empTable.draw(false);//update-chk-approve-checked-value
	batchUpdate(par_checked, (batch_cnt-1));
   }
  })
 }
 
 $(document).on('click', '.chk_term_approve', function(){
 	//if($("#employee_term_payment_id").val()!='0')
	if($(this).attr('data-regadv')!='0'){
		var batch_inv=invVal(0);//not-batch-update
		var paycancel=$(this).is(":checked")?batch_inv:(1-batch_inv);//$('#chk_term_approve').is(":checked")
		var summary_tr = $("#adv_"+$(this).attr('data-refgrp'));
		var paymentamt = summary_tr.children('td:nth-child(2)').html();
		issueSummary($(this), paymentamt, paycancel);//$("#chk_term_approve")
	}else{
		$(this).prop('checked', false);//$("#chk_term_approve")
		alert('No payment found for the selected employee');
	}
 });
 
 function issueSummary(paymentref, paymentamt, paycancel){
	$.ajax({
	   url:"freezeTermPayment",
	   method:'POST',
	   data:{id:$(paymentref).attr('data-regadv'), payment_cancel:paycancel, remuneration_id:$(paymentref).attr('data-refgrp'), payroll_profile_id:$("#payroll_profile_id").val(), payment_amount:paymentamt, _token:_token},
	   dataType:"JSON",
	   beforeSend:function(){
		$(paymentref).prop('disabled', true);
	   },
	   success:function(data){
		//alert(JSON.stringify(data));
		
		if(data.result=='error'){
			$(paymentref).prop('checked', !$(paymentref).prop('checked'));
			var disp_msg = ($(paymentref).attr('data-regadv')=='-1')?'Payment is finalized':'Payment status cannot be changed at the moment';
			alert('Something wrong.\r\n'+disp_msg);
		}else{
			$(paymentref).prop('disabled', false);//$("#employee_term_payment_cancel").val(paycancel);
		}
		
	   }
	});
 }
 
});


</script>

@endsection