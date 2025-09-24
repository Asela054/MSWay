@extends('layouts.app')

@section('content')

<main>
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.payroll_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-money-check-dollar-pen"></i></div>
                    <span>Facilities</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12 text-right">
                        <button type="button" name="create_record" id="create_record"  class="btn btn-primary btn-sm px-4"><i class="fas fa-plus"></i>&nbsp;Add Facilities</button>
                    </div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-bordered table-striped table-sm small w-100 nowrap" id="titletable" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>NAME</th>
                                        <th>TYPE</th>
                                        <th>EPF PAYABLE</th>
                                        <th>OT APPRICABLE</th>
                                        <th>NOPAY APPRICABLE</th>
                                        <th class="actlist_col text-right">ACTION</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="formModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <form id="frmInfo" class="" method="post" onsubmit="return buttonSubmitHandler();">
                        {{ csrf_field() }}
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="small font-weight-bolder">Name</label>
                                <input type="text" name="remuneration_name" id="remuneration_name" class="form-control form-control-sm" required />
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="small font-weight-bolder">Minimum attendance threshold</label>
                                <select name="advanced_option_id" id="advanced_option_id"
                                    class="form-control form-control-sm">
                                    <option data-optexclude="0" value="0">1 day (Daily basis)</option>
                                    <option data-optexclude="0" value="1">1 - 30 days (Monthly basis)
                                    </option>
                                    <option data-optexclude="1" value="1">Week days (Exclude holidays)
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="small font-weight-bolder">Type</label>
                                <select name="remuneration_type" id="remuneration_type"
                                    class="form-control form-control-sm">
                                    <option value="Addition">Addition</option>
                                    <option value="Deduction">Deduction</option>
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bolder">EPF Allocation</label>
                                <select name="epf_payable" id="epf_payable" class="form-control form-control-sm">
                                    <option value="0">Without EPF</option>
                                    <option value="1">With EPF</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="small font-weight-bolder">Taxation</label>
                                <select name="taxcalc_spec_code" id="taxcalc_spec_code"
                                    class="form-control form-control-sm">
                                    <option value="0">None</option>
                                    <option value="1">PAYE</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-1 mt-2">
                            <div class="col">
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="hidden" name="ot_applicable" value="0">
                                    <input type="checkbox" class="custom-control-input" id="ot_applicable" name="ot_applicable" value="1">
                                    <label class="custom-control-label small" for="ot_applicable">OT Applicable</label>
                                </div>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="hidden" name="nopay_applicable" value="0">
                                    <input type="checkbox" class="custom-control-input"  id="nopay_applicable" name="nopay_applicable" value="1">
                                    <label class="custom-control-label small" for="nopay_applicable">Nopay Applicable</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col text-right">
                                <hr>
                                <input type="hidden" name="action" id="action" value="Edit" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="allocation_method" id="allocation_method" value="M1" />
                                <!-- fixed -->
                                <input type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm mr-1 px-3" value="Edit" />
                                <!--button type="submit" name="action_button" id="option_button" value="1" class="btn btn-secondary">More</button-->
                                <input type="button" id="btn_next" value="More" class="btn btn-light btn-sm px-3" />
                            </div>
                        </div>
                    </form>
                    <form id="frmMore" class="sect_bg" method="post">
                        {{ csrf_field() }}
                        <div class="form-row mb-1">
                            <div class="col">
                                <span id="frm_more_sect_bg_title">&nbsp;</span>
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="small font-weight-bolder">Criteria</label>
                                <select name="remuneration_criteria" id="remuneration_criteria"
                                    class="form-control form-control-sm">
                                    @foreach($eligibleinfo as $preinfo)

                                    <option data-optgroup="{{$preinfo->remuneration_id}}"
                                        value="{{$preinfo->id}}">{{$preinfo->min_days}} -
                                        {{$preinfo->max_days}} days</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bolder">Payment</label>
                                <input type="text" name="pre_eligible_amount" id="pre_eligible_amount"
                                    class="form-control form-control-sm" />
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="small font-weight-bolder">Increment</label>
                                <input type="text" name="grp_increment" id="grp_increment" class="form-control form-control-sm" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col text-right">
                                <hr>
                                <input type="submit" name="setup_button" id="setup_button" class="btn btn-primary btn-sm px-3" value="Edit" />
                                <input type="button" id="btn_back" value="Back" class="btn btn-light btn-sm px-3" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <hr>
                                <table class="table table-bordered table-striped table-sm small w-100 nowrap" id="criteriatable">
                                    <thead>
                                        <tr>
                                            <th>CRITERIA</th>
                                            <th>PAYMENT</th>
                                            <th>INCREAMENT</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($eligibleinfo as $preinfo)
                                        <tr id="row-{{$preinfo->id}}" data-optgroup="{{$preinfo->remuneration_id}}">
                                            <td>{{$preinfo->min_days}} - {{$preinfo->max_days}} days</td>
                                            <td>{{$preinfo->pre_eligible_amount}}</td>
                                            <td>{{$preinfo->grp_increment}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span
                            class="btn-sm btn-danger" aria-hidden="true">X</span></button>

                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection


@section('script')

<script>
    $(document).ready(function () {        
        $('#payrollmenu').addClass('active');
        $('#payrollmenu_icon').addClass('active');
        $('#policymanagement').addClass('navbtnactive');

        var remunerationTable = $("#titletable").DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Customer  Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Location Information', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Customer  Information',
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
                url: scripturl + "/remunerationlist.php",
                type: "POST",
                data: {},
            },
            "columns": [
                { 
                    data: 'remuneration_name', 
                    name: 'remuneration_name'
                },
                { 
                    data: 'remuneration_type', 
                    name: 'remuneration_type'
                },
                {
                    "targets": -1,
                    "className": 'text-center',
                    "data": null,
                    "render": function(data, type, full) {
                        return (full['epf_payable'] == 1) ? 'Y' : 'N';
                    }
                }, 
                {
                    "targets": -1,
                    "className": 'text-center',
                    "data": null,
                    "render": function(data, type, full) {
                        return (full['ot_applicable'] == 1) ? 'Y' : 'N';
                    }
                }, 
                {
                    "targets": -1,
                    "className": 'text-center',
                    "data": null,
                    "render": function(data, type, full) {
                        return (full['nopay_applicable'] == 1) ? 'Y' : 'N';
                    }
                }, 
                {
                    "targets": -1,
                    "className": 'actlist_col text-right',
                    "data": null,
                    "orderable": false,
                    render: function (data, type, full) {
                        return '<button name="edit" data-refid="' + full['id'] +
                            '" class="edit btn btn-primary btn-sm mr-1" type="submit" data-toggle="tooltip" title="Edit">' +
                            '<i class="fas fa-pencil-alt"></i></button>' +
                            '<button type="submit" name="delete" data-refid="' + full['id'] +
                            '" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove">' +
                            '<i class="fas fa-trash-alt"></i></button>';
                    }
                }
            ],
            columnDefs: [
                {
                    targets: 5, // Target the first column (index 0)
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).attr('data-refopt', rowData.advanced_option_id);
                    }
                }
            ],
            drawCallback: function(settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        }); /**/

        var criteriaTable = $("#criteriatable").DataTable({
            "searching": false,
            "info": false,
            "paging": false
        });

        $('#create_record').click(function () {
            $('#formModalLabel').text('Add Remuneration');
            $('#action_button').val('Save');
            $('#action').val('Add');
            $('#form_result').html('');

            $("#btn_next").prop("disabled", true);
            //$("#btn_next").hide();
            //$("#option_button").show();
            $("#advanced_option_id").prop("disabled", false);

            $("#frmInfo").removeClass('sect_bg');
            $("#frmMore").addClass('sect_bg');

            $('#remuneration_name').val('');
            $('#remuneration_type').val('Addition');
            $('#epf_payable').val('0');
            //$('#hidden_id').val('');
            //$('#advanced_option_id').val('0');
            $('#advanced_option_id option[data-optexclude="0"][value="0"]').prop("selected", true);
            $("#taxcalc_spec_code").val('0');

            $("#ot_applicable").prop("checked", false);
            $("#nopay_applicable").prop("checked", false);

            $("#pre_eligible_amount").val('');
            $("#grp_increment").val('');

            $('#formModal').modal('show');
        });

        $('#frmInfo').on('submit', function (event) {
            event.preventDefault();
            var action_url = '';


            if ($('#action').val() == 'Add') {
                action_url = "{{ route('addRemuneration') }}";
            }

            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('Remuneration.update') }}";
            }
            /*
            alert(action_url);
            */
            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {
                    // console.log('new_obj:', data.new_obj);
                    // if (data.result == 'error') {
                    //     alert(data.msg);
                    // }

                    if (data.errors) {
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: 'Record Error',
                            message: data.errors,
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
                            title: 'Record Success',
                            message: data.success,
                            url: '',
                            target: '_blank',
                            type: 'success'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        action(actionJSON);

                        if ($("#action").val() == "Add") {
                            // var rowNode = remunerationTable.row.add([
                            //     data.new_obj.remuneration_name,
                            //     data.new_obj.remuneration_type,
                            //     data.new_obj.epf_payable,
                            //     data.new_obj.id
                            // ]).draw(false).node();
                            var rowData = {
                                remuneration_name: data.new_obj.remuneration_name,
                                remuneration_type: data.new_obj.remuneration_type,
                                epf_payable: data.new_obj.epf_payable,
                                id: data.new_obj.id
                                // Add other properties that match your column data names
                            };
                            
                            var rowNode = remunerationTable.row.add(rowData).draw(false).node();

                            if (data.new_obj.advanced_option_id > 0) {
                                $("#frm_more_sect_bg_title").html(data.new_obj.remuneration_name + ' - ( Important: <strong>update payment value</strong> )');

                                $('#remuneration_criteria option').hide();
                                criteriaTable.$('tr').hide();

                                $("#remuneration_criteria").append(
                                    '<option data-optgroup="' + data.new_obj.id +
                                    '" value="' + data.new_obj.advanced_option_id +
                                    '">1- 30 days</option>');
                                $('#remuneration_criteria option[data-optgroup="' + data
                                    .new_obj.id + '"]').show();
                                $('#remuneration_criteria').val($(
                                    '#remuneration_criteria option[data-optgroup="' +
                                    data.new_obj.id + '"]:first').val());

                                var newNode = criteriaTable.row.add([
                                    '1 - 30 days',
                                    0,
                                    0
                                ]).draw(false).node();
                                $(newNode).attr('id', 'row-' + data.new_obj
                                    .advanced_option_id);
                                $(newNode).attr('data-optgroup', data.new_obj.id);
                                criteriaTable.$('tr[data-optgroup="' + data.new_obj.id +
                                    '"]').show();

                                $('#frmInfo').addClass('sect_bg');
                                $('#frmMore').removeClass('sect_bg');
                            }
                        } else {
                            var selected_tr = remunerationTable.row('#row-' + $(
                                "#hidden_id").val() +
                            ''); //remunerationTable.$('tr.classname');
                            /*
                            alert(JSON.stringify(selected_tr.data()));
                            
                            var rowNode=selected_tr.node();
                            $( rowNode ).find('td').eq(0).html( data.alt_obj.remuneration_name );
                            $( rowNode ).find('td').eq(1).html( data.alt_obj.remuneration_type );
                            $( rowNode ).find('td').eq(2).html( data.alt_obj.epf_payable );
                            */
                            /*
                            var d=[data.alt_obj.remuneration_name, data.alt_obj.remuneration_type, data.alt_obj.epf_payable, data.alt_obj.alt_id];
                            */
                            var d = selected_tr.data();
                            d[0] = data.alt_obj.remuneration_name;
                            d[1] = data.alt_obj.remuneration_type;
                            d[2] = data.alt_obj.epf_payable;
                            remunerationTable.row(selected_tr).data(d).draw();

                            $("#frm_more_sect_bg_title").html(data.alt_obj
                                .remuneration_name);
                        }
                    }
                    // $('#form_result').html(html);
                }
            });
        });

        $(document).on('click', '.edit', async function () {
            var r = await Otherconfirmation("You want to Edit this ? ");
            if (r == true) {
                var id = $(this).data('refid');
                $("#btn_next").prop("disabled", ($(this).parent().data('refopt') == 0) ? true : false);
                //$("#btn_next").show();
                //$("#option_button").hide();
                $('#advanced_option_id').prop('disabled', true);
                $('#form_result').html('');

                $('#remuneration_criteria option').hide();
                criteriaTable.$('tr').hide();

                $("#frmInfo").removeClass('sect_bg');
                $("#frmMore").addClass('sect_bg');

                var par = $(this).parent().parent();
                $("#frm_more_sect_bg_title").html(par.children("td:nth-child(1)").html());

                $.ajax({
                    url: "Remuneration/" + id + "/edit",
                    dataType: "json",
                    success: function (data) {
                        $('#remuneration_name').val(data.pre_obj.remuneration_name);
                        $('#remuneration_type').val(data.pre_obj.remuneration_type);
                        $('#epf_payable').val(data.pre_obj.epf_payable);
                        $('#hidden_id').val(id);

                        //$('#advanced_option_id').val(data.pre_obj.advanced_option_id);
                        $('#advanced_option_id option[data-optexclude="' + data.pre_obj
                            .employee_work_rate_work_days_exclusions + '"][value="' + data
                            .pre_obj.advanced_option_id + '"]').prop("selected", true);
                        $('#remuneration_criteria option[data-optgroup="' + data.pre_obj.id +
                            '"]').show();
                        $('#remuneration_criteria').val($(
                            '#remuneration_criteria option[data-optgroup="' + data
                            .pre_obj.id + '"]:first').val());
                        criteriaTable.$('tr[data-optgroup="' + data.pre_obj.id + '"]').show();

                        $("#taxcalc_spec_code").val(data.pre_obj.taxcalc_spec_code);
                        
                        if (data.pre_obj.ot_applicable == "1") {
                            $('#ot_applicable').prop("checked", true);
                        } else {
                            $('#ot_applicable').prop("checked", false);
                        }

                        if (data.pre_obj.nopay_applicable == "1") {
                            $('#nopay_applicable').prop("checked", true);
                        } else {
                            $('#nopay_applicable').prop("checked", false);
                        }

                        $('#formModalLabel').text('Edit Remuneration');
                        $('#action_button').val('Update');
                        $('#action').val('Edit');
                        $('#formModal').modal('show');

                    }
                }) /**/
            }
        });

        $("#btn_next").on("click", function () {
            $('#frmInfo').addClass('sect_bg');
            $('#frmMore').removeClass('sect_bg');
        });
        $("#btn_back").on("click", function () {
            $('#frmMore').addClass('sect_bg');
            $('#frmInfo').removeClass('sect_bg');
        });
        $("#frmMore").on("submit", function (event) {
            event.preventDefault();
            var action_url = "{{ route('RemunerationEligibilityDay.update') }}";
            /*
            alert(action_url);
            */
            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {

                    var html = '';
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
                        action(actionJSON);

                        var selected_tr = criteriaTable.row('#row-' + $(
                            "#remuneration_criteria").find(":selected").val() +
                        ''); //remunerationTable.$('tr.classname');
                        /*
                        alert(JSON.stringify(selected_tr.data()));
                        
                        var rowNode=selected_tr.node();
                        $( rowNode ).find('td').eq(0).html( data.alt_obj.remuneration_name );
                        $( rowNode ).find('td').eq(1).html( data.alt_obj.remuneration_type );
                        $( rowNode ).find('td').eq(2).html( data.alt_obj.epf_payable );
                        */
                        var d = selected_tr.data();
                        d[1] = $('#pre_eligible_amount').val();
                        d[2] = $('#grp_increment').val();
                        criteriaTable.row(selected_tr).data(d).draw();
                    }
                    $('#form_result').html(html);
                }
            });
        });

        var remuneration_id;

        $(document).on('click', '.delete', async function () {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (r == true) {
                remuneration_id = $(this).data('refid');
                $.ajax({
                    url: "Remuneration/destroy/" + remuneration_id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        if (data.errors) {
                            // Handle server-side errors (including permission denied)
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: 'Permission Denied',
                                message: data.errors,
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        } 
                        if (data.result == 'success') {
                            // Success case
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
                    }
                })
            }
        });
        
        $('#formModal').on('hidden.bs.modal', function (event) {
            window.location.reload();
        });
        // $('#ok_button').click(function () {
        //     $.ajax({
        //         url: "Remuneration/destroy/" + remuneration_id,
        //         beforeSend: function () {
        //             $('#ok_button').text('Deleting...');
        //         },
        //         success: function (data) {
        //             //alert(JSON.stringify(data));
        //             setTimeout(function () {
        //                 $('#confirmModal').modal('hide');
        //                 //$('#user_table').DataTable().ajax.reload();
        //                 //alert('Data Deleted');
        //             }, 2000);
        //             //location.reload()
        //             if (data.result == 'success') {
        //                 remunerationTable.row('#row-' + remuneration_id + '').remove()
        //                 .draw();

        //             }
        //         }
        //     })
        // });
    });
</script>
<script>
    function buttonSubmitHandler() {
        var optwork = (typeof ($('#advanced_option_id').find(":selected").attr('data-optexclude')) == "undefined") ?
            "" : $('#advanced_option_id').find(":selected").attr('data-optexclude');
        //console.log('x1--'+paygroup);
        if (optwork != "") {
            var destobj = '#frmInfo'; //(destnum==1)?'#frmRevPdf':'#frmPrintPg';
            /*atleast-one-detail-record-must-exist*/
            $(destobj).append("<input type='hidden' name='employee_work_rate_work_days_exclusions' value='" + optwork +
                "' />");
            return true;
        } else {
            alert("Minimum attendance threshold is required");
            return false;
        }

    }
</script>
@endsection