<?php $page_stitle = 'Report on Employee Clearance '.$company_name.''; ?>
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header">
            <div class="container-fluid d-none d-sm-block shadow">
                @include('layouts.reports_nav_bar')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-file-contract"></i></div>
                        <span>Clearance Report</span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                    class="fas fa-filter mr-1"></i> Filter
                                Records</button><br><br>
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-success btn-sm float-right px-3 ml-2" type="button" id="btn-done" style="display: none;">
                                <i class="fas fa-check mr-1"></i> Done
                            </button>
                            <br><br>
                        </div>
                        <div class="col-md-12">
                            <div id="employee-info-section" class="alert alert-info" style="display: none;">
                                <h5 class="mb-0"><strong>Employee: </strong><span id="employee-name-display"></span></h5>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap"
                                    style="width: 100%" id="clearancereporttable">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;"></th>
                                            <th>DESCRIPTION</th>
                                            <th>QUANTITY/BALANCE</th>
                                            <th>AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                {{ csrf_field() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

              <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
                  aria-labelledby="offcanvasRightLabel">
                  <div class="offcanvas-header">
                      <h2 class="offcanvas-title font-weight-bolder" id="offcanvasRightLabel">Records Filter Options
                      </h2>
                      <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                          <span aria-hidden="true" class="h1 font-weight-bolder">&times;</span>
                      </button>
                  </div>
                  <div class="offcanvas-body">
                      <ul class="list-unstyled">
                          <form class="form-horizontal" id="formFilter">
                              <li class="mb-2">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Company</label>
                                      <select name="company" id="company" class="form-control form-control-sm">
                                      </select>
                                  </div>
                              </li>
                              <li class="mb-2">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Department</label>
                                      <select name="department" id="department" class="form-control form-control-sm">
                                      </select>
                                  </div>
                              </li>
                              <li class="mb-2">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Employee*</label>
                                      <select name="employee" id="employee" class="form-control form-control-sm" required>
                                      </select>
                                  </div>
                              </li>
                              <li>
                                  <div class="col-md-12 d-flex justify-content-between">
                                      
                                      <button type="button" class="btn btn-danger btn-sm filter-btn px-3"
                                          id="btn-reset">
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

    </main>

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#report_menu_link').addClass('active');
            $('#report_menu_link_icon').addClass('active');
            $('#employeedetailsreport').addClass('navbtnactive');

            let company = $('#company');
            let department = $('#department');
            let employee = $('#employee');
            

            company.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("company_list_sel2")}}',
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

            department.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("department_list_sel2")}}',
                    dataType: 'json',
                    data: function(params) {
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
                            department: department.val()
                        }
                    },
                    cache: true
                }
            });

            function load_dt(department, employee){
                $('#clearancereporttable').DataTable({
                    "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    "paging": false,
                    "searching": false,
                    "info": false,
                    dom: "<'row'<'col-sm-12 mb-2'B>>" + "<'row'<'col-sm-12'tr>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Clearance Report',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                            exportOptions: {
                                columns: [1, 2, 3]
                            }
                        },
                        {
                            extend: 'pdf',
                            className: 'btn btn-danger btn-sm',
                            title: 'Clearance Report',
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'portrait',
                            pageSize: 'legal',
                            exportOptions: {
                                columns: [1, 2, 3]
                            },
                            customize: function (doc) {
                                doc.content[1].table.widths = ['*', 'auto', 'auto'];
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Clearance Report',
                            className: 'btn btn-primary btn-sm',
                            text: '<i class="fas fa-print mr-2"></i> Print',
                            exportOptions: {
                                columns: [1, 2, 3]
                            },
                            customize: function (win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                    ],
                    ajax: {
                        "url": "{{url('/clearance_report_list')}}",
                        "data": {
                            'department': department,
                            'employee': employee
                        },
                        "dataSrc": function(json) {
                            if (json.aaData && json.aaData.length > 0) {
                                let employeeName = json.aaData[0].description.replace(/<\/?strong>/g, '');
                                $('#employee-name-display').html(employeeName);
                                $('#employee-info-section').show();
                                json.aaData.shift();
                                
                                let hasDevices = json.aaData.some(row => row.is_device_row === true);
                                if (hasDevices) {
                                    $('#btn-done').show();
                                } else {
                                    $('#btn-done').hide();
                                }
                            }
                            return json.aaData;
                        }
                    },
                    columns: [
                        {
                            data: null,
                            orderable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                if (row.is_device_row === true && row.device_id) {
                                    let checked = row.device_status == 2 ? 'checked' : '';
                                    return `<input type="checkbox" class="device-checkbox" data-device-id="${row.device_id}" ${checked}>`;
                                }
                                return '';
                            }
                        },
                        {
                            data: 'description',
                            render: function(data, type, row) {
                                return data;
                            }
                        },
                        {
                            data: 'quantity_balance',
                            className: 'text-center'
                        },
                        {
                            data: 'amount',
                            className: 'text-right'
                        }
                    ],
                    "createdRow": function(row, data, dataIndex) {
                        if (data.is_title) {
                            $(row).addClass('table-section-header');
                            $(row).find('td').css({
                                'background-color': '#f8f9fa',
                                'font-weight': 'bold'
                            });
                        }
                    },
                    "bDestroy": true,
                    "ordering": false
                });
            }

            $(document).on('change', '.device-checkbox', function() {
                let isChecked = $(this).is(':checked');
                let deviceId = $(this).data('device-id');
                
                console.log('Device ' + deviceId + ' checkbox: ' + (isChecked ? 'checked' : 'unchecked'));
            });

            $('#btn-done').on('click', async function() {
                let checkedDevices = [];
                let uncheckedDevices = [];
                
                $('.device-checkbox').each(function() {
                    let deviceId = $(this).data('device-id');
                    if ($(this).is(':checked')) {
                        checkedDevices.push(deviceId);
                    } else {
                        uncheckedDevices.push(deviceId);
                    }
                });

                if (checkedDevices.length === 0 && uncheckedDevices.length === 0) {
                    const actionObj = {
                        icon: 'fas fa-warning',
                        title: '',
                        message: 'No devices to update',
                        url: '',
                        target: '_blank',
                        type: 'warning'
                    };
                    const actionJSON = JSON.stringify(actionObj, null, 2);
                    action(actionJSON);
                    return;
                }

                var r = await Otherconfirmation("You want to update the clearance status of selected devices?");
                if (r == true) {
                    let save_btn = $(this);
                    save_btn.prop("disabled", true);
                    save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...');
                    
                    $.ajax({
                        url: "{{url('/update_device_clearance')}}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            checked_device_ids: checkedDevices,
                            unchecked_device_ids: uncheckedDevices
                        },
                        success: function(response) {
                            if (response.success) {
                                const actionObj = {
                                    icon: 'fas fa-check',
                                    title: '',
                                    message: 'Device clearance status updated successfully!',
                                    url: '',
                                    target: '_blank',
                                    type: 'success'
                                };
                                const actionJSON = JSON.stringify(actionObj, null, 2);
                                actionreload(actionJSON);
                            } else {
                                const actionObj = {
                                    icon: 'fas fa-warning',
                                    title: '',
                                    message: 'Error: ' + response.message,
                                    url: '',
                                    target: '_blank',
                                    type: 'danger'
                                };
                                const actionJSON = JSON.stringify(actionObj, null, 2);
                                action(actionJSON);
                            }
                            save_btn.prop("disabled", false);
                            save_btn.html('<i class="fas fa-check mr-1"></i> Done');
                        },
                        error: function() {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: 'An error occurred while updating devices',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                            save_btn.prop("disabled", false);
                            save_btn.html('<i class="fas fa-check mr-1"></i> Done');
                        }
                    });
                }
            });

            $('#btn-reset').on('click', function() {
                $('#formFilter')[0].reset();
                $('#company').val(null).trigger('change');
                $('#department').val(null).trigger('change');
                $('#employee').val(null).trigger('change');
                $('#employee-info-section').hide(); 
                $('#clearancereporttable').DataTable().clear().draw();
            });

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let department = $('#department').val();
                let employee = $('#employee').val();

                load_dt(department, employee);
                closeOffcanvasSmoothly();
            });
        });
    </script>

@endsection

