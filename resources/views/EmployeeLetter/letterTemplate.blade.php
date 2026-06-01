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
                    <span>Letter Templates</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>TEMPLATE NAME</th>
                                        <th>LETTER TYPE</th>
                                        <th>STATUS</th>
                                        <th class="text-right">ACTION</th>
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

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Letter Template</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="row">

                                    {{-- Left: form fields --}}
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="small font-weight-bold text-dark">
                                                        Template Name
                                                    </label>
                                                    <input type="text" name="name" id="name"
                                                        class="form-control form-control-sm"
                                                        placeholder="e.g. Letter Name" />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="small font-weight-bold text-dark">
                                                        Letter Type
                                                    </label>
                                                    <select name="letter_type_id" id="letter_type_id"
                                                        class="form-control form-control-sm">
                                                        <option value="">Select Letter Type</option>
                                                        @foreach($letter_types as $lt)
                                                        <option value="{{ $lt->id }}">
                                                            {{ $lt->letter_type }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Left: TinyMCE editor --}}
                                        <div class="form-group mb-2">
                                            <label class="small font-weight-bold text-dark">
                                                Letter Content
                                            </label>
                                            <textarea name="content" id="letterContent"
                                                class="form-control" rows="16"></textarea>
                                        </div>
                                    </div>

                                    {{-- Right: placeholder panel --}}
                                    <div class="col-md-3">
                                        <label class="small font-weight-bold text-dark d-block mb-1">
                                            Available Placeholders
                                        </label>
                                        <div class="border rounded p-2 bg-light"
                                            style="max-height:420px; overflow-y:auto;">
                                            <p class="text-muted mb-2" style="font-size:11px;">
                                                Click to insert placeholders to your letter.
                                            </p>
                                            @foreach($placeholders as $ph => $label)
                                            <div class="mb-1">
                                                <button type="button"
                                                    class="btn btn-outline-primary btn-sm btn-block text-left insert-placeholder"
                                                    style="font-size:11px;"
                                                    data-placeholder="{{ $ph }}">
                                                    {{ $label }}
                                                </button>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" value="" />

                                <div class="form-group mt-3 mb-0 text-right">
                                     <button type="button" id="check_button" class="btn btn-sm px-4 status mr-1" style="display:none;">
                                        <i class="fas fa-check-circle mr-1" id="status-icon"></i><span id="status-text">Activate Template</span>
                                    </button>
                                    <button type="submit" id="action_button" class="btn btn-primary btn-sm px-4">
                                        <i class="fas fa-save mr-1"></i>Save Template
                                    </button>
                                </div>
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

{{-- TinyMCE CDN --}}
<!-- <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.4/tinymce.min.js"></script> -->

<script src="https://cdn.tiny.cloud/1/z64mwm01zqxthsm5lyhooo4ldelf75bcgg0b0el1e38cp1fp/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>


<script>
    $(document).ready(function() {

        $('#employee_menu_link').addClass('active');
        $('#employee_menu_link_icon').addClass('active');
        $('#employeeletter').addClass('navbtnactive');

        // TinyMCE initialization
        tinymce.init({
            selector: '#letterContent',
            height: 420,
            menubar: false,
            plugins: [
                'anchor', 'autolink', 'charmap', 'codesample', 'link',
                'lists', 'media', 'searchreplace', 'table',
                'visualblocks', 'wordcount'
            ],
            toolbar: 'undo redo | bold italic underline | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist | table | removeformat',
            content_style: 'body { font-family: Times New Roman, serif; font-size: 13px; }',
            setup: function(editor) {
                window.letterEditor = editor;
            }
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
                    title: 'Letter Templates',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    title: 'Letter Templates',
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'portrait',
                    pageSize: 'legal',
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Letter Templates',
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
                url: scripturl + "/EmployeeLetter/letterTemplate_list.php",
                type: "POST",
                data: {},
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'letter_type',
                    name: 'letter_type'
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    render: function(data) {
                        return data == 1 ?
                            '<span class="badge badge-success">Active</span>' :
                            '<span class="badge badge-secondary">Inactive</span>';
                    }
                },
                {
                    data: 'id',
                    name: 'action',
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var buttons = '';
                        buttons += '<button name="edit" id="' + row.id + '" class="edit btn btn-primary btn-sm mr-1" type="submit" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                        buttons += '<button name="delete" id="' + row.id + '" class="delete btn btn-danger btn-sm" type="submit" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';
                        return buttons;
                    }
                }
            ],
        });

        $('#create_record').click(function() {
            $('.modal-title').text('Add Letter Template');
            $('#action').val('Add');
            $('#form_result').html('');
            $('#formTitle')[0].reset();
            $('#formModal').modal('show');
            $('#hidden_id').val('');
            $('#letter_type_id').val('');
            $('#check_button').hide();
            tinymce.get('letterContent').setContent('');
        });

        // Insert placeholder at TinyMCE cursor
        $(document).on('click', '.insert-placeholder', function() {
            var ph = $(this).data('placeholder');
            if (window.letterEditor) {
                window.letterEditor.insertContent(ph);
            }
        });

        $('#formTitle').on('submit', function(e) {
            e.preventDefault();

            // Save TinyMCE content (disabled)
            tinymce.triggerSave();

            var action_url = $('#action').val() === 'Add' ?
                "{{ route('letterTemplate.store') }}" :
                "{{ route('letterTemplate.update') }}";


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

        $(document).on('click', '.edit', async function() {
            var r = await Otherconfirmation("You want to Edit this ? ");
            if (!r) return;

            var id = $(this).attr('id');

            $.ajax({
                url: "letterTemplate/" + id + "/edit",
                dataType: "json",
                success: function(data) {
                    var res = data.result;
                    $('#name').val(res.name);
                    $('#letter_type_id').val(res.letter_type_id);
                    $('#hidden_id').val(res.id);
                    $('#action').val('Edit');
                    $('#form_result').html('');
                    $('#modalTitle').text('Edit Letter Template');
                    $('#check_button').show();
                    if(res.is_active == 1) {
                        $('#check_button').removeClass('btn-success').addClass('btn-secondary');
                        $('#status-text').text('Deactivate Template');
                        $('#status-icon').removeClass('fa-check-circle').addClass('fa-times-circle');
                    } else {
                        $('#check_button').removeClass('btn-secondary').addClass('btn-success');
                        $('#status-text').text('Activate Template');
                        $('#status-icon').removeClass('fa-times-circle').addClass('fa-check-circle');
                    }

                    // Set TinyMCE content
                    $('#formModal').modal('show');
                    setTimeout(function() {
                        tinymce.get('letterContent').setContent(res.content || '');
                    }, 300);
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                }
            })

        });


        $(document).on('click', '.delete', async function() {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (!r) return;

            var id = $(this).attr('id');

            $.ajax({
                url: "{{ url('letterTemplate/destroy') }}/" + id,
                dataType: 'json',
                success: function(data) {
                    const actionObj = {
                        icon: 'fas fa-trash-alt',
                        title: '',
                        message: 'Letter Template Removed Successfully',
                        url: '',
                        target: '_blank',
                        type: 'danger'
                    };
                    const actionJSON = JSON.stringify(actionObj, null, 2);
                    actionreload(actionJSON);
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                }
            });
        });

        // status button
        $(document).on('click', '.status', async function() {
            var r = await Otherconfirmation("You want to change the status of this template? ");
            if (!r) return;

            var id = $('#hidden_id').val();

            $.ajax({
                url: "{{ url('letterTemplate/status') }}/" + id,
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        const actionObj = {
                            icon: 'fas fa-check',
                            title: '',
                            message: data.success,
                            url: '',
                            target: '_blank',
                            type: 'success'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        actionreload(actionJSON);
                    } else if (data.error) {
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: '',
                            message: data.error,
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        action(actionJSON);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                }
            });
        });

    });
</script>

@endsection