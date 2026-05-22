@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.employee_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-users-gear"></i></div>
                    <span>Training Points</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row align-items-center mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-2 mb-sm-0" id="selected_employee_name">Employee Name: </h5>
                                <h5 class="mb-2 mb-sm-0" id="allocation_id_label">Training Name: </h5>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                    data-toggle="offcanvas" data-target="#offcanvasRight"
                                    aria-controls="offcanvasRight">
                                    <i class="fas fa-filter mr-1"></i> Filter Records
                                </button>
                            </div>
                            <div class="col-12">
                                <hr class="border-dark">
                            </div>
                        </div>
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap w-100"
                                id="attendreporttable">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>SESSION NAME</th>
                                        <th class="text-right">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{ csrf_field() }}
            </div>
        </div>

        {{-- offcanvas menu --}}
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h2 class="offcanvas-title font-weight-bolder" id="offcanvasRightLabel">Records Filter Options</h2>
                <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                    <span aria-hidden="true" class="h1 font-weight-bolder">&times;</span>
                </button>
            </div>
            <div class="offcanvas-body">
                <ul class="list-unstyled">
                    <form class="form-horizontal" id="formFilter">
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Allocation <span class="text-red">*</span></label>
                                <select name="allocation_id" id="allocation_id" class="form-control form-control-sm" required>
                                    <option value="">Select Allocation...</option>
                                </select>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Employee <span class="text-red">*</span></label>
                                <select name="employee" id="employee" class="form-control form-control-sm" required>
                                    <option value="">Select Employee...</option>
                                </select>
                            </div>
                        </li>
                        <li>
                            <div class="col-md-12 d-flex justify-content-between">
                                <button type="button" class="btn btn-danger btn-sm px-3" id="btn-reset">
                                    <i class="fas fa-redo mr-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm px-3" id="btn-filter">
                                    <i class="fas fa-search mr-2"></i>Search
                                </button>
                            </div>
                        </li>
                    </form>
                </ul>
            </div>
        </div>

        <!-- Types Modal -->
        <div class="modal fade" id="typesModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title">Training Points</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="types_session_id" />
                        <input type="hidden" id="types_allocation_id" />
                        <input type="hidden" id="types_emp_id" />
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="typesTable">
                                <thead>
                                    <tr>
                                        <th>TRAINING TYPE</th>
                                        <th class="text-center">DONE</th>
                                        <th>POINTS</th>
                                    </tr>
                                </thead>
                                <tbody id="types_tbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="button" class="btn btn-primary btn-sm" id="saveTypes">
                            <i class="fas fa-save mr-1"></i>Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection

@section('script')

<script>
    $(document).ready(function() {

        $('#employee_menu_link').addClass('active');
        $('#employee_menu_link_icon').addClass('active');
        $('#training').addClass('navbtnactive');

        // Load allocations into filter dropdown
        $.get('{{ url("get_allocations_list") }}', function(data) {
            var allocationSel = $('#allocation_id');
            allocationSel.empty().append('<option value="">Select Allocation...</option>');
            $.each(data, function(i, item) {
                allocationSel.append('<option value="' + item.id + '">' + item.training_name + '</option>');
            });
        });

        // Allocation wise load employees for that allocation
        $(document).on('change', '#allocation_id', function() {
            var allocationId = $(this).val();
            var employeeSel = $('#employee');
            employeeSel.empty().append('<option value="">Select Employee...</option>');
            if (!allocationId) return;
            $.get('{{ url("get_employees_by_allocation") }}', {
                allocation_id: allocationId
            }, function(data) {
                $.each(data.results, function(i, item) {
                    employeeSel.append('<option value="' + item.id + '">' + item.text + '</option>');
                });
            });
        });

        load_dt('', '', true);

        function load_dt(employee, allocation_id, isInitialLoad) {
            $('#attendreporttable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: false,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Defect Training Points',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm',
                        title: 'Defect Training Points',
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'landscape',
                        pageSize: 'legal'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-primary btn-sm',
                        title: 'Defect Training Points',
                        text: '<i class="fas fa-print mr-2"></i> Print'
                    }
                ],
                ajax: {
                    url: '{{ url("/train_defect_point_list") }}',
                    data: {
                        employee: employee,
                        allocation_id: allocation_id
                    }
                },
                language: {
                    emptyTable: isInitialLoad ?
                        "<div class='text-center py-4'><h5 class='text-muted'>No records to display</h5><p class='text-muted small'>Please use the filter options to search for records</p></div>" : "No data available in table"
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'session_name'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-right'
                    }
                ],
                order: [
                    [0, 'asc']
                ],
            });
        }

        // Reset filter
        $('#btn-reset').on('click', function() {
            $('#formFilter')[0].reset();
            $('#employee').empty().append('<option value="">Select Employee...</option>');
            $('#selected_employee_name').text('Employee Name: ');
            $('#allocation_id_label').text('Allocation Name: ');
            load_dt('', '', true);
        });

        // Submit filter
        $('#formFilter').on('submit', function(e) {
            e.preventDefault();
            var employeeText = $('#employee option:selected').text();
            var allocationText = $('#allocation_id option:selected').text();
            $('#selected_employee_name').text('Employee: ' + employeeText);
            $('#allocation_id_label').text('Allocation: ' + allocationText);
            load_dt($('#employee').val(), $('#allocation_id').val(), false);
            $('#offcanvasRight .btn-close').trigger('click');
        });

        // Open types modal
        $(document).on('click', '.open-types-modal', function() {
            var session_id = $(this).data('session');
            var allocation_id = $(this).data('allocation');
            var emp_id = $(this).data('employee');

            $('#types_session_id').val(session_id);
            $('#types_allocation_id').val(allocation_id);
            $('#types_emp_id').val(emp_id);
            $('#types_tbody').html('<tr><td colspan="3" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
            $('#typesModal').modal('show');

            $.get('{{ url("get_session_types") }}', {
                session_id: session_id,
                allocation_id: allocation_id,
                emp_id: emp_id
            }, function(data) {
                var rows = '';
                $.each(data.types, function(i, type) {
                    var checked = type.is_attend == 1 ? 'checked' : '';
                    var points = type.points ?? '';
                    var isDisabled = type.is_attend == 1 ? '' : 'disabled';
                    rows += '<tr>' +
                        '<td>' + type.type_name + '</td>' +
                        '<td class="text-center">' +
                        '<input type="checkbox" class="done-checkbox" ' + checked + ' ' +
                        'data-type="' + type.type_id + '" ' +
                        'data-defect-id="' + (type.defect_point_id ?? '') + '">' +
                        '</td>' +
                        '<td>' +
                        '<input type="number" step="0.01" class="form-control form-control-sm points-input" ' +
                        'style="width:100px" value="' + points + '" ' +
                        'data-type="' + type.type_id + '" ' + isDisabled + '>' +
                        '</td>' +
                        '</tr>';
                });
                $('#types_tbody').html(rows || '<tr><td colspan="3" class="text-center text-muted">No types found</td></tr>');
            });
        });

        // Toggle points input when done checkbox changes
        $(document).on('change', '.done-checkbox', function() {
            var pointsInput = $(this).closest('tr').find('.points-input');
            if ($(this).is(':checked')) {
                pointsInput.prop('disabled', false).focus();
            } else {
                pointsInput.val('').prop('disabled', true);
            }
        });

        // Save types modal
        $('#saveTypes').on('click', async function() {
            var confirmed = await Otherconfirmation("Save points for this session?");
            if (!confirmed) return;

            var session_id = $('#types_session_id').val();
            var allocation_id = $('#types_allocation_id').val();
            var emp_id = $('#types_emp_id').val();
            var allPoints = [];

            $('#types_tbody tr').each(function() {
                var typeId = $(this).find('.points-input').data('type');
                var is_attend = $(this).find('.done-checkbox').is(':checked') ? 1 : 0;
                var points = is_attend ? $(this).find('.points-input').val() : 0;
                allPoints.push({
                    session_id: session_id,
                    allocation_id: allocation_id,
                    emp_id: emp_id,
                    type_id: typeId,
                    points: points,
                    is_attend: is_attend
                });
            });

            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: '{{ route("train_defect_point_mark") }}',
                method: 'POST',
                data: {
                    _token: $('input[name=_token]').val(),
                    allPoints: allPoints
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        $('#typesModal').modal('hide');
                        const actionObj = {
                            icon: 'fas fa-save',
                            title: '',
                            message: data.success,
                            url: '',
                            target: '_blank',
                            type: 'success'
                        };
                        actionreload(JSON.stringify(actionObj));
                    }
                    btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Save');
                }
            });
        });
    });
</script>

@endsection