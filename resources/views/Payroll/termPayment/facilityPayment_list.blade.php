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
                                            Facilities and Payments
                                            <div>
                                            	<button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm">Allocate</button>
                                                <button type="button" name="upload_record" id="upload_record" class="btn btn-secondary btn-sm">Upload</button>
                                                
                                                <span class="nav-item dropdown" style="top:3px;">
                                                  <a class="nav-link dropdown-toggle mr-lg-2" id="facilityDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="<?php echo 'Add Facilities'; ?>">
                                                    New Facility&nbsp;<!--i class="fa fa-fw fa-plus"></i-->
                                                    <span class="d-lg-none">Facilities
                                                      <span class="badge badge-pill badge-warning"><?php echo 'Add New Facility'; ?></span>
                                                    </span>
                                                    <!--span class="indicator text-warning d-none d-lg-block">
                                                      <!--i class="fa fa-fw fa-circle"></i-//-><?php //echo ''; ?>
                                                    </span-->
                                                  </a>
                                                  <div class="dropdown-menu" id="addNewFacility" aria-labelledby="facilityDropdown" style="">
                                                    <h6 class="dropdown-header">
                                                    	<form id="frmFacility" method="post" class="form-inline my-2 my-lg-0 mr-lg-2">
                                                            {{ csrf_field() }}
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" id="facility_name" name="facility_name" autocomplete="off" />
                                                                <span class="input-group-append">
                                                                    <button type="submit" name="btn" id="btn" class="btn btn-primary" >
                                                                    	<i class="fa fa-save"></i> 
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </form>
                                                    </h6>
                                                    
                                                    <div>
                                                    
                                                      <div class="dropdown-divider"></div>
                                                      
                                                      <a class="dropdown-item" href="#">
                                                        <span id="form_result" class="text-success">
                                                          &nbsp;
                                                        </span>
                                                      </a>
                                                    
                                                    
                                                    
                                                    </div>
                                                    <!-- -->
                                                    <!--div class="dropdown-divider"></div-->
                                                    <!--a class="dropdown-item small" href="event_overview.php">View all events</a-->
                                                  </div>
                                                </span>
        
                                            </div>
                                        </div>
                                        
                                        <div class="card-body">
                                            @if (\Session::has('success'))
                                            <div class="row">
                                            	<div class="col">
                                                	<span id="row_heading" style="color:blue;" class="col">
                                                    {{ \Session::get('success') }}
                                                   </span>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="row">
                                            	<div class="form-group col-md-3">
                                                	<label class="control-label col">Additions</label>
                                                    <div class="col">
                                                        <select name="remuneration_filter" id="remuneration_filter" class="form-control" >
                                                            <option value="" selected="selected">Select the Facility</option>
                                                            <!--option value="0">Basic Salary</option-->
                                                            @foreach($remuneration as $payment)
                                                            
                                                            <option value="{{$payment->id}}">{{$payment->facility_name}}</option>
                                                            @endforeach
                                                            
                                                         </select>
                                                     </div>
                                                </div>
                                                <div class="form-group col-md-3">
                                                	<label class="control-label col">Payment Date</label>
                                                    <div class="col">
                                                    	<input type="month" name="month_filter" id="month_filter" class="form-control" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6" align="right">
                                                   
                                                   
                                                   
                                                </div>
                                            </div>
                                            <div class="datatable table-responsive" style="margin-top:10px;">
                                                <table class="table table-bordered table-hover" id="emptable" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr> 
                                                            <th class="actlist_col "><label class="form-check-label"><span class=""><input id="chk_approve" class="" type="checkbox" style="" title="" disabled="disabled"></span> <span style="display:block;">Approve</span></label></th>
                                                            <th>Employee Name</th>
                                                            <th>Addition Type</th>
                                                            <th>Paid Value</th>
                                                            <th>Date of Payment</th>
                                                            <th>Basic Salary</th>
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

                    

                    
                    
                    
                    <div id="incrementCancelModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                	<h5 class="modal-title" id="incrementCancelModalLabel">Confirmation</h5>
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

					<div id="incrementUploadModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <form action="{{ route('uploadOtherFacilities') }}" method="post" target="_self" enctype="multipart/form-data" onsubmit="return colValidate();">
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
                                               <a class="col" href="{{ url('/public/csvsample/other_facilities.csv') }}">
                                                CSV Format-Download Sample File
                                               </a>
                                           </label>
                                           <div class="col">
                                             <select name="remuneration_file" id="remuneration_file" class="form-control" >
                                                <option value="" selected="selected">Select Facility</option>
                                                @foreach($remuneration as $payment)
                                                
                                                <option value="{{$payment->id}}">{{$payment->facility_name}}</option>
                                                @endforeach
                                                
                                             </select>
                                           </div>
                                        </div>
                                    </div>
                                    
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
                    
                    <div id="facilityAllocModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <form id="frmFacilityAlloc" method="post">
                            	{{ csrf_field() }}
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="facilityAllocModalLabel">Facility Allocation</h5>
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                            
                                    </div>
                                    <div class="modal-body">
                                        <span id="alloc_result"></span>
                                        <div class="row">
                                            <div class="form-group col">
                                               <label class="control-label col">Employee</label>
                                               <div class="col">
                                                 <select name="payroll_profile_id" id="payroll_profile_id" class="form-control">
                                                    <option value="">Select employee</option>
                                                    @foreach($employee_list as $employee)
                                                    
                                                    <option value="{{$employee->payroll_profile_id}}">{{$employee->emp_first_name}}</option>
                                                    @endforeach
                                                    
                                                 </select>
                                               </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                        	<div class="form-group col">
                                                <label class="control-label col">Additions</label>
                                                <div class="col">
                                                    <select name="other_facility_id" id="other_facility_id" class="form-control" >
                                                        <option value="" selected="selected">Select the Facility</option>
                                                        <!--option value="0">Basic Salary</option-->
                                                        @foreach($remuneration as $payment)
                                                        
                                                        <option value="{{$payment->id}}">{{$payment->facility_name}}</option>
                                                        @endforeach
                                                        
                                                     </select>
                                                 </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="control-label col">Payment Date</label>
                                                <div class="col">
                                                    <input type="date" name="payment_date" id="payment_date" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6" align="">
                                                <label class="control-label col" >Amount</label>
                                                <div class="col">
                                                	<input type="text" name="payment_amount" id="payment_amount" class="form-control" autocomplete="off" />
                                                </div>
                                               
                                               
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="add_amount" id="add_amount" class="btn btn-primary">Save</button>
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
$(document).ready(function(){

    $('#payrollmenu').addClass('active');
    $('#payrollmenu_icon').addClass('active');
    $('#policymanagement').addClass('navbtnactive');
    
 var empTable=$("#emptable").DataTable({
		"processing": true,
        "serverSide": true,
        "ajax": "{{ route('facilitiesData.getData') }}",
		"order": [[1, 'asc'], [2, 'asc']], 
        "columns":[
            { "data": 'payment_approved' },
			{ "data": "emp_name_with_initial" },
            { "data": "increment_type" },
            { "data": "increment_value" },
            { "data": "effective_month" },
            { "data": "basic_salary" },
            { "data": "id" }
        ],
		"columnDefs":[{
				"targets":0, 
				"className":'actlist_col',
				"orderable":false,
				render:function( data, type, row ){
					var check_str=(data==1)?' checked="checked"':'';
					var block_str='';//($("#hidden_id").val()=='')?' disabled="disabled"':'';
					return '<input type="checkbox" class="freeze" data-refid="'+row.id+'" data-refemp=""'+check_str+block_str+' />';
				}
			},{
				"targets":2,
				render:function( data, type, row ){
					return '<div class="badge badge-primary badge-pill">'+row.increment_desc+'</div>';
				}
			},{
				"targets":4,
				render:function( data, type, row ){
					return '<div class="badge badge-primary badge-pill">'+row.effective_date+'</div>';
				}
			},{
				"targets":5,
				render:function( data, type, row ){
					return '<div class="badge badge-primary badge-pill">'+data+'</div>';
				}
			},{
				"targets":6,
				"orderable":false,
				"className":"actlist_col masked_col",
				render:function( data, type, row ){
					return '<button class="btn btn-datatable btn-icon btn-danger delete" data-refid="'+data+'"><i class="fas fa-trash"></i></button>';
				}
			}],
		"createdRow": function( row, data, dataIndex ){
			$('td', row).eq(6).removeClass('masked_col');
			$( row ).attr( 'id', 'row-'+data.id );//data[5] //$( row ).data( 'refid', data[3] );
		},
		"drawCallback":function( settings ){
			var objs_visible=$('input.freeze[type=checkbox]').length;
			var chk_disabled=(objs_visible==0);//?true:false;
			var chk_selected=((objs_visible>0)&&($('input.freeze[type=checkbox]:checked').length==objs_visible));
			$('#chk_approve').prop('disabled', chk_disabled);
			$('#chk_approve').prop('checked', chk_selected);
			
		}
	});
 $('#remuneration_filter').on('keyup change', function () {
		if (empTable.columns(2).search() !== this.value) {
			empTable.columns(2).search(this.value).draw();
		}
  });
 $('#month_filter').on('keyup change', function () {
		if (empTable.columns(4).search() !== this.value) {
			empTable.columns(4).search(this.value).draw();
		}
  });
 
 $('#upload_record').click(function(){
  //$('#formModalLabel').text('Find Employee');
  //$('#action_button').val('Add');
  //$('#action').val('Add');
  //$('#form_result').html('');

  $('#incrementUploadModal').modal('show');
 });
 
 $('#find_employee').click(function(){
 	$('#facilityAllocModal').modal('show');
 });
 
 $('#facility_name').on('keydown', function(){
	$('#form_result').html('&nbsp;');
 });
 
 $('#frmFacility').on('submit', function(event){
	event.preventDefault();
	var action_url = "{{ route('addOtherFacility') }}";
	
	$.ajax({
		url: action_url,
		method:"POST",
		data:$(this).serialize(),
		dataType:"json",
		success:function(data){
			var html = '';
			if(data.errors){
				html = '<div class="alert alert-danger">';
				for(var count = 0; count < data.errors.length; count++){
				  html += '<p>' + data.errors[count] + '</p>';
				}
				html += '</div>';
			}
			if(data.success){
				html = '<div class="alert alert-success">' + data.success + ''; 
				var new_facility_name = data.new_obj.facility_name;
				$('#other_facility_id').append('<option value="'+data.new_obj.id+'">'+new_facility_name+'</option>');
				$('#remuneration_filter').append('<option value="'+data.new_obj.id+'">'+new_facility_name+'</option>');
				$('#remuneration_file').append('<option value="'+data.new_obj.id+'">'+new_facility_name+'</option>');
			}
			$('#form_result').html(html);
		}
	});
 });
 
 $('#frmFacilityAlloc').on('submit', function(event){
	event.preventDefault();
	var action_url = "{{ route('allocateOtherFacility') }}";
	
	$.ajax({
		url: action_url,
		method:"POST",
		data:$(this).serialize(),
		dataType:"json",
		success:function(data){
			var html = '';
			if(data.errors){
				html = '<div class="alert alert-danger">';
				for(var count = 0; count < data.errors.length; count++){
				  html += '<p>' + data.errors[count] + '</p>';
				}
				html += '</div>';
			}
			if(data.success){
				html = '<div class="alert alert-success">' + data.success + '';
				
				/*
				var selected_tr=empTable.row('#row-'+data.new_obj.id+'');
				
				if(selected_tr.length==0){
					var rowNode = empTable.row.add({'id':data.new_obj.id,
						'emp_name_with_initial':$("#payroll_profile_id").find(":selected").text(),
						'effective_date':$('#payment_date').val(),
						'effective_month':'Now',
						'increment_type':$("#other_facility_id").find(":selected").val(),
						'increment_desc':$("#other_facility_id").find(":selected").text(),
						'increment_value':$("#payment_amount").val(),
						'basic_salary':'100'
						}).draw( true ).node();
				}else{
					var d=selected_tr.data();
					d.payment_date='Now';
					d.payment_amount=$('#eligible_amount').val();
					
					empTable.row(selected_tr).data(d).draw();
				}
				*/
				
				
				setTimeout(function(){
				 empTable.draw(); $('#facilityAllocModal').modal('hide');
				}, 1000);
			}
			$('#alloc_result').html(html);
		}
	});
 });
 
 /* approve-payments-begin */
 var _token = $('#frmFacility input[name="_token"]').val();
 
 function invVal(batch_cnt){
	return (batch_cnt>0)?1:0;
 }
 
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
 
 $(document).on('click', '.freeze', function(){
 	var batch_inv=invVal(0);//not-batch-update
	issuePayment($(this), 0, batch_inv, false);
 });
 
 function issuePayment(paymentref, batch_cnt, batch_inv, par_checked){
  $.ajax({
   url:"freezeOtherFacilityPayment",
   method:'POST',
   data:{id:$(paymentref).data('refid'), payment_approved:($(paymentref).is(":checked")?1-batch_inv:batch_inv), _token:_token},
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
			alert('Something wrong. Payment status cannot be approved at the moment');
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
  });
 }
 /* approve-payments-close */
 
 var increment_id;

 $(document).on('click', '.delete', function(){
  increment_id = $(this).data('refid');
  $('#ok_button').text('OK');
  $('#incrementCancelModal').modal('show');
 });

 $('#ok_button').click(function(){
  $.ajax({
   url:"OtherFacilities/destroy/"+increment_id,
   beforeSend:function(){
    $('#ok_button').text('Deleting...');
   },
   success:function(data){
	//alert(JSON.stringify(data));
    setTimeout(function(){
     $('#incrementCancelModal').modal('hide');
     //$('#user_table').DataTable().ajax.reload();
     //alert('Data Deleted');
    }, 2000);
    //location.reload()
	if(data.result=='success'){
		empTable.row('#row-'+increment_id+'').remove().draw();
		
	}
   }
  })
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

</script>

@endsection