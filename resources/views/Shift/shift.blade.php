@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.shift_nav_bar')
           
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

                        <div class="col-md-2">
                            <br>
                            <button type="submit" class="btn btn-primary btn-sm filter-btn " id="btn-filter"> Filter</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <span id="response"></span>
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-hover nowrap" style="width: 100%" id="divicestable">
                            <thead>
                                <tr>
                                    <th>Employee Name </th>
                                    <th>Department</th>
                                    <th>Shift </th>
                                    <th>Start Time</th>                                                
                                    <th>End Time</th>   
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
    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Shift</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form id="formTitle" method="post">
                                {{ csrf_field() }}

                                <div class="form-row pb-2">
                                    <label class="control-label col-md-1">Id: </label>
                                    <div class="col-md-2">
                                        <input type="text" name="uid" id="uid" class="form-control form-control-sm" readonly />
                                    </div>
                                    <label class="control-label col-md-2">Name: </label>
                                    <div class="col-md-7">
                                        <input type="text" name="uname" id="uname" class="form-control form-control-sm" readonly />
                                    </div>

                                </div>
                                <div class="form-row">
                                    <label class="control-label col-md-4">Shift</label>

                                    <div class="col-md-8">
                                        <select name="shift" id="shift" class="custom-select">
                                            <option value="">Please Select</option>
                                            @foreach($shifttype as $shifttypes)
                                            <option value="{{$shifttypes->id}}">{{$shifttypes->shift_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <br />
                                <div class="form-group float-right" >
                                    <input type="hidden" name="action" id="action" value="Edit" />
                                    <input type="hidden" name="hidden_id" id="hidden_id" />
                                    <input type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm"
                                        value="Edit" />
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <!-- Modal Area End -->

</main>
              
@endsection


@section('script')

<script>
// $('#todate').datepicker({
//     format: "yyyy/mm/dd",
//     autoclose: true
// });
// $('#fromdate').datepicker({
//     format: "yyyy/mm/dd",
//     autoclose: true
// });

$(document).ready(function () {

    $('#shift_menu_link').addClass('active');
    $('#shift_menu_link_icon').addClass('active');
    $('#shift_link').addClass('navbtnactive');

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
            ajax: {
                "url": "{!! route('shift_list_dt') !!}",
                "data": {'department':department, 'employee':employee, 'location': location, 'from_date': from_date, 'to_date': to_date},
            },
            columns: [
                { data: 'emp_name_with_initial', name: 'emp_name_with_initial' },
                { data: 'dep_name', name: 'dep_name' },
                { data: 'shift_name', name: 'shift_name' },
                { data: 'onduty_time', name: 'onduty_time' },
                { data: 'offduty_time', name: 'offduty_time' },
                { data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            "bDestroy": true,
            "order": [
                [3, "desc"]
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
    $('#create_record').click(function () {
        $('.modal-title').text('Apply Leave');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');

        $('#formModal').modal('show');
    });

    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';
        action_url = "{{ route('Shift.update') }}";

        $.ajax({
            url: action_url,
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
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
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                    //  $('#formTitle')[0].reset();
                    $('#divicestable').DataTable().ajax.reload();
                    setTimeout(function() { $('#formModal').modal('hide'); }, 1000);
                }
                $('#form_result').html(html);
            }
        });
    });



    $(document).on('click', '.edit', function () {
        var id = $(this).data('id');
        var empname = $(this).data('emp_name_with_initial');
        var shift = $(this).data('shift_type_id');

        $('#formModal').modal('show');
        $('#uid').val(id);
        $('#uname').val(empname);
    });

    var user_id;

    $(document).on('click', '.delete', function () {
        user_id = $(this).data('id');
        $('#confirmModal').modal('show');

    });

    $('#ok_button').click(function () {
        $.ajax({
            url: "Shift/destroy/" + user_id,
            beforeSend: function () {
                $('#ok_button').text('Deleting...');
            },
            success: function (data) {
                let html = '<div class="alert alert-success">' + data.success + '</div>';
                $('#response').html(html);
                $('#confirmModal').modal('hide');
                $('#divicestable').DataTable().ajax.reload();
                //location.reload();
            }
        })
    });

});
</script>

@endsection