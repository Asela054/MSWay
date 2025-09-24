@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header page-header-light bg-white shadow">
            <div class="container-fluid">
                @include('layouts.employee_nav_bar')
            </div>
            <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-users-gear"></i></div>
                    <span>Exam Subjects</span>
                </h1>
            </div>
        </div>
        </div>
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Subject</button>
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                        <div class="col-12">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" id="examTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="ol-tab" data-toggle="tab" href="#ol-subjects" role="tab" aria-controls="ol-subjects" aria-selected="true">O/L Subjects</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="al-tab" data-toggle="tab" href="#al-subjects" role="tab" aria-controls="al-subjects" aria-selected="false">A/L Subjects</a>
                                </li>
                            </ul>
                            
                            <!-- Tab content -->
                            <div class="tab-content" id="examTabsContent">
                                <div class="tab-pane fade show active" id="ol-subjects" role="tabpanel" aria-labelledby="ol-tab">
                                    <div class="mt-3">
                                        <div class="center-block fix-width scroll-inner">
                                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%" id="olDataTable">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>SUBJECT</th>
                                                    <th class="text-right">ACTION</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="al-subjects" role="tabpanel" aria-labelledby="al-tab">
                                    <div class="mt-3">
                                        <div class="center-block fix-width scroll-inner">
                                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%" id="alDataTable">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>SUBJECT</th>
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
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Area Start -->
         <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Subject</h5>
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
                                        <label class="small font-weight-bold text-dark">Exam Type</label>
                                        <select name="examtype" id="examtype" class="form-control form-control-sm" required>
                                            <option value="">Select Exam Type</option>
                                            <option value="O/L">O/L</option>
                                            <option value="A/L">A/L</option>
                                        </select>
                                    </div>
                                    <div class="form-row mb-1">
                                        <label class="small font-weight-bold text-dark">Subject*</label>
                                        <input type="text" name="subject" id="subject" class="form-control form-control-sm" required/>
                                    </div>
                                    <div class="form-group mt-3">
                                        <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                    </div>
                                    <input type="hidden" name="action" id="action" value="Add" />
                                    <input type="hidden" name="hidden_id" id="hidden_id" />
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
        $(document).ready(function(){

            $('#employee_menu_link').addClass('active');
            $('#employee_menu_link_icon').addClass('active');
            $('#employeemaster').addClass('navbtnactive');
            
            // Initialize O/L DataTable
            $('#olDataTable').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'O/L Subjects Details',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'O/L Subjects Details', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'portrait', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'O/L Subjects Details',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-print mr-2"></i> Print',
                        customize: function(win) {
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        },
                    },
                    // 'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                "order": [
                    [0, "desc"]
                ],
                ajax: {
                    "url": "{!! route('examsubjectlist') !!}",
                    "data": function(d) {
                        d.exam_type = 'O/L';
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'subject', name: 'subject' },
                    {data: 'action', name: 'action', orderable: false, searchable: false,
                    render: function (data, type, row) {
                        return '<div style="text-align: right;">' + data + '</div>';
                    }},
                ],
                "bDestroy": true,
                "order": [
                    [0, "desc"]
                ]
            });

            // Initialize A/L DataTable
            $('#alDataTable').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'A/L Subjects Details',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'A/L Subjects Details', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'portrait', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'A/L Subjects Details',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-print mr-2"></i> Print',
                        customize: function(win) {
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        },
                    },
                    // 'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                "order": [
                    [0, "desc"]
                ],
                ajax: {
                    "url": "{!! route('examsubjectlist') !!}",
                    "data": function(d) {
                        d.exam_type = 'A/L';
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'subject', name: 'subject' },
                    {data: 'action', name: 'action', orderable: false, searchable: false,
                    render: function (data, type, row) {
                        return '<div style="text-align: right;">' + data + '</div>';
                    }},
                ],
                "bDestroy": true,
                "order": [
                    [0, "desc"]
                ]
            });

            $('#create_record').click(function(){
                $('.modal-title').text('Add New Subject');
                $('#action_button').html('Add');
                $('#action').val('Add');
                $('#form_result').html('');
                $('#formTitle')[0].reset();
                $('#formModal').modal('show');
            });

            $('#formTitle').on('submit', function(event){
                event.preventDefault();
                var action_url = '';

                if ($('#action').val() == 'Add') {
                    action_url = "{{ route('examsubjectinsert') }}";
                }
                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('examsubjectupdate') }}";
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
                            $('#formModal').modal('hide'); 
                            actionreload(actionJSON);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: '',
                            message: 'Something went wrong!',
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        action(actionJSON);
                    }
                });
            });

            // edit function
            $(document).on('click', '.edit', async function () {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
                        var id = $(this).attr('id');
                        $('#form_result').html('');
                        $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            })

                        $.ajax({
                            url: '{!! route("examsubjectedit") !!}',
                                type: 'POST',
                                dataType: "json",
                                data: {id: id },
                            success: function (data) {
                                $('#examtype').val(data.result.exam_type);
                                $('#subject').val(data.result.subject);
                                $('#hidden_id').val(id);
                                $('.modal-title').text('Edit Subject');
                                $('#action_button').html('Edit');
                                $('#action').val('Edit');
                                $('#formModal').modal('show');
                            },
                        error: function(xhr, status, error) {
                            console.log('Error:', error);
                        }
                    })
                }
            });

            // delete model function
            var user_id;

            $(document).on('click', '.delete', async function () {
                var r = await Otherconfirmation("You want to remove this ? ");
                if (r == true) {
                    user_id = $(this).attr('id');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: '{!! route("examsubjectdelete") !!}',
                        type: 'POST',
                        data: {id: user_id},
                        dataType: 'json',
                        beforeSend: function () {
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
                        },
                        error: function(xhr, status, error) {
                            console.log('Error:', error);
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: 'Something went wrong!',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                    });
                }
            });
        });

    </script>

@endsection