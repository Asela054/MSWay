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
                         <span>Daily Summary Approve (OT & Production)</span>
                     </h1>
                 </div>
             </div>
         </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-12">
                            <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                    class="fas fa-filter mr-1"></i> Filter
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
                            <button type="button" class="btn btn-primary btn-sm float-right px-3" id="btn_approve_ot"><i
                                    class="fa-light fa-light fa-clipboard-check"></i>&nbsp;&nbsp;Approve OT</button><br>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" id="ot_table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>EMP ID</th>
                                        <th>ETF NO</th>
                                        <th>EMPLOYEE</th>
                                        <th>DATE</th>
                                        <th>DAY</th>
                                        <th>FROM</th>
                                        <th>TO</th>
                                        <th>OT TIME</th>
                                        <th>D/OT TIME</th>
                                        <th>T/OT TIME</th>
                                        <th>IS HOLIDAY</th>
                                    </tr>
                                </thead>
                                <tbody class="response">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

           @include('layouts.filter_menu_offcanves') 
        </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                            <div class="row align-items-center mb-4">
                                <div class="col-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input checkallocate" id="selectAll_production">
                                        <label class="form-check-label" for="selectAll_production">Select All Records</label>
                                    </div>
                                </div>
                                <div class="col-6 text-right">
                                    <button id="approve_att" class="btn btn-primary btn-sm px-3"><i
                                            class="fa-light fa-light fa-clipboard-check"></i>&nbsp;&nbsp;Approve All</button>
                                </div>
                            
                                
                            </div>

                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap" id="daliyproduction">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>EMPLOYEE ID</th>
                                            <th>EMPLOYEE</th>
                                            <th>DATE</th>
                                            <th>TARGET</th>
                                            <th>DAY PRODUCE</th>
                                            <th>PLUS/MINUS</th>
                                            <th>AVERAGE</th>
                                            <th>BONUS AMOUNT</th>
                                            <th>DAMAGE QTY</th>
                                        </tr>
                                    </thead>
                                    <tbody class="response2">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="employeeModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header p-2">
                            <h5 class="modal-title" id="staticBackdropLabel">Employee Production Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="employeeModalBody">
                            <div class="card">
                                <div class="card-body p-0">
                                    <table id="productionDetailsTable" class="table table-bordered table-sm mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ID</th>
                                                <th style="width: 40%;">Style</th>
                                                <th>Size</th>
                                                <th>Complete Status</th>
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

    </main>

@endsection

@section('script')

    <script>
        $(document).ready(function () {
            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#dailysummry').addClass('navbtnactive');

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
                    url: '{{url("employee_list_sel2")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company.val(),
                            department: department.val(),
                            location: location.val()
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
                    url: '{{url("location_list_sel2")}}',
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

             $('.response').html(
                '<tr>' +
                '<td colspan="13" class="text-center py-5">' +
                '<div class="d-flex flex-column align-items-center">' +
                '<i class="fas fa-filter fa-3x text-muted mb-3"></i>' +
                '<h4 class="text-muted mb-2">No Records Found</h4>' +
                '<p class="text-muted">Use the filter options to get records</p>' +
                '</div>' +
                '</td>' +
                '</tr>'
            );

            function load_table() {
                 let company = $('#company').val();
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                $('.response').html('');
                let btn = $('#btn-filter');
                btn.attr('disabled', true);
                btn.html('<i class="fa fa-spinner fa-spin"></i>');

                $.ajax({
                    url: "{{ route('get_ot_details') }}",
                    method: "POST",
                    data: {
                        company: company,
                        department: department,
                        employee: employee,
                        location: location,
                        from_date: from_date,
                        to_date: from_date,
                        _token: '{{csrf_token()}}'
                    },
                    success: function (res) {
                        btn.html('Filter');
                        btn.prop('disabled', false);

                         if ($.fn.DataTable.isDataTable('#ot_table')) {
                        $('#ot_table').DataTable().clear().destroy();
                    }
                    $('#ot_table tbody').empty();

                        let ot_data = res.ot_data;
                        let ot_data_html = '';
                        
                        if(ot_data.length > 0) {
                            ot_data.forEach(function(key, data) {
                                let is_approved = key.is_approved;
                                let obj = key.ot_breakdown;
                                let is_holiday = obj.is_holiday == 1 ? 'Yes' : 'No';
                                
                                let h_class = obj.is_morning ? 'bg-teal-light' : '';
                                
                                ot_data_html += '<tr class="'+h_class+'" >';
                                
                                if(is_approved == false){
                                    ot_data_html += '<td><input type="checkbox" class="cb" ' +
                                        'data-emp_id="'+obj.emp_id+'" ' +
                                        'data-date="'+obj.date+'" ' +
                                        ' /></td>';
                                }else{
                                    ot_data_html += '<td> <i class="fa fa-check text-success"> </i> </td>';
                                }
                                
                                ot_data_html += '<td>'+obj.emp_id+'</td>';
                                ot_data_html += '<td>'+obj.etf_no+'</td>';
                                ot_data_html += '<td>'+obj.name+'</td>';
                                ot_data_html += '<td>'+obj.date+'</td>';
                                ot_data_html += '<td>'+obj.day_name+'</td>';
                                ot_data_html += '<td>'+obj.from_24+'</td>';
                                ot_data_html += '<td>'+obj.to_24+'</td>';
                                ot_data_html += '<td>'+obj.hours +'</td>';
                                ot_data_html += '<td>'+obj.double_hours +'</td>';
                                ot_data_html += '<td>'+obj.triple_hours+'</td>';
                                ot_data_html += '<td>'+is_holiday+'</td>';
                                ot_data_html += '</tr>';
                            });
                        }
                        $('#ot_table tbody').html(ot_data_html);
                        
                        // Initialize DataTable with export buttons
                        $('#ot_table').DataTable({
                            destroy: true,
                            responsive: true,
                              dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                            "buttons": [{
                                    extend: 'csv',
                                    className: 'btn btn-success btn-sm',
                                    title: 'OT Approve Information',
                                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                                },
                                { 
                                    extend: 'pdf', 
                                    className: 'btn btn-danger btn-sm', 
                                    title: 'OT Approve Information', 
                                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                    orientation: 'landscape', 
                                    pageSize: 'legal', 
                                    customize: function(doc) {
                                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                    }
                                },
                                {
                                    extend: 'print',
                                    title: 'OT Approve  Information',
                                    className: 'btn btn-primary btn-sm',
                                    text: '<i class="fas fa-print mr-2"></i> Print',
                                    customize: function(win) {
                                        $(win.document.body).find('table')
                                            .addClass('compact')
                                            .css('font-size', 'inherit');
                                    },
                                },
                            ],
                            pageLength: 25,
                            order: [[1, 'desc']]
                        });
                        
                        $('.date_time').datetimepicker({
                            format:'Y-m-d H:i',
                            mask:false,
                        });
                    },
                    error: function(xhr, status, error) {
                        btn.html('Filter');
                        btn.prop('disabled', false);
                        console.error("Error:", error);
                    }
                });
            }

            $('#from_date').on('change', function() {
                let fromDate = $(this).val();
                $('#to_date').attr('min', fromDate); 
            });

            $('#to_date').on('change', function() {
                let toDate = $(this).val();
                $('#from_date').attr('max', toDate); 
            });

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                $('.info_msg').html('');

                 let employee = $('#employee').val();
                 let from_date = $('#from_date').val();

                load_table();

                load_dt_production(employee, from_date);
                closeOffcanvasSmoothly();

            });

            //document btn_approve_ot click
            $(document).on('click', '#btn_approve_ot', async function (e) {
                var r = await Otherconfirmation("You want to approve this ? ");
                if (r == true) {

                    let btn = $(this);

                    btn.attr('disabled', true);
                    btn.html('<i class="fa fa-spinner fa-spin"></i>');

                    let cb = $('.cb');
                    let ot_data = [];

                    cb.each(function (e1) {
                        let cb_obj = $(this);
                        if (cb_obj.is(':checked')) {
                            let emp_id = cb_obj.data('emp_id');
                            let date = cb_obj.data('date');

                            // Updated column references - now getting text content instead of input values
                            let from_cell = cb_obj.closest('tr').find('td:eq(6)');
                            let to_cell = cb_obj.closest('tr').find('td:eq(7)');
                            let hours_cell = cb_obj.closest('tr').find('td:eq(8)');
                            let double_hours_cell = cb_obj.closest('tr').find('td:eq(9)');
                            let triple_hours_cell = cb_obj.closest('tr').find('td:eq(10)');
                            let is_holiday_cell = cb_obj.closest('tr').find('td:eq(11)');

                            let from = from_cell.text();
                            let to = to_cell.text();
                            let hours = hours_cell.text();
                            let double_hours = double_hours_cell.text();
                            let triple_hours = triple_hours_cell.text();
                            let is_holiday = cb_obj.parent().parent().find('td:nth-child(12)');

                            let ot_data_obj = {
                                emp_id: emp_id,
                                date: date,
                                from: from,
                                to: to,
                                hours: hours,
                                //one_point_five_hours: one_point_five_hours,
                                double_hours: double_hours,
                                triple_hours: triple_hours,
                                is_holiday: is_holiday.text()
                            }

                            ot_data.push(ot_data_obj);

                        }

                    });

                    if (ot_data.length > 0) {
                        $(btn).html('<i class="fa fa-spinner fa-spin"></i>');
                        $(btn).prop('disabled', true);

                        $.ajax({
                            url: "{{ route('ot_approve_post') }}",
                            method: "POST",
                            data: {
                                ot_data: ot_data,
                                _token: '{{csrf_token()}}'
                            },
                            success: function (res) {
                                if (res.errors) {
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
                                if (res.success) {
                                    const actionObj = {
                                        icon: 'fas fa-save',
                                        title: '',
                                        message: res.success,
                                        url: '',
                                        target: '_blank',
                                        type: 'success'
                                    };
                                    const actionJSON = JSON.stringify(actionObj, null, 2);
                                    action(actionJSON);
                                }
                                btn.html('Approve');
                                btn.attr('disabled', false);
                            }
                        });
                    } else {
                        $('.info_msg').html('<div class="alert alert-danger">Please select at least one attendance</div>');
                        $('html, body').animate({
                            scrollTop: 100
                        }, 'fast');
                         btn.attr('disabled', false);
                    }
                }
            });

             $('#selectAll').click(function (e) {
                $('#ot_table').closest('table').find('td input:checkbox').prop('checked', this.checked);
            });


    function load_dt_production(employee, from_date) {
        $('#daliyproduction').DataTable({
                 destroy: true,
                 responsive: true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Production Daily Approve Information',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'Production Daily Approve Information', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'landscape', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Production Daily Approve Information',
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
                    [1, "desc"]
                ],
            ajax: {
                url:  "{{url('/opma_daliyproductionsummarygenerate')}}",
                type: 'POST',
                data: { 
                        _token: '{{ csrf_token() }}',
                    employee: employee, 
                    from_date: from_date
                },
            },
            columns: [
                    {
                    data: null,
                    name: 'checkbox',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (row.status == 0) {
                                return '<input type="checkbox" class="row-checkbox selectCheck removeIt" data-id="' + row.emp_auto_id + '">';
                            } else if (row.status == 1) {
                                return '<i class="fa fa-check text-success"></i>';
                            }
                    }
                },
                { data: 'emp_id', name: 'emp_id' },
                {
                    data: 'emp_name_with_initial', 
                    name: 'emp_name_with_initial',
                    render: function(data, type, row) {
                        return '<div style="display: flex; justify-content: space-between; align-items: center;">' +
                            '<span>' + data + '</span>' +
                            '<i class="fa fa-eye text-primary employee-info-icon" style="cursor: pointer; margin-left: 10px;" data-emp-id="' + row.emp_id + '" data-emp-date="' + row.recorddate + '"></i>' +
                            '</div>';
                    }
                },
                { data: 'recorddate', name: 'recorddate' },
                { data: 'total_target', name: 'total_target' },
                { data: 'total_produce_qty', name: 'total_produce_qty' },
                { data: 'total_difference', name: 'total_difference' },
                { 
                    data: 'daily_aveg', 
                    name: 'daily_aveg',
                    className: 'text-right',
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            return data + '%';
                        }
                        return data;
                    }
                },
                { data: 'total_amount', name: 'total_amount' },
                { data: 'total_damage', name: 'total_damage' }
                
            ]
        });
    }

    var selectedRowIdsapprove = [];

    $('#approve_att').click(async function () {
        var r = await Otherconfirmation("You want to Approve this ? ");
        if (r == true) {

                var from_date = $('#from_date').val();

                    selectedRowIdsapprove = [];
                    $('#daliyproduction tbody .selectCheck:checked').each(function () {
                        var rowData = $('#daliyproduction').DataTable().row($(this).closest('tr')).data();
                        if (rowData) {
                            selectedRowIdsapprove.push({
                                empid: rowData.emp_id,
                                emp_name: rowData.emp_name_with_initial,
                                total_target: rowData.total_target,
                                total_produce_qty: rowData.total_produce_qty,
                                total_difference: rowData.total_difference,
                                daily_aveg: rowData.daily_aveg,
                                total_amount: rowData.total_amount,
                                total_damage: rowData.total_damage,
                            });
                        }
                    });

                        if (selectedRowIdsapprove.length > 0) {
                                console.log(selectedRowIdsapprove);
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                })

                                $.ajax({
                                    url: '{!! route("opma_approvedailysummary") !!}',
                                    type: 'POST',
                                    dataType: "json",
                                    data: {
                                        dataarry: selectedRowIdsapprove,
                                        from_date: from_date
                                    },
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
                                            action(actionJSON);
                                        }

                                    }
                                })
                            } else {
                                Swal.fire({
                                    position: "top-end",
                                    icon: 'warning',
                                    title: 'Select Rows to Final Approve!',
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                            }
        }
    });


    $('#selectAll_production').click(function (e) {
        $('#daliyproduction').closest('table').find('td input:checkbox').prop('checked', this.checked);
    });

    $(document).on('click', '.employee-info-icon', function() {
        var empId = $(this).data('emp-id');
        var empDate = $(this).data('emp-date');

        $('#employeeModal').modal('show');
        
            $.ajax({
            url: '{!! route("opma_getEmployeeProductionDetails") !!}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                emp_id: empId,
                date: empDate
            },
            success: function(response) {
                if (response.success) {
                    displayProductionDetails(response.productions);
                }
            },
            error: function(xhr) {
                $('#employeeModalBody').html('<div class="alert alert-danger">Error: ' + xhr.statusText + '</div>');
            }
        });
    });


    $('.response2').html(
                '<tr>' +
                '<td colspan="10" class="text-center py-5">' +
                '<div class="d-flex flex-column align-items-center">' +
                '<i class="fas fa-filter fa-3x text-muted mb-3"></i>' +
                '<h4 class="text-muted mb-2">No Records Found</h4>' +
                '<p class="text-muted">Use the filter options to get records</p>' +
                '</div>' +
                '</td>' +
                '</tr>'
    );

   });

    function displayProductionDetails(productions) {
            // Show the employee info card
            $('#employeeInfoCard').show();
            
            var table = $('#productionDetailsTable').DataTable();
            if (table) {
                table.destroy();
            }
            
            $('#productionDetailsTable tbody').empty();
            
            if (productions && productions.length > 0) {
                $.each(productions, function(i, production) {
                    var row = `
                        <tr>
                            <td>${production.id }</td>
                            <td>${production.style_title || ' '}</td>
                            <td>${production.size_name || ' '}</td>
                            <td>
                                <span>
                                    ${production.complete_status == 1 ? 'Completed' : 'Not Completed'}
                                </span>
                            </td>
                        </tr>
                    `;
                    $('#productionDetailsTable tbody').append(row);
                });
            }
            
            // Initialize DataTable
            $('#productionDetailsTable').DataTable({
                "pageLength": 10,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"t>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });
    }
    </script>

@endsection