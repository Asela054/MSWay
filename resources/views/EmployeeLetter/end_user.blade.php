
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
                        <span>End User Letter</span>
                    </h1>
                </div>
            </div>
        </div>    

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body">
                    <form method="POST" action="{{ route('end_user_letterinsert') }}"  class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="form-row mb-1">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee_f" id="employee_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                    <label class="small font-weight-bold text-dark">Agreement Date</label>
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="date" id="effect_date" name="effect_date" class="form-control form-control-sm" required>
                                    </div>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Company Representative</label>
                                <select name="employee_r" id="employee_r" class="form-control form-control-sm">
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="recordOption" id="recordOption" value="1">
                        <input type="hidden" name="recordID" id="recordID" value="">
                        <div class="form-group mt-4 text-center">
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-sm fa-pull-right px-4"><i
                                    class="fas fa-plus"></i>&nbsp;&nbsp;Add</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-dark">ASSIGNED DEVICES</h6>
                            <table class="table table-bordered table-sm" id="deviceTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>DEVICE TYPE</th>
                                        <th>MODEL NUMBER</th>
                                        <th>SERIAL NUMBER</th>
                                        <th>ASSIGNED DATE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2 main_card">
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%" id="dataTable">
                                <thead>
                                <tr>
                                    <th>EMP ID </th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>DEPARTMENT</th>
                                    <th>COMPANY</th>
                                    <th>AGREEMENT DATE</th>
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

    </main>
    
@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#employee_menu_link').addClass('active');
            $('#employee_menu_link_icon').addClass('active');
            $('#appointmentletter').addClass('navbtnactive');

            let company_f = $('#company_f');
            let department_f = $('#department_f');
            let employee_f = $('#employee_f');
            let employee_r = $('#employee_r');

            company_f.select2({
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

            department_f.select2({
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
                            company: company_f.val()
                        }
                    },
                    cache: true
                }
            });

            employee_f.select2({
                placeholder: 'Select a Employee',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("employee_list_letter")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company_f.val(),
                            department: department_f.val()
                        }
                    },
                    cache: true
                }
            });

            employee_r.select2({
                placeholder: 'Select a Company Representative',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("employee_list_letter")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company_f.val()
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
                        title: 'End User Letter',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'End User Letter', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'portrait', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'End User Letter',
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
                    "url": "{!! route('end_user_letterlist') !!}",
                   
                },
                columns: [
                    { data: 'emp_id', name: 'emp_id' },
                    { data: 'employee_display', name: 'employee_display' },
                    { data: 'department', name: 'department' },
                    { data: 'companyname', name: 'companyname' },
                    { data: 'effect_date', name: 'effect_date' },
                    {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return '<div style="text-align: right;">' + data + '</div>';
                    }
                },
                ],
                
            });

            // edit function
            $(document).on('click', '.edit',async function () {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
                var id = $(this).attr('id');
            
                $('#form_result').html('');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })

                $.ajax({
                    url: '{!! route("end_user_letteredit") !!}',
                    type: 'POST',
                    dataType: "json",
                    data: {id: id },
                    success: function (data) {
                        $('#effect_date').val(data.result.effect_date);
                        // Set Company
                        if (data.result.company_id && data.result.company_name) {
                            var companyOption = new Option(data.result.company_name, data.result.company_id, true, true);
                            $('#company_f').append(companyOption).trigger('change');
                        }
                        // Set Department
                        if (data.result.department_id && data.result.department_name) {
                            var departmentOption = new Option(data.result.department_name, data.result.department_id, true, true);
                            $('#department_f').append(departmentOption).trigger('change');
                        }
                        // Set Employee
                        if (data.result.emp_id && data.result.emp_name) {
                            var employeeOption = new Option(data.result.emp_name, data.result.emp_id, true, true);
                            $('#employee_f').append(employeeOption).trigger('change');
                        }
                        // Set Company Representative
                        if (data.result.rep_emp_id && data.result.rep_emp_name) {
                            var repEmployeeOption = new Option(data.result.rep_emp_name, data.result.rep_emp_id, true, true);
                            $('#employee_r').append(repEmployeeOption).trigger('change');
                        }
                        
                        $('#recordID').val(id);
                        $('#recordOption').val(2);
                    }
                })
                }
            });

            var user_id;

            $(document).on('click', '.delete',async function () {
                 var r = await Otherconfirmation("You want to remove this ? ");
                if (r == true) {
                     user_id = $(this).attr('id');
                     $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                        })
                    $.ajax({
                        url: '{!! route("end_user_letterdelete") !!}',
                            type: 'POST',
                            dataType: "json",
                            data: {id: user_id },
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
                    })
                }
               
            });

            $('#employee_f').change(function () {
                let empId = $(this).val();

                if (empId) {
                    $.ajax({
                        url: '/get-employee-devices/' + empId,
                        type: 'GET',
                        success: function (response) {
                            let tableBody = $('#deviceTable tbody');
                            tableBody.empty(); // Clear previous data

                            if (response.length > 0) {
                                $.each(response, function (index, device) {
                                    tableBody.append(`
                                        <tr>
                                            <td>${device.device_type}</td>
                                            <td>${device.model_number}</td>
                                            <td>${device.serial_number}</td>
                                            <td>${device.assigned_date}</td>
                                        </tr>
                                    `);
                                });
                            } else {
                                tableBody.append('<tr><td colspan="4" class="text-center">No devices assigned</td></tr>');
                            }
                        }
                    });
                }
            });

            $(document).on('click', '.print', function (e) {
            e.preventDefault(); // Prevent default behavior if it's a link
            
            var id = $(this).attr('id');
            $('#form_result').html('');

            // Open the tab or window first
            var newTab = window.open('', '_blank');
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{!! route("end_user_letterprintdata") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function (data) {
                    var pdfBlob = base64toBlob(data.pdf, 'application/pdf');
                    var pdfUrl = URL.createObjectURL(pdfBlob);

                    // Write content to the opened tab
                    newTab.document.write('<html><body><embed width="100%" height="100%" type="application/pdf" src="' + pdfUrl + '"></body></html>');
                },
                error: function () {
                    console.log('PDF request failed.');
                    newTab.document.write('<html><body><p>Failed to load PDF. Please try again later.</p></body></html>');
                }
            });
        });

        });

        function deactive_confirm() {
        return confirm("Are you sure you want to deactive this?");
        }

        function active_confirm() {
            return confirm("Are you sure you want to active this?");
        }
        function base64toBlob(base64Data, contentType) {
        var byteCharacters = atob(base64Data);
        var byteArrays = [];

        for (var offset = 0; offset < byteCharacters.length; offset += 512) {
            var slice = byteCharacters.slice(offset, offset + 512);

            var byteNumbers = new Array(slice.length);
            for (var i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }

            var byteArray = new Uint8Array(byteNumbers);
            byteArrays.push(byteArray);
        }

        return new Blob(byteArrays, { type: contentType });
        }
    </script>

@endsection