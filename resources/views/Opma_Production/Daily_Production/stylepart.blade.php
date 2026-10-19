@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.production&task_nav_bar_opma')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-building"></i></div>
                    <span>Style Parts - {{ $style->title }}</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Style Part</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap text-uppercase" style="width: 100%" id="dataTable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>STYLE DETAILS</th>
                                    <th>PART NAME</th>
                                    <th>APPLICABLE SIZES</th>
                                    <th>SCALES</th>
                                    <th>CURRENCY TYPE</th>
                                    <th>UNIT PRICE</th>
                                    <th class="text-right">Action</th>
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Style Part</h5>
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
                                {{-- <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Style</label>
                                    <input type="text" class="form-control form-control-sm" value="{{ $style->title }}" readonly/>
                                </div> --}}
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Style Details*</label>
                                    <input type="text" name="style_details" id="style_details" class="form-control form-control-sm" required/>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Part Name*</label>
                                    <input type="text" name="part_name" id="part_name" class="form-control form-control-sm" required/>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Applicable Sizes</label>
                                    <select class="form-control form-control-sm" id="sizes" name="sizes[]" multiple="multiple">
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->size }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Scales*</label>
                                    <input type="text" name="scales" id="scales" class="form-control form-control-sm" required/>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Currency Type</label>
                                    <select class="form-control form-control-sm" id="currency" name="currency">
                                        <option value="">Select Currency</option>
                                        @foreach($currency as $curr)
                                            <option value="{{ $curr->id }}">{{ $curr->currency }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Unit Price*</label>
                                    <input type="number" name="unit_price" id="unit_price" class="form-control form-control-sm" step="any" required/>
                                </div>

                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="style_id" id="style_id" value="{{ $style_id }}" />
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

        $('#production_menu_link').addClass('active');
        $('#production_menu_link_icon').addClass('active');
        $('#dailymaster').addClass('navbtnactive');

        $('#sizes').select2({
            placeholder: 'Select Sizes',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#formModal')
        });

        $('#dataTable').DataTable({
            "destroy": true,
            "processing": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [
                {
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Style Part Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] }
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    title: 'Style Part Information',
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape',
                    pageSize: 'legal',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] },
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Style Part Information',
                    className: 'btn btn-primary btn-sm',
                    text: '<i class="fas fa-print mr-2"></i> Print',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] },
                    customize: function(win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ],
            "order": [[0, "desc"]],
            ajax: {
                url: "{{ url('StylePartList') }}/{{ $style_id }}",
                type: "GET",
                dataSrc: 'data'
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'style_details', name: 'style_details' },
                { data: 'part_name', name: 'part_name' },
                {
                    data: 'sizes',
                    name: 'sizes',
                    defaultContent: '<span class="text-muted">N/A</span>',
                    render: function(data) {
                        return data ? data : '<span class="text-muted">N/A</span>';
                    }
                },
                { data: 'scales', name: 'scales' },
                {
                    data: 'currency_name',
                    name: 'currency_name',
                    defaultContent: '<span class="text-muted">N/A</span>'
                },
                { data: 'unit_price', name: 'unit_price' },
                {
                    data: 'id',
                    name: 'action',
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var buttons = '';
                        buttons += '<button name="edit" id="' + row.id + '" class="edit btn btn-primary btn-sm mr-1" type="button" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                        buttons += '<button type="button" name="delete" id="' + row.id + '" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';
                        return buttons;
                    }
                }
            ],
            drawCallback: function(settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        $('#create_record').click(function(){
            $('.modal-title').text('Add New Style Part');
            $('#action_button').html('Add');
            $('#action').val('Add');
            $('#form_result').html('');
            $('#formTitle')[0].reset();
            $('#sizes').val(null).trigger('change');
            $('#hidden_id').val('');
            $('#formModal').modal('show');
        });

        $('#formTitle').on('submit', function(event){
            event.preventDefault();
            var action_url = '';

            if ($('#action').val() == 'Add') {
                action_url = "{{ url('StylePartStore') }}";
            }
            if ($('#action').val() == 'Edit') {
                action_url = "{{ url('StylePartUpdate') }}";
            }

            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {
                    if (data.errors) {
                        var html = '<div class="alert alert-danger p-2 small">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p class="mb-0">' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                        $('#form_result').html(html);

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
                    url: "{{ url('StylePartEdit') }}/" + id,
                    dataType: "json",
                    success: function (data) {
                        $('#style_details').val(data.result.style_details);
                        $('#part_name').val(data.result.part_name);
                        $('#scales').val(data.result.scales);
                        $('#currency').val(data.result.currency_type);
                        $('#unit_price').val(data.result.unit_price);
                        $('#sizes').val(data.sizes).trigger('change');
                        $('#hidden_id').val(id);
                        $('.modal-title').text('Edit Style Part');
                        $('#action_button').html('Edit');
                        $('#action').val('Edit');
                        $('#formModal').modal('show');
                    }
                });
            }
        });

        var user_id;

        $(document).on('click', '.delete', async function() {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (r == true) {
                user_id = $(this).attr('id');
                $.ajax({
                    url: "{{ url('StylePartDestroy') }}/" + user_id,
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
                });
            }
        });
    });
</script>

@endsection