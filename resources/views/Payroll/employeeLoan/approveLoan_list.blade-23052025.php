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
                                    <div class="card card-header-actions mb-4">
                                        <div class="card-header">
                                            Loan Applications for Approval
                                            
                                            <div>
                                            	<button type="button" name="approve_record" id="approve_record" class="btn btn-secondary btn-sm btn-light" disabled="disabled">Approve All</button>
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="card-body">
                                            
                                            <div class="datatable table-responsive" style="margin-top:0px;">
                                                <table class="table table-bordered table-hover" id="emptable" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Office</th>
                                                            <th>Active Loans</th>
                                                            <th>Loan Applications</th>
                                                            <th class="">Amount</th>
                                                            <th>Actions</th>
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
                    
                    
                    <div id="confirmModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <form id="frmConfirm" method="post" onsubmit="return buttonSubmitHandler();">
                            	{{ csrf_field() }}
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                            
                                    </div>
                                    <div class="modal-body">
                                        <span id="confirm_result"></span>
                                        <!--h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4-->
                                        <div class="row">
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >Loan Description</label>
                                               <div class="col">
                                                 <input type="text" name="loan_name" id="loan_name" class="form-control" autocomplete="off" />
                                               </div>
                                           </div>
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >Date of Issue</label>
                                               <div class="col">
                                                 <input type="date" name="loan_date" id="loan_date" class="form-control" />
                                               </div>
                                           </div>
                                        </div>
                                        <div class="row">
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >Loan Type</label>
                                               <div class="col">
                                                 <select name="loan_type" id="loan_type" class="form-control">
                                                    <option value="PL" data-ratekey="0">Personal</option>
                                                    <option value="FL" data-ratekey="0">Festival</option>
                                                    <option value="WL" data-ratekey="1">Welfare</option>
                                                 </select>
                                               </div>
                                           </div>
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >Interest Rate (%)</label>
                                               <div class="col">
                                                 <input type="text" name="interest_rate" id="interest_rate" class="form-control" readonly autocomplete="off" />
                                               </div>
                                           </div>
                                        </div>
                                        <div class="row">
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >Issue Amount</label>
                                               <div class="col">
                                                 <input type="text" name="issue_amount" id="issue_amount" class="form-control" autocomplete="off" readonly="readonly" />
                                               </div>
                                           </div>
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >Loan Value</label>
                                               <div class="col">
                                                 <input type="text" name="loan_amount" id="loan_amount" class="form-control" autocomplete="off" readonly />
                                               </div>
                                           </div>
                                        </div>
                                        <div class="row">
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >No. of Installments<!--Duration (Months)--></label>
                                               <div class="col">
                                                 <input type="text" name="loan_duration" id="loan_duration" class="form-control" autocomplete="off" readonly="readonly" />
                                               </div>
                                           </div>
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >Installment Value</label>
                                               <div class="col">
                                                 <input type="text" name="installment_value" id="installment_value" class="form-control" readonly autocomplete="off" />
                                               </div>
                                           </div>
                                        </div>
										<div class="row">
                                            <div class="form-group col-md-12">
                                                <label class="control-label col" id="warinig_1"></label>
                                                <label class="control-label col">Primary Loan Guarantor</label>

                                                <div class="col">
                                                    <select name="employeegarentee" id="employee_f" class="form-control" readonly>
                                                    </select>
                                                </div>
                                            </div>
                                         </div>

                                         <div class="row">
                                            <div class="form-group col-md-12">
                                                <label class="control-label col" id="warinig_2"></label>
                                                <label class="control-label col">Secondary Loan Guarantor</label>
                                                <div class="col">
                                                    <select name="employee_secondgarentee" id="employee_ff" class="form-control" readonly>
                                                    </select>
                                                </div> 
                                            </div>
                                         </div>
                                    </div>

                                    <div class="modal-footer">
                                        <input type="hidden" name="action" id="action" value="Edit" />
                                        
                                        
                                        <input type="hidden" name="employee_loan_id" id="employee_loan_id" />
                                        <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" />
                                        <input type="hidden" name="hidden_id" id="hidden_id" />
                                        
                                        <button type="submit" name="action_buttonv" id="action_button" class="btn btn-primary" value="Approve">Approve</button>
                                        <button type="submit" name="action_buttonv" id="reject_button" class="btn btn-danger" value="Reject">Reject</button>
                                        
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    
                    
                </main>
              
@endsection


@section('script')

<script>
function format(d){
	var application_tr = '';
	// `d` is the original data object for the row
    $.each(d, function(index, obj){
		application_tr += '<tr data-conf="0" class="loan_grp">'+
					'<th>Description</th>'+'<td>'+obj.loan_name+'</td>'+
					'<th>Amount</th>'+'<td>'+obj.loan_amount+'</td>'+
					'<th>Installments</th>'+'<td>'+obj.loan_duration+'</td>'+
					'<td class="actlist_col"><button class="btn btn-datatable btn-icon btn-primary edit" data-refid="'+obj.id+'"><i class="fa fa-edit"></i></button></td>'+
				'</tr>';
	});
	return '<table cellpadding="5" cellspacing="0" border="0" width="100%" align="right" style="padding-left:50px;">'+
        application_tr+
    '</table>';
}

$(document).ready(function(){

	$('#payrollmenu').addClass('active');
    $('#payrollmenu_icon').addClass('active');
    $('#policymanagement').addClass('navbtnactive');
	
	var _token = $('#frmConfirm input[name="_token"]').val();
	
	var empTable=$("#emptable").DataTable({
			"ajax":{
					url: "viewLoanApplicants",
					method: "POST",
					data:{_token:_token},
					dataSrc: "table_data"
				},
			"columns":[{data:'emp_first_name'}, {data:'location'}, {data:'active_loans'}, 
					{data:'loan_applications'}, {data:'loan_amount'}, 
					{"className":'actlist_col', "orderable":false, data:null, "defaultContent":''}
				],
			"order":[],
			"columnDefs":[{
				"targets":5, 
				"className":'', // 'details-control ',
				render:function( data, type, row ){
					if(row.loan_approved==1){
						return '<span><i class="fa fa-check-square"></i></span>';
					}else if(row.loan_rejected==1){
						return '<span><i class="fa fa-window-close"></i></span>';
					}
					if(row.loan_applications==1){
						return '<button class="btn btn-datatable btn-icon btn-primary edit" data-refid="'+row.loan_id+'"><i class="fa fa-edit"></i></button>';
					}
				}
			}],
			"createdRow": function( row, data, dataIndex ){
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$( row ).attr( 'id', 'row-'+data.profile_id );//$( row ).data( 'refid', data[3] );
				//$(row).addClass('myRow');
				$('td', row).eq(5).addClass(data.td_class);//'details-control'
				
				$( row ).attr( 'data-conf', '0' );
			}
		});
	
	// Add event listener for opening and closing details
    $('#emptable tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = empTable.row( tr );
		
		var d = row.data();
		var id = d.profile_id;//alert(d.location);
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            $.ajax({
			   url :"LoanApplicationList/"+id+"/review",
			   dataType:"json",
			   success:function(data){
				row.child( format(data.applications) ).show();
            	tr.addClass('shown');
			   }
			})
        }
    } );
	
	//var loanTable=$("#loantable").DataTable();
	
	$(document).on('click', '.edit', function(){
	  var id = $(this).data('refid');//row#
	  //var pack_id = $(this).data('refpack');
	  $("#confirm_result").html('');
	  var par = $(this).parent().parent();
	  
	  $('tr').attr('data-conf', "0");
	  $(par).attr('data-conf', "1");
	  
	  
	  $.ajax({
	   url :"EmployeeLoanApprove/"+id+"/edit",
	   dataType:"json",
	   success:function(data){
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
		$("#employee_loan_id").val(data.loan_obj.id);//$('#hidden_id')
		
		$('#employee_f').html('<option value="'+data.loan_obj.primery_guarantor+'">' + data.loan_obj.primary_emp_name + '</option>');

		$('#employee_ff').html('<option value="'+data.loan_obj.secondary_guarantor+'">' + data.loan_obj.secondary_emp_name + '</option>');


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
	  })/**/
	 
	});
	
	$('#approve_record').click(function(){
		alert('todo - Listed loans approved successfully');
	});
	
	$('#frmConfirm').on('submit', function(event){
	  event.preventDefault();
	  var action_url = "{{ route('EmployeeLoanApprove.update') }}";//EmployeeLoanApproval.update
	  
	  var param_interest=$('#interest_rate').val();
	  $('#interest_rate').val(0);//(realRate()); // set-interest-rate-of-loan
	 
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
		 
		 //if($('#hidden_id').val()==''){
			//$('#hidden_id').val(data.new_obj.id);
		 //}
		 
		 var par=$('tr[data-conf="1"]');
		 /*
		 alert(JSON.stringify(selected_tr.data()));
		 */
		 if(par.length==1){
			 if($(par).hasClass('loan_grp')){
				 if(data.loan_approved==1){
					 par.children('td:last-child').html('<span><i class="fa fa-check-square"></i></span>');
				 }else if(data.loan_rejected==1){
					 par.children('td:last-child').html('<span><i class="fa fa-window-close"></i></span>');
				 }
			 }else{
				 var selected_tr = empTable.row(par)
				 var d=selected_tr.data();
				 d.loan_approved=data.loan_approved;
				 d.loan_rejected=data.loan_rejected;
				 empTable.row(selected_tr).data(d).draw();
			 }
		 }
		 
		 $('#confirmModal').modal('hide');
		 
		}else{
		 $('#interest_rate').val(param_interest); // set-previous-value-on-error
		}
		
		$('#confirm_result').html(html);
		
	   }
	  });
	});
	
	$(".modal").on("shown.bs.modal", function(){
		var objinput=$("#loan_name");//$(this).find('input[type="text"]:first-child');//
		objinput.focus();
		objinput.select();
	});
	
	
	
});
function buttonSubmitHandler(){
	var btntxt = $(document.activeElement).val();
	$("#frmConfirm").append("<input type='hidden' name='act_btn' value='"+btntxt+"' />");
	// Print the value of the button that was clicked
	//console.log($(document.activeElement).val());//$(document.activeElement).attr('id');
	/*Note that if the form is submitted by hitting the Enter key, then document.activeElement will be whichever form input that was focused at the time. If this wasn't a submit button then in this case it may be that there is no "button that was clicked."*/
	
}
</script>

@endsection