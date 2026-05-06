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
                                        <th>READING HOURS</th>
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
                            <label class="small font-weight-bolder text-dark">Employee</label>
                            <select name="employee" id="employee_f" class="form-control form-control-sm"></select>
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
</main>

@endsection


@section('script')

<script>
    $(document).ready(function() {

        $('#inquiry_approve_menu_link').addClass('active');
        $('#inquiry_approve_menu_link_icon').addClass('active');
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


        // Employee Select2 Initialization
        employee.select2({
            placeholder: 'Select employee...',
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

        function load_dt(customer_name, inquiry, from_date, to_date, machine_name, employee, job_title) {

            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().destroy();
                $('#dataTable tbody').empty();
            }
            $('#dataTable').DataTable({
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
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'inquiry',
                        name: 'inquiry'
                    },
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

            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().destroy();
            }
            $('#dataTable tbody').empty();
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

            $.ajax({
                url: '{!! route("jobapproveinquiry") !!}',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}',
                    dataarray: selectedRowIdsapprove,
                },
                success: function(data) {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Approved!',
                            text: data.success,
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(function() {
                            load_dt($('#customer_f').val(), $('#inquiry_f').val(), $('#from_date').val(), $('#to_date').val(), $('#machine_f').val(), $('#employee_f').val(), $('#job_title_f').val());
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Something went wrong.',
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed!',
                        text: 'Request failed. Please try again.',
                    });
                }
            });
        });

        $('#selectAll').click(function(e) {
            $('#dataTable').closest('table').find('td input:checkbox:not(:disabled)').prop('checked', this.checked);
        });


    });
</script>


@endsection