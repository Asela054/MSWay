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
                    <span>Special Rate</span>
                </h1>
            </div>
        </div>
    </div>


    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Special Rate</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID </th>
                                        <th>JOB TITLE</th>
                                        <th>EMPLOYEE</th>
                                        <th>RATE</th>
                                        <th>REMARKS</th>
                                        <th class="text-right">ACTION</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Special Rate</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <form method="post" id="formTitle" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Job Title <span class="text-red">*</span></label>
                                    <select name="job_title" id="job_title_f" class="form-control form-control-sm" required></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Employee</label>
                                    <select name="employee" id="employee_f" class="form-control form-control-sm"></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Rate</label>
                                    <input type="text" name="rate" id="rate" class="form-control form-control-sm" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Remarks</label>
                                    <input type="text" name="remarks" id="remarks" class="form-control form-control-sm" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mt-3 mb-0">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="action" id="action" value="Add" />
                        <input type="hidden" name="hidden_id" id="hidden_id" />
                    </form>
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
        $('#erp_kt_master').addClass('navbtnactive');

        // Employee Select2 Initialization
        let employee = $('#employee_f');
        let job_title = $('#job_title_f')

        job_title.select2({
            placeholder: 'Select Job Title...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
            ajax: {
                url: '{{ url("kt_job_title_list_sel2") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    };
                },
                cache: true
            }
        });

        employee.select2({
            placeholder: 'Select Employee...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
            ajax: {
                url: '{{ url("kt_employee_list_by_title_sel2") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1,
                        job_title_id: $('#job_title_f').val()
                    };
                },
                cache: false
            }
        });

        job_title.on('change', function() {
            employee.val(null).trigger('change');
        });


        $('#dataTable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Special Rate Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    title: 'Special Rate Information',
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape',
                    pageSize: 'legal',
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Special Rate Information',
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
                url: scripturl + "/ERP_KT/special_rate_list.php",
                type: "POST",
                data: {},
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'job_title',
                    name: 'job_title'
                },
                {
                    data: 'employee',
                    name: 'employee'
                },
                {
                    data: 'rate',
                    name: 'rate'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
                },
                {
                    data: 'id',
                    name: 'action',
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var buttons = '';
                        buttons += '<button name="edit" id="' + row.id + '" class="edit btn btn-primary btn-sm  mr-1" type="submit" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                        buttons += '<button type="submit" name="delete" id="' + row.id + '" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';

                        return buttons;
                    }
                }
            ],
            drawCallback: function(settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        $('#create_record').click(function() {
            $('.modal-title').text('Add Special Rate');
            $('#action_button').val('Add');
            $('#action').val('Add');
            $('#form_result').html('');

            $('#employee_f').empty().val(null).trigger('change');
            $('#job_title_f').empty().val(null).trigger('change');

            $('#formTitle')[0].reset();
            $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');

            $('#formModal').modal('show');
        });


        $('#formTitle').on('submit', function(event) {
            event.preventDefault();
            var action_url = '';


            if ($('#action').val() == 'Add') {
                action_url = "{{ route('kt_addSpecial_Rate') }}";
            }

            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('KTSpecial_Rate.update') }}";
            }


            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {

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
                }
            });
        });

        $(document).on('click', '.edit', async function() {
            var r = await Otherconfirmation("You want to Edit this ? ");
            if (r == true) {
                var id = $(this).attr('id');
                var row = $(this).closest('tr');
                var jobTitleText = row.find('td:eq(1)').text();
                var employeeText = row.find('td:eq(2)').text();

                $('#form_result').html('');
                $.ajax({
                    url: "{{ url('KTSpecial_Rate/') }}/" + id + "/edit",
                    dataType: "json",
                    success: function(data) {
                        var jobTitleOption = new Option(jobTitleText, data.result.job_title, true, true);
                        $('#job_title_f').append(jobTitleOption).trigger('change');

                        if (data.result.emp_id) {
                            var empOption = new Option(employeeText, data.result.emp_id, true, true);
                            $('#employee_f').append(empOption).trigger('change');
                        } else {
                            $('#employee_f').val(null).trigger('change');
                        }

                        $('#rate').val(data.result.rate);
                        $('#remarks').val(data.result.remarks);
                        $('#hidden_id').val(id);
                        $('.modal-title').text('Edit Special Rate');
                        $('#action_button').html('<i class="fas fa-pencil-alt"></i>&nbsp;Edit');
                        $('#action').val('Edit');
                        $('#formModal').modal('show');
                    }
                })
            }
        });

        var user_id;

        $(document).on('click', '.delete', async function() {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (r == true) {
                user_id = $(this).attr('id');
                $.ajax({
                    url: "{{ url('KTSpecial_Rate/destroy/') }}/" + user_id,
                    beforeSend: function() {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function(data) {
                        const actionObj = {
                            icon: 'fas fa-trash-alt',
                            title: '',
                            message: 'Record Remove Successfully',
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        actionreload(actionJSON);
                    }
                })
            }

        });
    });
</script>

@endsection