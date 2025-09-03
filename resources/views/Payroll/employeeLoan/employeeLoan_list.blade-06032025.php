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
                                            	<button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm"><i class="fas fa-search mr-2"></i>Search</button>
                                            	<button type="button" name="create_record" id="create_record" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-2"></i>Add</button>
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
                                                               <label class="control-label col" >Employee Name</label>
                                                               <div class="col">
                                                                 <input type="text" name="emp_name" id="emp_name" class="form-control" readonly="readonly" />
                                                               </div>
                                                           </div>
                                                           <div class="form-group col-md-6">
                                                               <label class="control-label col" >Basic Salary</label>
                                                               <div class="col">
                                                                 <input type="text" name="basic_salary" id="basic_salary" class="form-control" readonly="readonly" />
                                                               </div>
                                                           </div>
                                                       </div>
                                                       
                                                    </div>
                                                    <!--div class="sbp-preview-text" style="text-align:right; padding:10px;">
                                                        
                                                    </div-->
                                                </div>
                                            </form>
                                            <div class="datatable table-responsive" style="margin-top:10px;">
                                                <table class="table table-bordered table-hover" id="titletable" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr> 
                                                            <th class="actlist_col">Active</th>
                                                            <th>Type</th>
                                                            <th>Date</th>
                                                            <th>Value</th>
                                                            <th>Paid</th>
                                                            <th>Balance</th>
                                                            <th>Duration</th>
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
                                    </div>
                                </div>
                                
                                
                                
                              
                            </div>
                           
                        </div>
                    </div>

                    <div id="formModal" class="modal fade" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                               <div class="modal-header">
                                   <h5 class="modal-title" id="formModalLabel">Find Employee</h5>
                                   <div style="margin-left:auto; margin-right:10px;">
                                       <select name="location_filter" id="location_filter" class="custom-select shipClass" style="font-size:10px; width:300px;" >
                                            <option value="">Please Select</option>
                                            @foreach($branch as $branches)
                                            
                                            <option value="{{$branches->location}}">{{$branches->location}}</option>
                                            @endforeach
                                            
                                       </select>
                                   </div>
                                   <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                               </div>
                               <div class="modal-body">
                                   <div class="datatable table-responsive">
                                        <table class="table table-bordered table-hover" id="emptable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Reg. No</th>
                                                    <th>Name</th>
                                                    <th>Office</th>
                                                    <th>Salary</th>
                                                    <th>Group</th>
                                                    <th class="actlist_col">Actions</th>
                                                </tr>
                                            </thead>
                                         
                                            <tbody>
                                            @foreach($employee as $employees)
                                                <tr>
                                                    <td>{{$employees->emp_etfno}}</td>
                                                    <td>{{$employees->emp_name_with_initial}}</td>
                                                    <td>{{$employees->location}}</td>
                                                    <td>{{$employees->basic_salary}}</td>
                                                    <td>{{$employees->process_name}}</td>
                                                    <td class="actlist_col masked_col">{{$employees->payroll_profile_id}}</td>
                                                </tr>
    
                                                @endforeach
                                                
                                             
                                            </tbody>
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
                                                 <input type="text" name="issue_amount" id="issue_amount" class="form-control" autocomplete="off" />
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
                                                 <input type="text" name="loan_duration" id="loan_duration" class="form-control" autocomplete="off" />
                                               </div>
                                           </div>
                                           <div class="form-group col-md-6">
                                               <label class="control-label col" >Installment Value</label>
                                               <div class="col">
                                                 <input type="text" name="installment_value" id="installment_value" class="form-control" readonly autocomplete="off" />
                                               </div>
                                           </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="control-label col" id="warinig_1"></label>
                                            <label class="control-label col">Primary Loan Guarantor</label>

                                            <div class="col">
                                                <select name="employeegarentee" id="employee_f" class="form-control">
                                                </select>
                                            </div>
                                        </div>
                                     </div>

                                     <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="control-label col" id="warinig_2"></label>
                                            <label class="control-label col">Secondary Loan Guarantor</label>
                                            <div class="col">
                                                <select name="employee_secondgarentee" id="employee_ff" class="form-control">
                                                </select>
                                            </div> 
                                        </div>
                                     </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="action" id="action" value="Edit" />
                                        
                                        <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" />
                                        <input type="hidden" name="hidden_id" id="hidden_id" />
                                        <input type="submit" name="action_button" id="action_button" class="btn btn-primary" value="Edit" />
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    
                    <div id="loanCancelModal" class="modal fade" role="dialog">
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
    
    let employee_f = $('#employee_f');
    let employee_ff = $('#employee_ff');

    employee_f.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("employee_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            cache: true
        }
    });

    employee_ff.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("employee_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            cache: true
        }
    });

 var empTable=$("#emptable").DataTable({
		"columnDefs":[{
				"targets":4,
				render:function( data, type, row ){
					return '<div class="badge badge-primary badge-pill">'+data+'</div>';
				}
			},{
				"targets":5,
				"orderable":false,
				render:function( data, type, row ){
					return '<button class="btn btn-datatable btn-icon btn-primary review" data-refid="'+data+'"><i class="fas fa-eye"></i></button>';
				}
			}],
		"createdRow": function( row, data, dataIndex ){
			$('td', row).eq(5).removeClass('masked_col');
			$( row ).attr( 'id', 'row-'+data[5] );//$( row ).data( 'refid', data[3] );
		}
	});
 $('#location_filter').on('keyup change', function () {
		if (empTable.columns(2).search() !== this.value) {
			empTable.columns(2).search(this.value).draw();
		}
 });
 
 var remunerationTable=$("#titletable").DataTable({
		"columns":[{data:'id'}, {data:'loan_type'}, {data:'loan_date'}, {data:'loan_amount'}, 
						   {data:'loan_paid'}, {data:'loan_amount'}, {data:'loan_duration'}, {data:'loan_freeze'}],
		"order":[],
		"columnDefs":[{
				"targets":0, 
				"orderable":false,
				"className":'actlist_col',
				render:function( data, type, row ){
					if(row.loan_complete==0){
						var check_str=(row.loan_freeze==0)?' checked="checked"':'';
						return '<input type="checkbox" class="freeze" data-refid="'+row.id+'"'+check_str+' />';
					}else{
						return '<span><i class="fa fa-check-square"></i></span>';
					}
				}
			},{
				"targets":5, 
				//"orderable":false,
				//"className":'actlist_col',
				render:function( data, type, row ){
					var loan_bal=row.loan_amount-row.loan_paid;
					return loan_bal;
				}
			},{
				"targets":7, 
				"orderable":false,
				"className":'actlist_col',
				render:function( data, type, row ){
					/*
					return '<button type="button" class="edit btn btn-datatable btn-icon btn-primary" data-refid="'+row.id+'" ><i class="fas fa-edit"></i></button><button type="button" class="delete btn btn-datatable btn-icon btn-danger" data-refid="'+row.id+'" ><i class="fas fa-trash"></i></button>';
					*/
					if(row.loan_complete==0){
						return '<button type="button" class="delete btn btn-datatable btn-icon btn-danger" data-refid="'+row.id+'" ><i class="fas fa-trash"></i></button>';
					}else{
						return '<span class="badge badge-success badge-pill">Completed</span>';
					}
				}
			}],
		"createdRow": function( row, data, dataIndex ){
			//$('td', row).eq(0).removeClass('masked_col');
			//$('td', row).eq(5).removeClass('masked_col');
			$( row ).attr( 'id', 'row-'+data.id);
		}
	});
 
 function calcInstallment(){
	 var loan_dura = parseFloat(realDuration());
	 var issue_amount = parseFloat(realAmount());
	 var loan_rate = parseFloat(realRate())/100;
	 var installment_value = (loan_dura>0)?(issue_amount+(issue_amount*loan_rate))/loan_dura:0;
	 $("#installment_value").val(isNaN(installment_value)?'0.00':installment_value.toFixed(2));
 }
 function calcLoan(){
	 var part_value = parseFloat(realInstallment());
	 var loan_dura = parseFloat(realDuration());
	 var checkrate = $('#loan_type').find(":selected").data('ratekey');
	 var loan_value = (checkrate==0)?parseFloat($("#issue_amount").val()):part_value*loan_dura;
	 $("#loan_amount").val(isNaN(loan_value)?'0.00':loan_value.toFixed(2));
 }
 
 function realAmount(){
	 var loanamt = 0;
	 
	 if($('#issue_amount').val()!=''){
	 	loanamt=isNaN($('#issue_amount').val())?0:$('#issue_amount').val();
	 }
	 
	 return loanamt;
 }
 function realDuration(){
	 var loandura = 0;
	 
	 if($('#loan_duration').val()!=''){
	 	loandura=isNaN($('#loan_duration').val())?0:$('#loan_duration').val();
	 }
	 
	 return loandura;
 }
 function realInstallment(){
	 var loaninstallment = 0;
	 
	 if($('#installment_value').val()!=''){
	 	loaninstallment=isNaN($('#installment_value').val())?0:$('#installment_value').val();
	 }
	 
	 return loaninstallment;
 }
 function realRate(){
	 var loanrate = 0;
	 var checkrate = $('#loan_type').find(":selected").data('ratekey');
	 
	 if($('#interest_rate').val()!=''){
	 	loanrate=isNaN($('#interest_rate').val())?0:$('#interest_rate').val();
	 }
	 
	 return loanrate*checkrate;
 }
 
 $("#issue_amount, #interest_rate, #loan_duration").on('keyup', function(){
 	calcInstallment();
	calcLoan();
 });
 /*
 $("#loan_duration, #installment_value").on('keyup', function(){
 	calcLoan();
 });
 */
 $('#loan_type').on('keyup change', function () {
	var check_rate=($(this).find(":selected").data('ratekey')==1)?false:true;
	$('#interest_rate').prop('readonly', check_rate);
	
	calcInstallment();
	calcLoan();
 });
 
 function findEmployee(){
//   $('#formModalLabel').text('Find Employee');
  //$('#action_button').val('Add');
  //$('#action').val('Add');
  $('#form_result').html('');

  $('#formModal').modal('show');
 }
 
 $('#find_employee').click(function(){
  findEmployee();
 });
 
 $('#create_record').click(function(){
  if($("#payroll_profile_id").val()!=""){
	  //$('#formModalLabel').text('Add Remuneration');
	  $('#action_button').val('Add Loan');
	  $('#action').val('Add');
	  $('#confirm_result').html('');
	  
	  $('#loan_name').val('');
	  $('#loan_date').val('');
	  $('#loan_amount').val('');
	  $('#loan_duration').val('');
	  $('#loan_type').prop("disabled", false);
	  $('#interest_rate').val('');
	  $('#installment_value').val('');
	  $('#issue_amount').val('');
	  
	  $('#hidden_id').val('');
	
	  $('#confirmModal').modal('show');
  }else{
	  var opt=confirm("You haven't selected the employee.\r\n\r\nSearch now ?");
	  if(opt){
		  findEmployee();
	  }
  }
 });
 
 var remuneration_id;
 
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
	$('#interest_rate').val(data.loan_obj.interest_rate);
	$('#loan_name').val(data.loan_obj.loan_name);
	$('#issue_amount').val(data.loan_obj.issue_amount);
	$('#loan_date').val(data.loan_obj.loan_date);
    //$('#formModalLabel').text('Edit Remuneration');
    //$('#action_button').val('Edit');
    //$('#action').val('Edit');
	$('#hidden_id').val(data.loan_obj.id);
	
	$('#confirmModal').modal('show');
    
   }
  })/**/
 
 });

 $('#frmConfirm').on('submit', function(event){
  event.preventDefault();
  var action_url = '';
  
  var param_interest=$('#interest_rate').val();
  $('#interest_rate').val(realRate()); // set-interest-rate-of-loan
 
  if($('#hidden_id').val() == ''){
   action_url = "{{ route('addEmployeeLoan') }}";
  }else{
   action_url = "{{ route('EmployeeLoan.update') }}";
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
	 
	 //if($('#hidden_id').val()==''){
	 	//$('#hidden_id').val(data.new_obj.id);
	 //}
	 
	 var selected_tr=remunerationTable.row('#row-'+data.new_id+'');//remunerationTable.$('tr.classname');
	 /*
	 alert(JSON.stringify(selected_tr.data()));
	 */
	 if(selected_tr.length==0){
		 var rowNode = remunerationTable.row.add({'id':data.new_id,
						'loan_type':$("#loan_type").val(),
						'loan_date':$("#loan_date").val(),
						'loan_amount':$("#loan_amount").val(),
						'loan_paid':0,
						'loan_duration':$("#loan_duration").val(),
						'loan_freeze':0, 'loan_complete':0}).draw( false ).node();
	 }else{
		 var d=selected_tr.data();
		 d.loan_date=$('#loan_date').val();
		 d.loan_duration=$('#loan_duration').val();
		 d.loan_amount=$('#loan_amount').val();
		 remunerationTable.row(selected_tr).data(d).draw();
	 }
	 
	 $('#confirmModal').modal('hide');
	 
    }else{
	 $('#interest_rate').val(param_interest); // set-previous-value-on-error
	}
	
    $('#confirm_result').html(html);
	
   }
  });
 });

 $(document).on('click', '.review', function(){
  var id = $(this).data('refid');
  $('#form_result').html('');
  $.ajax({
   url :"EmployeeLoan/"+id+"/review",
   dataType:"json",
   success:function(data){
    $('#emp_name').val(data.pre_obj.emp_name_with_initial);
	$('#basic_salary').val(data.pre_obj.basic_salary);
	$('#payroll_profile_id').val(id);
	
    //$('#formModalLabel').text('Edit Remuneration');
    //$('#action_button').val('Edit');
    //$('#action').val('Edit');
	
	remunerationTable.clear();
	remunerationTable.rows.add(data.package);
	remunerationTable.draw();
	
    $('#formModal').modal('hide');
    
   }
  })/**/
 });

 $(document).on('click', '.delete', function(){
  remuneration_id = $(this).data('refid');
  
  $('#ok_button').text('OK');
  $('#loanCancelModal').modal('show');
 });
 
 $(document).on('click', '.freeze', function(){
 	var _token = $('#frmInfo input[name="_token"]').val();
	//alert(_token);
	freezeLoan($(this), _token);
 });
 
 function freezeLoan(loanref, _token){
  $.ajax({
   url:"freezeEmployeeLoan",
   method:'POST',
   data:{id:$(loanref).data('refid'), loan_freeze:($(loanref).is(":checked")?0:1), _token:_token},
   dataType:"JSON",
   beforeSend:function(){
    $(loanref).prop('disabled', true);
   },
   success:function(data){
	//alert(JSON.stringify(data));
    if(data.result=='error'){
		$(loanref).prop('checked', !$(loanref).prop('checked'));
		alert('Something wrong. Loan status cannot be changed at the moment');
	}else{
		$(loanref).prop('disabled', false);
	}
   }
  })
 }
 
 $(document).on('click', '#ok_button', function(){
  $.ajax({
   url:"EmployeeLoan/destroy/"+remuneration_id,
   beforeSend:function(){
    $('#ok_button').text('Deleting...');
   },
   success:function(data){
	//alert(JSON.stringify(data));
    setTimeout(function(){
     $('#loanCancelModal').modal('hide');
     //$('#user_table').DataTable().ajax.reload();
     //alert('Data Deleted');
    }, 2000);
    //location.reload()
	if(data.result=='success'){
		remunerationTable.row('#row-'+remuneration_id+'').remove().draw();
		
	}else{
		alert("Loan cannot be deleted at the moment.\r\n\r\n"+data.more_info);
	}
   }
  })
 });


 $('#employee_f').on('keyup change', function () {
    var employeeId = $(this).val();

    $.ajax({
            url: '{{ route("checkloanguranteemployee") }}',
            method: 'GET',
            data: {
                employee_id: employeeId,
                _token: $('meta[name="csrf-token"]').attr('content') 
            },
            success: function (response) {
                if (response === 1) {
                $('#warinig_1')
                    .text('This employee is already signed to another loan.')
                    .css('color', 'red');
            } else {
                $('#warinig_1').text('');
            }
            }
        });
 });

 $('#employee_ff').on('keyup change', function () {
    var employeeId = $(this).val();

    $.ajax({
            url: '{{ route("checkloanguranteemployee") }}',
            method: 'GET',
            data: {
                employee_id: employeeId,
                _token: $('meta[name="csrf-token"]').attr('content') 
            },
            success: function (response) {
                if (response === 1) {
                $('#warinig_2')
                    .text('This employee is already signed to another loan.')
                    .css('color', 'red');
            } else {
                $('#warinig_2').text('');
            }
            }
        });
 });
 
});
</script>

@endsection