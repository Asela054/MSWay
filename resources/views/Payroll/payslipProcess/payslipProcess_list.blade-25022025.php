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
                                            Employee Payslip 
                                            <span id="lbl_duration" style="display:none; margin-right:auto; padding-left:10px;">
                                            	<span id="lbl_date_fr">&nbsp;</span> To <span id="lbl_date_to">&nbsp;</span>
                                                (<span id="lbl_payroll_name">&nbsp;</span>)
                                            </span>
                                            <div>
                                            	<button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm">Search</button>
                                            	<!--button type="button" name="create_record" id="create_record" class="btn btn-secondary btn-sm">Add</button-->
                                            </div>
                                        </div>
                                        
                                        <div class="card-body">
                                            
                                            <div class="datatable table-responsive" style="margin-top:0px;">
                                                <table class="table table-bordered table-hover" id="emptable" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th class="actlist_col">Select</th>
                                                            <th>Name</th>
                                                            <th>Office</th>
                                                            <th>Salary</th>
                                                            <th>Group</th>
                                                            <th class="actdrop_col">Loans</th>
                                                            <th class="actdrop_col">Additions</th>
                                                            <th>Work (W/o Holidays)</th>
                                                            <th>Work</th>
                                                            <th>Leave</th>
                                                            <th>No-pay</th>
                                                            <th>OT 1</th>
                                                            <th>OT 2</th>
                                                            <th>&nbsp;</th>
                                                        </tr>
                                                    </thead>
                                                 
                                                    <tbody class="">
                                                    </tbody>
                                                    
                                                </table>
                                            </div>
                                            
                                            <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" value="" /><!-- edit loans -->
                                            <input type="hidden" name="payment_period_id" id="payment_period_id" value="" />
                                            <input type="hidden" name="payslip_process_type_id" id="payslip_process_type_id" value="" />
                                            
                                        </div>
                                    </div>
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
                                        <!--div class="row">
                                        	<div class="form-group col-md-6">
                                            	<label class="control-label col">Working Period From</label>
                                                <div class="col">
                                                	<input type="date" class="form-control" name="work_date_fr" id="work_date_fr" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                            	<label class="control-label col">To</label>
                                                <div class="col">
                                                	<input type="date" class="form-control" name="work_date_to" id="work_date_to" value="" />
                                                </div>
                                            </div>
                                        </div-->
                                   </div>
                                   <div class="modal-footer" align="right">
                                       <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="Check Attendance" />
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
                                       <div class="row">
                                            <div class="form-group col">
                                               <label class="control-label col" >Employee</label>
                                               <div class="col">
                                                 <input type="text" name="loan_modal_employee_name" id="loan_modal_employee_name" class="form-control" readonly="readonly" />
                                               </div>
                                            </div>
                                       </div>
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
                                                        <div class="form-group col">
                                                           <label class="control-label col" >Loan</label>
                                                           <div class="col">
                                                             <input type="text" name="loan_modal_loan_name" id="loan_modal_loan_name" class="form-control" readonly="readonly" />
                                                           </div>
                                                        </div>
                                                   </div>
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
                    
                    
                    <div id="termModal" class="modal fade" role="dialog">
                        <div class="modal-dialog modal-lg">
                            
                                <div class="modal-content">
                                   <div class="modal-header">
                                       <h5 class="modal-title" id="termModalLabel">Salary Additions</h5>
                                       <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                                   </div>
                                   <div class="modal-body">
                                       <span id="term_result"></span>
                                       <div class="row">
                                            <div class="form-group col">
                                               <label class="control-label col" >Employee</label>
                                               <div class="col">
                                                 <input type="text" name="term_modal_employee_name" id="term_modal_employee_name" class="form-control" readonly="readonly" />
                                               </div>
                                            </div>
                                       </div>
                                       <form id="frmAdditionList" class="frm_link" method="post">
                                       {{ csrf_field() }}
                                           <div class="">
                                               <div class="" style="">
                                                   <div class="datatable table-responsive" style="margin-top:10px;">
                                                        <table class="table table-bordered table-hover" id="termtable" width="100%" cellspacing="0">
                                                            <thead>
                                                                <tr> 
                                                                    <th>Addition Name</th>
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
                                       <form id="frmAdditionInfo" class="frm_info sect_bg" method="post">
                                       {{ csrf_field() }}
                                           <div class="">
                                               <div class="" style="">
                                               	   <div class="row">
                                                        <div class="form-group col">
                                                           <label class="control-label col" >Addition name</label>
                                                           <div class="col">
                                                             <input type="text" name="term_modal_addition_name" id="term_modal_addition_name" class="form-control" readonly="readonly" />
                                                           </div>
                                                        </div>
                                                   </div>
                                                   <div class="row">
                                                       <div class="form-group col-md-6">
                                                           <label class="control-label col" >Previous Payment</label>
                                                           <div class="col">
                                                             <input type="text" name="pre_allocated_amount" id="pre_allocated_amount" class="form-control" readonly="readonly" />
                                                           </div>
                                                       </div>
                                                       <div class="form-group col-md-6">
                                                           <label class="control-label col" >New Payment</label>
                                                           <div class="col">
                                                             <input type="text" name="new_allocated_amount" id="new_allocated_amount" class="form-control" />
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="" align="right" style="padding:5px; border-top:none;">
                                               	   <input type="submit" name="allocate_button" id="allocate_button" class="btn btn-warning" value="Edit" />
                                                   <input type="button" id="btn_term_list" value="Back" class="btn btn-light btn_back" />
                                                   <input type="hidden" name="hidden_term_id" id="hidden_term_id" value="" />
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
function format(d){
	// `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" width="100%" style="padding-left:50px;">'+
        '<tr>'+
            '<td width="150">Full name:</td>'+
            '<td>'+d.emp_first_name+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Extension number:</td>'+
            '<td>'+d.location+'</td>'+
        '</tr>'+
    '</table>';
}

$(document).ready(function(){

	$('#payrollmenu').addClass('active');
    $('#payrollmenu_icon').addClass('active');
    $('#policymanagement').addClass('navbtnactive');
	
	var empTable=$("#emptable").DataTable({
			"columns":[{data:'payslip_cancel'}, {data:'emp_first_name'}, {data:'location'}, {data:'basic_salary'}, 
				{data:'process_name'}, {data:'loan_installments'}, 
				{data:'term_payments'}, {data:'opt_work'}, {data:'emp_work'}, {data:'emp_leave'}, {data:'emp_nopay'}, 
				{data:'emp_ot1'}, {data:'emp_ot2'}, 
				{"className":'details-control', "orderable":false, "data":null, "defaultContent":''}],
			"order":[],
			"columnDefs":[{
				"targets":0, 
				"className":'actlist_col',
				"orderable":false,
				render:function( data, type, row ){
					var check_str='';//(data==0)?' checked="checked"':'';
					var block_str='';//($("#hidden_id").val()=='')?' disabled="disabled"':'';
					var delete_str='';
					
					if(data==0){
						check_str=' checked="checked"';
					}
					
					if(row.id!=''){
						delete_str='<button type="button" class="btn btn-transparent-dark btn-icon opts_held actbtn_delete" data-payid="'+row.id+'" data-empid="'+row.payroll_profile_id+'"><i class="fas fa-window-close"></i></button>';
					}
					
					return '<input type="checkbox" class="freeze" data-refid="'+row.id+'" data-refemp="'+row.payroll_profile_id+'"'+check_str+block_str+' />'+delete_str;
				}
			}, {
				"targets":5,
				"className":'actdrop_col',
				render:function( data, type, row ){
					return data+'<button class="btn btn-transparent-dark btn-icon loan_list" type="button" data-refemp="'+row.payroll_profile_id+'" data-refpay="'+row.emp_payslip_no+'">'+'<i class="fas fa-list-alt"></i>'+'</button>';
				}
			}, {
				"targets":6,
				"className":'actdrop_col',
				render:function( data, type, row ){
					return data+'<button class="btn btn-transparent-dark btn-icon term_list" type="button" data-refemp="'+row.payroll_profile_id+'" data-refpay="'+row.emp_payslip_no+'">'+'<i class="fas fa-list-alt"></i>'+'</button>';
				}
			}],
			"createdRow": function( row, data, dataIndex ){
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$( row ).attr( 'id', 'row-'+data.payroll_profile_id );//$( row ).data( 'refid', data[3] );
			}
		});
	
	// Add event listener for opening and closing details
    $('#emptable tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = empTable.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	
	var loanTable=$("#loantable").DataTable({
			"info":false,
			"searching":false,
			"paging":false,
			"columns":[{data:'loan_name'}, {data:'installment_value'}, {data:'id'}],
			"columnDefs":[{
				"targets":2,
				"orderable":false,
				"className":'actlist_col',
				render:function( data, type, row ){
					var btn_act=' btn-primary loan_edit';
					var block_str='';
					
					if(row.loan_complete==1){
						btn_act=' btn-light';
						block_str=' disabled="disabled"';
					}
					
					return '<button type="button" class="btn btn-datatable btn-icon'+btn_act+'" data-refid="'+data+'"'+block_str+'><i class="fas fa-edit"></i></button>';
				}
			}],
			"createdRow": function( row, data, dataIndex ){
				$( row ).attr( 'id', 'row-'+data.id );//$( row ).data( 'refid', data[3] );
			}
		});
	
	var termTable=$("#termtable").DataTable({
			"info":false,
			"searching":false,
			"paging":false,
			"columns":[{data:'term_name'}, {data:'payment_value'}, {data:'id'}],
			"columnDefs":[{
				"targets":2,
				"orderable":false,
				"className":'actlist_col',
				render:function( data, type, row ){
					var btn_act=' btn-primary term_edit';
					var block_str='';
					
					if(row.value_group==-1){
						btn_act=' btn-light';
						block_str=' disabled="disabled"';
					}
					
					return '<button type="button" class="btn btn-datatable btn-icon'+btn_act+'" data-refid="'+data+'"'+block_str+'><i class="fas fa-edit"></i></button>';
				}
			}],
			"createdRow": function( row, data, dataIndex ){
				$( row ).attr( 'id', 'row-'+data.id );//$( row ).data( 'refid', data[3] );
			}
		});
	
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
	
	$("#frmSearch").on('submit', function(event){
	  event.preventDefault();
	  
	  $.ajax({
	   url:"checkAttendance",
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
			$('#formModal').modal('hide');
		}
	   }
	  })
	});
	
	$(document).on("click", ".loan_edit", function(){
		var id = $(this).data('refid');
		$('#loan_result').html('');
		
		$(".show .frm_info").removeClass('sect_bg');
		$(".show .frm_link").addClass('sect_bg');
		
		var par=$(this).parent().parent();
		$('#loan_modal_loan_name').val(par.children("td:nth-child(1)").html());
		
		var selected_tr=loanTable.row('#row-'+id);
		var d=selected_tr.data();
		
		$.ajax({
			url :"EmployeeLoanInstallment/"+id+"/edit",
			dataType:"json",
			success:function(data){
				$('#pre_installment_amount').val(d.installment_value);//data.loan_obj.installment_value
				$('#new_installment_amount').val(data.loan_obj.installment_value);
				$('#hidden_loan_id').val(id);//update-loan-installment
				
			}
		})/**/
	});
	
	$(document).on("click", ".term_edit", function(){
		var id = $(this).data('refid');
		$('#term_result').html('');
		
		$(".show .frm_info").removeClass('sect_bg');
		$(".show .frm_link").addClass('sect_bg');
		
		var par=$(this).parent().parent();
		$('#term_modal_addition_name').val(par.children("td:nth-child(1)").html());
		
		$.ajax({
			url :"EmployeeTermPayment/"+id+"/edit",
			dataType:"json",
			success:function(data){
				$('#pre_allocated_amount').val(data.term_obj.payment_amount);
				$('#new_allocated_amount').val(data.term_obj.payment_amount);
				$('#hidden_term_id').val(id);//update-loan-installment
				
			}
		})/**/
	});
	
	$(document).on("click", ".loan_list", function(){
		var refemp=$(this).data('refemp');//payroll-profile
		var refpay=$(this).data('refpay');//payroll-payslip
		$("#payroll_profile_id").val(refemp);//update-employee-payslip-rows-td
		
		var selected_payslip=empTable.row('#row-'+$("#payroll_profile_id").val()+'');
		var d=selected_payslip.data();
		$('#loan_modal_employee_name').val(d.emp_first_name);
		
		loanList(refemp, refpay, selected_payslip, d);
	});
	
	$(document).on("click", ".term_list", function(){
		var refemp=$(this).data('refemp');//payroll-profile
		var refpay=$(this).data('refpay');//payroll-payslip
		$("#payroll_profile_id").val(refemp);//update-employee-payslip-rows-td
		
		var selected_payslip=empTable.row('#row-'+$("#payroll_profile_id").val()+'');
		var d=selected_payslip.data();
		$('#term_modal_employee_name').val(d.emp_first_name);
		
		termList(refemp, refpay);
	});
	
	function loanList(refemp, refpay, paysliprow, payslipdata){
	  $.ajax({
	   url:"checkLoanInstallment",
	   method:'POST',
	   data:{payroll_profile_id:refemp, emp_payslip_no:refpay, _token:_token},
	   dataType:"JSON",
	   beforeSend:function(){
		//$('#find_employee').prop('disabled', true);
	   },
	   success:function(data){
		//alert(JSON.stringify(data));
		loanTable.clear();
		loanTable.rows.add(data.loan_list);
		loanTable.draw();
		
		payslipdata.loan_installments=data.loan_sums;
		empTable.row(paysliprow).data(payslipdata).draw();
		//$('#find_employee').prop('disabled', false);
		$("#loanModal").modal("show");
	   }
	  })
	}
	
	function termList(refemp, refpay){
	  $.ajax({
	   url:"checkTermPayment",
	   method:'POST',
	   data:{payroll_profile_id:refemp, emp_payslip_no:refpay, filter_by:'1', _token:_token},
	   dataType:"JSON",
	   beforeSend:function(){
		//$('#find_employee').prop('disabled', true);
	   },
	   success:function(data){
		//alert(JSON.stringify(data));
		termTable.clear();
		termTable.rows.add(data.employee_detail);
		termTable.draw();
		
		//$('#find_employee').prop('disabled', false);
		$("#termModal").modal("show");
	   }
	  })
	}
	
	$("#frmInstallmentInfo").on("submit", function(event){
	  event.preventDefault();
	  var action_url = "{{ route('EmployeeLoanInstallment.update') }}";
	  
	 
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
		 
	
		 var selected_tr=loanTable.row('#row-'+$("#hidden_loan_id").val()+'');//remunerationTable.$('tr.classname');
		 /*
		 alert(JSON.stringify(selected_tr.data()));
		 
		 var rowNode=selected_tr.node();
		 $( rowNode ).find('td').eq(0).html( data.alt_obj.remuneration_name );
		 $( rowNode ).find('td').eq(1).html( data.alt_obj.remuneration_type );
		 $( rowNode ).find('td').eq(2).html( data.alt_obj.epf_payable );
		 */
		 var d=selected_tr.data();
		 //alert(JSON.stringify(d));
		 d.installment_value=$('#new_installment_amount').val();
		 loanTable.row(selected_tr).data(d).draw();
		 
		 var selected_payslip=empTable.row('#row-'+$("#payroll_profile_id").val()+'');
		 var d=selected_payslip.data();
		 var diff=parseFloat($('#new_installment_amount').val())-parseFloat($('#pre_installment_amount').val());//data.pre_installment_value;
		 //console.log(diff);
		 
		 d.loan_installments=parseFloat(d.loan_installments)+diff;
		 empTable.row(selected_payslip).data(d).draw();
		 
		 //going-back-to-loan-list
		 $(".show .frm_info").addClass('sect_bg');
		 $(".show .frm_link").removeClass('sect_bg');
		 
		}
		$('#loan_result').html(html);
	   }
	  });
	});
	
	$("#frmAdditionInfo").on("submit", function(event){
	  event.preventDefault();
	  var action_url = "{{ route('EmployeeTermPayment.update') }}";
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
		 var selected_tr=termTable.row('#row-'+$("#hidden_term_id").val()+'');//remunerationTable.$('tr.classname');
		 /*
		 alert(JSON.stringify(selected_tr.data()));
		 
		 var rowNode=selected_tr.node();
		 $( rowNode ).find('td').eq(0).html( data.alt_obj.remuneration_name );
		 $( rowNode ).find('td').eq(1).html( data.alt_obj.remuneration_type );
		 $( rowNode ).find('td').eq(2).html( data.alt_obj.epf_payable );
		 */
		 var d=selected_tr.data();
		 //alert(JSON.stringify(d));
		 d.payment_value=$('#new_allocated_amount').val();
		 termTable.row(selected_tr).data(d).draw();
		 
		 var selected_payslip=empTable.row('#row-'+$("#payroll_profile_id").val()+'');
		 var d=selected_payslip.data();
		 var diff=parseFloat($('#new_allocated_amount').val())-parseFloat($('#pre_allocated_amount').val());
		 d.term_payments=parseFloat(d.term_payments)+diff;
		 empTable.row(selected_payslip).data(d).draw();
		 
		 //going-back-to-loan-list
		 $(".show .frm_info").addClass('sect_bg');
		 $(".show .frm_link").removeClass('sect_bg');
		 
		}
		$('#term_result').html(html);
	   }
	  });
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
	
	$(document).on('click', '.freeze', function(){
		var _token = $('#frmSearch input[name="_token"]').val();
		var selected_tr=empTable.row('#row-'+$(this).data('refemp')+'');//alert(JSON.stringify(selected_tr.data()))
		freezePayslip($(this), selected_tr.data(), _token);//alert(selected_tr.data().emp_leave);//
	});
	 
	function freezePayslip(payslip, workinfo, _token){
	  $.ajax({
	   url:"freezePayslip",
	   method:'POST',
	   data:{id:workinfo.id, payslip_cancel:($(payslip).is(":checked")?0:1), payroll_profile_id:workinfo.payroll_profile_id, employee_id:workinfo.employee_id, emp_job_code:workinfo.emp_job_code, emp_status:workinfo.emp_status, emp_etfno:workinfo.emp_etfno, emp_payslip_no:workinfo.emp_payslip_no, pay_per_day:workinfo.pay_per_day, basic_salary:workinfo.basic_salary, day_salary:workinfo.day_salary, payment_period_id:$("#payment_period_id").val(), payment_period_fr:$("#lbl_date_fr").html(), payment_period_to:$("#lbl_date_to").html(), payroll_process_type_id:$("#payslip_process_type_id").val(), emp_work:(parseFloat(workinfo.emp_work)+parseFloat(workinfo.emp_leave)), opt_work:parseFloat(workinfo.opt_work), _token:_token},
	   dataType:"JSON",
	   beforeSend:function(){
		$(payslip).prop('disabled', true);
	   },
	   success:function(data){
		//alert(JSON.stringify(data));
		if(data.result=='error'){
			$(payslip).prop('checked', !$(payslip).prop('checked'));
			alert('Something wrong. Payslip cannot be processed at the moment\r\n'+data.msg);
		}else{
			var selected_tr=empTable.row('#row-'+workinfo.payroll_profile_id+'');
			workinfo.id=data.employee_payslip_id;
			workinfo.payslip_cancel=($(payslip).is(":checked")?0:1);
			/*
			$(payslip).prop('disabled', false);
			$(payslip).data('refid', data.employee_payslip_id);
			*/
			empTable.row(selected_tr).data(workinfo).draw();
			
			/*
			var selected_tr=empTable.row('#row-'+$(loanref).data('refloan')+'');
			var rowNode=selected_tr.node();
			var new_val=parseFloat($( rowNode ).find('td').eq(5).html())+data.payment_value;
			
			$( rowNode ).find('td').eq(5).html( new_val );
			*/
		}
	   }
	  });
	}
	
	$(document).on('click', '.actbtn_delete', function(){
		var _token = $('#frmSearch input[name="_token"]').val();
		var empid=$(this).data('empid');//emp-id
		var delete_payslip=empTable.row('#row-'+empid+'');
		var paidinfo=delete_payslip.data();
		
		var confres=confirm("Are you sure you want to delete "+paidinfo.emp_first_name);
		
		if(confres){
			deletePayslip($(this), paidinfo, _token);
		}
	});
	
	function deletePayslip(payslip, paidinfo, _token){
	  $.ajax({
	   url:"deletePayslip",
	   method:'POST',
	   data:{id:paidinfo.id, emp_payslip_no:paidinfo.emp_payslip_no, _token:_token},
	   dataType:"JSON",
	   beforeSend:function(){
		$(payslip).prop('disabled', true);
	   },
	   success:function(data){
		//alert(JSON.stringify(data));
		if(data.result=='error'){
			alert('Something wrong. Payslip cannot be processed at the moment\r\n'+data.msg);
		}else{
			var selected_tr=empTable.row('#row-'+paidinfo.payroll_profile_id+'');
			paidinfo.id=data.employee_payslip_id;//''
			paidinfo.payslip_cancel="1";//($(payslip).is(":checked")?0:1);
			
			empTable.row(selected_tr).data(paidinfo).draw();
			
			
		}
	   }
	  });
	}
});
</script>

@endsection