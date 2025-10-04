<?php $page_stitle = 'Report on Employee Attendance - Multi Offset'; ?>
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
                         <span>Late Attendance Approve</span>
                     </h1>
                 </div>
             </div>
         </div>


        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body p-0 p-2 ">
                     <div class="row">
                        <div class="col-md-12">
                            <div class="row align-items-center mb-4">
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                        <label class="form-check-label" for="selectAll">Select All Records</label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button id="approve" class="btn btn-primary float-right mt-2 btn-sm px-3"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp; Approve Late Attendance</button>
                                </div>
                            </div>
                            <div class="col-12">
                                <hr class="border-dark">
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                    data-toggle="offcanvas" data-target="#offcanvasRight"
                                    aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                    Options</button>
                            </div><br><br>
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap w-100"
                                    id="attendreporttable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>ID</th>
                                            <th>EMPLOYEE</th>
                                            <th>DATE</th>
                                            <th>CHECK IN TIME</th>
                                            <th>CHECK OUT TIME</th>
                                            <th>WORKING HOURS</th>
                                            <th>LOCATION</th>
                                            <th>DEPARTMENT</th>
                                            <th>IS APPROVED ?</th>
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
                 @include('layouts.filter_menu_offcanves')
            </div>

             <!-- approve modal -->
            <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Approve Late Attendances</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="message_modal"></div>
                            <form class="form-horizontal" id="formApprove">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Leave Type</label>
                                    <select name="leave_type" id="leave_type" class="form-control form-control-sm">
                                        <option value="">Select Leave Type</option>
                                        @foreach($leave_types as $leave_type)
                                            <option value="{{ $leave_type->id }}">{{ $leave_type->leave_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm px-3" id="btn-approve"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Approve</button>
                        </div>
                    </div>
                </div>

        </div>

    </main>

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#attendantmaster').addClass('navbtnactive');

            let selected_cb = [];

            let company = $('#company');
            let department = $('#department');
            let employee = $('#employee');
            let location = $('#location');

            company.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("company_list_sel2")}}',
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

            department.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("department_list_sel2")}}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company.val()
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
                    url: '{{url("employee_list_from_attendance_sel2")}}',
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

            location.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("location_list_from_attendance_sel2")}}',
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

            load_dt('','','','','');

            function load_dt(department, company, location, from_date, to_date) {
                $('#attendreporttable').DataTable({
                    "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Late Attendance Approve Information',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Late Attendance Approve Information', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Late Attendance Approve  Information',
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
                        url: scripturl +"/late_attendance_approve_list.php", 
                        type: "POST",
                        data : {
                            'department': department,
                            'company': company,
                            'location': location,
                            'from_date': from_date,
                            'to_date': to_date
                        },
                    },

                    columns: [
                        { data: 'id' ,
                            render : function ( data, type, row, meta ) {
                                if (type === 'display') {
                                    return '<div class="custom-control custom-checkbox">' +
                                        '<input type="checkbox" ' +
                                        'data-id="' + data + '" ' +
                                        'data-emp_name_with_initial="' + row["emp_name_with_initial"] + '" ' +
                                        'data-date="' + row["date"] + '" ' +
                                        'data-check_in_time="' + row["check_in_time"] + '" ' +
                                        'data-check_out_time="' + row["check_out_time"] + '" ' +
                                        'data-working_hours="' + row["working_hours"] + '" ' +
                                        'data-location_id="' + row["location_id"] + '" ' +
                                        'data-dept_id="' + row["dept_id"] + '" ' +
                                        'class="form-check-input cb" id="cb_' + data + '">' +
                                        '<label class="form-check-label" for="cb_' + data + '"></label>' +
                                        '</div>';
                                }
                                return data;
                            }},
                        {data: 'id'},
                        {data: 'employee_display'},
                        {data: 'date'},
                        {data: 'check_in_time'},
                        {data: 'check_out_time'},
                        {data: 'working_hours'},
                        {data: 'location'},
                        {data: 'dept_name'},
                        {data: 'is_approved'}
                    ],
                    "bDestroy": true,
                    "order": [[9, "asc"]],

                    "drawCallback": function( settings ) {
                        check_changed_text_boxes();
                    }
                });
            }

            $('#formFilter').on('submit', function (e) {
                e.preventDefault();
                let department = $('#department').val();
                let company = $('#company').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                load_dt(department, company, location, from_date, to_date);
                 closeOffcanvasSmoothly();
            });

            $('body').on('click', '.cb', function (){
                let id = $(this).data('id');

                let b = {};
                b["id"] = id;
                b["emp_name_with_initial"] = $(this).data('emp_name_with_initial');
                b["date"] = $(this).data('date');
                b["check_in_time"] = $(this).data('check_in_time');
                b["check_out_time"] = $(this).data('check_out_time');
                b["working_hours"] = $(this).data('working_hours');
                b["location_id"] = $(this).data('location_id');
                b["dept_id"] = $(this).data('dept_id');
                b["is_approved_int"] = $(this).data('is_approved_int');

                if($(this).is(':checked')){
                    if(jQuery.inArray(b, selected_cb) === -1){
                        selected_cb.push(b);

                        let selector = $('.cb[data-id="' + id + '"]');
                        selector.parent().parent().parent().css('background-color', '#f7c8c8');
                    }
                }else {
                    removeA(selected_cb, id)
                }
                //show_selected_po_nos(selected_cb)
            });

            $(document).on('click', '#approve',async function (e) {
                e.preventDefault();
                  var r = await Otherconfirmation("You want to Approve this ? ");
                if (r == true) {
                     $('.message_modal').html('');

                $('#approveModal').modal('show');

                //#btn-approve
                $('#btn-approve').on('click', function (e) {
                    e.preventDefault();

                    $('.error_msg').remove();

                    let save_btn = $(this);
                    let leave_type = $('#leave_type').val();

                    if(leave_type == ''){
                         Swal.fire({
                        position: "top-end",
                        icon: 'warning',
                        title: 'Please select leave type!',
                        showConfirmButton: false,
                        timer: 2500
                        });


                        return false;
                    }

                    save_btn.prop("disabled", true);
                    save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
                    $.ajax({
                        url: "lateAttendance_mark_as_late_approve",
                        method: "POST",
                        data: {
                            'selected_cb': selected_cb,
                            'leave_type': leave_type,
                            _token: $('input[name=_token]').val(),
                        },
                        success: function (data) {
                            if(data.status == true){
                                 const actionObj = {
                                            icon: 'fas fa-save',
                                            title: '',
                                            message:'Late Attendance Approved',
                                            url: '',
                                            target: '_blank',
                                            type: 'success'
                                        };
                                        const actionJSON = JSON.stringify(actionObj, null, 2);
                                        actionreload(actionJSON);
                                $('#approveModal').modal('hide');
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
                            save_btn.html('Approve' );
                        }
                    });

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

            // Select All functionality
            $('body').on('click', '#selectAll', function (){
                let isChecked = $(this).is(':checked');
                let table = $('#attendreporttable').DataTable();
                
                // Loop through all rows in the DataTable
                table.rows().every(function () {
                    let row = this.node();
                    let checkbox = $(row).find('.cb');
                    
                    // Only process checkboxes that exist and are not disabled
                    if (checkbox.length > 0) {
                        if (isChecked) {
                            // Check the checkbox
                            checkbox.prop('checked', true);
                            
                            // Trigger the same process as individual click
                            let id = checkbox.data('id');
                            
                            let b = {};
                            b["id"] = id;
                            b["emp_name_with_initial"] = checkbox.data('emp_name_with_initial');
                            b["date"] = checkbox.data('date');
                            b["check_in_time"] = checkbox.data('check_in_time');
                            b["check_out_time"] = checkbox.data('check_out_time');
                            b["working_hours"] = checkbox.data('working_hours');
                            b["location_id"] = checkbox.data('location_id');
                            b["dept_id"] = checkbox.data('dept_id');
                            b["is_approved_int"] = checkbox.data('is_approved_int');

                            // Add to selected_cb array if not already present
                            if (jQuery.inArray(b, selected_cb) === -1) {
                                selected_cb.push(b);
                            }
                            
                            // Apply background color
                            $(row).css('background-color', '#f7c8c8');
                            
                        } else {
                            // Uncheck the checkbox
                            checkbox.prop('checked', false);
                            
                            // Remove from selected_cb array
                            removeA(selected_cb, checkbox.data('id'));
                            
                            // Remove background color
                            $(row).css('background-color', '');
                        }
                    }
                });
            });
    
            function removeA(arr, id) {
                for (let i = 0; i < arr.length; i++) {
                    if (arr[i].id === id) {
                        arr.splice(i, 1);
                        break;
                    }
                }

                // Also remove the background color from the unselected row
                let selector = $('.cb[data-id="' + id + '"]');
                selector.closest('tr').css('background-color', '');
            }
        });
    </script>

@endsection

