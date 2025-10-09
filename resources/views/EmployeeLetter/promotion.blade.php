
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
                        <span>Promotion Letter</span>
                    </h1>
                </div>
            </div>
        </div>    

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body">
                    <form method="POST" action="{{ route('promotionletterinsert') }}"  class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="form-row mb-1">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm" required>
                                    <option value="">Please Select</option>
                                    @foreach ($companies as $company){
                                        <option value="{{$company->id}}" data-deptid="{{$company->id}}" >{{$company->name}}</option>
                                    }  
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="old_department" id="old_department" class="form-control form-control-sm" required>
                                    <option value="">Please Select</option>
                                    @foreach ($departments as $department){
                                        <option value="{{$department->id}}" >{{$department->name}}</option>
                                    }  
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee_f" id="employee_f" class="form-control form-control-sm" required>
                                    <option value="">Please Select</option>
                                    @foreach ($employees as $employee){
                                        <option value="{{$employee->id}}" >{{$employee->emp_name_with_initial}}</option>
                                    }  
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Current Job Title</label>
                                <select id="old_jobtitle" name="old_jobtitle" class="form-control form-control-sm" required>
                                    <option value="">Select Job Title</option>
                                    @foreach ($job_titles as $job_title){
                                        <option value="{{$job_title->id}}" >{{$job_title->title}}</option>
                                    }  
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">New Job Title</label>
                                <select name="new_jobtitle" id="new_jobtitle" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                    @foreach ($job_titles as $job_title){
                                    <option value="{{$job_title->id}}">{{$job_title->title}}</option>
                                    }
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">New Department</label>
                                <select name="new_department" id="new_department" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                    @foreach ($departments as $department){
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                    }
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-3 ">
                                <label class="small font-weight-bold text-dark">Date</label>
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
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-sm fa-pull-right px-4"><i
                                    class="fas fa-plus"></i>&nbsp;&nbsp;Add</button>
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
                                    <th>ID </th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>CURRENT DEPARTMENT</th>
                                    <th>CURRENT JOB TITLE</th>
                                    <th>NEW JOB TITLE</th>
                                    <th>COMPANY</th>
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

            $('#employee_f').select2({ width: '100%' });

            $('#dataTable').DataTable({
                 "destroy": true,
                "processing": true,
                "serverSide": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Promotion Letter',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'Promotion Letter', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'portrait', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Promotion Letter',
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
                    "url": "{!! route('promotionletterlist') !!}",
                   
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'employee_display', name: 'employee_display' },
                    { data: 'old_department', name: 'old_department' },
                    { data: 'old_emptitle', name: 'old_emptitle' },
                    { data: 'new_emptitle', name: 'new_emptitle' },
                    { data: 'companyname', name: 'companyname' },
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
                    url: '{!! route("promotionletteredit") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                    success: function (data) {
                        $('#company').val(data.result.company_id);
                        $('#old_department').val(data.result.old_department_id);
                        $('#employee_f').val(data.result.employee_id);
                        $('#old_jobtitle').val(data.result.old_jobtitle);
                        $('#new_jobtitle').val(data.result.new_jobtitle);
                        $('#new_department').val(data.result.new_department_id);
                        $('#date').val(data.result.date);
                        $('#comment1').val(data.result.comment1);
                        $('#comment2').val(data.result.comment2);
                        
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
                        url: '{!! route("promotionletterdelete") !!}',
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
                url: '{!! route("promotionletterprintdata") !!}',
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


            // Department filter insert part
            $('#company').change(function () {
            var company = $(this).val();
            if (company !== '') {
                $.ajax({
                    url: '{!! route("promotionlettergetdepartmentfilter", ["company_id" => "company_id"]) !!}'
                        .replace('company_id', company),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#old_department').empty().append('<option value="">Select Department</option>');
                        $.each(data, function (index, department) {
                            $('#old_department').append('<option value="' + department.id + '">' + department.name + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                        $('#old_department').html('<option>Error loading departments</option>'); // Show error message
                    }
                });
            } else {
                $('#old_department').empty().append('<option value="">Select Departments</option>');
            }
            });

            // Employee filter insert part
            $('#old_department').change(function () {
            var department = $(this).val();
            if (department !== '') {
                $.ajax({
                    url: '{!! route("servicelettergetemployeefilter", ["emp_department" => "emp_department"]) !!}'
                        .replace('emp_department', department),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#employee_f').empty().append('<option value="">Select Employee</option>');
                        $.each(data, function (index, employee) {
                            $('#employee_f').append('<option value="' + employee.id + '">' + employee.emp_name_with_initial + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                        $('#employee_f').html('<option>Error loading Employee</option>'); // Show error message
                    }
                });
            } else {
                $('#employee_f').empty().append('<option value="">Select Employee</option>');
            }
            });

            // Job title filter insert part
            $('#employee_f').change(function () {
                var employee_f = $(this).val();
                if (employee_f !== '') {
                    $.ajax({
                        url: '{!! route("promotionlettergetjobfilter", ["id" => "id"]) !!}'
                            .replace('id', employee_f),
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            if (data && data.length > 0) {
                                $('#old_jobtitle').empty().append('<option value="">Select Job Title</option>');
                                
                                $.each(data, function (index, jobtitle) {
                                    $('#old_jobtitle').append('<option value="' + jobtitle.id + '">' + jobtitle.title + '</option>');
                                });
                            } else {
                                $('#old_jobtitle').empty().append('<option value="">No job title found</option>');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(error);
                            $('#old_jobtitle').empty().append('<option value="">Error loading job title</option>');
                        }
                    });
                } else {
                    $('#old_jobtitle').empty().append('<option value="">Select Job Title</option>');  
                }
            });

            $('#company').change(function () {
                var deptid = $('#company option:selected').data('deptid');
                $('#new_department').val(deptid);
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