@extends('layouts.app')

@section('content')

<main> 
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('ProductionEmployee.production_nav_bar')
        </div>
    <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-hard-hat"></i></div>
                    <span>Production Approve</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <div class="row align-items-center mb-4">
                            <div class="col-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Select All Records</label>
                                </div>
                            </div>
                            <div class="col-6 text-right">
                                <button id="approve_att" class="btn btn-primary btn-sm">Approve All</button>
                            </div>
                            <div class="col-12">
                                <hr class="border-dark">
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                    data-toggle="offcanvas" data-target="#offcanvasRight"
                                    aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                    Records</button>
                            </div>
                        </div>

                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>EMPLOYEE ID</th>
                                    <th>EMPLOYEE</th>
                                    <th class="actdrop_col">READING DATES</th>
                                    <th>OVERALL TOTAL</th>
                                    <th class="d-none">Employee auto id</th>
                                </tr>
                            </thead>
                            <tbody>   
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
              <div class="offcanvas-header">
                  <h2 class="offcanvas-title font-weight-bolderer" id="offcanvasRightLabel">Records Filter Options</h2>
                  <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                      <span aria-hidden="true" class="h1 font-weight-bolderer">&times;</span>
                  </button>
              </div>
              <div class="offcanvas-body">
                  <ul class="list-unstyled">
                      <form class="form-horizontal" id="formFilter">
                           <li class="mb-2">
                              <div class="col-md-12">
                                   <label class="small font-weight-bolder text-dark">Company</label>
                                <select name="company" id="company_f" class="form-control form-control-sm"></select>
                            </div>
                          </li>
                           <li class="mb-2">
                              <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">Department</label>
                                <select name="department" id="department_f" class="form-control form-control-sm"></select>
                            </div>
                          </li>
                           <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">Employee</label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm">
                                </select>
                            </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark"> From Date* </label>
                                  <input type="date" id="from_date" name="from_date"
                                      class="form-control form-control-sm" placeholder="yyyy-mm-dd"
                                      value="{{date('Y-m-d') }}" required>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark"> To Date*</label>
                                  <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                      placeholder="yyyy-mm-dd" value="{{date('Y-m-d') }}" required>
                              </div>
                          </li>
                          <li>
                              <div class="col-md-12 d-flex justify-content-between">
                                 
                                  <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
                                      <i class="fas fa-redo mr-1"></i> Reset
                                  </button>
                                   <button type="submit" class="btn btn-primary btn-sm filter-btn px-3" id="btn-filter">
                                      <i class="fas fa-search mr-2"></i>Search
                                  </button>
                              </div>
                          </li>
                      </form>
                  </ul>
              </div>
        </div>


    <div class="modal fade" id="approveconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Approve Production Data </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col text-center">
                            <h4 class="font-weight-normal">Are you sure you want to Approve this data?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="approve_button" id="approve_button"
                        class="btn btn-primary px-3 btn-sm">Approve</button>
                    <button type="button" class="btn btn-danger px-3 btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- approve modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Approve Production Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="message_modal"></div>
                        <form class="form-horizontal" id="formApprove">
                            <div class="form-group mb-1">
                                <div class="col-12">
                                        <label class="small font-weight-bolder text-dark">Addition Type</label>
                                        <select name="remunitiontype" id="remunitiontype" class="form-control form-control-sm">
                                            <option value="">Select Remuneration</option>
                                                @foreach ($remunerations as $remuneration){
                                                    <option value="{{$remuneration->id}}" >{{$remuneration->remuneration_name}}</option>
                                                }  
                                                @endforeach
                                        </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm px-3" id="btn-approve"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Approve</button>
                    </div>
                </div>
            </div>
    </div>

    <!-- date modal -->
     <div id="DateModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="loanModalLabel">Incentive Dates</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
				</div>
				<div class="modal-body">
					<span id="loan_result"></span>
					<div class="form-row">
						<div class="col">
							<label class="font-weight-bolder small">Employee</label>
							<input type="text" name="loan_modal_employee_name" id="loan_modal_employee_name" class="form-control form-control-sm" readonly="readonly" />
						</div>
					</div>
					<form id="frmInstallmentList" class="frm_link" method="post">
						{{ csrf_field() }}
						<div class="form-row mt-3">
							<div class="col-12" style="">
								<div class="center-block fix-width scroll-inner">
									<table class="table table-bordered table-striped table-sm small w-100 nowrap" id="loantable" width="100%"
										cellspacing="0">
										<thead>
											<tr>
												<th>DATE</th>
                                                <th>DEPARTMENT</th>
												<th>INCENTIVE</th>
											</tr>
										</thead>
									</table>
								</div>
							</div>
							<div class="col-12 text-right mt-2">
								<button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal">Close</button>
							</div>
						</div>
					</form>
					<form id="frmInstallmentInfo" class="frm_info sect_bg" method="post">
						{{ csrf_field() }}
						<div class="form-row mb-1">
							<div class="col">
								<label class="font-weight-bolder small">Total Incentive</label>
								<input type="text" name="total_Incentive" id="date_modal_total_incentive" class="form-control form-control-sm" readonly="readonly" />
							</div>
						</div>
						<div class="form-row mt-3">
							<div class="col-12 text-right">
								<hr>
								<input type="button" id="" value="Back" class="btn btn-light btn_back btn-sm px-3" />
								<input type="hidden" name="hidden_loan_id" id="hidden_loan_id" value="" />
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
$(document).ready(function(){

    $('#production_employee_menu_link').addClass('active');
    $('#production_employee_menu_link_icon').addClass('active');
    $('#production_employee').addClass('navbtnactive');

     let company_f = $('#company_f');
     let employee_f = $('#employee_f');
     let department_f = $('#department_f');

    company_f.select2({
        placeholder: 'Select a Company',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("company_list_sel2")}}',
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

    department_f.select2({
        placeholder: 'Select a Department',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("department_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company_f.val(),
                }
            },
            cache: true
        }
    });

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
                    page: params.page || 1,
                    company: company_f.val(),
                    department: department_f.val()
                }
            },
            cache: true
        }
    });

    function load_dt(company, department, employee, from_date, to_date) {
        $('#dataTable').DataTable({
        "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Production Approve Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Production Approve Information', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Production Approve Information',
                    className: 'btn btn-primary btn-sm',
                    text: '<i class="fas fa-print mr-2"></i> Print',
                    customize: function(win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    },
                },
            ],
            "order": [
                [1, "desc"]
            ],
            ajax: {
                url:  "{{url('/emp_prod_approvegenerate')}}",
                type: 'POST',
                data: { 
                        _token: '{{ csrf_token() }}',
                    company: company,
                    department: department,
                    employee: employee, 
                    from_date: from_date,
                    to_date: to_date
                },
            },
            columns: [
                {
                    data: null,
                    name: 'checkbox',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (row.is_approved == 1) {
                            return '<input type="checkbox" class="row-checkbox selectCheck removeIt" data-id="' + row.emp_auto_id + '" checked disabled>';
                        } else {
                            return '<input type="checkbox" class="row-checkbox selectCheck removeIt" data-id="' + row.emp_auto_id + '">';
                        }
                    }
                },
                { data: 'emp_id', name: 'emp_id' },
                { data: 'emp_name_with_initial', name: 'emp_name_with_initial' },
                {
                    data: 'date_count',
                    name: 'date_count',
                    className: 'actdrop_col',
                    render: function(data, type, row) {
                        return data +
                            '<button class="btn btn-transparent-dark btn-sm date_list" type="button" data-refemp="' +
                            row.emp_id + '" data-refpay="' + row.emp_id +
                            '">' + '<i class="fal fa-question-circle text-primary"></i>' + '</button>';
                    }
                },
                { data: 'overall_total', name: 'overall_total' },
                {
                    data: 'emp_auto_id',
                    name: 'emp_auto_id',
                    visible: false
                }
            ],
            "bDestroy": true,
        });
    }

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let company = $('#company_f').val();
        let department = $('#department_f').val();
        let employee = $('#employee_f').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        if (!from_date || !to_date) {
            alert('Please select both From and To dates');
            return;
        }
        
        if (from_date > to_date) {
            alert('From date cannot be greater than To date');
            return;
        }

        load_dt(company, department, employee, from_date, to_date);
        closeOffcanvasSmoothly();
    });

    $('#btn-reset').click(function() {
        $('#formFilter')[0].reset();
        company_f.val(null).trigger('change');
        department_f.val(null).trigger('change');
        employee_f.val(null).trigger('change');
        
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }
        $('#dataTable tbody').empty();
    });


    var selectedRowIdsapprove = [];

    $('#approve_att').click(function () {
        selectedRowIdsapprove = [];
        $('#dataTable tbody .selectCheck:checked').each(function () {
            var rowData = $('#dataTable').DataTable().row($(this).closest('tr')).data();

            if (rowData) {
                selectedRowIdsapprove.push({
                    empid: rowData.emp_id, 
                    emp_name: rowData.emp_name_with_initial, 
                    date_count: rowData.date_count, 
                    overall_total: rowData.overall_total,
                    emp_auto_id: rowData.emp_auto_id
                });
            }
        });
        
        if (selectedRowIdsapprove.length > 0) {
            $('#approveconfirmModal').modal('show');
        } else {
            alert('Please select at least one record to approve!');
        }
    });

    $('#approve_button').off('click').on('click', function() {
        $('#approveconfirmModal').modal('hide');
        $('.message_modal').html('');
        $('#approveModal').modal('show');
    });

    $(document).on('click', '#btn-approve', function (e) {
        e.preventDefault();
        var remunitiontype = $('#remunitiontype').val();
        var employee = $('#employee_f').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        if(remunitiontype == ''){
            $('.message_modal').html('<div class="alert alert-warning">Please select Remuneration Type!</div>');
            return false;
        }

        console.log(selectedRowIdsapprove);
        console.log('Remunition type:', remunitiontype);
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '{!! route("approveproduction") !!}',
            type: 'POST',
            dataType: "json",
            data: {
                dataarry: selectedRowIdsapprove,
                remunitiontype: remunitiontype,
                employee_f: employee,
                from_date: from_date,
                to_date: to_date
            },
            success: function (data) {
                if (data.success) {
                    let successHtml = `<div class='alert alert-success'>${data.success}</div>`;
                    
                    if (data.errors && data.errors.length > 0) {
                        let errorHtml = '<div class="alert alert-warning mt-2"><strong>Some issues occurred:</strong><ul>';
                        data.errors.forEach(error => {
                            errorHtml += `<li>${error}</li>`;
                        });
                        errorHtml += '</ul></div>';
                        successHtml += errorHtml;
                    }
                    
                    $('.message_modal').html(successHtml);
                    
                    if (!data.errors || data.errors.length === 0) {
                        $('#formApprove')[0].reset();
                        $('#remunitiontype').val('').trigger('change');
                        
                        setTimeout(function() {
                            $('#approveModal').modal('hide');
                            location.reload();
                        }, 2000);
                    }
                } else {
                    let html = '<div class="alert alert-danger">';
                    if (data.errors && Array.isArray(data.errors)) {
                        html += '<strong>Errors occurred:</strong><ul>';
                        data.errors.forEach(error => {
                            html += `<li>${error}</li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += data.message || 'Something went wrong. Please try again.';
                    }
                    html += '</div>';
                    $('.message_modal').html(html);
                }
                
                $('#approveModal').scrollTop(0);
            },
            error: function(xhr) {
                let errorMessage = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                $('.message_modal').html(`<div class="alert alert-danger">${errorMessage}</div>`);
                $('#approveModal').scrollTop(0);
            }
        });
    });
    
    $('#selectAll').click(function (e) {
        $('#dataTable').closest('table').find('td input:checkbox:not(:disabled)').prop('checked', this.checked);
    });

    // Date List Modal Handler
    $(document).on('click', '.date_list', function () {
        var emp_id   = $(this).data('refemp');
        var autoid   = $(this).data('refpay');
        var from_date = $('#from_date').val();
        var to_date   = $('#to_date').val();

        $('#loan_modal_employee_name').val('');
        $('#date_modal_total_incentive').val('');
        $('#loantable').DataTable().clear().destroy();

        $.ajax({
            url: '{{ url("/emp_prod_date_details") }}',
            type: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                emp_id: emp_id,
                from_date: from_date,
                to_date: to_date
            },
            success: function (data) {
                $('#loan_modal_employee_name').val(data.emp_name);
                $('#date_modal_total_incentive').val(data.total_incentive);

                $('#loantable').DataTable({
                    destroy: true,
                    data: data.dates,
                    columns: [
                        { data: 'date',            title: 'DATE' },
                        { data: 'department_name', title: 'DEPARTMENT' },
                        { data: 'incentive',       title: 'INCENTIVE' }
                    ],
                    paging: false,
                    searching: false,
                    info: false
                });

                $('#DateModal').modal('show');
            },
            error: function () {
                alert('Failed to load date details. Please try again.');
            }
        });
    });

});
</script>


@endsection