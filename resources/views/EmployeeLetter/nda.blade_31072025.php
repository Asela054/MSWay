
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.employee_nav_bar')
               
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <form method="POST" action="{{ route('NDAletterinsert') }}"  class="form-horizontal">
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
                                    <label class="small font-weight-bold text-dark">Effective Date</label>
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="date" id="effect_date" name="effect_date" class="form-control form-control-sm" required>
                                    </div>
                            </div>
                            <!-- <div class="col-md-3 ">
                                <label class="small font-weight-bold text-dark">Comment 01</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" id="comment1" name="comment1" class="form-control form-control-sm" placeholder="review">
                                </div>
                            </div>

                            <div class="col-md-3 ">
                                <label class="small font-weight-bold text-dark">Comment 02</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" id="comment2" name="comment2" class="form-control form-control-sm" placeholder="review">
                                </div>
                            </div> -->
                        </div>
                        <input type="hidden" name="recordOption" id="recordOption" value="1">
                        <input type="hidden" name="recordID" id="recordID" value="">
                        <div class="form-group mt-4 text-center">
                            @can('NDA-letter-create')
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-sm px-5"><i class="far fa-save"></i>&nbsp;&nbsp;Add</button>
                            @endcan
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
                                    <th>Emp ID </th>
                                    <th>Employee Name</th>
                                    <th>Department</th>
                                    <th>Company</th>
                                    <th>Effective Date</th>
                                    <th class="text-right">Action</th>
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
                    <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    
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

            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{!! route('NDAletterlist') !!}",
                   
                },
                columns: [
                    { data: 'emp_id', name: 'emp_id' },
                    { data: 'emp_name', name: 'emp_name' },
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
                "bDestroy": true,
                "order": [
                    [0, "asc"]
                ]
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
                    url: '{!! route("NDAletteredit") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                    success: function (data) {
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
                        if (data.result.employee_id && data.result.emp_name) {
                            var employeeOption = new Option(data.result.emp_name, data.result.emp_id, true, true);
                            $('#employee_f').append(employeeOption).trigger('change');
                        }
                        $('#effect_date').val(data.result.effect_date);
                        // $('#comment1').val(data.result.comment1);
                        // $('#comment2').val(data.result.comment2);
                        
                        $('#recordID').val(id);
                        $('#recordOption').val(2);
                    }
                })
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
                    url: '{!! route("NDAletterdelete") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: user_id },
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {//alert(data);
                        setTimeout(function () {
                            $('#confirmModal').modal('hide');
                            $('#dataTable').DataTable().ajax.reload();
                        }, 2000);
                        location.reload()
                    }
                })
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
                url: '{!! route("NDAletterprintdata") !!}',
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