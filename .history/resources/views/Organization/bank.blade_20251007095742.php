@extends('layouts.app')

@section('content')

    <main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.corporate_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-building"></i></div>
                    <span>Bank</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                            @can('bank-create')
                                <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Bank</button>
                            @endcan
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                        <div class="col-12">
                            <div class="center-block fix-width scroll-inner">
                            @can('bank-list')
                                <table class="table table-striped table-bordered table-sm small nowrap text-uppercase" style="width: 100%" id="dataTable">
                                    <thead>
                                    <tr>
                                        <th>ID </th>
                                        <th>Code</th>
                                        <th>Bank</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                    </thead>
                                </table>
                            @endcan
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
                        <h5 class="modal-title" id="staticBackdropLabel">Add Bank</h5>
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
                                        <div class="col-9">
                                            <label class="small font-weight-bold text-dark">Name*</label>
                                            <input type="text" name="name" id="name" class="form-control form-control-sm" />
                                        </div>
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Code*</label>
                                            <input type="text" name="code" id="code" class="form-control form-control-sm" />
                                        </div>
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
        <div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col text-center">
                                <h4 class="font-weight-normal">Are you sure you want to remove this data?</h4>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="button" name="ok_button" id="ok_button" class="btn btn-danger px-3 btn-sm">OK</button>
                        <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
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

            $('#organization_menu_link').addClass('active');
            $('#organization_menu_link_icon').addClass('active');
            $('#banklink').addClass('navbtnactive');

            $('#dataTable').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Bank  Information',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm',
                        title: 'Bank Information',
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'landscape',
                        pageSize: 'legal',
                        customize: function (doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Bank  Information',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-print mr-2"></i> Print',
                        customize: function (win) {
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
                    url: scripturl + "/banklist.php",
                    type: "POST",
                    data: {},
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'bank',
                        name: 'bank'
                    },
                    {
                        data: 'id',
                        name: 'action',
                        className: 'text-right',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            var buttons = '';
                            var url = "{{ route('bank_branch_show', ':code') }}";
                            url = url.replace(':code', row.code);

                            buttons += '<a title="View Branches" href="' + url + '" class="branches btn btn-info btn-sm mr-1"><i class="fas fa-building"></i></a>';
                            buttons += '<button name="edit" id="' + row.id + '" class="edit btn btn-primary btn-sm mr-1" type="button" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                            buttons += '<button type="submit" name="delete" id="' + row.id + '" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';

                            return buttons;
                        }
                    }
                ],
                drawCallback: function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('#create_record').click(function(){
                $('.modal-title').text('Add New Bank');
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
                    action_url = "{{ route('addBank') }}";
                }
                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('Bank.update') }}";
                }

                $.ajax({
                    url: action_url,
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (data) {//alert(data);        
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

            $(document).on('click', '.edit', async function () {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
                    var id = $(this).attr('id');
                    $('#form_result').html('');
                    $.ajax({
                        url: "Bank/" + id + "/edit",
                        dataType: "json",
                        success: function (data) {
                            $('#name').val(data.result.bank);
                            $('#code').val(data.result.code);
                            $('#hidden_id').val(id);
                            $('.modal-title').text('Edit Bank');
                            $('#action_button').html('Edit');
                            $('#action').val('Edit');
                            $('#formModal').modal('show');
                        }
                    })
                }
            });

            var user_id;

    var user_id;

    $(document).on('click', '.delete', async function() {
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            user_id = $(this).attr('id');
            $.ajax({
                url: "Bank/destroy/" + user_id,
                beforeSend: function () {
                    $('#ok_button').text('Deleting...');
                },
                success: function (data) {//alert(data);
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