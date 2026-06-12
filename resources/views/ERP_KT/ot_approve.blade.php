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
                    <span>OT Approve</span>
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
                        <div class="col-12">
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%"
                                    id="dataTable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>EMPLOYEE NAME</th>
                                            <th>DATE</th>
                                            <th>IN TIME</th>
                                            <th>OUT TIME</th>
                                            <th>OT HOURS</th>
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
                                <label class="small font-weight-bolder text-dark">Employee Name <span class="text-danger">*</span></label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm"></select>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark"> From Date <span class="text-danger">*</span></label>
                                <input type="date" id="from_date" name="from_date"
                                    class="form-control form-control-sm" placeholder="yyyy-mm-dd"
                                    value="{{date('Y-m-d') }}" required>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark"> To Date <span class="text-danger">*</span></label>
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
        <div class="modal fade" id="approveconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Approve OT Data </h5>
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

        $('#erp_menu_link_KT').addClass('active');
        $('#erp_menu_link_KT_icon').addClass('active');
        $('#erp_kt_shiftot').addClass('navbtnactive');

        // Employee Select2 Initialization
        let employee = $('#employee_f');
        employee.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#offcanvasRight'),
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

        function load_dt(employee, from_date, to_date) {
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().destroy();
            }
            $('#dataTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{ route("kt_ot_approve_generate") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        employee: employee,
                        from_date: from_date,
                        to_date: to_date
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row) {
                            if (row.approve_status == 1) {
                                return '<input type="checkbox" class="selectCheck" checked disabled>';
                            }
                            return '<input type="checkbox" class="selectCheck" data-id="' + row.id + '" data-emp="' + row.emp_auto_id + '" data-ot="' + row.ot_hours + '">';
                        }
                    },
                    {
                        data: 'employee'
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'in_time'
                    },
                    {
                        data: 'out_time'
                    },
                    {
                        data: 'ot_hours'
                    },
                ]
            });
        }

        //filter handler
        $('#formFilter').on('submit', function(e) {
            e.preventDefault();
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

            load_dt(employee, from_date, to_date);
            closeOffcanvasSmoothly();
        });

        $('#btn-reset').click(function() {
            $('#formFilter')[0].reset();
            $('#employee_f').val(null).trigger('change');
            load_dt('', '', '');
        });

        //
        var selectedRowIdsapprove = [];

        $('#approve_att').click(function() {
            selectedRowIdsapprove = [];
            $('#dataTable tbody .selectCheck:checked:not(:disabled)').each(function() {
                let ot_hours = $(this).data('ot');

                selectedRowIdsapprove.push({
                    cusid: $(this).data('id'),
                    emp_auto_id: $(this).data('emp'),
                    ot_hours: ot_hours,
                });
            });


            if (selectedRowIdsapprove.length > 0) {
                $('#approveconfirmModal').modal('show');
            } else {
                alert('Please select at least one record to approve!');
            }
        });

        $('#approve_button').off('click').on('click', function(e) {
            e.preventDefault();
            $(this).blur();
            $('#approveconfirmModal').modal('hide');
            $('.message_modal').html('');
            
            var employee = $('#employee_f').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{!! route("kt_ot_approve_submit") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    dataarray: selectedRowIdsapprove
                },
                success: function(data) {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Approved!',
                            text: 'OT Successfully Approved.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            location.reload();
                        });
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