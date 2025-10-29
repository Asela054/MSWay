@extends('layouts.app')

@section('content')
<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
             @include('layouts.shift_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-business-time"></i></div>
                    <span>Employee Night Shift Assign </span>
                </h1>
            </div>
        </div>
    </div>
      <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        @can('employee-shift-allocation-create')
                        <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right mr-2" name="create_record"
                            id="create_record"><i class="fas fa-plus mr-2"></i>Add - Single Employee</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm fa-pull-right mr-2" name="csv_upload"
                            id="csv_upload"><i class="fas fa-plus mr-2"></i>Add - CSV Upload</button>
                            @endif
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%"
                                id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID </th>
                                        <th>DATE</th>
                                        <th>SHIFT</th>
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
    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Shift</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mt-3">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                {{-- <div class="form-row mb-1">
                                    <div class="col-4"></div>
                                    <div class="col-8">
                                        <h3 class="small font-weight-bold text-center text-danger">(Please select only consecutive working days from the calendar.)</h3>
                                    </div>
                                </div> --}}
                                <div class="form-row mb-1">
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Shift Type*</label>
                                        <select name="shift" id="shift" class="form-control form-control-sm"
                                            required readonly style="pointer-events: none">
                                            <option value="">Select Shift</option>
                                            <option selected value="1">Night</option>
                                            <option value="2">Day</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Date*</label>
                                        <input type="date" name="fromdate" id="fromdate" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-4 d-none">
                                        <label class="small font-weight-bold text-dark">Date To*</label>
                                        <input type="date" name="todate" id="todate" class="form-control form-control-sm" required />
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row mb-1">
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Employee*</label>
                                        <select name="employee" id="employee" class="form-control form-control-sm"
                                            required>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <div class="col-6">
                                    <button type="button" id="formsubmit"
                                        class="btn btn-primary btn-sm px-4 float-right"><i
                                            class="fas fa-plus"></i>&nbsp;Add to list</button>
                                    <input name="submitBtn" type="submit" value="Save" id="submitBtn" class="d-none">
                                    <button type="button" name="Btnupdatelist" id="Btnupdatelist"
                                        class="btn btn-primary btn-sm px-4 fa-pull-right" style="display:none;"><i
                                            class="fas fa-plus"></i>&nbsp;Update List</button>
                                    </div>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="detailsid" id="detailsid">

                            </form>
                        </div>
                        <div class="col-12 mt-3">
                            <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                <thead>
                                    <tr>
                                        <th>Emp ID</th>
                                        <th>Employee Name</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tableorderlist"></tbody>
                            </table>
                            <div class="form-group mt-2">
                                <button type="button" name="btncreateorder" id="btncreateorder"
                                    class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i
                                        class="fas fa-plus"></i>&nbsp;Create</button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <div class="modal fade" id="uploadAtModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel1" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg">
       <div class="modal-content">
           <div class="modal-header p-2">
               <h5 class="csvmodal-title" id="staticBackdropLabel1">Upload CSV</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div>
           <div class="modal-body">
               <div id="upload_response"></div>
               <row>
                <div class="col">
                    <a href="{{ url('/public/csvsample/Night Shift Allocation.csv') }}" class="control-label d-flex justify-content-end" >CSV Format-Download Sample File</a>
                </div>
               </row>
               <form method="post" id="formUpload" class="form-horizontal">
                   {{ csrf_field() }}
                   <div class="row">
                       <div class="col">
                           <div class="form-row mb-1">
                               {{-- <div class="col">
                                   <label class="small font-weight-bold text-dark">Date</label>
                                   <input required type="date" id="date_u" name="date" class="form-control form-control-sm" value="{{Date('Y-m-d')}}" />
                               </div> --}}
                               <div class="col">
                                <label class="small font-weight-bold text-dark">Shift Type*</label>
                                <select name="csv_shift" id="csv_shift" class="form-control form-control-sm"
                                    required style="pointer-events: none">
                                    <option value="">Select Shift</option>
                                    <option selected value="1">Night</option>
                                    {{-- <option value="2">Day</option> --}}
                                </select>
                            </div>
                               <div class="col">
                                    <label class="small font-weight-bold text-dark">CSV File</label>
                                    <input required type="file" id="csv_file_u" name="csv_file_u" class="form-control form-control-sm" accept=".csv"/>
                                </div>                            
                           </div>

                       </div>
                   </div>

                   <div class="row">
                       <div class="col">
                           <div class="loading"></div>

                       </div>
                   </div>

                   <div class="row">
                       <div class="col">
                           <div class="form-group mt-3">
                               <button type="submit" name="action_button" id="btn-upload" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-upload"></i>&nbsp;Upload </button>
                           </div>
                       </div>
                   </div>

               </form>

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
                    <button type="button" class="btn btn-dark px-3 btn-sm closebtn" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="confirmModal2" data-backdrop="static" data-keyboard="false" tabindex="-1"
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
                <button type="button" name="ok_button2" id="ok_button2"
                    class="btn btn-danger px-3 btn-sm">OK</button>
                <button type="button" class="btn btn-dark px-3 btn-sm closebtn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="aviewmodal-title" id="staticBackdropLabel">View Employee Shifts</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-row mb-1">
                                <div class="col-6">
                                    <label class="small font-weight-bold text-dark">Shift Type*</label>
                                    <select name="view_shift" id="view_shift" class="form-control form-control-sm"
                                        required readonly style="pointer-events: none">
                                        <option value="">Select Shift</option>
                                        <option value="1">Night</option>
                                        <option value="2">Day</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="small font-weight-bold text-dark">Date*</label>
                                    <input type="date" name="view_fromdate" id="view_fromdate" class="form-control form-control-sm" required readonly style="pointer-events: none"/>
                                </div>
                                <div class="col-4 d-none">
                                    <label class="small font-weight-bold text-dark">Date To*</label>
                                    <input type="date" name="view_todate" id="view_todate" class="form-control form-control-sm" required readonly style="pointer-events: none"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                <div class="row">
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small" id="view_tableorder">
                                <thead>
                                    <tr>
                                        <th>Emp ID</th>
                                        <th>Employee Name</th>
                                    </tr>
                                </thead>
                                <tbody id="view_tableorderlist"></tbody>
                            </table>
                        </div>
                    </div>
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

<script>
    $(document).ready(function () {

    $('#shift_menu_link').addClass('active');
    $('#shift_menu_link_icon').addClass('active');
    $('#employeeshift_link').addClass('navbtnactive');

        $('#viewconfirmModal .close').click(function(){
            $('#viewconfirmModal').modal('hide');
        });

        $('#confirmModal2 .close').click(function(){
            $('#confirmModal2').modal('hide');
        });
        $('#confirmModal2 .closebtn').click(function(){
            $('#confirmModal2').modal('hide');
        });
        let employee = $('#employee');

            employee.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
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

        $('#dataTable').DataTable({
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
                url: scripturl + "/employeeshiftlist.php",
                type: "POST",
                data: {},
            },
            columns: [
                { 
                    data: 'id', 
                    name: 'id'
                },
                { 
                    data: 'date_from', 
                    name: 'date_from'
                },
               {
                    data: 'shift_id',
                    name: 'shift_id',
                    render: function(data, type, row) {
                        if (data == 1) {
                            return 'Night Shift';
                        } else if (data == 2) {
                            return 'Day Shift';
                        } else {
                            return 'Unknown shift'; 
                        }
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
                        // View button
                        buttons += '<button name="view" id="'+row.id+'" class="view btn btn-secondary btn-sm mr-1" type="button"><i class="fas fa-eye"></i></button>';
                        // Edit button
                        buttons += '<button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm mr-1" type="button" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                        // Delete button
                        buttons += '<button type="button" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm mr-1" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';
                        // Status toggle button
                        if (row.status == 1) {
                            buttons += '<a href="/employeeshiftstatus/'+row.id+'/2" onclick="return deactive_confirm()" class="btn btn-success btn-sm mr-1" data-toggle="tooltip" title="Active"><i class="fas fa-check"></i></a>';
                        } else {
                            buttons += '<a href="/employeeshiftstatus/'+row.id+'/1" onclick="return active_confirm()" class="btn btn-warning btn-sm mr-1" data-toggle="tooltip" title="Inactive"><i class="fas fa-times"></i></a>';
                        }

                        return buttons;
                    }

                }
            ],
            drawCallback: function(settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        $('#create_record').click(function () {
            $('.modal-title').text('Add Shift');
            $('#action').val('Add');
            $('#form_result').html('');
            $('#formTitle')[0].reset();
            $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus"></i> Create');
            
            $('#formModal').modal('show');
        });


        $("#formsubmit").click(function () {
            if (!$("#formTitle")[0].checkValidity()) {
                // If the form is invalid, submit it. The form won't actually submit;
                // this will just cause the browser to display the native HTML5 error messages.
                $("#submitBtn").click();
            } else {
                var emp_id = $('#employee').val();
                var selectedText = $('#employee option:selected').text();
                // var employeename = selectedOption.attr('name-data');

                $('#tableorder > tbody:last').append('<tr class="pointer"><td>' + emp_id +
                    '</td><td>' + selectedText +
                    '</td><td class="d-none">NewData</td><td class="text-right"><button type="button" onclick= "productDelete(this);" id="btnDeleterow" class=" btn btn-danger btn-sm "><i class="fas fa-trash-alt"></i></button></td></tr>'
                );

                $('#employee').val('').trigger('change');
            }
        });


        $('#btncreateorder').click(function () {

            var action_url = '';

            if ($('#action').val() == 'Add') {
                action_url = "{{ route('employeeshiftinsert') }}";
            }
            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('employeeshiftupdate') }}";
            }

            $('#btncreateorder').prop('disabled', true).html(
                '<i class="fas fa-circle-notch fa-spin mr-2"></i> Creating');

            var tbody = $("#tableorder tbody");

            if (tbody.children().length > 0) {
                var jsonObj = [];
                $("#tableorder tbody tr").each(function () {
                    var item = {};
                    $(this).find('td').each(function (col_idx) {
                        item["col_" + (col_idx + 1)] = $(this).text();
                    });
                    jsonObj.push(item);
                });

                var shift = $('#shift').val();  
                var datefrom = $('#fromdate').val();  
                var dateto = $('#todate').val();  
                var hidden_id = $('#hidden_id').val();

                $.ajax({
                    method: "POST",
                    dataType: "json",
                    data: {
                        _token: '{{ csrf_token() }}',
                        tableData: jsonObj,
                        shift: shift,
                        datefrom: datefrom,
                        dateto: dateto,
                        hidden_id: hidden_id,

                    },
                    url: action_url,
                    success: function (data) { //alert(data);
                        var html = '';
                        if (data.errors) {
                            html = '<div class="alert alert-danger">';
                            for (var count = 0; count < data.errors.length; count++) {
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                        }
                        if (data.success) {
                            html = '<div class="alert alert-success">' + data.success +
                                '</div>';
                            $('#formTitle')[0].reset();
                            $('#tableorder tbody').empty();
                            $('#dataTable').DataTable().ajax.reload();
                            setTimeout(function(){
                                $('#formModal').modal('hide');
                            }, 2000);
                            //$('#titletable').DataTable().ajax.reload();
                            // window.location.reload(); // Use window.location.reload()
                            $('#btncreateorder').prop('disabled', false).html(
                                '<i class="fas fa-plus mr-2"></i> Create');
                        }

                        $('#form_result').html(html);
                        // resetfield();

                    }
                });
            }else{
                alert('Cannot Create..Table Empty!!');
                $('#btncreateorder').prop('disabled', false).html(
                '<i class="fas fa-plus mr-2"></i> Create');

            }
        });

        // edit function
        $(document).on('click', '.edit', function () {
            var id = $(this).attr('id');

            $('#form_result').html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("employeeshiftedit") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#shift').val(data.result.mainData.shift_id); 
                    $('#fromdate').val(data.result.mainData.date_from); 
                    $('#todate').val(data.result.mainData.date_to); 
                    $('#tableorderlist').html(data.result.requestdata);

                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Shift');
                    $('#btncreateorder').html('Update Request');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
            })
        });

          // request detail edit
          $(document).on('click', '.btnEditlist', function () {
            var id = $(this).attr('id');
            $('#employee').val('').trigger('change');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("employeeshifteditdetails") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#employee').val(data.result.emp_id).trigger('change');
                      
                    $('#detailsid').val(data.result.id);
                    $('#Btnupdatelist').show();
                    $('#formsubmit').hide();
                }
            })
        });

        // request detail update list


        $(document).on("click", "#Btnupdatelist", function () {
            if (!$("#formTitle")[0].checkValidity()) {
                // If the form is invalid, submit it. The form won't actually submit;
                // this will just cause the browser to display the native HTML5 error messages.
                $("#submitBtn").click();
            } else {
                var emp_id = $('#employee').val();
                var selectedOption = $('#employee option:selected');
                var employeename = selectedOption.attr('name-data');

                var detailid = $('#detailsid').val();
            
            $("#tableorder> tbody").find('input[name="hiddenid"]').each(function () {
                var hiddenid = $(this).val();
                if (hiddenid == detailid) {
                    $(this).parents("tr").remove();
                }
            });

            $('#tableorder> tbody:last').append('<tr class="pointer"><td name="empid">' + emp_id +
                '</td><td name="empname">' + employeename +
                '</td><td class="d-none">Updated</td><td class="d-none">' +
                detailid +
                '</td><td class="text-right"><button type="button" id="'+detailid+'" class="btnEditlist btn btn-primary btn-sm "><i class="fas fa-pen"></i>'+
                '</button>&nbsp;<button type="button" onclick= "productDelete(this);" id="btnDeleterow" class=" btn btn-danger btn-sm "><i class="fas fa-trash-alt"></i></button></td>'+
                '<td class="d-none"><input type ="hidden" id ="hiddenid" name="hiddenid" value="'+detailid+'"></td>'+
                '</tr>'
            );
            $('#employee').val('').trigger('change');
            $('#Btnupdatelist').hide();
            $('#formsubmit').show();
            }
        });

        //   details delete
        var rowid
        $(document).on('click', '.btnDeletelist', function () {
            rowid = $(this).attr('rowid');
            $('#confirmModal2').modal('show');

        });

        $('#ok_button2').click(function () {

            $('#form_result').html('');
            productDelete(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("employeeshiftdeletelist") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: rowid
                },
                beforeSend: function () {
                    $('#ok_button2').text('Deleting...');
                },
                success: function (data) {
                    setTimeout(function () {
                        $('#confirmModal2').modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                        // alert('Data Deleted');
                    }, 2000);
                    location.reload()
                }
            })
        });

        $('#csv_upload').click(function () {
                $('#uploadAtModal').modal('show');
                $('#upload_response').html('');
        });
        $('#formUpload').on('submit',function(e) {
                e.preventDefault();
                let save_btn=$("#btn-upload");
                let btn_prev_text = save_btn.html();
               
                //save_btn.prop("disabled", true);
                save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
                let formData = new FormData($('#formUpload')[0]);
                
                let url_text = '{{ url("/night_shiftallocate_csv") }}';
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                $.ajax({
                    url: url_text,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(res) {
                        if (res.status == 1) {
                            $('#upload_response').html("<div class='alert alert-success'>"+res.msg+"</div>");

                            save_btn.html(btn_prev_text);
                            save_btn.prop("disabled", false);
                            $("#formUpload")[0].reset();
                            //scroll monthAtModal
                            $('#uploadAtModal').scrollTop(0);
                            $('#dataTable').DataTable().ajax.reload();
                            //wait 2 seconds
                            setTimeout(function(){
                                $('#uploadAtModal').modal('hide');
                            }, 2000);

                        }else {

                            var html = '';
                            if (res.errors) {
                                html = '<div class="alert alert-danger">';
                                for (var count = 0; count < res.errors.length; count++) {
                                    html +=   res.errors[count]+'<br>' ;
                                }
                                html += '</div>';
                            }

                            $('#upload_response').html(html);

                            save_btn.prop("disabled", false);
                            save_btn.html(btn_prev_text);
                        }
                    },
                    error: function(res) {
                        alert(data);
                    }
                });
            });


        var user_id;

        $(document).on('click', '.delete', function () {
            user_id = $(this).attr('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                url: '{!! route("employeeshiftdelete") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: user_id
                },
                beforeSend: function () {
                    $('#ok_button').text('Deleting...');
                },
                success: function (data) { //alert(data);
                    setTimeout(function () {
                        $('#confirmModal').modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                        // alert('Data Deleted');
                    }, 2000);
                    location.reload()
                }
            })
        });

        // view modal 
        $(document).on('click', '.view', function () {
            id = $(this).attr('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("employeeshiftview") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#view_shift').val(data.result.mainData.shift_id); 
                    $('#view_fromdate').val(data.result.mainData.date_from); 
                    $('#view_todate').val(data.result.mainData.date_to); 
                    $('#view_tableorderlist').html(data.result.requestdata);

                    $('#viewconfirmModal').modal('show');

                }
            })


        });

        $('#csv_sample').click(function () {
            // var csvContent = "data:text/csv;charset=utf-8," +
            //                 "emp_id,emp_name,date_from,date_to\n" +
            //                 "1,John Doe,2024-08-01,2024-08-31\n" +
            //                 "2,Jane Smith,2024-08-01,2024-08-31";

        });
    });

    function productDelete(row) {
        $(row).closest('tr').remove();
    }

    function deactive_confirm() {
        return confirm("Are you sure you want to deactive this?");
    }

    function active_confirm() {
        return confirm("Are you sure you want to active this?");
    }
</script>
<script>
    $('#fromdate').on('change', function() {
        $('#todate').val($(this).val());
    });
</script>

@endsection