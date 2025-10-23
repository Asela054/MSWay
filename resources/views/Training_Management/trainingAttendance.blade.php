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
                        <span>Training Attendance</span>
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
                                    <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                        data-toggle="offcanvas" data-target="#offcanvasRight"
                                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                        Records</button>
                                </div>
                                 <div class="col-12">
                                    <hr class="border-dark">
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                        <label class="form-check-label" for="selectAll">Select All Records</label>
                                    </div>
                                </div>
                                <div class="col-6 text-right">
                                    <button id="mark_as_attend" class="btn btn-primary float-right mt-2 btn-sm px-3"><i class="fas fa-plus mr-2"></i>Mark as Attend</button>
                                </div>
                            </div>
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap w-100"
                                    id="attendreporttable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>EMP ID</th>
                                            <th>EMPLOYEE</th>
                                            <th>TRAINING TYPE</th>
                                            <th>VENUE</th>
                                            <th>START TIME</th>
                                            <th>END TIME</th>
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
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
                aria-labelledby="offcanvasRightLabel">
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
                                    <label class="small font-weight-bolder text-dark">Training Type</label>
                                    <select name="type" id="type" class="form-control form-control-sm">
                                    </select>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">Venue</label>
                                    <select name="venue" id="venue" class="form-control form-control-sm">
                                    </select>
                                </div>
                            </li>
                            <!-- <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">Department</label>
                                    <select name="department" id="department" class="form-control form-control-sm">
                                    </select>
                                </div>
                            </li> -->
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">Employee</label>
                                    <select name="employee" id="employee" class="form-control form-control-sm">
                                    </select>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">From Date </label>

                                    <input type="date" id="from_date" name="from_date"
                                        class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">To Date</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                        placeholder="yyyy-mm-dd">
                                </div>
                            </li>
                            <li>
                                <div class="col-md-12 d-flex justify-content-between">
                                    
                                    <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
                                        <i class="fas fa-redo mr-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm filter-btn px-3"
                                        id="btn-filter">
                                        <i class="fas fa-search mr-2"></i>Search
                                    </button>
                                </div>
                            </li>
                        </form>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Modal Area Start -->
        <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Training Marks</h5>
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
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bold text-dark">Marks</label>
                                        <input type="number" step="0.01" name="marks" id="marks" class="form-control form-control-sm" />
                                    </div>
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bold text-dark">Remarks</label>
                                        <textarea name="remarks" id="remarks" class="form-control form-control-sm" rows="3"></textarea>
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
        $(document).ready(function () {

            $('#employee_menu_link').addClass('active');
            $('#employee_menu_link_icon').addClass('active');
            $('#training').addClass('navbtnactive');

            let selected_cb = [];

            let type = $('#type');
            let venue = $('#venue');
            let employee = $('#employee');

            type.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("trainType_list_sel2")}}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });

            venue.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("trainVenue_list_sel2")}}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            type: type.val()
                        }
                    },
                    cache: true
                }
            });

            employee.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("trainEmp_list_sel2")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            venue: venue.val()
                        }
                    },
                    cache: true
                }
            });

            load_dt('', '', '', '', '', true);

            function load_dt(type, venue, employee, from_date, to_date, isInitialLoad) {
                $('#attendreporttable').DataTable({
                    "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    "searching": false,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Training Attendance  Information',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Training Attendance  Information', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Training Attendance   Information',
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
                        "url": "{{url('/train_Attendance_list')}}",
                        "data": {
                            'type': type,
                            'venue': venue,
                            'employee': employee,
                            'from_date': from_date,
                            'to_date': to_date
                        },
                    },
                    "language": {
                        "emptyTable": isInitialLoad ? 
                            "<div class='text-center py-4'>" +
                            "<h5 class='text-muted'>No records to display</h5>" +
                            "<p class='text-muted small'>Please use the filter options to search for training records</p>" +
                            "</div>" : 
                            "No data available in table"
                    },
                    columns: [
                        { 
                            data: 'id',
                            render: function (data, type, row, meta) {
                                if (type === 'display') {
                                    if (row["is_attend"] == 1) {
                                        return '<i class="fa fa-check text-success"></i>';
                                    } else {
                                        return '<label>' +
                                            '<input type="checkbox"' +
                                            ' data-id="'+data+'"' +
                                            ' data-uid="'+row["uid"]+'"' +
                                            ' data-emp_name_with_initial="'+row["emp_name_with_initial"]+'"' +
                                            ' data-type="'+row["type"]+'"' +
                                            ' data-venue="'+row["venue"]+'"' +
                                            ' data-start_time="'+row["start_time"]+'"' +
                                            ' data-end_time="'+row["end_time"]+'"' +
                                            ' class="cb"/>' +
                                            '</label>';
                                    }
                                }
                                return data;
                            }
                        },
                        {data: 'uid'},
                        {data: 'employee_display'},
                        {data: 'type'},
                        {data: 'venue'},
                        {data: 'start_time'},
                        {data: 'end_time'},
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-right'
                        },
                    ],
                    "bDestroy": true,
                    "order": [[0, "desc"]],
                });
            }

            $('#formFilter').on('submit', function (e) {
                e.preventDefault();
                let type = $('#type').val();
                let venue = $('#venue').val();
                let employee = $('#employee').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                 load_dt(type, venue, employee, from_date, to_date, false);
                 closeOffcanvasSmoothly();
            });

            $('body').on('click', '.cb', function (){
                let id = $(this).data('id');

                let b = {};
                b["id"] = id;
                b["uid"] = $(this).data('uid');
                b["emp_name_with_initial"] = $(this).data('emp_name_with_initial');
                b["type"] = $(this).data('type');
                b["venue"] = $(this).data('venue');
                b["start_time"] = $(this).data('start_time');
                b["end_time"] = $(this).data('end_time');

                if($(this).is(':checked')){
                    if(jQuery.inArray(b, selected_cb) === -1){
                        selected_cb.push(b);
                        let selector = $('.cb[data-id="' + id + '"]');
                        selector.parent().parent().parent().css('background-color', '#f7c8c8');
                    }
                }else {
                    removeA(selected_cb, id)
                }
            });

            $(document).on('click', '#mark_as_attend',async function (e) {

                e.preventDefault();
                let save_btn = $(this);
                let late_type = $('#late_type').val();

                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
                    save_btn.prop("disabled", true);
                    save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
                    $.ajax({
                        url: "train_Attendance_mark",
                        method: "POST",
                        data: {
                            'selected_cb': selected_cb,
                            late_type: late_type,
                            _token: $('input[name=_token]').val(),
                        },
                        success: function (data) {
                            if(data.status == true){
                                     const actionObj = {
                                            icon: 'fas fa-save',
                                            title: '',
                                            message:'Training Attendance Marked',
                                            url: '',
                                            target: '_blank',
                                            type: 'success'
                                        };
                                        const actionJSON = JSON.stringify(actionObj, null, 2);
                                        actionreload(actionJSON);
                            }else{
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
                            save_btn.prop("disabled", false);
                            save_btn.html('<i class="fas fa-plus mr-2"></i>Mark as Attend' );
                        }
                    });
                }


            });

            function removeA(arr, id) {
                $.each(arr , function(index, val) {
                    if(id == val.id){
                        //remove val
                        selected_cb.splice(index,1);
                        let selector = $('.cb[data-id="' + id + '"]');
                        selector.parent().parent().parent().css('background-color', 'inherit');
                    }
                });
            }

            function check_changed_text_boxes(){
                for(let a = 0; a < selected_cb.length; a++){
                    let id = selected_cb[a]['id'];
                    let selector = $('.cb[data-id="' + id + '"]');

                    selector.prop("checked", true);
                    selector.parent().parent().parent().css('background-color', '#f7c8c8');
                }
            }


            $('#btn-reset').on('click', function () {
                $('#formFilter')[0].reset();
                $('#from_date').val('');
                $('#to_date').val('');
                load_dt('', '', '', '', '', false);
            });

            $('#formTitle').on('submit', function (event) {
                event.preventDefault();
                var action_url = '';

                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('Trainingmark.update') }}";
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

            $(document).on('click', '.edit', async function () {
                var r = await Otherconfirmation("You want to Add Marks ? ");
                if (r == true) {
                    var id = $(this).attr('id');
                    $('#form_result').html('');
                    $.ajax({
                        url: "{{ url('train_attendance') }}/" + id + "/edit",
                        dataType: "json",
                        success: function (data) {
                            $('#marks').val(data.result.marks);
                            $('#remarks').val(data.result.remarks);
                            $('#hidden_id').val(id);
                            $('.modal-title').text('Edit Training Marks');
                            $('#action_button').html('<i class="fas fa-save"></i>&nbsp;Update');
                            $('#action').val('Edit');
                            $('#formModal').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.log('Error:', error);
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: 'Failed to load data!',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                    })
                }
            });
        });
        $('body').on('click', '#selectAll', function () {
            let isChecked = $(this).is(':checked');
            let table = $('#attendreporttable').DataTable();

            // Clear existing selection
            selected_cb = [];

            // Loop through all rows in the DataTable
            table.rows().every(function () {
                let row = this.node();
                let checkbox = $(row).find('.cb');

                // Only process checkboxes that are not already marked as late
                if (checkbox.length > 0 && !checkbox.closest('td').find('.fa-check').length) {
                    if (isChecked) {
                        // Check the checkbox
                        checkbox.prop('checked', true);

                        // Add to selected_cb array
                        let b = {};
                        b["id"] = checkbox.data('id');
                        b["uid"] = checkbox.data('uid');
                        b["emp_name_with_initial"] = checkbox.data('emp_name_with_initial');
                        b["type"] = checkbox.data('type');
                        b["venue"] = checkbox.data('venue');
                        b["start_time"] = checkbox.data('start_time');
                        b["end_time"] = checkbox.data('end_time');

                        if (jQuery.inArray(b, selected_cb) === -1) {
                            selected_cb.push(b);
                        }
                        // Apply background color
                        $(row).css('background-color', '#f7c8c8');
                    } else {
                        // Uncheck the checkbox
                        checkbox.prop('checked', false);
                        // Remove from selected_cb array by ID
                        selected_cb = selected_cb.filter(item => item.id !== checkbox.data('id'));
                        // Remove background color
                        $(row).css('background-color', '');
                    }
                }
            });
            // Update the select all checkbox state
            $(this).prop('checked', isChecked);
        });

         
    </script>

@endsection

