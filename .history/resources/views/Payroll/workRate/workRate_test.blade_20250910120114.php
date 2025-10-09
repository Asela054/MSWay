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
                                            Work summary for testing
                                            <!--button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm">Add</button-->
                                        </div>
                                        <div class="card-body">
                                           <span id="form_result"></span>
                                           <form id="frmInfo" class="" method="post">
                                           {{ csrf_field() }}
                                               <div class="sbp-preview">
                                                   <div class="sbp-preview-content" style="padding:15px 5px;">
                                                       <div class="row">
                                                           <div class="form-group col-md-6">
                                                               <label class="control-label col" >Year</label>
                                                               <div class="col">
                                                                 <select name="work_year" id="work_year" class="form-control" >
                                                                    <option value="" disabled="disabled" selected="selected">Select Year</option>
                                                                    <?php 
																	date_default_timezone_set("Asia/Kolkata");
																	$mydate=getdate(date("U"));
																	$yearfr=$mydate['year'];
																	for($optyear=$yearfr;$optyear<($yearfr+5);$optyear++){
																	?>
																	
																	<option value="<?php echo $optyear; ?>"><?php echo $optyear; ?></option>
																	<?php } ?>
                                                                    
                                                                 </select>
                                                               </div>
                                                           </div>
                                                           <div class="form-group col-md-6">
                                                               <label class="control-label col" >Month</label>
                                                               <div class="col">
                                                                 <select name="work_month" id="work_month" class="form-control" >
                                                                    <option value="" disabled="disabled" selected="selected">Select Month</option>
                                                                    <option value="0">Jan</option>
                                                                    <option value="1">Feb</option>
                                                                    <option value="2">Mar</option>
                                                                    <option value="3">Apr</option>
                                                                    <option value="4">May</option>
                                                                    <option value="5">Jun</option>
                                                                    <option value="6">Jul</option>
                                                                    <option value="7">Aug</option>
                                                                    <option value="8">Sep</option>
                                                                    <option value="9">Oct</option>
                                                                    <option value="10">Nov</option>
                                                                    <option value="11">Dec</option>
                                                                 </select>
                                                               </div>
                                                           </div>
                                                       </div>
                                                       <div class="row">
                                                       	   <div class="form-group col">
                                                           	   <label class="control-label col">Employee</label>
                                                               <div class="col">
                                                               	 <select name="emp_etfno" id="emp_etfno" class="form-control">
                                                                 	@foreach($employee_list as $employee)
                                                                    
                                                                    <option value="{{$employee->emp_etfno}}">{{$employee->emp_first_name}}</option>
                                                                    @endforeach
                                                                    
                                                                 </select>
                                                               </div>
                                                           </div>
                                                       </div>
                                                       <div class="row">
                                                           <div class="form-group col-md-4">
                                                               <label class="control-label col" >Work Days</label>
                                                               <div class="col">
                                                                 <input type="text" name="work_days" id="work_days" class="form-control" autocomplete="off" />
                                                               </div>
                                                           </div>
                                                           <div class="form-group col-md-4">
                                                               <label class="control-label col" >Leave</label>
                                                               <div class="col">
                                                                 <input type="text" name="leave_days" id="leave_days" class="form-control" autocomplete="off" />
                                                               </div>
                                                           </div>
                                                           <div class="form-group col-md-4">
                                                               <label class="control-label col" >No-pay</label>
                                                               <div class="col">
                                                                 <input type="text" name="nopay_days" id="nopay_days" class="form-control" autocomplete="off" />
                                                               </div>
                                                           </div>
                                                       </div>
                                                       <div class="row">
                                                           <div class="form-group col-md-6">
                                                               <label class="control-label col" >Normal OT Hours</label>
                                                               <div class="col">
                                                                 <input type="text" name="normal_rate_otwork_hrs" id="normal_rate_otwork_hrs" class="form-control" autocomplete="off" />
                                                               </div>
                                                           </div>
                                                           <div class="form-group col-md-6">
                                                               <label class="control-label col" >Double OT</label>
                                                               <div class="col">
                                                                 <input type="text" name="double_rate_otwork_hrs" id="double_rate_otwork_hrs" class="form-control" autocomplete="off" />
                                                               </div>
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="" align="right" style="padding:5px; border-top:none;">
                                                    
                                                       <input type="hidden" name="action" id="action" value="Edit" />
                                                       <input type="hidden" name="hidden_id" id="hidden_id" />
                                                       <input type="hidden" name="payroll_process_type_id" id="payroll_process_type_id" value="" />
                                                       <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="Edit" />
                                                       <!--input type="button" id="btn_next" value="More" class="btn btn-light" /-->
                                                    
                                                   </div>
                                               </div>
                                           </form>
                                           
                                           
                                           
                                        </div>
                                    </div>
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
 /*
 var remunerationTable=$("#titletable").DataTable();
 */
 
 $('#frmInfo').on('submit', function(event){
  event.preventDefault();
  var action_url = "{{ route('addWorkSummary') }}";
  /*
 
  if($('#action').val() == 'Add'){
   action_url = "{{ route('addWorkSummary') }}";
  }
  
  if($('#action').val() == 'Edit'){
   action_url = "{{ route('PaymentPeriod.update') }}";
  }
  
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
	 
    }
    $('#form_result').html(html);
   }
  });
 });
});
</script>

@endsection