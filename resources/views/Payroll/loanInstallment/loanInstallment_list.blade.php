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
                                            Loan Details
                                            <div>
                                                <div style="display:inline;">
                                                   <select name="location_filter" id="location_filter" class="custom-select shipClass" style="font-size:10px; width:300px;" >
                                                        <option value="">Please Select</option>
                                                        @foreach($branch as $branches)
                                                        
                                                        <option value="{{$branches->location}}">{{$branches->location}}</option>
                                                        @endforeach
                                                        
                                                   </select>
                                                </div>
                                                <!--button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm">Search</button>
                                                <button type="button" name="create_record" id="create_record" class="btn btn-secondary btn-sm">Add</button-->
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <form id="frmInfo" method="post">
                                                {{ csrf_field() }}	
                                                <div class="sbp-preview">
                                                    <div class="sbp-preview-content">
                                                       <span id="form_result"></span>
                                                       <div class="row">
                                                           <div class="form-group col-md-6">
                                                               <label class="control-label col" >Settle Date</label>
                                                               <div class="col">
                                                                 <input type="text" name="payment_name" id="payment_name" class="form-control" readonly="readonly" />
                                                               </div>
                                                           </div>
                                                           <div class="form-group col-md-6">
                                                               <label class="control-label col" >Remarks</label>
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
                                            <div class="datatable table-responsive" style="margin-top:10px;">
                                                <table class="table table-bordered table-hover" id="emptable" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th class="actlist_col">Select</th>
                                                            <th>Name</th>
                                                            <th>Office</th>
                                                            <th>Loan Description</th>
                                                            <th>Loan Amount</th>
                                                            <th>Paid Amount</th>
                                                            <th>Balance</th>
                                                            <th class="actlist_col">Actions</th>
                                                        </tr>
                                                    </thead>
                                                 
                                                    
                                                    
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
                                   <h5 class="modal-title" id="formModalLabel">Previous Installments</h5>
                                   
                                   <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                               </div>
                               <div class="modal-body">
                                   <div class="sbp-preview">
                                   		<div class="sbp-preview-content" style="padding:15px 5px;">
                                        	<div class="row">
                                            	<div class="form-group col-md-6">
                                                   <label class="control-label col" >Employee</label>
                                                   <div class="col">
                                                     <input type="text" name="form_modal_employee" id="form_modal_employee" class="form-control" readonly="readonly" />
                                                   </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                   <label class="control-label col" >Loan</label>
                                                   <div class="col">
                                                     <input type="text" name="form_modal_loan" id="form_modal_loan" class="form-control" readonly="readonly" />
                                                   </div>
                                                </div>
                                            </div>
                                        </div>
                                   </div>
                                   <div class="datatable table-responsive" style="margin-top:10px;">
                                        <table class="table table-bordered table-hover" id="titletable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr> 
                                                    <th>Date</th>
                                                    <th>Value</th>
                                                    
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
                               <div class="modal-footer" align="right">
                                   <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                               </div>
                               
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
                                               <label class="control-label col" >Name</label>
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
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="action" id="action" value="Edit" />
                                        
                                        <input type="hidden" name="hidden_id" id="hidden_id" />
                                        
                                        <input type="hidden" name="allocation_method" id="allocation_method" value="M2" /><!-- terms -->
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

					 
                    
                </main>
              
@endsection


@section('script')

<script>
$(document).ready(function(){

    $('#payrollmenu').addClass('active');
    $('#payrollmenu_icon').addClass('active');
    $('#policymanagement').addClass('navbtnactive');
    
 var empTable=$("#emptable").DataTable({
		"processing": true,
        "serverSide": true,
        "ajax": "{{ asset('/public/scripts/get_employee_loan_installments_data.php') }}",//
        "columns":[
            { "data": "installment_cancel" },
            { "data": "emp_first_name" },
            { "data": "location" },
            { "data": "loan_name" },
            { "data": "loan_amount" },
            { "data": "loan_paid" },
			{ "data": "loan_balance" },
			{ "data": "employee_loan_id" }
        ],
		"order":[[1, 'asc']],
		"columnDefs":[{
				"targets":0,
				"orderable":false,
				"className":'actlist_col',
				render:function( data, type, row ){
					var installment_cancel=(row.installment_cancel!=null)?row.installment_cancel:1;
					var check_str=(installment_cancel==0)?' checked="checked"':'';
					return '<input type="checkbox" class="freeze" data-refloan="'+row.employee_loan_id+'" data-refemp="'+row.payroll_profile_id+'" data-refid="'+row.installment_id+'"'+check_str+' />';
				}
			},{
				"targets":4,
				render:function( data, type, row ){
					return '<div class="badge badge-primary badge-pill">'+data+'</div>';
				}
			},{
				"targets":7,
				"orderable":false,
				render:function( data, type, row ){
					return '<button class="btn btn-datatable btn-icon btn-primary review" data-refid="'+data+'"><i class="fas fa-list"></i></button>';
				}
			}],
		"createdRow": function( row, data, dataIndex ){
			$('td', row).eq(7).addClass('actlist_col');
			$('td', row).eq(7).removeClass('masked_col');
			$( row ).attr( 'id', 'row-'+data.employee_loan_id );//$( row ).data( 'refid', data[3] );
			//$( row ).attr( 'id', 'row-'+data.payroll_profile_id );//$( row ).data( 'refid', data[3] );
		}
	});
 $('#location_filter').on('keyup change', function () {
		if (empTable.columns(2).search() !== this.value) {
			empTable.columns(2).search(this.value).draw();
		}
  });
 
 var remunerationTable=$("#titletable").DataTable({
		"columns":[{data:'payment_date'}, {data:'installment_value'}],
		"createdRow": function( row, data, dataIndex ){
			$( row ).attr( 'id', 'row-'+data.id);
		}
	});
 
 /*
 $('#find_payment').click(function(){
  $('#formModalLabel').text('Find Employee');
  //$('#action_button').val('Add');
  //$('#action').val('Add');
  $('#form_result').html('');

  $('#formModal').modal('show');
 });
 */
 
 /*
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
 */
 
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

 $('#frmConfirm').on('submit', function(event){
  event.preventDefault();
  
 });

 $(document).on('click', '.review', function(){
  var id = $(this).data('refid');
  $('#form_result').html('');
  
  var par=$(this).parent().parent();
  $("#form_modal_employee").val(par.children("td:nth-child(2)").html());
  $("#form_modal_loan").val(par.children("td:nth-child(4)").html());
  
  $.ajax({
   url :"EmployeeLoanInstallment/"+id+"/review",
   dataType:"json",
   success:function(data){
    remunerationTable.clear();
	remunerationTable.rows.add(data.package);
	remunerationTable.draw();
	
    $('#formModal').modal('show');
    
   }
  })/**/
 });

 
 $(document).on('click', '.delete', function(){
  /*
  remuneration_id = $(this).data('refid');
  
  $('#ok_button').text('OK');
  $('#paymentCancelModal').modal('show');
  */
 });
 
 
 $(document).on('click', '.freeze', function(){
 	var _token = $('#frmInfo input[name="_token"]').val();
	freezeLoan($(this), _token);
 });
 
 function freezeLoan(loanref, _token){
  $.ajax({
   url:"freezeLoanInstallment",
   method:'POST',
   data:{id:$(loanref).data('refid'), installment_cancel:($(loanref).is(":checked")?0:1), employee_loan_id:$(loanref).data('refloan'), payroll_profile_id:$(loanref).data('refemp'), _token:_token},
   dataType:"JSON",
   beforeSend:function(){
    $(loanref).prop('disabled', true);
   },
   success:function(data){
	//alert(JSON.stringify(data));
    if(data.result=='error'){
		$(loanref).prop('checked', !$(loanref).prop('checked'));
		alert('Something wrong. Loan installment status cannot be changed at the moment\r\n'+data.msg);
	}else{
		$(loanref).prop('disabled', false);
		$(loanref).data('refid', data.installment_id);
		
		var selected_tr=empTable.row('#row-'+$(loanref).data('refloan')+'');
		var rowNode=selected_tr.node();
		var new_val=data.payment_value;//parseFloat($( rowNode ).find('td').eq(5).html())+data.payment_value;
		
		$( rowNode ).find('td').eq(5).html( parseFloat(new_val).toFixed(2) );
		
		var d=selected_tr.data();
		var loan_tot=parseFloat(d.loan_amount);//d[4]//console.log("1="+loan_tot+"--"+d[3]+"--"+d[2]+"--"+d[1]);
		var loan_settle=parseFloat($( rowNode ).find('td').eq(5).html());//console.log("2="+loan_settle);
		var loan_bal=loan_tot-loan_settle;//console.log("3="+loan_bal);
		$( rowNode ).find('td').eq(6).html( parseFloat(loan_bal).toFixed(2) );
	}
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

});
</script>

@endsection