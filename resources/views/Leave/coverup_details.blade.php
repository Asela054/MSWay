@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
             <div class="container-fluid d-none d-sm-block shadow">
                 @include('layouts.attendant&leave_nav_bar')
             </div>
             <div class="container-fluid">
                 <div class="page-header-content py-3 px-2">
                     <h1 class="page-header-title ">
                         <div class="page-header-icon"><i class="fa-light fa-calendar-pen"></i></div>
                         <span>CoverUp Details</span>
                     </h1>
                 </div>
             </div>
         </div>
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary btn-sm fa-pull-right"
                                    name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Covering Details
                            </button>
                        </div>
                        <div class="col-md-12">
                            <hr class="border-dark">
                        </div>
                        <div class="col-md-12">
                             <div class="col-md-12">
                                    <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                        data-toggle="offcanvas" data-target="#offcanvasRight"
                                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                        Options</button>
                                </div><br><br>

                            <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="divicestable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>EMPLOYEE</th>
                                    <th>DEPARTMENT</th>
                                    <th>DATE</th>
                                    <th>START TIME</th>
                                    <th>END TIME</th>
                                    <th>COVERING HOURS</th>
                                    <th class="text-right">ACTION</th>
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
             @include('layouts.filter_menu_offcanves') 
        </div>


        <!-- Modal Area Start -->
        <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Coverup Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <span id="form_result"></span>
                                <form method="post" id="formTitle" class="form-horizontal">
                                    {{ csrf_field() }}
                                    <div class="form-row mb-1">
                                        <div class="col-sm-12 col-md-12">
                                            <label class="small font-weight-bolder text-dark">Covering Employee</label>
                                            <select name="coveringemployee" id="coveringemployee"
                                                    class="form-control form-control-sm" required>
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col-sm-12 col-md-12">
                                            <label class="small font-weight-bolder text-dark">Date</label>
                                            <input type="date" name="date" id="date"
                                                   class="form-control form-control-sm" required/>
                                        </div>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col-sm-12 col-md-6">
                                            <label class="small font-weight-bolder text-dark">Start Time</label>
                                            <input type="time" name="start_time" id="start_time"
                                                   class="form-control form-control-sm" placeholder="hh:mm:ss" required/>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <label class="small font-weight-bolder text-dark">End Time</label>
                                            <input type="time" name="end_time" id="end_time"
                                                   class="form-control form-control-sm" placeholder="hh:mm:ss" required/>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <input type="submit" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4" value="Add"/>
                                    </div>
                                    <input type="hidden" name="action" id="action" value="Add"/>
                                    <input type="hidden" name="hidden_id" id="hidden_id"/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Area End -->
    </main>

@endsection


@section('script')

<script>
    $(document).ready(function () {

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#leavemaster').addClass('navbtnactive');

            let company_f = $('#company');
            let department_f = $('#department');
            let employee_f = $('#employee');
            let location_f = $('#location');

            company_f.select2({
                placeholder: 'Select...',
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
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("department_list_sel2")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company_f.val()
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

            location_f.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("location_list_sel2")}}',
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

            let c_employee = $('#coveringemployee');
            c_employee.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                parent: '#formModal',
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

            function load_dt(department, employee, location){
                $('#divicestable').DataTable({
                   "destroy": true,
                        "processing": true,
                        "serverSide": true,
                        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                        "buttons": [{
                                extend: 'csv',
                                className: 'btn btn-success btn-sm',
                                title: 'Cover UP Details',
                                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                            },
                            { 
                                extend: 'pdf', 
                                className: 'btn btn-danger btn-sm', 
                                title: 'Cover UP Details', 
                                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                orientation: 'landscape', 
                                pageSize: 'legal', 
                                customize: function(doc) {
                                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Cover UP Details',
                                className: 'btn btn-primary btn-sm',
                                text: '<i class="fas fa-print mr-2"></i> Print',
                                customize: function(win) {
                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');
                                },
                            },
                        ],
                    ajax: {
                        "url": "{!! route('coverup_list_dt') !!}",
                        "data": {'department':department, 'employee':employee, 'location': location},
                    },
                    columns: [
                        { data: 'emp_id', name: 'emp_id' },
                        { data: 'employee_display', name: 'employee_display' },
                        { data: 'dep_name', name: 'emp_name' },
                        { data: 'date', name: 'date' },
                        { data: 'start_time', name: 'start_time' },
                        { data: 'end_time', name: 'end_time' },
                        { data: 'covering_hours', name: 'covering_hours' },
                        { data: 'action', name: 'action', className: 'text-right', orderable: false, searchable: false},
                    ],
                    "bDestroy": true,
                    "order": [
                        [3, "desc"]
                    ]
                });
            }

            load_dt('', '', '');


            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();

                load_dt(department, employee, location);
                 closeOffcanvasSmoothly();
            });

        });    

            $(document).ready(function () {

            $('#create_record').click(function () {
                $('.modal-title').text('Covering Details');
                $('#action_button').val('Add');
                $('#action').val('Add');
                $('#form_result').html('');

                //form reset                
                $('#formTitle')[0].reset();
                $('#coveringemployee').val('').trigger('change');
                $('#date').val('');
                $('#start_time').val('');
                $('#end_time').val('');
                
                $('#formModal').modal('show');
            });

            $('#formTitle').on('submit', function (event) {
                event.preventDefault();
                var action_url = '';


                if ($('#action').val() == 'Add') {
                    action_url = "{{ route('addCoverup') }}";
                }

                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('Coverup.update') }}";
                }

                $.ajax({
                    url: action_url,
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (data) {

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
                            $('#formTitle')[0].reset();
                            actionreload(actionJSON);
                        }
                    }
                });
            });

            $(document).on('click', '.edit',async function () {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
                    var id = $(this).attr('id');
                    $('#form_result').html('');
                    $.ajax({
                        url: "Coverup/" + id + "/edit",
                        dataType: "json",
                        success: function (data) {
                            let coveringemployeeOption = $("<option selected></option>").val(data.result.emp_id).text(data.result.covering_employee.emp_name_with_initial);
                            $('#coveringemployee').append(coveringemployeeOption).trigger('change');
                            $('#coveringemployee').val(data.result.emp_id);
                            $('#date').val(data.result.date);
                            $('#start_time').val(data.result.start_time);
                            $('#end_time').val(data.result.end_time);
                            $('#hidden_id').val(id);
                            $('.modal-title').text('Edit Covering Details');
                            $('#action_button').val('Edit');
                            $('#action').val('Edit');
                            $('#formModal').modal('show');
                        }
                    })
                }
            });

            var user_id;

            $(document).on('click', '.delete',async function () {
                user_id = $(this).attr('id');
                var r = await Otherconfirmation("You want to remove this ? ");
                if (r == true) {
                    $.ajax({
                        url: "Coverup/destroy/" + user_id,
                        beforeSend: function () {
                            $('#ok_button').text('Deleting...');
                        },
                        success: function (data) {
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