@extends('layouts.app')

@section('content')

<main>
    <div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            <div class="page-header-content py-3">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="fas fa-user-check"></i></div>
                    <span>Leave Approval</span>
                </h1>
            </div>
        </div>
    </div>
                    
    <div class="container-fluid mt-4">
        <div class="card mb-2">
            <div class="card-body">
                <form class="form-horizontal" id="formFilter">
                    <div class="form-row mb-1">
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Company</label>
                            <select name="company" id="company_f" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department_f" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Location</label>
                            <select name="location" id="location_f" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Employee</label>
                            <select name="employee" id="employee_f" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small font-weight-bold text-dark">Date : From - To</label>
                            <div class="input-group input-group-sm mb-3">
                                <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-sm"> </span>
                                </div>
                                <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-sm filter-btn float-right" id="btn-filter"> Filter</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0 p-2">
                <div id="message"></div>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-striped table-bordered table-sm small" id="divicestable">
                            <thead>
                                <tr>
                                    <th>Emp Id </th>
                                    <th>Name With Initial </th>
                                    <th>Department </th>
                                    <th>Leave Type</th>
                                    <th>Leave From</th>
                                    <th>Leave To</th>
                                    <th>Reason</th>
                                    <th>Covering By</th>
                                    <th>Status</th>
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
    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="exampleModalLabel">Approval Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <span id=""></span>
                    <div class="form-group">
                        <label class="small font-weight-bold text-dark">Comment</label>
                        <textarea class="form-control form-control-sm" id="comment" name="comment" rows="3"></textarea>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="status" id="status" value="Approved"> Approve</label>
                    </div>

                    <div class="radio">
                        <label><input type="radio" name="status" id="reject" value="Rejected"> Reject</label>
                    </div>

                    <input type="hidden" name="id" id="id" class="form-control" readonly />
                    <input type="hidden" name="emp_id" id="emp_id" class="form-control" readonly />
                </div>
                <div class="modal-footer p-2">
                    <button type="submit" class="btn btn-danger px-3 btn-sm" id="approve">Submit</button>
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

        $('#leaves_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
        $('#leave_collapse').addClass('show');
        $('#leave_approvals_link').addClass('active');

        let company_f = $('#company_f');
        let department_f = $('#department_f');
        let employee_f = $('#employee_f');
        let location_f = $('#location_f');

        company_f.select2({
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

        department_f.select2({
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
                        company: company_f.val()
                    }
                },
                cache: true
            }
        });

        employee_f.select2({
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

        location_f.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("location_list_sel2")}}',
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

        function load_dt(department, employee, location, from_date, to_date){
            $('#divicestable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: 'th:not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Print',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: 'th:not(:last-child)'
                        }
                    }
                ],
                processing: true,
                serverSide: true,
                stateSave: true,
                ajax: {
                    "url": "{!! route('leave_approve_list_dt') !!}",
                    "data": {'department':department, 'employee':employee, 'location': location, 'from_date': from_date, 'to_date': to_date},
                },
                columns: [
                    { data: 'emp_id', name: 'emp_id' },
                    { data: 'emp_name', name: 'emp_name' },
                    { data: 'dep_name', name: 'emp_name' },
                    { data: 'leave_type', name: 'leave_type' },
                    { data: 'leave_from', name: 'leave_from' },
                    { data: 'leave_to', name: 'leave_to' },
                    { data: 'reson', name: 'reson' },
                    { data: 'covering_emp', name: 'covering_emp' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "bDestroy": true,
                "order": [
                    [5, "desc"]
                ]
            });
        }

        load_dt('', '', '', '', '');

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let department = $('#department_f').val();
            let employee = $('#employee_f').val();
            let location = $('#location_f').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            load_dt(department, employee, location, from_date, to_date);
        });
    });

$(document).ready(function () {


    $(document).on('click', '.view', function () {
        let id = $(this).attr('data-id');
        let emp_id = $(this).attr('data-empid');

        $('#id').val(id);
        $('#emp_id').val(emp_id);

        $('#confirmModal').modal('show');
    });

    $(document).on("click", "#approve", function () {
        let comment = $("#comment").val();
        let emp_id = $('#emp_id').val();
        let status = $("input[name='status']:checked").val();

        let id = $('#id').val();

        $.ajax({
            url: "{{ route('approvelupdate') }}",
            type: "POST",
            data: {
                id: id,
                emp_id: emp_id,
                status: status,
                comment: comment,
                _token: "{{ csrf_token() }}",

            },
            success: function (data) {
                var html = '';
                if (data.errors) {
                    html = '<div class="alert alert-danger">';
                    for (var count = 0; count < data.errors.length; count++) {
                        html += '<p>' + data.errors[count] + '</p>';
                    }
                    html += '</div>';
                }
                if (data.success) {
                    $('#message').html("<div class='alert alert-success'> "+ data.success +" </div>");
                    $('#confirmModal').modal('hide');
                    $('#divicestable').DataTable().ajax.reload(null, false);
                }
                $('#form_result').html(html);
            }
        });
    });
    $(document).on("click", "#notApproved", function () {
        var id = $('#id').val();
        var emp_id = $('#emp_id').val();
        var comment = $("#comment").val();


        $.ajax({
            url: "{{ route('approvelupdate') }}",
            type: "POST",
            cache: false,
            data: {
                id: id,
                emp_id: emp_id,
                status: 'Not Approved',
                comment: comment,
                _token: "{{ csrf_token() }}",

            },
            success: function (data) {

                var html = '';
                if (data.errors) {
                    html = '<div class="alert alert-danger">';
                    for (var count = 0; count < data.errors.length; count++) {
                        html += '<p>' + data.errors[count] + '</p>';
                    }
                    html += '</div>';
                }
                if (data.success) {
                    $('#message').html("<div class='alert alert-danger'>Leave Not Approved</div>");
                    location.reload()
                }
                $('#form_result').html(html);
            }
        });
    });
});
</script>


@endsection