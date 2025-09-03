@extends('layouts.payrollapp')

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
                                    <div class="card card-header-actions mb-4">
                                        <div class="card-header">
                                            Payment Details
                                            <div>
                                                <div style="display:inline;">
                                                   <select name="location_filter" id="location_filter" class="custom-select shipClass" style="font-size:10px; width:300px;" >
                                                        <option value="">Please Select</option>
                                                        @foreach($branch as $branches)
                                                        
                                                        <option value="{{$branches->location}}">{{$branches->location}}</option>
                                                        @endforeach
                                                        
                                                   </select>
                                                </div>
                                                <button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm">Allocate</button>
                                                <button type="button" name="create_record" id="create_record" class="btn btn-secondary btn-sm">Add</button>
                                                <button type="button" name="upload_record" id="upload_record" class="btn btn-secondary btn-sm">Upload</button>
                                            </div>
                                            
                                        </div>
                                        <div class="card-body">
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
                                                       <div class="row">
                                                           <div class="form-group col-md-6">
                                                               <label class="control-label col" >Payment Name</label>
                                                               <div class="col">
                                                                 <input type="text" name="payment_name" id="payment_name" class="form-control" readonly="readonly" />
                                                               </div>
                                                           </div>
                                                           <div class="form-group col-md-6">
                                                               <label class="control-label col" >Amount</label>
                                                               <div class="col">
                                                                 <input type="text" name="payment_amount" id="payment_amount" class="form-control" readonly="readonly" />
                                                               </div>
                                                           </div>
                                                       </div>
                                                       
                                                    </div>
                                                    <!--div class="sbp-preview-text" style="text-align:right; padding:10px;">
                                                        
                                                    </div-->
                                                </div>
                                            </form>
                                            <form id="frmExport" method="post" onsubmit="return buttonSubmitHandler(1);">
                                                {{ csrf_field() }}
                                                <div class="sbp-preview">
                                                    <div class="sbp-preview-content d-flex align-items-center justify-content-between">
                                                        <div>
                                                            Download Payment Details
                                                        </div>
                                                        <!--div-->
                                                        	<div class="col">
                                                            	<select name="payroll_process_type_id" id="payroll_process_type_id" class="form-control" >
                                                                    <option value="" disabled="disabled" selected="selected">Please select payroll type</option>
                                                                    @foreach($payroll_process_type as $payroll)
                                                                    
                                                                    <option value="{{$payroll->id}}" data-totdays="{{$payroll->total_work_days}}">{{$payroll->process_name}}</option>
                                                                    @endforeach
                                                                    
                                                                 </select>
                                                            </div>
                                                            <div class="col">
                                                            	<select name="period_filter_id" id="period_filter_id" class="custom-select" style="" >
                                                                    <option value="" disabled="disabled" selected="selected">Please Select pay period</option>
                                                                    @foreach($payment_period as $schedule)
                                                                    
                                                                    <option value="{{$schedule->id}}" disabled="disabled" data-payroll="{{$schedule->payroll_process_type_id}}" style="display:none;">{{$schedule->payment_period_fr}} to {{$schedule->payment_period_to}}</option>
                                                                    @endforeach
                                                                    
                                                               </select>
                                                            </div>
                                                        <!--/div-->
                                                        <div>
                                                            <button type="submit" name="print_record" id="print_record_pdf" disabled="disabled" class="btn btn-secondary btn-sm btn-light" onclick="this.form.action='{{ url('DownloadTermPaymentPdf') }}'" style="width:auto;" value="2"><i class="fa fa-file-pdf"></i></button>
                                                        </div>
                                                    </div>
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
                                                         <input type="text" name="form_modal_employee" id="form_modal_employee" class="form-control" readonly="readonly" />
                                                       </div>
                                                    </div>
                                               </div>
                                               <div class="row">
                                                   <div class="form-group col-md-6">
                                                       <label class="control-label col" >Type</label>
                                                       <div class="col">
                                                         <select name="remuneration_id" id="remuneration_id" class="form-control" >
                                                            <option value="" disabled="disabled" selected="selected">Select Payment</option>
                                                            @foreach($remuneration as $payment)
                                                            
                                                            <option value="{{$payment->id}}">{{$payment->remuneration_name}}</option>
                                                            @endforeach
                                                            
                                                         </select>
                                                       </div>
                                                   </div>
                                                   <div class="form-group col-md-6">
                                                       <label class="control-label col" >Payment</label>
                                                       <div class="col">
                                                         <input type="text" name="eligible_amount" id="eligible_amount" class="form-control" autocomplete="off" />
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>
                                           <div class="" align="right" style="padding:5px; border-top:none;">
                                               <input type="submit" name="setup_button" id="setup_button" class="btn btn-warning" value="Allocate Payment" />
                                               <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                               <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" value="" />
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
                                        </table>
                                    </div> 
                                   
                               </div>
                               <!--div class="modal-footer" align="right">
                                   
                               </div-->
                               
                            </div>
                        </div>
                    </div>

                    <div id="confirmModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <form id="frmConfirm" method="post">
                            	{{ csrf_field() }}
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Payment Details</h5>
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                            
                                    </div>
                                    <div class="modal-body">
                                        <span id="confirm_result"></span>
                                        <!--h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4-->
                                        <div class="row">
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >Payment Name</label>
                                               <div class="col">
                                                 <input type="text" name="remuneration_name" id="remuneration_name" class="form-control" />
                                               </div>
                                           </div>
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >Type</label>
                                               <div class="col">
                                                 <select name="remuneration_type" id="remuneration_type" class="form-control" >
                                                    <option value="Addition">Addition</option>
                                                    <option value="Deduction">Deduction</option>
                                                 </select>
                                               </div>
                                           </div>
                                        </div>
                                        <div class="row">
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >EPF Allocation</label>
                                               <div class="col">
                                                 <select name="epf_payable" id="epf_payable" class="form-control" >
                                                    <option value="0">Without EPF</option>
                                                    <option value="1">With EPF</option>
                                                 </select>
                                               </div>
                                           </div>
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >Amount</label>
                                               <div class="col">
                                                 <input type="text" name="term_payment_amount" id="term_payment_amount" class="form-control" />
                                               </div>
                                           </div>
                                           
                                        </div>
                                        
                                        
                                        <div class="row">
                                           <div class="form-group col-md-12">
                                               <label class="control-label col" >Taxation</label>
                                               <div class="col">
                                                 <select name="taxcalc_spec_code" id="taxcalc_spec_code" class="form-control" >
                                                    <option value="0">None</option>
                                                    <option value="1">PAYE</option>
                                                 </select>
                                               </div>
                                           </div>
                                       </div>
                                       
                                       
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="action" id="action" value="Edit" />
                                        
                                        <input type="hidden" name="hidden_id" id="hidden_id" /><!-- newly created remuneration -->
                                        
                                        <input type="hidden" name="advanced_option_id" id="advanced_option_id" value="0" />
                                        
                                        <input type="hidden" name="allocation_method" id="allocation_method" value="M2" /><!-- terms -->
                                        
                                        <input type="hidden" name="employee_work_rate_work_days_exclusions" id="employee_work_rate_work_days_exclusions" value="0" />
                                        
                                        <input type="submit" name="action_button" id="action_button" class="btn btn-primary" value="Edit" />
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    
                    <div id="paymentCancelModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                	<h5 class="modal-title" id="loanCancelModalLabel">Confirmation</h5>
                              		<button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                        
                                </div>
                                <div class="modal-body">
                                    <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
                                </div>
                                <div class="modal-footer">
                                 	<button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div id="paymentUploadModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <form action="{{ route('uploadTermPayment') }}" method="post" target="_self" enctype="multipart/form-data" onsubmit="return colValidate();">
                                {{ csrf_field() }}
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="fileModalLabel">Confirmation</h5>&nbsp;
                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                                  </div>
                                  <div class="modal-body">
                                    <div class="row">
                                    	<div class="form-group col">
                                           <label class="control-label col" >
                                           	File Content :
                                               <a class="col" href="{{ url('/public/csvsample/salary_additions.csv') }}">
                                                CSV Format-Download Sample File
                                               </a>
                                           </label>
                                           <div class="col">
                                             <select name="remuneration_file" id="remuneration_file" class="form-control" >
                                                <option value="" disabled="disabled" selected="selected">Select File Content</option>
                                                @foreach($remuneration as $payment)
                                                
                                                <option value="{{$payment->id}}">{{$payment->remuneration_name}}</option>
                                                @endforeach
                                                
                                             </select>
                                           </div>
                                        </div>
                                    </div>
                                    
                                    <!--div class="row">
                                    	<div class="col">
                                        	
                                        </div>
                                    </div-->
                                    
                                    <p id="lblstatus"></p>
                                  </div>
                                  <div class="modal-footer">
                                    <input class="form-control col" type="file" name="file" id="file" style="padding-bottom:38px;">
                                    
                                    <button type="submit" name="import_file" value="import" class="btn btn-primary" required="required">Upload</button>
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
					return '<input type="checkbox" class="freeze" data-refid="'+row.id+'" data-refemp="'+row.payroll_profile_id+'"'+check_str+block_str+' />';
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
 
 
 $('#upload_record').click(function(){
  //$('#formModalLabel').text('Find Employee');
  //$('#action_button').val('Add');
  //$('#action').val('Add');
  //$('#form_result').html('');

  $('#paymentUploadModal').modal('show');
 });
 /**/
 
 $('#create_record').click(function(){
  //$('#formModalLabel').text('Add Remuneration');
  $('#action_button').val('Add Payment');
  $('#action').val('Add');
  $('#confirm_result').html('');
  
  $('#remuneration_name').val('');
  $('#term_payment_amount').val('');
  //$('#hidden_id').val('');

  $('#confirmModal').modal('show');
 });
 
 $('#find_employee').click(function(){
	remunerationTable.clear();
	
	$('#form_result').html('');
	$('#allocate_result').html('');
	
	$('#payroll_profile_id').val('');
	$('#eligible_amount').val('');
	
	$('#formModal div.datatable, #employee_info').addClass('sect_bg');//hide-table
	$('#formModal').modal('show');
 });
 
 function viewEmployees(payment_id){
  $.ajax({
   url:"checkTermPayment",
   method:'POST',
   data:{id:payment_id, _token:_token},
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
	
	$("#print_record_pdf").prop('disabled', false);
	$("#print_record_pdf").removeClass('btn-light');
   }
  })
 }
 
 var remuneration_id;
 
 /*
 $(document).on('click', '.edit', function(){
  var id = $(this).data('refid');//row#
  //var pack_id = $(this).data('refpack');
  $("#confirm_result").html('');
  $.ajax({
   url :"EmployeeLoan/"+id+"/edit",
   dataType:"json",
   success:function(data){
    $('#action').val('Edit');
	$('#action_button').val('Edit Loan');
	remuneration_id = id;
	$('#loan_amount').val(data.loan_obj.loan_amount);
	$('#loan_duration').val(data.loan_obj.loan_duration);
	$('#loan_type').val(data.loan_obj.loan_type);
	$('#loan_type').prop('disabled', true);
	$('#loan_date').val(data.loan_obj.loan_date);
    //$('#formModalLabel').text('Edit Remuneration');
    //$('#action_button').val('Edit');
    //$('#action').val('Edit');
	$('#hidden_id').val(data.loan_obj.id);
	
	$('#confirmModal').modal('show');
    
   }
  })
 
 });
 */

 $('#frmMore').on('submit', function(event){
  event.preventDefault();
  var action_url = "{{ route('addTermPayment') }}";
  
 
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
		 
		 var selected_tr=remunerationTable.row('#row-'+data.new_obj.id+'')
		 
		 if(selected_tr.length==0){
			var rowNode = remunerationTable.row.add({'id':data.new_obj.id,
				'remuneration_name':$("#remuneration_id").find(":selected").text(),
				'payment_date':'Now',
				'payment_amount':$("#eligible_amount").val(),
				'payment_cancel':0}).draw( false ).node();
		 }else{
			 var d=selected_tr.data();
			 d.payment_date='Now';
			 d.payment_amount=$('#eligible_amount').val();
			 
			 remunerationTable.row(selected_tr).data(d).draw();
		 }
		 
		 if($("#remuneration_id").find(":selected").val()==$("#hidden_id").val()){
			 var empterm_tr=empTable.row('#row-'+$("#payroll_profile_id").val()+'')
			 var d=empterm_tr.data();
			 d.id=data.new_obj.id;
			 d.payment_cancel=0;
			 empTable.row(empterm_tr).data(d).draw();
		 }
		 
		}
		$('#allocate_result').html(html);
	   }
	  });
  }else{
	  var err_desc='';
	  if($("#remuneration_id").find(":selected").val()==''){
		  err_desc='Select the payment name';
	  }else if($("#eligible_amount").val()==''){
		  err_desc='Payment value is required';
	  }
	  if(err_desc==''){
		  $("#hidden_id").val($("#remuneration_id").find(":selected").val());
		  $("#payment_name").val($("#remuneration_id").find(":selected").text());
		  $("#payment_amount").val($("#eligible_amount").val());
		  viewEmployees($('#hidden_id').val());
		  $("#formModal").modal('hide');
	  }else{
		  $('#allocate_result').html('<div class="alert alert-danger">'+err_desc+'</div>');
	  }
  }
 });
 
 $('#frmConfirm').on('submit', function(event){
  event.preventDefault();
  var action_url = '';
  
 
  if($('#action').val() == 'Add'){
   action_url = "{{ route('addRemuneration') }}";
  }else{
   action_url = "{{ route('Remuneration.update') }}";
  }
  /*
  alert(action_url);
  */
  $.ajax({
   url: action_url,
   method:"POST",
   data:$(this).serialize(),
   dataType:"json",
   success:function(data)
   {
       
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
	 
	 if($('#action').val()=='Add'){
	 	$('#hidden_id').val(data.new_obj.header.id);
		
		$('#payment_name').val(data.new_obj.header.remuneration_name);
		$('#payment_amount').val($("#term_payment_amount").val());
		
		empTable.clear();
		empTable.rows.add(data.new_obj.detail);
		empTable.draw();
		
		$("#confirmModal").modal('hide');
	 }
	 
	 
    }
    $('#confirm_result').html(html);
   }
  });
 });

 $(document).on('click', '.review', function(){
  var id = $(this).data('refid');
  $('#form_result').html('');
  $('#allocate_result').html('');
  
  var par=$(this).parent().parent();
  $("#form_modal_employee").val(par.children("td:nth-child(2)").html());
  
  $.ajax({
   url :"EmployeeTermPayment/"+id+"/review",
   dataType:"json",
   success:function(data){
    remunerationTable.clear();
	remunerationTable.rows.add(data.package);
	remunerationTable.draw();
	
	$('#payroll_profile_id').val(id); // emp-payroll-id
	$('#eligible_amount').val('');
	
	if($('#formModal div.datatable, #employee-info').hasClass('sect_bg')){
		$('#formModal div.datatable, #employee_info').removeClass('sect_bg');
	}
	
    $('#formModal').modal('show');
    
   }
  })/**/
 });

 
 $(document).on('click', '.delete', function(){
  alert('to-be done: Delete employee salary addition');
  /*
  remuneration_id = $(this).data('refid');
  
  $('#ok_button').text('OK');
  $('#paymentCancelModal').modal('show');
  */
 });
 
 function invVal(batch_cnt){
	return (batch_cnt>0)?1:0;
 }
 
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
			issuePayment(objs_list[0], objs_cnt, batch_inv, par_checked);
		}
	}
 }
 
 $(document).on('click', '.freeze', function(){
 	var batch_inv=invVal(0);//not-batch-update
	issuePayment($(this), 0, batch_inv, false);
 });
 
 function issuePayment(paymentref, batch_cnt, batch_inv, par_checked){
  $.ajax({
   url:"freezeTermPayment",
   method:'POST',
   data:{id:$(paymentref).data('refid'), payment_cancel:($(paymentref).is(":checked")?batch_inv:1-batch_inv), remuneration_id:$('#hidden_id').val(), payroll_profile_id:$(paymentref).data('refemp'), payment_amount:$("#payment_amount").val(), _token:_token},
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
			alert('Something wrong. Payment status cannot be changed at the moment');
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
	
	empTable.draw();//update-chk-approve-checked-value
	batchUpdate(par_checked, (batch_cnt-1));
   }
  })
 }
 
 /*
 $(document).on('click', '#ok_button', function(){
  $.ajax({
   url:"EmployeeLoan/destroy/"+remuneration_id,
   beforeSend:function(){
    $('#ok_button').text('Deleting...');
   },
   success:function(data){
	//alert(JSON.stringify(data));
    setTimeout(function(){
     $('#paymentCancelModal').modal('hide');
     //$('#user_table').DataTable().ajax.reload();
     //alert('Data Deleted');
    }, 2000);
    //location.reload()
	if(data.result=='success'){
		remunerationTable.row('#row-'+remuneration_id+'').remove().draw();
		
	}
   }
  })
 });
 */
 
 $("#payroll_process_type_id").on("change", function(){
	$('#period_filter_id').val('');
	$('#period_filter_id option').prop("disabled", true);
	$('#period_filter_id option:not(:first-child)').hide();
	$('#period_filter_id option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').prop("disabled", false);
	$('#period_filter_id option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').show();
 });
 
});


function colValidate(){
	var remuneration_file=$('#remuneration_file').find(":selected").val();
	
	if(remuneration_file==''){
		alert('Select file content');
		return false;
	}else{
		return true;
	}
	
}

function buttonSubmitHandler(destnum){
	var doc_id = (typeof($('#hidden_id').val())=="undefined")?"":$('#hidden_id').val();
	var doc_title = $("#payment_name").val();
	
	if(doc_id!=""){
		var destobj='#frmExport';//(destnum==1)?'#frmPrintItem':'#frmPrintData';
		/*atleast-one-detail-record-must-exist*/
		$(destobj).append("<input type='hidden' name='term_regnum' value='"+doc_id+"' />");
		$(destobj).append("<input type='hidden' name='rpt_info' value='"+doc_title+"' />");
		return true;
	}else{
		alert("You have to select the payment");
		return false;
	}
	
}

</script>

@endsection