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
                                            Additions
                                            <button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm">Add</button>
                                        </div>
                                        <div class="card-body">
                                            <div class="datatable table-responsive">
                                                <table class="table table-bordered table-hover" id="titletable" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Type</th> 
                                                            <th>EPF Payable</th>
                                                            <th class="actlist_col">Action</th>   
                                                        </tr>
                                                    </thead>
                                                  
                                                    <tbody>
                                                    @foreach($remuneration as $remunerations)
                                                    
                                                        <tr>
                                                            <td>{{$remunerations->remuneration_name}}</td>
                                                            <td>{{$remunerations->remuneration_type}}</td>
                                                            <td>{{$remunerations->epf_payable}}</td>
                                                            <td class="actlist_col masked_col" data-refopt="{{$remunerations->advanced_option_id}}">
                                                            	{{$remunerations->id}}
                                                            </td>
                                                        </tr>
                                                       @endforeach
                                                     
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
                        <div class="modal-dialog">
                            
                                <div class="modal-content">
                                   <div class="modal-header">
                                       <h5 class="modal-title" id="formModalLabel"></h5>
                                       <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                                   </div>
                                   <div class="modal-body">
                                       <span id="form_result"></span>
                                       <form id="frmInfo" class="" method="post" onsubmit="return buttonSubmitHandler();">
                                       {{ csrf_field() }}
                                           <div class="sbp-preview">
                                               <div class="sbp-preview-content" style="padding:15px 5px;">
                                                   <div class="form-group">
                                                       <label class="control-label col" >Name</label>
                                                       <div class="col">
                                                         <input type="text" name="remuneration_name" id="remuneration_name" class="form-control" />
                                                       </div>
                                                   </div>
                                                   <div class="row">
                                                   	   <div class="form-group col">
                                                           <label class="control-label col" >Minimum attendance threshold</label>
                                                           <div class="col">
                                                             <select name="advanced_option_id" id="advanced_option_id" class="form-control" >
                                                                <option data-optexclude="0" value="0">1 day (Daily basis)</option>
                                                                <option data-optexclude="0" value="1">1 - 30 days (Monthly basis)</option>
                                                                <option data-optexclude="1" value="1">Week days (Exclude holidays)</option>
                                                             </select>
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="row">
                                                       <div class="form-group col-md-6">
                                                           <label class="control-label col" >Type</label>
                                                           <div class="col">
                                                             <select name="remuneration_type" id="remuneration_type" class="form-control" >
                                                                <option value="Addition">Addition</option>
                                                                <option value="Deduction">Deduction</option>
                                                             </select>
                                                           </div>
                                                       </div>
                                                       <div class="form-group col-md-6">
                                                           <label class="control-label col" >EPF Allocation</label>
                                                           <div class="col">
                                                             <select name="epf_payable" id="epf_payable" class="form-control" >
                                                                <option value="0">Without EPF</option>
                                                                <option value="1">With EPF</option>
                                                             </select>
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
                                               <div class="" align="right" style="padding:5px; border-top:none;">
                                                
                                                   <input type="hidden" name="action" id="action" value="Edit" />
                                                   <input type="hidden" name="hidden_id" id="hidden_id" />
                                                   <input type="hidden" name="allocation_method" id="allocation_method" value="M1" /><!-- fixed -->
                                                   <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="Edit" />
                                                   <!--button type="submit" name="action_button" id="option_button" value="1" class="btn btn-secondary">More</button-->
                                                   <input type="button" id="btn_next" value="More" class="btn btn-light" />
                                                
                                               </div>
                                           </div>
                                       </form>
                                       <form id="frmMore" class="sect_bg" method="post">
                                       {{ csrf_field() }}
                                           <div class="sbp-preview">
                                               <div class="sbp-preview-content" style="padding:15px 5px;">
                                               	   <div class="row col">
                                                   	   <span id="frm_more_sect_bg_title" class="col">&nbsp;</span>
                                                   </div>
                                                   <div class="row">
                                                       <div class="form-group col-md-6">
                                                           <label class="control-label col" >Criteria</label>
                                                           <div class="col">
                                                             <select name="remuneration_criteria" id="remuneration_criteria" class="form-control" >
                                                                @foreach($eligibleinfo as $preinfo)
                                                                
                                                                <option data-optgroup="{{$preinfo->remuneration_id}}" value="{{$preinfo->id}}">{{$preinfo->min_days}} - {{$preinfo->max_days}} days</option>
                                                                @endforeach
                                                                
                                                             </select>
                                                           </div>
                                                       </div>
                                                       <div class="form-group col-md-6">
                                                           <label class="control-label col" >Payment</label>
                                                           <div class="col">
                                                             <input type="text" name="pre_eligible_amount" id="pre_eligible_amount" class="form-control" />
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="row">
                                                       <div class="form-group col">
                                                           <label class="control-label col" >Increment</label>
                                                           <div class="col">
                                                             <input type="text" name="grp_increment" id="grp_increment" class="form-control" />
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="" align="right" style="padding:5px; border-top:none;">
                                               	   <input type="submit" name="setup_button" id="setup_button" class="btn btn-warning" value="Edit" />
                                                   <input type="button" id="btn_back" value="Back" class="btn btn-light" />
                                               </div>
                                           </div>
                                           
                                           <div class="datatable table-responsive" style="margin-top:10px;">
                                                <table class="table table-bordered table-hover" id="criteriatable" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr> 
                                                            <th>Criteria</th>
                                                            <th>Payment</th>
                                                            <th>Increment</th>
                                                        </tr>
                                                    </thead>
                                                  
                                                    <tbody>
                                                    @foreach($eligibleinfo as $preinfo)
                                                    
                                                        <tr id="row-{{$preinfo->id}}" data-optgroup="{{$preinfo->remuneration_id}}">
                                                            <td>{{$preinfo->min_days}} - {{$preinfo->max_days}} days</td>
                                                            <td>{{$preinfo->pre_eligible_amount}}</td>
                                                            <td>{{$preinfo->grp_increment}}</td>
                                                        </tr>
                                                       @endforeach
                                                     
                                                    </tbody>
                                                </table>
                                           </div>
                                       </form>
                                       
                                       
                                   </div>
                                   
                                   
                                </div>
                            
                        </div>
                    </div>

                    <div id="confirmModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                	<h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
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

 var remunerationTable=$("#titletable").DataTable({
		"columnDefs":[{
				"targets":2,
				render:function( data, type, row ){
					return (data==1)?'Y':'N';
				}
			},{
				"targets":3,
				"className":'actlist_col',
				"orderable":false,
				render:function( data, type, row ){
					return '<button name="edit" data-refid="'+data+'" class="edit btn btn-primary btn-datatable btn-icon" type="submit">'+
					'<i class="fas fa-edit"></i></button>'+
					'<button type="submit" name="delete" data-refid="'+data+'" class="delete btn btn-danger btn-datatable btn-icon">'+
					'<i class="fas fa-trash"></i></button>';
				}
			}],
		"createdRow": function( row, data, dataIndex ){
			$('td', row).eq(3).removeClass('masked_col');
			$( row ).attr( 'id', 'row-'+data[3] );//$( row ).data( 'refid', data[3] );
		}
	});/**/
 
 var criteriaTable=$("#criteriatable").DataTable({
		"searching":false,
		"info":false,
		"paging":false
	});
 
 $('#create_record').click(function(){
  $('#formModalLabel').text('Add Remuneration');
  $('#action_button').val('Save');
  $('#action').val('Add');
  $('#form_result').html('');
  
  $("#btn_next").prop("disabled", true);
  //$("#btn_next").hide();
  //$("#option_button").show();
  $("#advanced_option_id").prop("disabled", false);
  
  $("#frmInfo").removeClass('sect_bg');
  $("#frmMore").addClass('sect_bg');
  
  $('#remuneration_name').val('');
  $('#remuneration_type').val('Addition');
  $('#epf_payable').val('0');
  //$('#hidden_id').val('');
  //$('#advanced_option_id').val('0');
  $('#advanced_option_id option[data-optexclude="0"][value="0"]').prop("selected", true);
  $("#taxcalc_spec_code").val('0');
  
  $("#pre_eligible_amount").val('');
  $("#grp_increment").val('');

  $('#formModal').modal('show');
 });

 
 $('#frmInfo').on('submit', function(event){
  event.preventDefault();
  var action_url = '';
  
 
  if($('#action').val() == 'Add'){
   action_url = "{{ route('addRemuneration') }}";
  }
  
  if($('#action').val() == 'Edit'){
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
       
    if(data.result=='error'){
		alert(data.msg);
	}
	
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
	 
	 if($("#action").val()=="Add"){
		 var rowNode = remunerationTable.row.add([
						data.new_obj.remuneration_name,
						data.new_obj.remuneration_type,
						data.new_obj.epf_payable,
						data.new_obj.id]).draw( false ).node();
		 
		 if(data.new_obj.advanced_option_id>0){
			 $("#frm_more_sect_bg_title").html(data.new_obj.remuneration_name+' - ( Important: <strong>update payment value</strong> )');
			 
			 $('#remuneration_criteria option').hide();
			 criteriaTable.$('tr').hide();
			 
			 $("#remuneration_criteria").append('<option data-optgroup="'+data.new_obj.id+'" value="'+data.new_obj.advanced_option_id+'">1- 30 days</option>');
			 $('#remuneration_criteria option[data-optgroup="'+data.new_obj.id+'"]').show();
			 $('#remuneration_criteria').val($('#remuneration_criteria option[data-optgroup="'+data.new_obj.id+'"]:first').val());
			 
			 var newNode = criteriaTable.row.add([
							'1 - 30 days',
							0,
							0]).draw( false ).node();
			 $( newNode ).attr('id', 'row-'+data.new_obj.advanced_option_id);
			 $( newNode ).attr('data-optgroup', data.new_obj.id);
			 criteriaTable.$('tr[data-optgroup="'+data.new_obj.id+'"]').show();
			 
			 $('#frmInfo').addClass('sect_bg');
			 $('#frmMore').removeClass('sect_bg');
		 }
	 }else{
		 var selected_tr=remunerationTable.row('#row-'+$("#hidden_id").val()+'');//remunerationTable.$('tr.classname');
		 /*
		 alert(JSON.stringify(selected_tr.data()));
		 
		 var rowNode=selected_tr.node();
		 $( rowNode ).find('td').eq(0).html( data.alt_obj.remuneration_name );
		 $( rowNode ).find('td').eq(1).html( data.alt_obj.remuneration_type );
		 $( rowNode ).find('td').eq(2).html( data.alt_obj.epf_payable );
		 */
		 /*
		 var d=[data.alt_obj.remuneration_name, data.alt_obj.remuneration_type, data.alt_obj.epf_payable, data.alt_obj.alt_id];
		 */
		 var d=selected_tr.data();
		 d[0]=data.alt_obj.remuneration_name;
		 d[1]=data.alt_obj.remuneration_type;
		 d[2]=data.alt_obj.epf_payable;
		 remunerationTable.row(selected_tr).data(d).draw();
		 
		 $("#frm_more_sect_bg_title").html(data.alt_obj.remuneration_name);
	 }
	 
    }
    $('#form_result').html(html);
   }
  });
 });

 $(document).on('click', '.edit', function(){
  var id = $(this).data('refid');
  $("#btn_next").prop("disabled", ($(this).parent().data('refopt')==0)?true:false);
  //$("#btn_next").show();
  //$("#option_button").hide();
  $('#advanced_option_id').prop('disabled', true);
  $('#form_result').html('');
  
  $('#remuneration_criteria option').hide();
  criteriaTable.$('tr').hide();
  
  $("#frmInfo").removeClass('sect_bg');
  $("#frmMore").addClass('sect_bg');
  
  var par=$(this).parent().parent();
  $("#frm_more_sect_bg_title").html(par.children("td:nth-child(1)").html());
  
  $.ajax({
   url :"Remuneration/"+id+"/edit",
   dataType:"json",
   success:function(data){
    $('#remuneration_name').val(data.pre_obj.remuneration_name);
	$('#remuneration_type').val(data.pre_obj.remuneration_type);
	$('#epf_payable').val(data.pre_obj.epf_payable);
    $('#hidden_id').val(id);
	
	//$('#advanced_option_id').val(data.pre_obj.advanced_option_id);
	$('#advanced_option_id option[data-optexclude="'+data.pre_obj.employee_work_rate_work_days_exclusions+'"][value="'+data.pre_obj.advanced_option_id+'"]').prop("selected", true);
	$('#remuneration_criteria option[data-optgroup="'+data.pre_obj.id+'"]').show();
	$('#remuneration_criteria').val($('#remuneration_criteria option[data-optgroup="'+data.pre_obj.id+'"]:first').val());
	criteriaTable.$('tr[data-optgroup="'+data.pre_obj.id+'"]').show();
	
	$("#taxcalc_spec_code").val(data.pre_obj.taxcalc_spec_code);
	
    $('#formModalLabel').text('Edit Remuneration');
    $('#action_button').val('Update');
    $('#action').val('Edit');
    $('#formModal').modal('show');
    
   }
  })/**/
 });
 
 $("#btn_next").on("click", function(){
 	$('#frmInfo').addClass('sect_bg');
	$('#frmMore').removeClass('sect_bg');
 });
 $("#btn_back").on("click", function(){
 	$('#frmMore').addClass('sect_bg');
	$('#frmInfo').removeClass('sect_bg');
 });
 $("#frmMore").on("submit", function(event){
  event.preventDefault();
  var action_url = "{{ route('RemunerationEligibilityDay.update') }}";
  
 
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
	 

	 var selected_tr=criteriaTable.row('#row-'+$("#remuneration_criteria").find(":selected").val()+'');//remunerationTable.$('tr.classname');
	 /*
	 alert(JSON.stringify(selected_tr.data()));
	 
	 var rowNode=selected_tr.node();
	 $( rowNode ).find('td').eq(0).html( data.alt_obj.remuneration_name );
	 $( rowNode ).find('td').eq(1).html( data.alt_obj.remuneration_type );
	 $( rowNode ).find('td').eq(2).html( data.alt_obj.epf_payable );
	 */
	 var d=selected_tr.data();
	 d[1]=$('#pre_eligible_amount').val();
	 d[2]=$('#grp_increment').val();
	 criteriaTable.row(selected_tr).data(d).draw();
	 
	 
    }
    $('#form_result').html(html);
   }
  });
 });

 var remuneration_id;

 $(document).on('click', '.delete', function(){
  remuneration_id = $(this).data('refid');
  $('#ok_button').text('OK');
  $('#confirmModal').modal('show');
 });

 $('#ok_button').click(function(){
  $.ajax({
   url:"Remuneration/destroy/"+remuneration_id,
   beforeSend:function(){
    $('#ok_button').text('Deleting...');
   },
   success:function(data){
	//alert(JSON.stringify(data));
    setTimeout(function(){
     $('#confirmModal').modal('hide');
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

});
</script>
<script>
	function buttonSubmitHandler(){
		var optwork = (typeof($('#advanced_option_id').find(":selected").attr('data-optexclude'))=="undefined")?"":$('#advanced_option_id').find(":selected").attr('data-optexclude');
		//console.log('x1--'+paygroup);
		if(optwork!=""){
			var destobj='#frmInfo';//(destnum==1)?'#frmRevPdf':'#frmPrintPg';
			/*atleast-one-detail-record-must-exist*/
			$(destobj).append("<input type='hidden' name='employee_work_rate_work_days_exclusions' value='"+optwork+"' />");
			return true;
		}else{
			alert("Minimum attendance threshold is required");
			return false;
		}
		
	}
</script>
@endsection