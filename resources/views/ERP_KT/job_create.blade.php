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
                    <span>Create Job</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <!-- Data Table Card -->
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right mr-2" name="create_record" id="create_record">
                            <i class="fas fa-plus mr-2"></i>Add Jobs
                        </button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>CUSTOMER</th>
                                        <th>INQUIRY</th>
                                        <th>START FROM</th>
                                        <th>END AT</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Create Job</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mt-2">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Customer Name <span class="text-red">*</span></label>
                                        <select name="customer_name" id="customer_name" class="form-control form-control-sm" required></select>
                                    </div>
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Inquiry <span class="text-red">*</span></label>
                                        <select name="inquiry" id="inquiry_f" class="form-control form-control-sm" required></select>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">Start From <span class="text-red">*</span></label>
                                        <input type="datetime-local" name="start_from" id="start_from" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">End At <span class="text-red">*</span></label>
                                        <input type="datetime-local" name="end_at" id="end_at" class="form-control form-control-sm" required />
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Job Description</span></label>
                                        <input type="text" name="job_description" id="job_description" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Remarks</span></label>
                                        <input type="text" name="remarks" id="remarks" class="form-control form-control-sm" />
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Machine</label>
                                        <select name="machine_name" id="machine_f" class="form-control form-control-sm"></select>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Job Title</span></label>
                                        <select name="job_title" id="job_title_f" class="form-control form-control-sm"></select>
                                    </div>
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Employee</span></label>
                                        <select name="employee" id="employee_f" class="form-control form-control-sm"></select>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group mt-3">
                                    <div class="col-12 col-sm-6">
                                        <button type="button" id="formsubmit" class="btn btn-primary btn-sm px-4 float-right">
                                            <i class="fas fa-plus"></i>&nbsp;Add
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                            </form>
                        </div>

                        <!-- Preview Table -->
                        <div class="col-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                    <thead>
                                        <tr>
                                            <th>MACHINE</th>
                                            <th>EMPLOYEE</th>
                                            <th>JOB TITLE</th>
                                            <th class="text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableorderlist"></tbody>
                                </table>
                            </div>
                            <div class="form-group mt-2">
                                <button type="button" name="btncreateorder" id="btncreateorder" class="btn btn-primary btn-sm float-right px-4">
                                    <i class="fas fa-save"></i>&nbsp;Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="aviewmodal-title" id="staticBackdropLabel">View Jobs</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mt-2">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Customer Name <span class="text-red">*</span></label>
                                        <select name="view_customer_name" id="view_customer_name" class="form-control form-control-sm" disabled></select>
                                    </div>
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Inquiry <span class="text-red">*</span></label>
                                        <select name="view_inquiry" id="view_inquiry" class="form-control form-control-sm" disabled></select>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">Start From <span class="text-red">*</span></label>
                                        <input type="datetime-local" name="view_start_from" id="view_start_from" class="form-control form-control-sm" readonly />
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">End At <span class="text-red">*</span></label>
                                        <input type="datetime-local" name="view_end_at" id="view_end_at" class="form-control form-control-sm" readonly />
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Job Description</span></label>
                                        <input type="text" name="view_job_description" id="view_job_description" class="form-control form-control-sm" readonly />
                                    </div>
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Remarks</span></label>
                                        <input type="text" name="view_remarks" id="view_remarks" class="form-control form-control-sm" readonly />
                                    </div>
                                </div>
                                <!-- <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Machine</label>
                                        <select name="view_machine_body" id="view_machine_body" class="form-control form-control-sm " disabled></select>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Job Title</span></label>
                                        <select name="view_job_title" id="view_job_title" class="form-control form-control-sm" disabled></select>
                                    </div>
                                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                        <label class="small font-weight-bold text-dark">Employee</span></label>
                                        <select name="view_employee_body" id="view_employee_body" class="form-control form-control-sm" disabled></select>
                                    </div>
                                </div>
                                <hr> -->
                            </form>
                        </div>

                        <!-- Preview Table -->
                        <div class="col-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small" id="view_tableorder">
                                    <thead>
                                        <tr>
                                            <th>MACHINE</th>
                                            <th>EMPLOYEE</th>
                                            <th>JOB TITLE</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view_tableorderlist"></tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- View Modal End-->

</main>

@endsection

@section('script')
<script>
    $(document).ready(function() {

        $('#erp_menu_link_KT').addClass('active');
        $('#erp_menu_link_KT_icon').addClass('active');
        $('#erp_kt_calculations').addClass('navbtnactive');

        // Initialize filter dropdowns
        let employee = $('#employee_f');
        let customer = $('#customer_name');
        let inquiry = $('#inquiry_f');
        let machine = $('#machine_f')
        let job_title = $('#job_title_f')

        // Customer Select2 Initialization
        customer.select2({
            placeholder: 'Select Customer...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
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

        //Job title Select2 Initialization
        job_title.select2({
            placeholder: 'Select Job Title...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
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
            dropdownParent: $('#formModal'),
            ajax: {
                url: '{{ url("kt_inquiry_list_sel2") }}',
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

        //Machine Select2 Initialization
        machine.select2({
            placeholder: 'Select Machine...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
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

        // Employee by title Select2 Initialization
        function initEmployeeSelect(jobTitleId) {

            employee.select2({
                placeholder: 'Select Employee...',
                width: '100%',
                allowClear: true,
                dropdownParent: $('#formModal'),
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


        // Date change handler
        $('#date').on('change', function() {
            if ($(this).val()) {
                $('#employee').prop('disabled', false);
                $('#employee').val(null).trigger('change');
            } else {
                $('#employee').prop('disabled', true);
                $('#employee').val(null).trigger('change');
            }
        });

        // Load DataTable
        function load_dt(customer_name, inquiry, machine_name, employee_name, job_title, start_from, end_at) {
            $('#dataTable').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Job Creation Information',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm',
                        title: 'Job Creation Information',
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'portrait',
                        pageSize: 'legal',
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Job Creation Information',
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
                    [0, "desc"]
                ],
                ajax: {
                    url: scripturl + '/ERP_KT/job_list.php',
                    type: "POST",
                    data: {
                        customer: customer_name,
                        inquiry: inquiry,
                        start_from: start_from,
                        end_at: end_at,
                        // machine_name: machine_name,
                        // employee_name: employee,
                        // job_title: job_title
                    },
                },
                columns: [{
                        data: 'id',
                        name: 'id'
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
                        data: 'start_from',
                        name: 'start_from'
                    },
                    {
                        data: 'end_at',
                        name: 'end_at'
                    },
                    {
                        data: 'id',
                        name: 'action',
                        className: 'text-right',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var buttons = '';

                            buttons += ' <button name="view" id="' + row.id + '" class="view btn btn-secondary btn-sm mr-1" type="button" data-toggle="tooltip" title="View"><i class="fas fa-eye"></i></button>';

                            buttons += '<button name="edit" id="' + row.id + '" class="edit btn btn-primary btn-sm  mr-1" type="submit" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                            buttons += '<button type="submit" name="delete" id="' + row.id + '" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';

                            return buttons;
                        }
                    }
                ],
            });
        }

        // Initial load
        load_dt();

        // Create new record
        $('#create_record').click(function() {
            $('.modal-title').text('Create A Job');
            $('#action').val('Add');
            $('#form_result').html('');
            $('#formTitle')[0].reset();

            // FULL RESET SELECT2
            $('#customer_name').empty().val(null).trigger('change');
            $('#inquiry_f').empty().val(null).trigger('change');
            $('#machine_f').empty().val(null).trigger('change');
            $('#employee_f').empty().val(null).trigger('change');
            $('#job_title_f').empty().val(null).trigger('change');
            initEmployeeSelect(null);
            $('#job_description').val('');
            $('#remarks').val('');
            $('#start_from').val('');
            $('#end_at').val('');

            $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-save"></i>&nbsp;Save');
            $('#tableorder > tbody').html('');
            $('#hidden_id').val('');

            $('#formsubmit').show();
            $('#formModal').modal('show');
        });

        $("#formsubmit").click(function() {
            let customerVal = $('#customer_name').val();
            let startFrom = $('#start_from').val();
            let endAt = $('#end_at').val();
            let inquiryVal = $('#inquiry_f').val();
            let machineVal = $('#machine_f').val();
            let employeeVal = $('#employee_f').val();
            let job_titleVal = $('#job_title_f').val();
            let job_description = $('#job_description').val();
            let remarks = $('#remarks').val();



            if (!customerVal) {
                Swal.fire({
                    position: "top-end",
                    icon: 'warning',
                    title: 'Please select a customer',
                    showConfirmButton: false,
                    timer: 2500
                });
                return;
            }

            if (!startFrom) {
                Swal.fire({
                    position: "top-end",
                    icon: 'warning',
                    title: 'Please select start date & time',
                    showConfirmButton: false,
                    timer: 2500
                });
                return;
            }

            if (!endAt) {
                Swal.fire({
                    position: "top-end",
                    icon: 'warning',
                    title: 'Please select end date & time',
                    showConfirmButton: false,
                    timer: 2500
                });
                return;
            }

            if (!inquiryVal) {
                Swal.fire({
                    position: "top-end",
                    icon: 'warning',
                    title: 'Please select inquiry',
                    showConfirmButton: false,
                    timer: 2500
                });
                return;
            }

            if (!machineVal) {
                Swal.fire({
                    position: "top-end",
                    icon: 'warning',
                    title: 'Please select a machine',
                    showConfirmButton: false,
                    timer: 2500
                });
                return;
            }

            if (!employeeVal) {
                Swal.fire({
                    position: "top-end",
                    icon: 'warning',
                    title: 'Please select an employee',
                    showConfirmButton: false,
                    timer: 2500
                });
                return;
            }

            if (!job_titleVal) {
                Swal.fire({
                    position: "top-end",
                    icon: 'warning',
                    title: 'Please select a job title',
                    showConfirmButton: false,
                    timer: 2500
                });
                return;
            }

            let customerText = $('#customer_name option:selected').text();
            let inquiryText = $('#inquiry_f option:selected').text();
            let machineText = $('#machine_f option:selected').text();

            let employeeText = $('#employee_f option:selected').text();

            let job_titleText = $('#job_title_f option:selected').text();

            let duplicate = false;

            $('#tableorder tbody tr').each(function() {
                if (
                    $(this).data('machine-id') === machineVal &&
                    $(this).data('employee-id') === employeeVal
                ) {
                    duplicate = true;
                    return false;
                }
            });

            if (duplicate) {
                Swal.fire({
                    position: "top-end",
                    icon: 'warning',
                    title: 'This machine/operator entry is already added',
                    showConfirmButton: false,
                    timer: 2500
                });
                return;
            }

            var currentCustomerId = $('#customer_name').val() || customerVal;
            var currentInquiryId = $('#inquiry_f').val() || inquiryVal;
            var currentStartFrom = $('#start_from').val() || startFrom;
            var currentEndAt = $('#end_at').val() || endAt;

            $('#tableorder > tbody:last').append(
                '<tr class="pointer" ' +
                'data-customer-id="' + currentCustomerId + '" ' +
                'data-inquiry-id="' + currentInquiryId + '" ' +
                'data-machine-id="' + machineVal + '" ' +
                'data-employee-id="' + employeeVal + '" ' +
                'data-job_title-id="' + job_titleVal + '" ' +
                'data-start-from="' + currentStartFrom + '" ' +
                'data-end-at="' + currentEndAt + '" ' +
                'data-job-description="' + $('#job_description').val() + '" ' +
                'data-remarks="' + $('#remarks').val() + '">' +

                '<td>' + machineText + '</td>' +
                '<td>' + employeeText + '</td>' +
                '<td>' + job_titleText + '</td>' +

                '<td class="text-right">' +
                '<button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm">' +
                '<i class="fas fa-trash-alt"></i>' +
                '</button>' +
                '</td>' +
                '</tr>'
            );

            $('#employee_f').val(null).trigger('change');
        });

        // Save/Update functionality
        $('#btncreateorder').click(function() {
            var action_url = '';

            if ($('#action').val() == 'Add') {
                action_url = "{{ route('kt_addJob_Create') }}";
            }
            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('KTJob_Create.update') }}";
            }

            $('#btncreateorder').prop('disabled', true).html(
                '<i class="fas fa-circle-notch fa-spin mr-2"></i> Saving');

            var tbody = $("#tableorder tbody");

            if (tbody.children().length > 0) {
                var jsonObj = [];
                $("#tableorder tbody tr").each(function() {
                    var item = {};
                    item["col_1"] = $(this).data('customer-id') || $(this).find('td:eq(0)').text();
                    item["col_2"] = $(this).data('start-from') || $(this).find('td:eq(1)').text();
                    item["col_3"] = $(this).data('end-at') || $(this).find('td:eq(2)').text();
                    item["col_4"] = $(this).data('inquiry-id') || $(this).find('td:eq(3)').text();
                    item["col_5"] = $(this).data('machine-id') || $(this).find('td:eq(4)').text();
                    item["col_6"] = $(this).data('employee-id') || $(this).find('td:eq(5)').text();
                    item["col_7"] = $(this).data('job_title-id') || $(this).find('td:eq(6)').text();
                    item["col_8"] = $('#job_description').val();
                    item["col_9"] = $('#remarks').val();
                    jsonObj.push(item);
                });


                var hidden_id = $('#hidden_id').val();

                $.ajax({
                    method: "POST",
                    dataType: "json",
                    data: {
                        _token: '{{ csrf_token() }}',
                        tableData: jsonObj,
                        hidden_id: hidden_id,
                    },
                    url: action_url,
                    success: function(data) {
                        var html = '';
                        if (data.errors) {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: 'Record Error',
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
                            actionreload(actionJSON);
                        }
                        $('#form_result').html(html);
                        $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-save"></i>&nbsp;Save');
                    },
                    error: function(xhr) {
                        var html = '<div class="alert alert-danger">An error occurred while saving</div>';
                        $('#form_result').html(html);
                        $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-save"></i>&nbsp;Save');
                    }
                });
            } else {
                Swal.fire({
                    position: "top-end",
                    icon: 'warning',
                    title: 'Cannot Create..Table Empty!',
                    showConfirmButton: false,
                    timer: 2500
                });
                $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
            }
        });

        // Edit functionality
        $(document).on('click', '.edit', async function() {
            var r = await Otherconfirmation("You want to Edit this ? ");
            if (r == true) {
                var id = $(this).attr('id');

                $.ajax({
                    url: '{{ url("KTJob_Create_edit") }}/' + id,
                    type: 'POST',
                    dataType: "json",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {

                        $('#formTitle')[0].reset();
                        $('#tableorder > tbody').html('');
                        $('#form_result').html('');


                        $('.modal-title').text('Edit Job');
                        $('#action').val('Edit');
                        $('#hidden_id').val(id);
                        $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-save"></i>&nbsp;Update');


                        $('#customer_name').empty();
                        if (data.result.customer_id) {
                            $('#customer_name').append(new Option(data.result.customer_name, data.result.customer_id, true, true)).trigger('change');
                        }


                        $('#inquiry_f').empty();
                        if (data.result.inquiry_id) {
                            $('#inquiry_f').append(new Option(data.result.inquiry, data.result.inquiry_id, true, true)).trigger('change');
                        }


                        $('#start_from').val(data.result.start_from ? data.result.start_from.replace(' ', 'T').substring(0, 16) : '');
                        $('#end_at').val(data.result.end_at ? data.result.end_at.replace(' ', 'T').substring(0, 16) : '');


                        $('#machine_f').empty();
                        $('#employee_f').empty();
                        $('#job_title_f').empty();
                        $('#job_description').val(data.result.job_description);
                        $('#remarks').val(data.result.remarks);
                        $('#tableorderlist').empty();

                        // Store header-level data for use in update submission
                        var editCustomerId = data.result.customer_id;
                        var editInquiryId = data.result.inquiry_id;
                        var editStartFrom = data.result.start_from ? data.result.start_from.replace(' ', 'T').substring(0, 16) : '';
                        var editEndAt = data.result.end_at ? data.result.end_at.replace(' ', 'T').substring(0, 16) : '';

                        if (data.details && data.details.length) {
                            $.each(data.details, function(i, d) {
                                if (d.machine_id) $('#machine_f').append(new Option(d.machine_name, d.machine_id, true, true)).trigger('change');
                                if (d.emp_id) $('#employee_f').append(new Option(d.emp_name, d.emp_id, true, true)).trigger('change');
                                if (d.job_title) $('#job_title_f').append(new Option(d.job_title_name, d.job_title, true, true)).trigger('change');

                                 // Build preview table row
                                $('#tableorder > tbody').append(
                                    '<tr class="pointer" ' +
                                    'data-detail-id="' + (d.id || '') + '" ' +
                                    'data-customer-id="' + editCustomerId + '" ' +
                                    'data-inquiry-id="' + editInquiryId + '" ' +
                                    'data-start-from="' + editStartFrom + '" ' +
                                    'data-end-at="' + editEndAt + '" ' +
                                    'data-machine-id="' + (d.machine_id || '') + '" ' +
                                    'data-employee-id="' + (d.emp_id || '') + '" ' +
                                    'data-job_title-id="' + (d.job_title || '') + '" ' +
                                    'data-job-description="' + (data.result.job_description || '') + '" ' +
                                    'data-remarks="' + (data.result.remarks || '') + '">' +
                                    '<td>' + (d.machine_name || '') + '</td>' +
                                    '<td>' + (d.emp_name || '') + '</td>' +
                                    '<td>' + (d.job_title_name || '') + '</td>' +
                                    '<td class="text-right">' +
                                    '<button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm">' +
                                    '<i class="fas fa-trash-alt"></i></button>' +
                                    '</td>' +
                                    '</tr>'
                                );
                            });
                        }
                        $('#machine_f').val(null).trigger('change');
                        $('#employee_f').val(null).trigger('change');
                        $('#job_title_f').val(null).trigger('change');
                        initEmployeeSelect(null);
                        $('#formModal').modal('show');

                        window.isEditLoading = false;
                        $('#edit_record_id').val(id);
                        $('#edit_form_result').html('');
                        $('#editModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            position: "top-end",
                            icon: 'error',
                            title: 'Failed to load job data',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                });
            }
        });

        //Handle delete click
        var user_id;
        $(document).on('click', '.delete', async function() {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (r == true) {
                user_id = $(this).attr('id');
                $.ajax({
                    url: "{{ url('KTJob_Create/destroy') }}/" + user_id,
                    type: 'GET',
                    beforeSend: function() {},
                    success: function(data) {
                        const actionObj = {
                            icon: 'fas fa-trash-alt',
                            title: '',
                            message: 'Record Removed Successfully',
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        actionreload(actionJSON);
                    },
                    error: function() {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'Failed to remove record',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                });
            }
        });

    });

    // View modal 
    $(document).on('click', '.view', function() {
        var id = $(this).attr('id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: "{{ url('KTJob_Create/') }}/" + id + "/edit",
            type: 'GET',
            dataType: "json",
            data: {
                id: id
            },
            success: function(data) {
                $('#view_customer_name').empty();
                if (data.result.customer_id) {
                    $('#view_customer_name').append(new Option(data.result.customer_name, data.result.customer_id, true, true));
                }

                $('#view_inquiry').empty();
                if (data.result.inquiry_id) {
                    $('#view_inquiry').append(new Option(data.result.inquiry, data.result.inquiry_id, true, true));
                }

                $('#view_start_from').val(data.result.start_from ? data.result.start_from.replace(' ', 'T').substring(0, 16) : '');
                $('#view_end_at').val(data.result.end_at ? data.result.end_at.replace(' ', 'T').substring(0, 16) : '');

                $('#view_machine_body').empty();
                $('#view_employee_body').empty();
                $('#view_job_title').empty();
                $('#view_job_description').val(data.result.job_description);
                $('#view_remarks').val(data.result.remarks);
                $('#view_tableorderlist').empty();

                if (data.details && data.details.length) {
                    $.each(data.details, function(i, d) {
                        if (d.machine_id) $('#view_machine_body').append(new Option(d.machine_name, d.machine_id, true, true));
                        if (d.emp_id) $('#view_employee_body').append(new Option(d.emp_name, d.emp_id, true, true));
                        if (d.job_title) $('#view_job_title').append(new Option(d.job_title_name, d.job_title, true, true));

                        $('#view_tableorderlist').append(
                            '<tr>' +
                            '<td>' + (d.machine_name || '') + '</td>' +
                            '<td>' + (d.emp_name || '') + '</td>' +
                            '<td>' + (d.job_title_name || '') + '</td>' +
                            '</tr>'
                        );
                    });
                }
                $('#viewconfirmModal').modal('show');
            }
        })

    });

    function productDelete(row) {
        $(row).closest('tr').remove();
    }

    function editTableDelete(row) {
        $(row).closest('tr').remove();
    }
</script>

@endsection