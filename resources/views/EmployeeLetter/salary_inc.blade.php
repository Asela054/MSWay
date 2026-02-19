
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
                        <span>Salary Increment Letter</span>
                    </h1>
                </div>
            </div>
        </div>    

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body">
                    <form method="POST" action="{{ route('salary_incletterinsert') }}"  class="form-horizontal">
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
                                <select name="employee" id="employee_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Job Title</label>
                                <select id="jobtitle" name="jobtitle" class="form-control form-control-sm" required>
                                    <option value="">Select Job Title</option>
                                    @foreach ($job_titles as $job_title)
                                        <option value="{{$job_title->id}}" >{{$job_title->title}}</option>
                                    @endforeach
                                </select>
                            </div>

                            
                            <div class="col-md-3 ">
                                <label class="small font-weight-bold text-dark">Previous Salary</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" id="basic_salary" name="basic_salary" class="form-control form-control-sm" required readonly>
                                </div>
                            </div>

                            <div class="col-md-3 ">
                                <label class="small font-weight-bold text-dark">New Salary</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" id="new_salary" name="new_salary" class="form-control form-control-sm" required >
                                </div>
                            </div>

                            <div class="col-md-3 ">
                                <label class="small font-weight-bold text-dark">Effective Date</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="date" name="date" class="form-control form-control-sm" placeholder="yyyy-mm-dd" required>
                                </div>
                            </div>

                            <div class="col-md-3 ">
                                <label class="small font-weight-bold text-dark">Comment</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" id="comment1" name="comment1" class="form-control form-control-sm" placeholder="review">
                                </div>
                            </div>

                            <!-- <div class="col-md-3 ">
                                <label class="small font-weight-bold text-dark">Comment 02</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" id="comment2" name="comment2" class="form-control form-control-sm" placeholder="review">
                                </div>
                            </div> -->
                           
                        </div>
                        <input type="hidden" name="recordOption" id="recordOption" value="1">
                        <input type="hidden" name="recordID" id="recordID" value="">
                        <div class="form-group mt-4 text-center">
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-sm fa-pull-right px-4">
                                <i class="fas fa-plus"></i>&nbsp;&nbsp;Add
                            </button>
                        </div>
                    </form>
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
                                    <th>JOB TITLE</th>
                                    <th>COMPANY</th>
                                    <th>PREVIOUS SALARY</th>
                                    <th>NEW SALARY</th>
                                    <th class="text-right">ACTIONS</th>
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
                    url: '{{url("employee_list_sel2")}}',
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

            $('#dataTable').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Salary Increment Letter',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'Salary Increment Letter', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'portrait', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Salary Increment Letter',
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
                    "url": "{!! route('salary_incletterlist') !!}",
                   
                },
                columns: [
                    { data: 'emp_id', name: 'emp_id' },
                    { data: 'employee_display', name: 'employee_display' },
                    { data: 'department', name: 'department' },
                    { data: 'emptitle', name: 'emptitle' },
                    { data: 'companyname', name: 'companyname' },
                    { data: 'pre_salary', name: 'pre_salary' },
                    { data: 'new_salary', name: 'new_salary' },
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

            function resetForm() {
                $('#company_f').val(null).trigger('change');
                $('#department_f').val(null).trigger('change');
                $('#employee_f').val(null).trigger('change');
                $('#jobtitle').val('');
                $('#basic_salary').val('');
                $('#new_salary').val('');
                $('#date').val('');
                $('#comment1').val('');
                $('#recordID').val('');
                $('#recordOption').val(1);
                $('#submitBtn').html('<i class="fas fa-plus"></i>&nbsp;&nbsp;Add');
            }

            // Modify the form submit to reload DataTable and reset form
            $('form').on('submit', function() {
                // After successful submission (you may need to convert to AJAX)
                setTimeout(function() {
                    $('#dataTable').DataTable().ajax.reload();
                    resetForm();
                }, 1000);
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
                        url: '{!! route("salary_incletteredit") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                        success: function (data) {
                            var companyOption = new Option(data.result.company_name, data.result.company_id, true, true);
                            company_f.append(companyOption).trigger('change');
                            
                            var deptOption = new Option(data.result.department_name, data.result.department_id, true, true);
                            department_f.append(deptOption).trigger('change');
                            
                            var empOption = new Option(data.result.employee_name, data.result.employee_id, true, true);
                            employee_f.append(empOption).trigger('change');

                            $('#jobtitle').val(data.result.jobtitle);
                            $('#basic_salary').val(data.result.pre_salary);
                            $('#new_salary').val(data.result.new_salary);
                            $('#date').val(data.result.date);
                            $('#comment1').val(data.result.comment1);
                            // $('#comment2').val(data.result.comment2); // Uncommented if needed
                            
                            $('#recordID').val(id);
                            $('#recordOption').val(2);
                            $('#submitBtn').html('<i class="fas fa-edit"></i>&nbsp;&nbsp;Update');
                        },
                        error: function(xhr, status, error) {
                            console.error('Edit failed:', error);
                            alert('Failed to load data for editing');
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
                        url: '{!! route("salary_incletterdelete") !!}',
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
                url: '{!! route("salary_incletterprintdata") !!}',
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

        //job title & basic_salary insert filter
        $('#employee_f').change(function () {
            var employee_f = $(this).val();
            if (employee_f !== '') {
                $.ajax({
                    url: '{!! route("salary_inclettergetdetails", ["id" => "id"]) !!}'.replace('id', employee_f),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        // Job title handling
                        if (data.jobTitle && data.jobTitle.length > 0) {
                            $('#jobtitle').empty().append('<option value="' + data.jobTitle[0].id + '">' + data.jobTitle[0].title + '</option>');
                        } else {
                            $('#jobtitle').empty().append('<option value="">No job title found</option>');
                        }

                        // basic_salary handling
                        if (data.basic_salary && data.basic_salary.length > 0) {
                            $('#basic_salary').val(data.basic_salary[0].basic_salary);
                        } else {
                            $('#basic_salary').val('');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                        $('#jobtitle').empty().append('<option value="">Error loading job title</option>');
                        $('#basic_salary').val('Error loading basic salary');
                    }
                });
            } else {
                $('#jobtitle').empty().append('<option value="">Select Job Title</option>');
                $('#basic_salary').val('');
            }
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