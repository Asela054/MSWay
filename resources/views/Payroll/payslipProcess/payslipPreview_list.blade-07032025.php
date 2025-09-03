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
                                            Preview Payslip 
                                            <span id="lbl_duration" style="display:none; margin-right:auto; padding-left:10px;">
                                            	<span id="lbl_date_fr">&nbsp;</span> To <span id="lbl_date_to">&nbsp;</span>
                                                (<span id="lbl_payroll_name">&nbsp;</span>)
                                            </span>
                                            <div>
                                            	<button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm">Search</button>
                                            	<button type="button" name="approve_record" id="approve_record" class="btn btn-secondary btn-sm btn-light" disabled="disabled">Approve All</button>
                                            </div>
                                        </div>
                                        
                                        <div class="card-body">
                                            
                                            <div class="datatable table-responsive" style="margin-top:0px;">
                                                <table class="table table-bordered table-hover" id="emptable" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th class="actlist_col" style="min-width:80px;">Payslip Held</th>
                                                            <th class="actlist_col" style="min-width:80px;">
                                                            	<label class="form-check-label"><span><input id="chk_approve" class="" type="checkbox" style="" title="" disabled="disabled"></span> <span style="display:block;">Approve</span></label>
                                                            </th>
                                                            <th>Name</th>
                                                            <th>Office</th>
                                                            <th>Basic</th>
                                                            <th>No-pay</th>
                                                            <th class="">Normal OT</th>
                                                            <th class="">Double OT</th>
                                                            <th class="">Facility</th>
                                                            <th>Loan</th>
                                                            <th>Additions</th>
                                                            <!--th>EPF-8</th>
                                                            <th>EPF-12</th>
                                                            <th>ETF-3</th>
                                                            <th>PAYE</th-->
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
                                               <label class="control-label col">Branch</label>
                                               <div class="col">
                                                   <select name="location_filter_id" id="location_filter_id" class="custom-select shipClass nest_head" style="" data-findnest="deptnest" >
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
                                                   <select name="department_filter_id" id="department_filter_id" class="custom-select" style="" data-nestname="deptnest" >
                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                        @foreach($department as $section)
                                                        
                                                        <option class="nestopt d-none" value="{{$section->id}}" data-nestcode="{{$section->company_id}}" data-sectcode="{{$section->id}}">{{$section->name}}</option>
                                                        @endforeach
                                                        
                                                   </select>
                                               </div>
                                            </div>
                                            
                                        </div>
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
                                       <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="View Payslips" />
                                       <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                   </div>
                                   
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    
                    <div id="payslipModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            
                                <div class="modal-content">
                                   <div class="modal-header">
                                       <h5 class="modal-title" id="payslipModalLabel">Reason to hold salary</h5>
                                       <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                                   </div>
                                   <div class="modal-body">
                                       <span id="payslip_result"></span>
                                       
                                       <form id="frmSpecInfo" class="frm_info" method="post" enctype="multipart/form-data" autocomplete="off">
                                       {{ csrf_field() }}
                                           <div class="">
                                               <div class="" style="">
                                               	   <div class="row">
                                                       <div class="form-group col-md-12">
                                                           <label class="control-label col" >Employee Name</label>
                                                           <div class="col">
                                                             <input type="text" name="employee_name" id="employee_name" class="form-control" readonly="readonly" />
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="row">
                                                       <div class="form-group col-md-12">
                                                           <label class="control-label col" >Payment Held Reason</label>
                                                           <div class="col">
                                                             <input type="text" name="payslip_remarks" id="payslip_remarks" class="form-control" />
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="row">
                                                   		<div class="col">
                                                            <input type="file" name="payslip_remarks_file" class="col" style="padding-bottom:10px;">
                                                        </div>
                                                   </div>
                                                   <hr />
                                                   <div class="row">
                                                       <div class="form-group col-md-12">
                                                           <label class="control-label col" >Payment Release Reason</label>
                                                           <div class="col">
                                                             <input type="text" name="release_remarks" id="release_remarks" class="form-control" />
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="row">
                                                   		<div class="col">
                                                            <input type="file" name="release_remarks_file" class="col" style="padding-bottom:10px;">
                                                        </div>
                                                   </div>
                                               </div>
                                               <div class="card-footer d-flex align-items-center justify-content-between" align="" style="padding:15px 5px 3px; border-top:none;">
                                               	   <div class="small" id="fileattach_info">
                                                   	   
                                                   </div>
                                                   <div class="small">
                                                       <input type="submit" name="setup_button" id="setup_button" class="btn btn-warning" value="Save Reason" />
                                                       <!--input type="button" id="" value="Back" class="btn btn-light btn_back" /-->
                                                       <input type="hidden" name="employee_payslip_id" id="employee_payslip_id" value="" />
                                                   </div>
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
    return '<table cellpadding="5" cellspacing="0" border="0" width="100%" align="right" style="padding-left:50px;">'+
        '<tr>'+
            '<td>EPF 8%</td>'+
            '<td>EPF 12%</td>'+
			'<td>ETF 3%</td>'+
			'<td>PAYE Tax</td>'+
        '</tr>'+
        '<tr>'+
            '<td>'+d.EPF8+'</td>'+
            '<td>'+d.EPF12+'</td>'+
			'<td>'+d.ETF3+'</td>'+
			'<td>'+d.PAYE+'</td>'+
        '</tr>'+
    '</table>';
}

$(document).ready(function(){
	$('#payrollmenu').addClass('active');
    $('#payrollmenu_icon').addClass('active');
    $('#policymanagement').addClass('navbtnactive');

	var empTable=$("#emptable").DataTable({
			"columns":[{data:'payslip_cancel'}, {data:'payslip_approved'}, {data:'emp_first_name'}, {data:'location'}, {data:'BASIC'}, 
					{data:'NOPAY'}, {data:'OTHRS1'}, {data:'OTHRS2'}, 
					{data:'FACILITY'}, {data:'LOAN'}, {data:'ADDITION'}, 
					//{data:'EPF8'}, {data:'EPF12'}, {data:'ETF3'}, {data:'PAYE'}, 
					{"className":'details-control', "orderable":false, "data":null, "defaultContent":''}
				],
			"order":[],
			"columnDefs":[{
				"targets":0, 
				"className":'actlist_col',
				"orderable":false,
				render:function( data, type, row ){
					var check_str='';
					var block_str='';//' disabled="disabled"';//:'';
					var comment_str='';
					
					if(row.payslip_held==1){
						check_str=' checked="checked"';
						comment_str='<button type="button" class="btn btn-transparent-dark btn-icon opts_held" data-payid="'+row.id+'"><i class="fas fa-list-alt"></i></button>';
					}
					
					return '<input type="checkbox" class="freeze" data-refid="'+row.id+'" data-refemp=""'+check_str+block_str+' />'+comment_str;
				}
			}, {
				"targets":1, 
				"className":'actlist_col',
				"orderable":false,
				render:function( data, type, row ){
					var check_str='';
					var block_str='';//' disabled="disabled"';//:'';
					
					if(row.payslip_approved==1){
						check_str=' checked="checked"';
					}
					
					return '<input type="checkbox" class="finalize" data-refid="'+row.id+'" data-refemp=""'+check_str+block_str+' />';
				}
			}],
			"createdRow": function( row, data, dataIndex ){
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				$( row ).attr( 'id', 'row-'+data.id );//$( row ).data( 'refid', data[3] );
			},
			"drawCallback":function( settings ){
				var objs_visible=$('input.finalize[type=checkbox]').length;
				var chk_disabled=(objs_visible==0);//?true:false;
				var chk_selected=((objs_visible>0)&&($('input.finalize[type=checkbox]:checked').length==objs_visible));
				$('#chk_approve').prop('disabled', chk_disabled);
				$('#chk_approve').prop('checked', chk_selected);
				/*
				console.log("tot--"+$('input.finalize[type=checkbox]').length);
				console.log("act--"+$('input.finalize[type=checkbox]:checked').length);
				
				var api = this.api();
				// Output the data for the visible rows to the browser's console
				console.log( api.rows( {page:'current'} ).data() );
				*/
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
	
	//var loanTable=$("#loantable").DataTable();
	
	var _token = $('#frmSearch input[name="_token"]').val();
	
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
	
	$('#approve_record').click(function(){
		alert('todo - Listed payslips approved successfully');
	});
	
	$('#chk_approve').on('click', function(){
		var par_checked=$(this).is(':checked');
		$('#chk_approve').parent().addClass('masked_obj');
		//var objs_list=(par_checked)?$('input.finalize[type=checkbox]:not(:checked)'):$('input.finalize[type=checkbox]:checked');
		//var objs_cnt=$(objs_list).length;
		//var _token = $('#frmSearch input[name="_token"]').val();
		//var batch_inv = invVal(1);//update-multiple-records
		batchUpdate(par_checked, 1);//set objs-cnt as 1 to begin
		/*
		$(objs_list).each(function(index, obj){
			approvePayment($(obj), $(obj).data('refid'), (objs_cnt-index), batch_inv);
		});
		*/
	});
	
	function batchUpdate(par_checked, objs_cnt){
		if(objs_cnt>0){
			//var par_checked=$('#chk_approve').is(':checked');
			//if(!(par_checked)&&(pos>0)){par_checked=!(par_checked)};
			var objs_list=(par_checked)?$('input.finalize[type=checkbox]:not(:checked)'):$('input.finalize[type=checkbox]:checked');
			objs_cnt=$(objs_list).length;
			//prev_cnt=$(objs_list[0]).length;
			var batch_inv = invVal(1);//update-multiple-records
			//alert(objs_cnt+'>>'+prev_cnt);
			if(objs_cnt>0){
				approvePayment($(objs_list[0]), $(objs_list[0]).data('refid'), objs_cnt, batch_inv, par_checked);
			}
		}
	}
	
	$(document).on('click', '.opts_held', function(){
		var payid=$(this).data('payid');
		var selected_payslip=empTable.row('#row-'+payid+'');
		
		var d=selected_payslip.data();
		
		$.ajax({
		   url :"EmployeeSalaryRemark/"+payid+"/edit",
		   dataType:"json",
		   success:function(data){
			$('#employee_payslip_id').val(payid);
			$('#employee_name').val(d.emp_first_name);
			$('#payslip_remarks').val(data.pre_obj.payslip_remarks);
			$('#release_remarks').val(data.pre_obj.release_remarks);
			
			var comm_fileattach = 'No Attachments';
			var held_attachment = '';
			if(data.pre_obj.payslip_remarks_file!=''){
				comm_fileattach = '';
				held_attachment = '<a href="'+data.pre_obj.payslip_remarks_file+'" class="nav-link" style="display:inline;" target="_blank"><i class="fas fa-paperclip"></i>&nbsp;Salary Held Ref.</>';
			}
			
			var released_attachment = '';
			if(data.pre_obj.release_remarks_file!=''){
				comm_fileattach = '';
				released_attachment = '<a href="'+data.pre_obj.release_remarks_file+'" class="nav-link" style="display:inline;" target="_blank"><i class="fas fa-paperclip"></i>&nbsp;Released Ref.</>';
			}
			
			$("#fileattach_info").html(comm_fileattach+held_attachment+released_attachment);
			
			$('#payslip_result').html('');
			$('#payslipModal').modal('show');
		   }
		});
		
	});
	
	$(document).on('click', '.finalize', function(){
		//var _token = $('#frmSearch input[name="_token"]').val();
		var reginfo=$(this).data('refid');
		var payslip=$(this);
		var batch_inv=invVal(0);//not-batch-update
		approvePayment(payslip, reginfo, 0, batch_inv, false);
		
	});
	
	function invVal(batch_cnt){
		return (batch_cnt>0)?1:0;
	}
	
	function approvePayment(payslip, reginfo, batch_cnt, batch_inv, par_checked){
		$.ajax({
		   url :"approvePayment",
		   method:'POST',
		   data:{id:reginfo, payslip_approve:($(payslip).is(":checked")?batch_inv:1-batch_inv), _token:_token},
		   dataType:"json",
		   beforeSend:function(){
			$(payslip).prop('disabled', true);
		   },
		   success:function(data){
			var act_finalize=false;
			var head_obj=null;
			
			if(data.result=='error'){
				if(batch_cnt==0){
					$(payslip).prop('checked', !$(payslip).prop('checked'));
					alert('Something wrong.\r\n'+data.resmsg);
				}
				
				else{
					alert('Payment update error. Please reload the page to abort process.\r\n'+data.resmsg);
					/*
					$(payslip).addClass('check_inactive');
					*/
				}
				
			}else{
				$(payslip).prop('disabled', false);
				
				if(batch_cnt>0){
					$(payslip).prop('checked', !$(payslip).prop('checked'));
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
	
	$(document).on('click', '.freeze', function(){
		var _token = $('#frmSearch input[name="_token"]').val();
		var selected_payslip=$(this).data('refid');//alert(JSON.stringify(selected_tr.data()))
		holdPayment($(this), selected_payslip, _token);//alert(selected_tr.data().emp_leave);//
		/*
		var btnObj = $(this).next();
		if($(btnObj).hasClass('opts_held')){
			$(btnObj).prop('disabled', !($(this).is(":checked")))
		}else{
			$('<button type="button" class="btn btn-transparent-dark btn-icon opts_held" data-payid="'+$(this).data('refid')+'"><i class="fas fa-list-alt"></i></button>').insertAfter($(this));
		}
		*/
	});
	
	function holdPayment(payslip, reginfo, _token){
	  $.ajax({
	   url:"holdPayment",
	   method:'POST',
	   data:{id:reginfo, payslip_held:($(payslip).is(":checked")?0:1), _token:_token},
	   dataType:"JSON",
	   beforeSend:function(){
		$(payslip).prop('disabled', true);
	   },
	   success:function(data){
		//alert(JSON.stringify(data));
		if(data.result=='error'){
			$(payslip).prop('checked', !$(payslip).prop('checked'));
			alert('Something wrong.\r\n'+data.resmsg);
		}else{
			$(payslip).prop('disabled', false);
			
			/*
			var selected_tr=empTable.row('#row-'+$(loanref).data('refloan')+'');
			var rowNode=selected_tr.node();
			var new_val=parseFloat($( rowNode ).find('td').eq(5).html())+data.payment_value;
			
			$( rowNode ).find('td').eq(5).html( new_val );
			*/
			
			var btnObj = $(payslip).next();
			if($(btnObj).hasClass('opts_held')){
				$(btnObj).prop('disabled', !($(payslip).is(":checked")))
			}else{
				$('<button type="button" class="btn btn-transparent-dark btn-icon opts_held" data-payid="'+reginfo+'"><i class="fas fa-list-alt"></i></button>').insertAfter($(payslip));
			}
		}
	   }
	  });
	}
	
	$('#frmSpecInfo').on("submit", function(event){
	  event.preventDefault();
	  var action_url = "{{ route('setRemark') }}";
	  
	 
	  /*
	  alert(action_url);
	  */
	  $.ajax({
	   url: action_url,
	   method:"POST",
	   data:new FormData(this),//$(this).serialize(),
	   dataType:"json",
	   contentType: false,
	   cache: false,
	   processData: false,
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
		 setTimeout(function(){
			 $('#payslipModal').modal('hide');
			}, 1000);
		}
		$('#payslip_result').html(html);
	   }
	  });
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
	  
	  $.ajax({
	   url:"checkPayslipListByDept",
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
			$("#approve_record").prop('disabled', false);
			$("#approve_record").removeClass('btn-light');
			
			$('#formModal').modal('hide');
		}
	   }
	  })
	});
	/*
	$('#frmInstallmentInfo').on('submit', function(event){
		event.preventDefault();
		alert('todo - Save reason');
	});
	*/
	$(".modal").on("shown.bs.modal", function(e){
		
	});
	/*
	$(".modal").on("hide.bs.modal", function(e){
		$(this).removeClass('active');
	});
	*/
	
});
</script>

@endsection