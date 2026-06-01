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
                    <span>Issue Letter</span>
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
                                        <th>EMPLOYEE</th>
                                        <th>LETTER TYPE</th>
                                        <th>TEMPLATE NAME</th>
                                        <th>ISSUED DATE</th>
                                        <th class="text-right">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($issued_letters as $il)
                                    <tr>
                                        <td>{{ $il->id }}</td>
                                        <td>{{ $il->emp_name_with_initial }}</td>
                                        <td>{{ $il->letter_type }}</td>
                                        <td>{{ $il->template_name }}</td>
                                        <td>{{ $il->issued_date }}</td>
                                        <td class="text-right">
                                            <button id="{{ $il->id }}" class="edit btn btn-primary btn-sm">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <button id="{{ $il->id }}" class="delete btn btn-danger btn-sm">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                            <button id="{{ $il->id }}" class="print btn btn-info btn-sm">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="modalTitle">Add Issue Letter</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <form id="formTitle" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-dark">Letter Type</label>
                                    <select name="letter_type_id" id="letter_type_id"
                                        class="form-control form-control-sm">
                                        <option value="">Select Letter Type</option>
                                        @foreach($letter_types as $lt)
                                        <option value="{{ $lt->id }}">{{ $lt->letter_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-dark">Employee</label>
                                    <select name="employee_id" id="employee_id"
                                        class="form-control form-control-sm select2-employee">
                                        <option value="">Select Employee</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-dark">Issued Date</label>
                                    <input type="date" name="issued_date" id="issued_date"
                                        class="form-control form-control-sm"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>

                            {{-- Template load status --}}
                            <div class="col-12">
                                <div id="template_load_status" class="mb-2 d-none">
                                    <span class="badge badge-info">
                                        <i class="fas fa-spinner fa-spin mr-1"></i>
                                        Loading template...
                                    </span>
                                </div>
                            </div>

                            {{-- TinyMCE --}}
                            <div class="col-12">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-dark">
                                        Letter Content
                                        <small class="text-muted font-weight-normal">
                                            — auto-filled from template, edit if needed
                                        </small>
                                    </label>
                                    <textarea name="content" id="issueContent"
                                        class="form-control" rows="18"></textarea>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="template_id" id="template_id" value="">
                        <input type="hidden" name="action" id="action" value="Add">
                        <input type="hidden" name="hidden_id" id="hidden_id" value="">

                        <div class="form-group mt-2 mb-0 text-right">
                            <button type="submit" id="action_button"
                                class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-save mr-1"></i>Save Issued Letter
                            </button>
                        </div>
                    </form>
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
            selector: '#issueContent',
            height: 420,
            menubar: false,
            plugins: ['lists', 'table', 'link'],
            toolbar: 'undo redo | bold italic underline | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist | table | removeformat',
            content_style: 'body { font-family: Times New Roman, serif; font-size: 13px; }',
            setup: function(editor) {
                window.letterEditor = editor;
            }
        });

        $('.select2-employee').select2({
            placeholder: 'Search employee...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{ url("employee_list_sel2") }}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    };
                },
                cache: true
            },
            dropdownParent: $('#formModal')
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
                    title: 'Issued Letters',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    title: 'Issued Letters',
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'portrait',
                    pageSize: 'legal',
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Issued Letters',
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
                url: scripturl + "/EmployeeLetter/issueLetter_list.php",
                type: "POST",
                data: {},
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'emp_name_with_initial',
                    name: 'emp_name_with_initial'
                },
                {
                    data: 'letter_type',
                    name: 'letter_type'
                },
                {
                    data: 'template_name',
                    name: 'template_name'
                },
                {
                    data: 'issued_date',
                    name: 'issued_date'
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
                        buttons += '<button name="print" id="' + row.id + '" class="print btn btn-info btn-sm mr-1" type="submit" data-toggle="tooltip" title="Print"><i class="fas fa-print"></i></button>';
                        buttons += '<button name="delete" id="' + row.id + '" class="delete btn btn-danger btn-sm" type="submit" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';
                        return buttons;
                    }
                }
            ],
        });

        $('#create_record').click(function() {
            $('#modalTitle').text('Add Issue Letter');
            $('#action').val('Add');
            $('#form_result').html('');
            $('#formTitle')[0].reset();
            $('#hidden_id').val('');
            $('#letter_type_id').val('');
            tinymce.get('issueContent').setContent('');
            $('#template_id').val('');
            $('.select2-employee').val(null).trigger('change');
            $('#issued_date').val('{{ date("Y-m-d") }}');
            $('#letter_type_id').prop('disabled', false);
            $('#employee_id').prop('disabled', false);
            $('#issued_date').prop('readonly', false);
            $('#template_load_status').addClass('d-none');
            $('#formModal').modal('show');

            // Wait for TinyMCE before clearing
            var editor = tinymce.get('issueContent');
            if (editor) {
                editor.setContent('');
            }

            $('#formModal').modal('show');
        });

        $('#formTitle').on('submit', function(e) {
            e.preventDefault();

            tinymce.triggerSave();

            var action_url = $('#action').val() === 'Add' ?
                "{{ route('issueLetter.store') }}" :
                "{{ route('issueLetter.update') }}";


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
                url: "issueLetter/" + id + "/edit",
                dataType: "json",
                success: function(data) {
                    var res = data.result;
                    var empOption = new Option(res.emp_name_with_initial, res.employee_id, true, true);
                    $('#letter_type_id').val(res.letter_type_id).prop('disabled', true);
                    $('#employee_id').append(empOption).trigger('change').prop('disabled', true);
                    $('#issued_date').prop('readonly', true);
                    $('#template_id').val(res.template_id);
                    $('#issued_date').val(res.issued_date);
                    $('#hidden_id').val(res.id);
                    $('#action').val('Edit');
                    $('#form_result').html('');
                    $('#modalTitle').text('Edit Issued Letter');
                    $('#formModal').modal('show');

                    setTimeout(function() {
                        tinymce.get('issueContent').setContent(res.content || '');
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
                url: "{{ url('issueLetter/destroy') }}/" + id,
                dataType: 'json',
                success: function(data) {
                    const actionObj = {
                        icon: 'fas fa-trash-alt',
                        title: '',
                        message: 'Letter Removed Successfully',
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

    });


    // Auto-load template
    function AutoLoadTemplate() {
        var typeId = $('#letter_type_id').val();
        var empId = $('#employee_id').val();

        if (!typeId || !empId || $('#action').val() === 'Edit') return;

        $('#template_load_status').removeClass('d-none');

        $.ajax({
            url: '{{ route("issueLetter.loadTemplate") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                letter_type_id: typeId,
                employee_id: empId,
            },
            dataType: 'json',
            success: function(res) {
                $('#template_load_status').addClass('d-none');
                if (res.success) {
                    tinymce.get('issueContent').setContent(res.content);
                    $('#template_id').val(res.template_id);
                    $('#form_result').html('');
                } else {
                    $('#form_result').html(
                        '<div class="alert alert-warning p-1 small">' + (res.message || 'No template found.') + '</div>'
                    );
                }
            },
            error: function() {
                $('#template_load_status').addClass('d-none');
                $('#form_result').html(
                    '<div class="alert alert-danger p-1 small">Failed to load template.</div>'
                );
            }
        });
    }

    $('#letter_type_id').on('change', AutoLoadTemplate);
    $('#employee_id').on('change', AutoLoadTemplate);

    // Print
    $(document).on('click', '.print', function() {
        var id = $(this).attr('id');
        var newTab = window.open('', '_blank');

        $.ajax({
            url: '{{ url("issueLetter/print") }}/' + id,
            dataType: 'json',
            success: function(data) {
                var blob = base64toBlob(data.pdf, 'application/pdf');
                var pdfUrl = URL.createObjectURL(blob);
                var fname = data.filename || 'letter.pdf';

                //load pdf in new tab
                newTab.document.write(
                    '<html><head><title>' + fname + '</title></head>' +
                    '<body style="margin:0;display:flex;flex-direction:column;height:100vh">' +
                    '<div style="padding:6px 12px;background:#333;display:flex;align-items:center;gap:10px">' +
                    '<span style="color:#fff;font-size:14px">' + fname + '</span>' +
                    '<a id="dlBtn" style="color:#4fc3f7;font-size:13px;cursor:pointer;text-decoration:underline">Download</a>' +
                    '</div>' +
                    '<embed width="100%" style="flex:1" type="application/pdf" src="' + pdfUrl + '">' +
                    '</body></html>'
                );
                newTab.document.close();

                //download with correct filename
                newTab.document.getElementById('dlBtn').addEventListener('click', function() {
                    var a = newTab.document.createElement('a');
                    a.href = pdfUrl;
                    a.download = fname;
                    a.click();
                });
            },
            error: function() {
                newTab.document.write('<p>Failed to load PDF.</p>');
            }
        });
    });

    // PDF helper
    function base64toBlob(base64, type) {
        var bytes = atob(base64);
        var arr = [];
        for (var i = 0; i < bytes.length; i += 512) {
            var slice = bytes.slice(i, i + 512);
            var nums = new Array(slice.length);
            for (var j = 0; j < slice.length; j++) nums[j] = slice.charCodeAt(j);
            arr.push(new Uint8Array(nums));
        }
        return new Blob(arr, {
            type: type
        });
    }
</script>

@endsection