@extends('layouts.app')

@section('content')

<main>
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('ERP_KT.erp_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-industry"></i></div>
                    <span>Job Approve</span>
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
                                        <th>EMPLOYEE</th>
                                        <th>JOB TITLE</th>
                                        <th>MACHINE</th>
                                        <th>WORK HOURS</th>
                                        <th>INCENTIVE</th>
                                        <th>CUSTOMER</th>
                                        <th>INQUIRY</th>
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

    <!-- filter function -->
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
                            <label class="small font-weight-bolder text-dark">Customer Name</label>
                            <select name="customer_name" id="customer_f" class="form-control form-control-sm"></select>
                        </div>
                    </li>
                    <li class="mb-2">
                        <div class="col-md-12">
                            <label class="small font-weight-bolder text-dark">Inquiry</label>
                            <select name="inquiry" id="inquiry_f" class="form-control form-control-sm"></select>
                        </div>
                    </li>
                    <li class="mb-2">
                        <div class="col-md-12">
                            <label class="small font-weight-bolder text-dark">Machine</label>
                            <select name="machine_name" id="machine_f" class="form-control form-control-sm"></select>
                        </div>
                    </li>
                    <li class="mb-2">
                        <div class="col-md-12">
                            <label class="small font-weight-bolder text-dark">Job Title</label>
                            <select name="job_title" id="job_title_f" class="form-control form-control-sm"></select>
                        </div>
                    </li>
                    <li class="mb-2">
                        <div class="col-md-12">
                            <label class="small font-weight-bolder text-dark">Employee</label>
                            <select name="employee" id="employee_f" class="form-control form-control-sm"></select>
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

    <!-- approve confirm modal -->
    <div class="modal fade" id="approveconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Approve Job Data </h5>
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
                        <h5 class="modal-title" id="exampleModalLabel">Approve Meter Reading</h5>
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

</main>

@endsection


@section('script')

<script>
    $(document).ready(function() {

        $('#erp_menu_link_KT').addClass('active');
        $('#erp_menu_link_KT_icon').addClass('active');
        $('#erp_kt_calculations').addClass('navbtnactive');


        let employee = $('#employee_f');
        let customer = $('#customer_f');
        let inquiry = $('#inquiry_f');
        let machine = $('#machine_f')
        let job_title = $('#job_title_f')

        // Customer Select2 Initialization
        customer.select2({
            placeholder: 'Select Customer...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#offcanvasRight'),
            ajax: {
                url: '{{ url("kt_customer_list_sel2") }}',
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


        // Employee by title Select2 Initialization
        function initEmployeeSelect(jobTitleId) {

            employee.select2({
                placeholder: 'Select Employee...',
                width: '100%',
                allowClear: true,
                dropdownParent: $('#offcanvasRight'),
                ajax: {
                    url: '{{ url("kt_employee_list_by_title_sel2") }}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            job_title_id: jobTitleId || ''
                        };
                    },
                    cache: false
                }
            });
        }

        initEmployeeSelect(null);

        $('#job_title_f').on('change', function() {
            var selectedTitleId = $(this).val();
            $('#employee_f').val(null);
            initEmployeeSelect(selectedTitleId);
        });

        //Job title Select2 Initialization
        job_title.select2({
            placeholder: 'Select Job Title...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#offcanvasRight'),
            ajax: {
                url: '{{ url("kt_job_title_list_sel2") }}',
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

        //Inquiry Select2 Initialization
        inquiry.select2({
            placeholder: 'Select Inquiry...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#offcanvasRight'),
            ajax: {
                url: '{{ url("kt_inquiry_list_sel2") }}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1,
                        customer_id: $('#customer_f').val() || ''
                    }
                },
                cache: true
            }
        });

        // Reset inquiry when customer changes
        customer.on('change', function() {
            inquiry.val(null).trigger('change');
        });

        //Machine Select2 Initialization
        machine.select2({
            placeholder: 'Select Machine...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#offcanvasRight'),
            ajax: {
                url: '{{ url("kt_machine_list_sel2") }}',
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

        let dataTable = null;

        function load_dt(customer_name, inquiry, from_date, to_date, machine_name, employee, job_title) {

            if (dataTable !== null) {
                dataTable.ajax.url('{{ route("kt_approve_approvegenerate") }}').load();
                // Update the ajax data params
                dataTable.settings()[0].ajax = {
                    url: '{{ route("kt_approve_approvegenerate") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_name: customer_name,
                        inquiry: inquiry,
                        from_date: from_date,
                        to_date: to_date,
                        machine_name: machine_name,
                        employee: employee,
                        job_title: job_title,
                    }
                };
                dataTable.ajax.reload();
                return;
            }

            dataTable = $('#dataTable').DataTable({
                "processing": true,
                "serverSide": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Job Approve Information',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm',
                        title: 'Job Approve Information',
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'landscape',
                        pageSize: 'legal',
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Job Approve Information',
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
                    url: '{{ route("kt_approve_approvegenerate") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_name: customer_name,
                        inquiry: inquiry,
                        from_date: from_date,
                        to_date: to_date,
                        machine_name: machine_name,
                        employee: employee,
                        job_title: job_title,
                    },
                },
                columns: [{
                        data: null,
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (row.approve_status == 1) {
                                return '<input type="checkbox" class="row-checkbox selectCheck removeIt" data-id="' + row.id + '" checked disabled>';
                            } else {
                                return '<input type="checkbox" class="row-checkbox selectCheck removeIt" data-id="' + row.id + '">';
                            }
                        }
                    },
                    {
                        data: 'employee',
                        name: 'employee'
                    },
                    {
                        data: 'job_title',
                        name: 'job_title'
                    },
                    {
                        data: 'machine',
                        name: 'machine'
                    },
                    {
                        data: 'reading_hours',
                        name: 'reading_hours'
                    },
                    {
                        data: 'incentive',
                        name: 'incentive',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return data != null ? parseFloat(data).toFixed(2) : '0.00';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'inquiry',
                        name: 'inquiry'
                    },
                    {
                        data: 'emp_auto_id',
                        name: 'emp_auto_id',
                        visible: false
                    }
                ],
                "Destroy": true,
            });
        }

        //filter handler
        $('#formFilter').on('submit', function(e) {
            e.preventDefault();
            let customer = $('#customer_f').val();
            let inquiry = $('#inquiry_f').val();
            let machine = $('#machine_f').val();
            let employee = $('#employee_f').val();
            let job_title = $('#job_title_f').val();
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

            load_dt(customer, inquiry, from_date, to_date, machine, employee, job_title);
            closeOffcanvasSmoothly();
        });

        $('#btn-reset').click(function() {
            $('#formFilter')[0].reset();
            customer.val(null).trigger('change');
            inquiry.val(null).trigger('change');
            machine.val(null).trigger('change');
            employee.val(null).trigger('change');
            job_title.val(null).trigger('change');

            load_dt('', '', '', '', '', '', '');
        });


        var selectedRowIdsapprove = [];

        $('#approve_att').click(function() {
            selectedRowIdsapprove = [];
            $('#dataTable tbody .selectCheck:checked').each(function() {
                var rowData = $('#dataTable').DataTable().row($(this).closest('tr')).data();

                if (rowData) {
                    selectedRowIdsapprove.push({
                        cusid: rowData.id,
                        customer_name: rowData.customer_name,
                        inquiry: rowData.inquiry,
                        emp_auto_id: rowData.emp_auto_id,
                        incentive: rowData.incentive,
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
            url: '{!! route("jobapproveinquiry") !!}',
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

        $('#selectAll').click(function(e) {
            $('#dataTable').closest('table').find('td input:checkbox:not(:disabled)').prop('checked', this.checked);
        });


    });
</script>


@endsection
