@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.payroll_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-money-check-dollar-pen"></i></div>
                    <span>Payroll Profile</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 text-right">
                        <button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm px-3"><i class="fa-light fa-magnifying-glass mr-1"></i>Search Employee</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <form id="frmInfo" method="post">
                            {{ csrf_field() }}
                            <span id="form_result"></span>
                            <div class="form-row mb-1">
                                <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                    <label class="small font-weight-bolder">Employee Name</label>
                                    <input type="text" name="emp_name" id="emp_name" class="form-control form-control-sm" />
                                </div>
                                <div class="col col-lg-2 col-xl-2">
                                    <label class="small font-weight-bolder">EPF No.</label>
                                    <input type="text" name="emp_etfno" id="emp_etfno" class="form-control form-control-sm" readonly="readonly" />
                                </div>
                                <div class="col col-lg-2 col-xl-2">
                                    <label class="small font-weight-bolder">Contribution</label>
                                    <select class="form-control form-control-sm" name="epfetf_contribution" id="epfetf_contribution">
                                        <option value="ACTIVE" selected="selected">Active</option>
                                        <option value="INACTIVE">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                    <label class="small font-weight-bolder">Job Category</label>
                                    <select name="payroll_act_id" id="payroll_act_id" class="form-control form-control-sm">
                                        <option value="" disabled="disabled" selected="selected"
                                            data-totdays="0">Please select</option>
                                        @foreach($payroll_acts as $act)

                                        <option value="{{$act->id}}"
                                            data-totdays="{{$act->total_work_days}}">{{$act->act_name}}
                                        </option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bolder">Payroll type</label>
                                    <select name="payroll_process_type_id" id="payroll_process_type_id" class="form-control form-control-sm nest_head"  data-findnest="paydaynest" >
                                        <option value="" disabled="disabled" selected="selected"  data-regcode="">Please
                                            select</option>
                                        @foreach($payroll_process_type as $payroll)

                                        <option value="{{$payroll->id}}"
                                            data-totdays="{{$payroll->total_work_days}}" data-regcode="{{$payroll->id}}" >
                                            {{$payroll->process_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                    <label class="small font-weight-bolder">Bank AC</label>
                                    <select name="employee_bank_id" id="employee_bank_id" class="form-control form-control-sm">
                                        <option value="" selected="selected">Please select</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                    <label class="small font-weight-bolder">Employee Position</label>
                                    <select name="employee_executive_level"
                                        id="employee_executive_level" class="form-control form-control-sm">
                                        <option value="0" selected="selected">Office staff</option>
                                        <option value="2">Factory staff</option>
                                        <option value="1">Executive staff</option>

                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                	<label class="small font-weight-bolder">Pay day</label>
                                    <select name="employee_payday_id" id="employee_payday_id" class="form-control form-control-sm" data-nestname="paydaynest" required>
                                    	<option value="0" selected="selected">General</option>
                                        @foreach($paydays as $payday)
                                        
                                        <option value="{{$payday->id}}" data-nestcode="{{$payday->payroll_process_type_id}}" data-paydaycode="{{$payday->id}}" class="nestopt d-none" >{{$payday->payday_name}}</option>
                                        @endforeach
                                        
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                    <label class="small font-weight-bolder">Basic Salary</label>
                                    <input type="text" name="basic_salary" id="basic_salary" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                    <label class="small font-weight-bolder">Day Salary</label>
                                    <input type="text" name="day_salary" id="day_salary" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                            </div>
                            <div class="form-row mt-3">
                                <div class="col-12 text-right">
                                    <input type="hidden" name="action" id="action" value="Edit" />
        
                                    <input type="hidden" name="hidden_work_days" id="hidden_work_days" />
                                    <!--input type="hidden" name="emp_etfno" id="emp_etfno" /-->
                                    <input type="hidden" name="emp_id" id="emp_id" />
                                    <input type="hidden" name="hidden_id" id="hidden_id" />
                                    <input type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm px-3" value="Save Profile" />
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-12">
                        <hr>
                        <div style="padding:10px;"><!-- class="card" -->
                            <div style="padding-bottom:10px;"><!-- class="card-header border-bottom" -->
                                <ul class="nav nav-tabs card-header-tabs" id="cardTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="overview-tab" href="#overview" data-toggle="tab" role="tab" aria-controls="overview" aria-selected="true">Privileges</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="example-tab" href="#example" data-toggle="tab" role="tab" aria-controls="example" aria-selected="false">Salary Advance</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="remarks-tab" href="#remarks" data-toggle="tab" role="tab" aria-controls="remarks" aria-selected="false">Bonuses</a>
                                    </li>
                                </ul>
                            </div>
                            <div><!-- class="card-body" -->
                                <div id="cardTabContent" class="tab-content"><!--  -->
                                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab" style="min-height:200px;">
                        
                                        <div class="center-block fix-width scroll-inner">
                                            <table class="table table-bordered table-striped table-sm small w-100 nowrap" id="titletable" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th class="actlist_col">Active</th>
                                                        <th>Name</th>
                                                        <th>Value</th>
                                                        <th class="actlist_col">Action</th>
                                                    </tr>
                                                </thead>
                
                                                <tbody>
                                                    @foreach($remuneration as $remunerations)
                
                                                    <tr>
                                                        <td class="masked_col">-</td>
                                                        <td>{{$remunerations->remuneration_name}}</td>
                                                        <td>0</td>
                                                        <td class="masked_col text-right" data-refopt="{{$remunerations->advanced_option_id}}">
                                                            {{$remunerations->id}}</td>
                                                    </tr>
                                                    @endforeach
                
                                                </tbody>
                                            </table>
                                        </div>
                                    
                                    </div>
                                    <div class="tab-pane fade" id="example" role="tabpanel" aria-labelledby="example-tab" style="min-height:200px;">
                                        <!--new tab 2-->
                                        <div class="datatable table-responsive" style="margin-top:10px;">
                                            <table class="table table-bordered table-hover" id="quotatable" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr> 
                                                        <th class="actlist_col">Active</th>
                                                        <th>Name</th>
                                                        <th>Value</th>
                                                        <th class="actlist_col">Action</th>   
                                                    </tr>
                                                </thead>
                                              
                                                <tbody>
                                                @foreach($empquota as $qfig)
                                                
                                                    <tr>
                                                        <td class="masked_col">-</td>
                                                        <td>{{$qfig->extras_label}}</td>
                                                        <td>0</td>
                                                        <td class="masked_col" data-refgrp="{{$qfig->remuneration_id}}">{{$qfig->id}}</td>
                                                    </tr>
                                                   @endforeach
                                                 
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="remarks" role="tabpanel" aria-labelledby="remarks-tab" style="min-height:200px;">
                                        <!--new tab 3-->
                                        <div class="datatable table-responsive" style="margin-top:10px;">
                                            <table class="table table-bordered table-hover" id="bonustable" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr> 
                                                        <th class="actlist_col">Active</th>
                                                        <th>Name</th>
                                                        <th>Value</th>
                                                        <th class="actlist_col">Action</th>   
                                                    </tr>
                                                </thead>
                                              
                                                <tbody>
                                                @foreach($empbonus as $bfig)
                                                
                                                    <tr>
                                                        <td class="masked_col">-</td>
                                                        <td>{{$bfig->extras_label}}</td>
                                                        <td>0</td>
                                                        <td class="masked_col" data-refgrp="{{$bfig->remuneration_id}}">{{$bfig->id}}</td>
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
            </div>
        </div>
    </div>

    <div id="formModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-end">
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                            <select name="location_filter" id="location_filter" class="shipClass form-control form-control-sm">
                                <option value="">Please Select</option>
                                @foreach($branch as $branches)

                                <option value="{{$branches->location}}">{{$branches->location}}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <hr>
                        </div>
                    </div>
                    <div class="center-block fix-width scroll-inner">
                        <table class="table table-bordered table-striped table-sm small nowrap" id="emptable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>REG. NO (EPF)</th>
                                    <th>NAME</th>
                                    <th>OFFICE</th>
                                    <th>SALARY</th>
                                    <th>GROUP</th>
                                    <th class="actlist_col">ACTIONS</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="frmConfirm" method="post">
                {{ csrf_field() }}
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <span id="frm_confirm_title">&nbsp;</span>
                            </div>
                        </div>
                        <span id="confirm_result"></span>
                        <!--h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4-->
                        <div class="row">
                            <div class="col-12">
                                <label class="small font-weight-bolder">Amount (<span id="allocation_info">Daily basis</span>)</label>
                                <input type="text" name="new_eligible_amount" id="new_eligible_amount" class="form-control form-control-sm" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" value="" />
                        <input type="hidden" name="remuneration_id" id="remuneration_id" value="" />
                        <input type="hidden" name="subscription_id" id="subscription_id" value="" />
                        <button type="submit" name="add_amount" id="add_amount" class="btn btn-primary btn-sm px-3">Save</button>
                        <button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div id="extraQuotesModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <form id="frmExtraQuotes" method="post">
                {{ csrf_field() }}
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Confirmation</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
            
                    </div>
                    <div class="modal-body">
                        <div class="row col">
                           <span id="frm_quotes_title" class="col">&nbsp;</span>
                        </div>
                        <span id="checkup_result"></span>
                        <!--h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4-->
                        <div class="row">
                            <div class="form-group col">
                               <label class="control-label col" >Amount (<span id="remuneration_extra_info">Advance</span>)</label>
                               <div class="col">
                                 <input type="text" name="extra_entitle_amount" id="extra_entitle_amount" class="form-control" autocomplete="off" />
                               </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="payroll_profile_id" id="frmExtraQuotes_payroll_profile_id" value="" />
                        <input type="hidden" name="remuneration_id" id="frmExtraQuotes_remuneration_id" value="" />
                        <input type="hidden" name="remuneration_extra_id" id="remuneration_extra_id" value="" />
                        <input type="hidden" name="subscription_id" id="extra_subscription_id" value="" />
                        <button type="submit" name="add_amount" id="frmExtraQuotes_add_amount" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="remunerationCancelModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="remunerationCancelModalLabel">Confirmation</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span
                            class="btn-sm btn-danger" aria-hidden="true">X</span></button>

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
    $(document).ready(function () {

        $('#payrollmenu').addClass('active');
        $('#payrollmenu_icon').addClass('active');
        $('#policymanagement').addClass('navbtnactive');

        var empTable = $("#emptable").DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": scripturl + "/get_employee_data.php", //"{{ route('employeeData.getData') }}",
            "columns": [{
                    "data": "emp_etfno"
                },
                {
                    "data": "emp_name_with_initial"
                },
                {
                    "data": "location"
                },
                {
                    "data": "basic_salary"
                },
                {
                    "data": "process_name"
                },
                {
                    "data": "id"
                }
            ],
            "columnDefs": [{
                "targets": 3,
                "orderable": false,
                "className": "text-right",
            }, {
                "targets": 5,
                "orderable": false,
                "className": "actlist_col masked_col",
                render: function (data, type, row) {
                    return '<button class="btn btn-primary btn-sm review" data-refid="' +
                        data + '"><i class="fas fa-pencil-alt"></i></button>';
                }
            }],
            "createdRow": function (row, data, dataIndex) {
                $('td', row).eq(5).removeClass('masked_col');
                $(row).attr('id', 'row-' + data.id); //data[5] //$( row ).data( 'refid', data[3] );
            }
        });
        $('#location_filter').on('keyup change', function () {
            if (empTable.columns(2).search() !== this.value) {
                empTable.columns(2).search(this.value).draw();
            }
        });

        var remunerationTable = $("#titletable").DataTable({
            "order": [],
            "columnDefs": [{
                "targets": 0,
                "className": 'actlist_col',
                "orderable": false,
                render: function (data, type, row) {

                    if (data == '-1') {
                        data = '-';
                    }
                    /**/
                    if (data == '-') {
                        return '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" disabled="disabled" /><label class="custom-control-label" for="customCheck1"></label></div>';
                    } else {
                        var check_str = (data == 0) ? ' checked="checked"' : '';
                        return '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input chk_act"' + check_str +
                            ' disabled="disabled" /><label class="custom-control-label" for="customCheck1"></label></div>';
                    }
                }
            }, {
                "targets": 3,
                "className": 'actlist_col text-right',
                "orderable": false,
                render: function (data, type, row) {
                    if (row[0] == '-') {
                        return '<button class="btn btn-sm btn-light" type="button" disabled="disabled"><i class="fas fa-stop-circle"></i></button>';
                    } else if (row[0] == '-1') {
                        return '<button name="signup" data-refid="' + data +
                            '" class="signup btn btn-primary btn-sm" type="button">' +
                            '<i class="fas fa-plus-square"></i></button>';
                    } else {
                        var del_str = (row[0] == '0') ?
                            '<button type="button" name="delete" data-refid="' + data +
                            '" data-refpack="' + ((row[0] == '-') ? '' : row[4]) +
                            '" class="delete btn btn-danger btn-sm">' +
                            '<i class="fas fa-trash-alt"></i></button>' : '';
                        return '<button name="edit" data-refid="' + data +
                            '" data-refpack="' + ((row[0] == '-') ? '' : row[4]) +
                            '" class="edit btn btn-primary btn-sm mr-1" type="button">' +
                            '<i class="fas fa-pencil-alt"></i></button>' + del_str;
                    }
                }
            }],
            "createdRow": function (row, data, dataIndex) {
                $('td', row).eq(0).removeClass('masked_col');
                $('td', row).eq(3).removeClass('masked_col');
                if (data.length == 6) {
                    $('td', row).eq(3).attr('data-refopt', data[5]);
                }
                $(row).attr('id', 'row-' + data[3]); //$( row ).data( 'refid', data[3] );
                //$( row ).data('refpack', (data[0]=='-')?'':data[4]);
            }
        });
		
		var quotaTable=$("#quotatable").DataTable({
				"order":[],
				"columnDefs":[{
						"targets":0,
						"className":'actlist_col',
						"orderable":false,
						render:function( data, type, row ){
							
							if(data=='-1'){
								data='-';
							}
							/**/
							if(data=='-'){
								return '<input type="checkbox" class="" disabled="disabled" />';
							}else{
								var check_str=(data==0)?' checked="checked"':'';
								return '<input type="checkbox" class="chk_act"'+check_str+' disabled="disabled" />';
							}
						}
					},{
						"targets":3,
						"className":'actlist_col',
						"orderable":false,
						render:function( data, type, row ){
							if(row[0]=='-'){
								return '<button class="btn btn-datatable btn-icon btn-light" type="button" disabled="disabled"><i class="fas fa-stop-circle"></i></button>';
							}else if(row[0]=='-1'){
								return '<button name="checkin" data-refid="'+data+'" data-feattype="'+((row[0]=='-')?'':row[7])+'" class="checkin btn btn-primary btn-datatable btn-icon" type="button">'+'<i class="fas fa-plus-square"></i></button>';
							}else{
								var del_str=(row[0]=='0')?'<button type="button" name="checkout" data-refid="'+data+'" data-refpack="'+((row[0]=='-')?'':row[4])+'" data-feattype="'+((row[0]=='-')?'':row[7])+'" class="checkout btn btn-danger btn-datatable btn-icon">'+'<i class="fas fa-trash"></i></button>':'';
								return '<button name="checkup" data-refid="'+data+'" data-refpack="'+((row[0]=='-')?'':row[4])+'" data-feattype="'+((row[0]=='-')?'':row[7])+'" class="checkup btn btn-primary btn-datatable btn-icon mr-2" type="button">'+'<i class="fas fa-edit"></i></button>'+del_str;
							}
						}
					}],
				"createdRow": function( row, data, dataIndex ){
					$('td', row).eq(0).removeClass('masked_col');
					$('td', row).eq(3).removeClass('masked_col');
					//if(data.length==6)
					if(data.length == 8){
						$('td', row).eq(3).attr('data-refgrp', data[5]);
					}
					$( row ).attr( 'id', 'row-'+data[3] );//$( row ).data( 'refid', data[3] );
					//$( row ).data('refpack', (data[0]=='-')?'':data[4]);
				}
			});
		 
		var bonusTable=$("#bonustable").DataTable({
				"order":[],
				"columnDefs":[{
						"targets":0,
						"className":'actlist_col',
						"orderable":false,
						render:function( data, type, row ){
							
							if(data=='-1'){
								data='-';
							}
							/**/
							if(data=='-'){
								return '<input type="checkbox" class="" disabled="disabled" />';
							}else{
								var check_str=(data==0)?' checked="checked"':'';
								return '<input type="checkbox" class="chk_act"'+check_str+' disabled="disabled" />';
							}
						}
					},{
						"targets":3,
						"className":'actlist_col',
						"orderable":false,
						render:function( data, type, row ){
							if(row[0]=='-'){
								return '<button class="btn btn-datatable btn-icon btn-light" type="button" disabled="disabled"><i class="fas fa-stop-circle"></i></button>';
							}else if(row[0]=='-1'){
								return '<button name="checkin" data-refid="'+data+'" data-feattype="'+((row[0]=='-')?'':row[7])+'" class="checkin btn btn-primary btn-datatable btn-icon" type="button">'+'<i class="fas fa-plus-square"></i></button>';
							}else{
								var del_str=(row[0]=='0')?'<button type="button" name="checkout" data-refid="'+data+'" data-refpack="'+((row[0]=='-')?'':row[4])+'" data-feattype="'+((row[0]=='-')?'':row[7])+'" class="checkout btn btn-danger btn-datatable btn-icon">'+'<i class="fas fa-trash"></i></button>':'';
								return '<button name="checkup" data-refid="'+data+'" data-refpack="'+((row[0]=='-')?'':row[4])+'" data-feattype="'+((row[0]=='-')?'':row[7])+'" class="checkup btn btn-primary btn-datatable btn-icon mr-2" type="button">'+'<i class="fas fa-edit"></i></button>'+del_str;
							}
						}
					}],
				"createdRow": function( row, data, dataIndex ){
					$('td', row).eq(0).removeClass('masked_col');
					$('td', row).eq(3).removeClass('masked_col');
					//if(data.length==6)
					if(data.length == 8){
						$('td', row).eq(3).attr('data-refgrp', data[5]);
					}
					$( row ).attr( 'id', 'row-'+data[3] );//$( row ).data( 'refid', data[3] );
					//$( row ).data('refpack', (data[0]=='-')?'':data[4]);
				}
			});
 
		$(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
			$.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
		});

        function calcDaySal() {
            var tot_days = $("#payroll_act_id").find(":selected").data("totdays"); //payroll_process_type_id
            var day_salary = $("#basic_salary").val() / tot_days;
            $("#day_salary").val(isNaN(day_salary) ? '0.00' : day_salary.toFixed(2));
        }

        function calcBasicSal() {
            if ($("#day_salary").val() != '') {
                var tot_days = $("#payroll_act_id").find(":selected").data("totdays"); //payroll_process_type_id
                var basic_salary = $("#day_salary").val() * tot_days;
                $("#basic_salary").val(isNaN(basic_salary) ? '0.00' : basic_salary.toFixed(2));
            }
        }
		
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
				selected_pos = 0;//27112025 //(selected_opt>0)?selected_opt:0;
			}else{
				var actobj=$(childobj).find('option[data-nestcode="'+(nestcode)+'"][data-paydaycode="'+(selectedval)+'"]');
				//console.log('1--'+$(actobj).index());
				var selected_opt=$(actobj).index();
				selected_pos=(selected_opt>0)?selected_opt:0;
			}
			
			//$(childobj).val(selected_val);
			$(childobj).find('option').eq(selected_pos).prop("selected", true);
			
		}

        $(".modal").on("shown.bs.modal", function () {
            var objinput = $(this).find('input[type="text"]:first-child');
            objinput.focus();
            objinput.select();
        });

        $("#basic_salary").on('keyup', function () {
            calcDaySal();
        });
        $("#payroll_process_type_id").on("change", function () {
            //calcBasicSal();
        });
        $("#day_salary").on('keyup', function () {
            calcBasicSal();
        });

        $('#create_record').click(function () {
            $('#formModalLabel').text('Find Employee');
            //$('#action_button').val('Add');
            //$('#action').val('Add');

            //$('#form_result').html('');

            $('#formModal').modal('show');
        });

        var remuneration_id, subscription_id;
		var remuneration_extra_id, remuneration_extra_group_id;

        $(document).on('click', '.signup', function () {
            $("#confirm_result").html('');
            $('#new_eligible_amount').val('');
            $('#action').val('Add');
            remuneration_id = $(this).data('refid');

            var allocation_info = 'Fixed';
            var payment_info = '';

            if ($(this).parent().data('refopt') == '0') {
                allocation_info = 'Daily basis';

            } else {
                $('#new_eligible_amount').val('0.00');
                payment_info =
                    '<br /><em>This facility has predefined payment scheme. New value will be effective on situations which goes below the least criteria defined by scheme.</em><br />&nbsp;';
            }

            $("#allocation_info").html(allocation_info);

            $('#confirmModal').modal('show');
            //$('#new_eligible_amount').focus();

            var par = $(this).parent().parent();
            $("#frm_confirm_title").html('<strong>' + par.children("td:nth-child(2)").html() +
                '</strong>' + payment_info);
        });
		
		$(document).on('click', '.checkin', function(){
			$("#checkup_result").html('');
			$('#extra_entitle_amount').val('');
			$('#action').val('Add');
			remuneration_extra_id = $(this).data('refid');
			remuneration_extra_group_id = $(this).parent().data('refgrp');
			
			var allocation_info='Bonus';
			var payment_info = '';
			
			if($(this).data('feattype')==='Q*'){
			  allocation_info='Advance';
			}else{
			  payment_info = '';
			}
			/**/
			$("#remuneration_extra_info").html(allocation_info);
			
			$('#extraQuotesModal').modal('show');
			//$('#new_eligible_amount').focus();
			
			var par=$(this).parent().parent();
			$("#frm_confirm_title").html('<strong>'+par.children("td:nth-child(2)").html()+'</strong>'+payment_info);
		});

        $(document).on('click', '.edit', async function () {
            var r = await Otherconfirmation("You want to Edit this ? ");
            if (r == true) {
                var id = $(this).data('refid'); //row#
                var pack_id = $(this).data('refpack');

                var allocation_info = 'Fixed';
                var payment_info = '';

                if ($(this).parent().data('refopt') == '0') {
                    allocation_info = 'Daily basis';
                } else {
                    payment_info =
                        '<br /><em>This facility has predefined payment scheme. New value will be effective on situations which goes below the least criteria defined by scheme.</em><br />&nbsp;';
                }

                $("#allocation_info").html(allocation_info);

                var par = $(this).parent().parent();
                $("#frm_confirm_title").html('<strong>' + par.children("td:nth-child(2)").html() +
                    '</strong>' + payment_info);

                $("#confirm_result").html('');
                $.ajax({
                    url: "RemunerationProfile/" + pack_id + "/edit",
                    dataType: "json",
                    success: function (data) {
                        $('#action').val('Edit');
                        remuneration_id = id;

                        $('#new_eligible_amount').val(data.pre_obj.new_eligible_amount);
                        $('#subscription_id').val(data.pre_obj.id);
                        //$('#formModalLabel').text('Edit Remuneration');
                        //$('#action_button').val('Edit');
                        //$('#action').val('Edit');

                        $('#confirmModal').modal('show');
                        //$('#new_eligible_amount').focus();
                        //$('#new_eligible_amount').select();

                    }
                }) /**/
            }
        });
		
		$(document).on('click', '.checkup', function(){
			var id = $(this).data('refid');//row#
			var pack_id = $(this).data('refpack');
			remuneration_extra_group_id = $(this).parent().data('refgrp');
			
			var allocation_info='Bonus';
			var payment_info = '';
			
			if($(this).data('feattype')==='Q*'){
				allocation_info='Advance';
			}else{
				payment_info = '';
			}
			/**/
			$("#remuneration_extra_info").html(allocation_info);
			
			var par=$(this).parent().parent();
			$("#frm_quotes_title").html('<strong>'+par.children("td:nth-child(2)").html()+'</strong>'+payment_info);
			
			$("#checkup_result").html('');
			$.ajax({
			   url :"RemunerationProfile/review/"+pack_id+"",
			   dataType:"json",
			   success:function(data){
				$('#action').val('Edit');
				remuneration_extra_id = id;
				
				$('#extra_entitle_amount').val(data.pre_obj.extra_entitle_amount);
				$('#extra_subscription_id').val(data.pre_obj.id);
				
				$('#extraQuotesModal').modal('show');
			   }
			})
		});

        $('#frmConfirm').on('submit', function (event) {
            event.preventDefault();
            var action_url = '';
            var payment_info = '';

            $('#payroll_profile_id').val($('#hidden_id').val());
            $('#remuneration_id').val(remuneration_id);

            if ($('#action').val() == 'Add') {
                action_url = "{{ route('addRemunerationProfile') }}";
            } else {
                action_url = "{{ route('RemunerationProfile.update') }}";
            }
            /*
            alert(action_url);
            */
            /*
            if($("#allocation_info").html()!="Daily basis"){
                payment_info="\r\nThis facility has predefined payment scheme. New value will be effective on situations which goes below the least criteria defined by scheme.";
            }
            */
            //if(x){
            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) { //alert(JSON.stringify(data));
                    console.log(data);
                    
                    var html = '';
                    // if (data.errors) {
                    //     html = '<div class="alert alert-danger">';
                    //     for (var count = 0; count < data.errors.length; count++) {
                    //         html += '<p>' + data.errors[count] + '</p>';
                    //     }
                    //     html += '</div>';
                    // }
                    if (data.errors) {
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: '',
                            message: data.errors,
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        action(actionJSON);
                    }
                    if (data.success) {
                        const actionObj = {
                            icon: 'fas fa-save',
                            title: '',
                            message: data.success,
                            url: '',
                            target: '_blank',
                            type: 'success'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        action(actionJSON);

                        html = '<div class="alert alert-success">' + data.success +
                        '</div>';
                        // $('#frmInfo')[0].reset();
                        // $('#titletable').DataTable().ajax.reload();
                        // location.reload()

                        //html += '<div class="alert alert-success">' + payment_info + '</div>';


                        var selected_tr = remunerationTable.row('#row-' + remuneration_id +
                            ''); //remunerationTable.$('tr.classname');
                        /*
                        alert(JSON.stringify(selected_tr.data()));
                        
                        var rowNode=selected_tr.node();
                        $( rowNode ).find('td').eq(0).html( data.alt_obj.remuneration_name );
                        $( rowNode ).find('td').eq(1).html( data.alt_obj.remuneration_type );
                        $( rowNode ).find('td').eq(2).html( data.alt_obj.epf_payable );
                        */
                        var d = selected_tr.data();
                        d[0] = 0;
                        d[2] = $('#new_eligible_amount').val();
                        if ($('#action').val() == 'Add') {
                            d[4] = (data.new_obj.id);
                        }

                        remunerationTable.row(selected_tr).data(d).draw();

                        setTimeout(function () {
                            $('#confirmModal').modal('hide');
                        }, 2000);

                    }
                    $('#confirm_result').html(html);
                }
            });
            //}
        });
		
		$('#frmExtraQuotes').on('submit', function(event){
			event.preventDefault();
			var action_url = "{{ route('addPayrollProfileExtras') }}";
			var payment_info = '';
			
			$('#frmExtraQuotes_payroll_profile_id').val($('#hidden_id').val());
			$('#remuneration_extra_id').val(remuneration_extra_id);
			$('#frmExtraQuotes_remuneration_id').val(remuneration_extra_group_id);
			/*
			if($('#action').val() == 'Add'){
			   action_url = "{{ route('addPayrollProfileExtras') }}";
			}else{
			   action_url = "{{ route('addPayrollProfileExtras') }}"; // updatePayrollProfileExtras
			}
			*/
			/*
			  alert(action_url);
			*/
			
			//if(x){
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
					 //alert(quotaTable.row('#row-'+remuneration_extra_id+'').length);
					 var act_table=(quotaTable.row('#row-'+remuneration_extra_id+'').length==1)?quotaTable:bonusTable;
					 var selected_tr=act_table.row('#row-'+remuneration_extra_id+'');//quotaTable.row('#row-'+remuneration_extra_id+'');
					 
					 var d=selected_tr.data();
					 d[0]=0;
					 d[2]=$('#extra_entitle_amount').val();
					 if($('#action').val()=='Add'){
						d[4]=(data.new_obj.id);
					 }
					 
					 act_table.row(selected_tr).data(d).draw();//quotaTable.row(selected_tr).data(d).draw();
					 
					 setTimeout(function(){
						 $('#extraQuotesModal').modal('hide');
						}, 2000);
					 
					}
					$('#checkup_result').html(html);
				   }
				});
			//}
		});

        $('#frmInfo').on('submit', function (event) {
            event.preventDefault();
            var action_url = '';


            if ($('#hidden_id').val() == '') {
                action_url = "{{ route('addPayrollProfile') }}";
            } else {
                action_url = "{{ route('PayrollProfile.update') }}";
            }
            /*
            alert(action_url);
            */
            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {

                    var html = '';
                    // if (data.errors) {
                    //     html = '<div class="alert alert-danger">';
                    //     for (var count = 0; count < data.errors.length; count++) {
                    //         html += '<p>' + data.errors[count] + '</p>';
                    //     }
                    //     html += '</div>';
                    // }
                    if (data.errors) {
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: '',
                            message: data.errors,
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        action(actionJSON);
                    }
                    if (data.success) {
                        const actionObj = {
                            icon: 'fas fa-save',
                            title: '',
                            message: data.success,
                            url: '',
                            target: '_blank',
                            type: 'success'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        action(actionJSON);
                        html = '<div class="alert alert-success">' + data.success +
                        ''; //'</div>';
                        // $('#frmInfo')[0].reset();
                        // $('#titletable').DataTable().ajax.reload();
                        // location.reload()

                        if ($('#hidden_id').val() == '') {
                            $('#hidden_id').val(data.new_obj.id);
                        }

                        var selected_tr = empTable.row('#row-' + $('#emp_id').val() +
                        ''); //$('#emp_etfno').val() //remunerationTable.$('tr.classname');
                        /*
                        alert(JSON.stringify(selected_tr.data()));
                        
                        var rowNode=selected_tr.node();
                        $( rowNode ).find('td').eq(0).html( data.alt_obj.remuneration_name );
                        $( rowNode ).find('td').eq(1).html( data.alt_obj.remuneration_type );
                        $( rowNode ).find('td').eq(2).html( data.alt_obj.epf_payable );
                        */
                        var d = selected_tr.data();

                        if ((d.basic_salary != $('#basic_salary').val()) || (d
                                .process_name != $('#payroll_process_type_id').find(
                                    ":selected").text())) {
                            html += ' - Please review salary additions of ' + $("#emp_name")
                                .val() + ' if necessary';
                        }

                        html += '</div>';

                        d.basic_salary = $('#basic_salary').val();
                        d.process_name = $('#payroll_process_type_id').find(":selected")
                            .text();
                        empTable.row(selected_tr).data(d).draw();


                    }
                    $('#form_result').html(html);
                }
            });
        });

        $(document).on('click', '.review', function () {
            var id = $(this).data('refid');
            //$('#form_result').html('');
            $.ajax({
                url: "PayrollProfile/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    $('#emp_name').val(data.pre_obj.emp_name_with_initial);
                    $('#payroll_process_type_id').val(data.pre_obj.payroll_process_type_id);

                    $('#day_salary').val(data.pre_obj.day_salary);
                    $('#basic_salary').val(data.pre_obj.basic_salary);

                    var employee_position = (data.pre_obj.employee_executive_level == '1') ?
                        '1' : '0';
                    $('#employee_executive_level').val(employee_position);

                    $('#emp_etfno').val(data.pre_obj.employee_etfno);
                    $('#emp_id').val(data.pre_obj.employee_id);
                    $('#payroll_act_id').val(data.pre_obj.payroll_act_id);
                    $('#hidden_id').val(data.pre_obj.id);

                    $('#epfetf_contribution').val(data.pre_obj.epfetf_contribution);
					
					
					//$('#employee_payday_id').val(data.pre_obj.employee_payday_id);
					prep_nest('paydaynest', data.pre_obj.payroll_process_type_id, data.pre_obj.employee_payday_id);
					

                    $("select[name='payroll_act_id']").children("option").prop("disabled",
                        true); //children("option[class='addr_key']")
                    $("select[name='payroll_act_id']").children("option:selected").prop(
                        "disabled", false);

                    //$('#formModalLabel').text('Edit Remuneration');
                    //$('#action_button').val('Edit');
                    //$('#action').val('Edit');

                    $('#employee_bank_id').html('<option value="">Please select</option>');
                    var sel_str = '';
                    $.each(data.bank_ac_list, function (i, ac_info) {
                        sel_str = (data.pre_obj.employee_bank_id == ac_info.id) ?
                            ' selected="selected"' : '';
                        $('#employee_bank_id').append('<option value="' + ac_info
                            .id + '"' + sel_str + '>' + ac_info.bank_ac_no +
                            '</option>'); // ('+ac_info.bank_name+')
                    });

                    remunerationTable.clear();
                    remunerationTable.rows.add(data.package);
                    remunerationTable.draw();
					
					quotaTable.clear();
					quotaTable.rows.add(data.extra_quota);
					quotaTable.draw();
					
					bonusTable.clear();
					bonusTable.rows.add(data.bonus_figs);
					bonusTable.draw();

                    $('#formModal').modal('hide');

                    if ($('#hidden_id').val() == '') {
                        $('#form_result').html(
                            '<div class="alert alert-info">Please complete employee salary details</div>'
                            );
                    } else {
                        $('#form_result').html(data.form_result_html); //''
                        if (data.revw_salary == '1') {
                            calcDaySal();
                        }
                    }
                }
            }) /**/
        });

        $(document).on('click', '.delete', async function () {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (r == true) {
                remuneration_id = $(this).data('refid');
                subscription_id = $(this).data('refpack');
				remuneration_extra_id = 'D*';

                $.ajax({
                    url: "RemunerationProfile/destroy/" + subscription_id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        //alert(JSON.stringify(data));
                        setTimeout(function () {
                            $('#remunerationCancelModal').modal('hide');
                            //$('#user_table').DataTable().ajax.reload();
                            //alert('Data Deleted');
                        }, 2000);
                        //location.reload()
                        if (data.errors) {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: data.errors,
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                        if (data.result == 'success') {
                            const actionObj = {
                                icon: 'fas fa-trash-alt',
                                title: '',
                                message: 'Record Remove Successfully',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                            
                            var selected_tr = remunerationTable.row('#row-' + remuneration_id +
                                '');

                            var d = selected_tr.data();
                            d[0] = 1;
                            remunerationTable.row(selected_tr).data(d).draw();
                        }
                    }
                })
            }
            // $('#ok_button').text('OK');
            // $('#remunerationCancelModal').modal('show');
        });
		
		$(document).on('click', '.checkout', async function () {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (r == true) {
                remuneration_id = $(this).data('feattype');
                subscription_id = $(this).data('refpack');
				remuneration_extra_id = $(this).data('refid');

                $.ajax({
                    url: "RemunerationProfile/checkout/" + subscription_id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        //alert(JSON.stringify(data));
                        setTimeout(function () {
                            $('#remunerationCancelModal').modal('hide');
                            //$('#user_table').DataTable().ajax.reload();
                            //alert('Data Deleted');
                        }, 2000);
                        //location.reload()
                        if (data.errors) {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: data.errors,
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                        if (data.result == 'success') {
                            const actionObj = {
                                icon: 'fas fa-trash-alt',
                                title: '',
                                message: 'Record Remove Successfully',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                            
							var act_table=(quotaTable.row('#row-'+remuneration_extra_id+'').length==1)?quotaTable:bonusTable;
                            var selected_tr = act_table.row('#row-' + remuneration_extra_id +
                                '');

                            var d = selected_tr.data();
                            d[0] = 1;
                            act_table.row(selected_tr).data(d).draw();
                        }
                    }
                })
            }
            // $('#ok_button').text('OK');
            // $('#remunerationCancelModal').modal('show');
        });

        // $(document).on('click', '#ok_button', function () {
            
        // });

    });
</script>

@endsection