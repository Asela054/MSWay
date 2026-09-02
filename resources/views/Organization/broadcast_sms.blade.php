@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
             @include('layouts.corporate_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-file-contract"></i></div>
                    <span>Broadcast Messages</span>
                </h1>
            </div>
        </div>
    </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body p-0 p-2">
                      <div class="row">
                        <div class="col-12">
                             <button class="btn btn-primary btn-sm fa-pull-right" type="button" id="btn-new-broadcast"><i class="fas fa-paper-plane mr-1"></i> Send Broadcast</button>
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                         <div class="col-md-12">
                              <button class="btn btn-warning btn-sm filter-btn  float-right px-3" type="button"
                                data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                    class="fas fa-filter mr-1"></i> Filter Records</button>
                         </div><br><br>

                          <div class="col-12">
                            <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="emptable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>DATE</th>
                                        <th>TYPE</th>
                                        <th>DEPARTMENT</th>
                                        <th>EMPLOYEE</th>
                                        <th>MESSAGE</th>
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

           <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
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
                                  <label class="small font-weight-bolder text-dark">Company*</label>
                                <select name="company" id="company" class="form-control form-control-sm" required>
                                </select>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                 <label class="small font-weight-bolder text-dark">Department*</label>
                                <select name="department" id="department" class="form-control form-control-sm" required>
                                </select>
                              </div>
                          </li>
                          <li>
                              <div class="col-md-12 d-flex justify-content-between">
                                  
                                  <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
                                      <i class="fas fa-redo mr-1"></i> Reset
                                  </button>
                                  <button type="submit" class="btn btn-primary btn-sm filter-btn px-3" id="btn-filter">
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

    <div class="modal fade" id="new_broadcast_modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="newBroadcastLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="newBroadcastLabel">Send Broadcast Message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formBroadcastSetup">
                        <div class="form-group mb-2">
                            <label class="small font-weight-bolder text-dark">Company*</label>
                            <select name="bc_company" id="bc_company" class="form-control form-control-sm" required>
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label class="small font-weight-bolder text-dark d-block">Send To*</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input bc-type-radio" type="radio" name="bc_type" id="bc_type_1" value="1" checked>
                                <label class="form-check-label" for="bc_type_1">All Departments (Everyone in Company)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input bc-type-radio" type="radio" name="bc_type" id="bc_type_2" value="2">
                                <label class="form-check-label" for="bc_type_2">A Selected Department (All Employees)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input bc-type-radio" type="radio" name="bc_type" id="bc_type_3" value="3">
                                <label class="form-check-label" for="bc_type_3">Selected Employees</label>
                            </div>
                        </div>

                        <div class="form-group mb-2 bc_department_wrap d-none">
                            <label class="small font-weight-bolder text-dark">Department*</label>
                            <select name="bc_department" id="bc_department" class="form-control form-control-sm">
                            </select>
                        </div>

                        <div class="form-group mb-2 bc_employees_wrap d-none">
                            <label class="small font-weight-bolder text-dark">Employees*</label>
                            <select name="bc_employees[]" id="bc_employees" class="form-control form-control-sm" multiple="multiple">
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label class="small font-weight-bolder text-dark">Message*</label>
                            <textarea id="bc_message" name="bc_message" class="form-control form-control-sm" rows="5" maxlength="459" placeholder="Type your message here..." required></textarea>
                            <small class="form-text text-muted"><span id="bc_char_count">0</span>/459 characters</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-danger btn-sm px-3" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btn-submit-broadcast">
                        <i class="fas fa-paper-plane mr-1"></i> Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <script>
        $(document).ready(function () {

        $('#organization_menu_link').addClass('active');
        $('#organization_menu_link_icon').addClass('active');
        $('#broadcastlink').addClass('navbtnactive');

            let company = $('#company');
            let department = $('#department');

            company.select2({
                placeholder: 'Select a Company',
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
                placeholder: 'Select a Department',
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

        $('#company').on('change', function() {
            var companyId = $(this).val();
            if (companyId) {
                $.ajax({
                    url: '/getdepartments/' + companyId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#department').empty(); 
                        $('#department').append('<option value="">Please Select</option>');
                        $('#department').append('<option value="All">All Departments</option>');

                        $.each(data, function(key, department) {
                            $('#department').append('<option value="' + department.id + '">' + department.name + '</option>');
                        });
                    }
                });
            } else {
                $('#department').empty();
                $('#department').append('<option value="">Please Select</option>');
            }
        });

         $('#btn-reset').on('click', function () {
            $('#formFilter')[0].reset();
            $('#company').val(null).trigger('change');
            $('#department').val(null).trigger('change');
            $('#emptable').DataTable().ajax.reload();
        });

        var empTable = $('#emptable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Broadcast Messages',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    title: 'Broadcast Messages',
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape',
                    pageSize: 'legal',
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Broadcast Messages',
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
                url: scripturl + "/broadcastsms_list.php",
                type: "POST",
                data: function(d) {
                    d.company = company.val();
                    d.department = department.val();
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                },
            },
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'type_label',
                    name: 'type_label'
                },
                {
                    data: 'department_name',
                    name: 'department_name'
                },
                {
                    data: 'employee_label',
                    name: 'employee_label'
                },
                {
                    data: 'message',
                    name: 'message'
                }
            ],
            drawCallback: function(settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        $('#formFilter').on('submit', function (e) {
            e.preventDefault();
            empTable.ajax.reload();
        });


        let bcCompany = $('#bc_company');
        let bcDepartment = $('#bc_department');
        let bcEmployees = $('#bc_employees');

        bcCompany.select2({
            placeholder: 'Select a Company',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#new_broadcast_modal'),
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

        bcDepartment.select2({
            placeholder: 'Select a Department',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#new_broadcast_modal'),
            ajax: {
                url: '{{url("department_list_sel2")}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1,
                        company: bcCompany.val()
                    }
                },
                cache: true
            }
        });

        bcEmployees.select2({
            placeholder: 'Search and select employees',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#new_broadcast_modal'),
            ajax: {
                url: '{{url("employee_list_sel2")}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1,
                        company: bcCompany.val(),
                        department: bcDepartment.val()
                    }
                },
                cache: true
            }
        });

        // open the broadcast modal
        $('#btn-new-broadcast').on('click', function () {
            $('#formBroadcastSetup')[0].reset();
            bcCompany.val(null).trigger('change');
            bcDepartment.val(null).trigger('change');
            bcEmployees.val(null).trigger('change');
            $('#bc_type_1').prop('checked', true);
            $('#bc_char_count').text('0');
            toggleBroadcastTypeFields('1');
            $('#new_broadcast_modal').modal('show');
        });

        // show only the field relevant to the selected type - the other one stays hidden
        $('.bc-type-radio').on('change', function () {
            toggleBroadcastTypeFields($(this).val());
        });

        function toggleBroadcastTypeFields(type) {
            if (type == '1') {
                // All Departments - no department or employee picker needed
                $('.bc_department_wrap').addClass('d-none');
                $('.bc_employees_wrap').addClass('d-none');
            } else if (type == '2') {
                // A Selected Department - only the department picker
                $('.bc_department_wrap').removeClass('d-none');
                $('.bc_employees_wrap').addClass('d-none');
                bcEmployees.val(null).trigger('change');
            } else {
                // Selected Employees - only the employee picker, department stays hidden
                $('.bc_department_wrap').addClass('d-none');
                $('.bc_employees_wrap').removeClass('d-none');
                bcDepartment.val(null).trigger('change');
            }
        }

        // live character count
        $('#bc_message').on('input', function () {
            $('#bc_char_count').text($(this).val().length);
        });

        // submit -> validate then send the SMS
        $('#btn-submit-broadcast').on('click', function () {
            let type = $('input[name="bc_type"]:checked').val();
            let message = $('#bc_message').val().trim();

            if (!bcCompany.val()) {
                alert('Please select a company.');
                return;
            }
            if (type == '2' && !bcDepartment.val()) {
                alert('Please select a department.');
                return;
            }
            if (type == '3' && (!bcEmployees.val() || bcEmployees.val().length === 0)) {
                alert('Please select at least one employee.');
                return;
            }
            if (!message) {
                alert('Please type a message.');
                return;
            }

            let payload = {
                _token: '{{ csrf_token() }}',
                company: bcCompany.val(),
                type: type,
                department: (type == '2') ? bcDepartment.val() : null,
                employees: (type == '3') ? bcEmployees.val() : null,
                message: message
            };

            let $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sending...');

            $.ajax({
                url: "{{ route('broadcast_messages_send') }}",
                type: 'POST',
                dataType: 'json',
                data: payload,
                success: function (data) {
                    $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Submit');
                    $('#new_broadcast_modal').modal('hide');
                    $('#bc_message').val('');
                    $('#bc_char_count').text('0');
                    location.reload();
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
                        empTable.ajax.reload();
                    }
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Submit');
                    let msg = 'Something went wrong while sending the message.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                }
            });
        });

        });

    </script>

@endsection