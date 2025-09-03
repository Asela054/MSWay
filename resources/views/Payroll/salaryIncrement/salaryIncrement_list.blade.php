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
                                            Increment Details
                                            <div>
                                            	<button type="button" name="upload_record" id="upload_record" class="btn btn-secondary btn-sm">Upload</button>
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
                                                	<label class="control-label col">Increment Type</label>
                                                    <div class="col">
                                                        <select name="remuneration_filter" id="remuneration_filter" class="form-control" >
                                                            <option value="" selected="selected">Select</option>
                                                            <option value="0">Basic Salary</option>
                                                            @foreach($remuneration as $payment)
                                                            
                                                            <option value="{{$payment->id}}">{{$payment->remuneration_name}}</option>
                                                            @endforeach
                                                            
                                                         </select>
                                                     </div>
                                                </div>
                                                <div class="form-group col-md-3">
                                                	<label class="control-label col">Effective Date</label>
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
                                                            <th>Employee Name</th>
                                                            <th>Increment Type</th>
                                                            <th>Increment Value</th>
                                                            <th>Effective Date</th>
                                                            <th>Paid Value</th>
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
                            <form action="{{ route('uploadSalaryIncrement') }}" method="post" target="_self" enctype="multipart/form-data" onsubmit="return colValidate();">
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
                                               <a class="col" href="{{ url('/public/csvsample/salary_increments.csv') }}">
                                                CSV Format-Download Sample File
                                               </a>
                                           </label>
                                           <div class="col">
                                             <select name="remuneration_file" id="remuneration_file" class="form-control" >
                                                <option value="0" selected="selected">Basic Salary</option>
                                                @foreach($remuneration as $payment)
                                                
                                                <option value="{{$payment->id}}">{{$payment->remuneration_name}}</option>
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
        "ajax": "{{ route('incrementData.getData') }}",
		"order": [[0, 'asc'], [1, 'asc']], 
        "columns":[
            { "data": "emp_name_with_initial" },
            { "data": "increment_type" },
            { "data": "increment_value" },
            { "data": "effective_month" },
            { "data": "paid_value" },
            { "data": "id" }
        ],
		"columnDefs":[{
				"targets":1,
				render:function( data, type, row ){
					return '<div class="badge badge-primary badge-pill">'+row.increment_desc+'</div>';
				}
			},{
				"targets":3,
				render:function( data, type, row ){
					return '<div class="badge badge-primary badge-pill">'+row.effective_date+'</div>';
				}
			},{
				"targets":4,
				render:function( data, type, row ){
					return '<div class="badge badge-primary badge-pill">'+data+'</div>';
				}
			},{
				"targets":5,
				"orderable":false,
				"className":"actlist_col masked_col",
				render:function( data, type, row ){
					return '<button class="btn btn-datatable btn-icon btn-danger delete" data-refid="'+data+'"><i class="fas fa-trash"></i></button>';
				}
			}],
		"createdRow": function( row, data, dataIndex ){
			$('td', row).eq(5).removeClass('masked_col');
			$( row ).attr( 'id', 'row-'+data.id );//data[5] //$( row ).data( 'refid', data[3] );
		}
	});
 $('#remuneration_filter').on('keyup change', function () {
		if (empTable.columns(1).search() !== this.value) {
			empTable.columns(1).search(this.value).draw();
		}
  });
 $('#month_filter').on('keyup change', function () {
		if (empTable.columns(3).search() !== this.value) {
			empTable.columns(3).search(this.value).draw();
		}
  });
 
 $('#upload_record').click(function(){
  //$('#formModalLabel').text('Find Employee');
  //$('#action_button').val('Add');
  //$('#action').val('Add');
  //$('#form_result').html('');

  $('#incrementUploadModal').modal('show');
 });
 
 var increment_id;

 $(document).on('click', '.delete', function(){
  increment_id = $(this).data('refid');
  $('#ok_button').text('OK');
  $('#incrementCancelModal').modal('show');
 });

 $('#ok_button').click(function(){
  $.ajax({
   url:"SalaryIncrement/destroy/"+increment_id,
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