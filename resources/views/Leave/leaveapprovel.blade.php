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
                        <span>Leave Approvel</span>
                    </h1>
                </div>
            </div>
        </div>
                    
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card mb-2">
            <div class="card-body p-0 p-2">
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
                <div class="row">
                    <div class="col-12">
                        <div class="row align-items-center mb-4">
                            <div class="col-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Select All Records</label>
                                </div>
                            </div>
                            <div class="col-6 text-right">
                                <button id="allapproveel" class="btn btn-primary btn-sm px-3">Approve All Leave</button>
                            </div>
                        </div>
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="divicestable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>EMP ID</th>
                                    <th>NAME WITH INITIAL</th>
                                    <th>DEPARTMENT </th>
                                    <th>LEAVE TYPE</th>
                                    <th>LEAVE FROM</th>
                                    <th>LEAVE TO</th>
                                    <th class="nowrap">REASON</th>
                                    <th>COVERING BY</th>
                                    <th>STATUS</th>
                                    <th class="text-right">ACTION</th>
                                    <th class="d-none">ID</th>
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

        <!-- Modal Area Start -->
        <div class="modal fade" id="approveconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-sm">
              <div class="modal-content">
                  <div class="modal-header p-2">
                      <h5 class="modal-title" id="exampleModalLabel">All Approval Confirmation</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <span id="form_result"></span>
                      <span id=""></span>
                      <div class="form-group">
                          <label class="small font-weight-bold text-dark">Comment</label>
                          <textarea class="form-control form-control-sm" id="allcomment" name="allcomment" rows="3"></textarea>
                      </div>
  
                      <div class="radio">
                          <label><input type="radio" name="allstatus" id="status" value="Approved"> Approve</label>
                      </div>
  
                      <div class="radio">
                          <label><input type="radio" name="allstatus" id="reject" value="Rejected"> Reject</label>
                      </div>
  
                      <input type="hidden" name="id" id="id" class="form-control" readonly />
                      <input type="hidden" name="emp_id" id="emp_id" class="form-control" readonly />
                  </div>
                  <div class="modal-footer p-2">
                      <button type="submit" class="btn btn-danger px-3 btn-sm" id="approveall">Submit</button>
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

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#leavemaster').addClass('navbtnactive');

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
                            page: params.page || 1,
                            company: company_f.val(),
                            department: department_f.val()
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
                "destroy": true,
                        "processing": true,
                        "serverSide": true,
                        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                        "buttons": [{
                                extend: 'csv',
                                className: 'btn btn-success btn-sm',
                                title: 'Leave Approve Details',
                                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                            },
                            { 
                                extend: 'pdf', 
                                className: 'btn btn-danger btn-sm', 
                                title: 'Leave Approve Details', 
                                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                orientation: 'landscape', 
                                pageSize: 'legal', 
                                customize: function(doc) {
                                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Leave Approve Details',
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
                    "url": "{!! route('leave_approve_list_dt') !!}",
                    "data": {'department':department, 'employee':employee, 'location': location, 'from_date': from_date, 'to_date': to_date},
                },
                columns: [
                    { data: 'leavestatus', name: 'leavestatus' },
                    { data: 'emp_id', name: 'emp_id' },
                    { data: 'employee_display', name: 'employee_display' },
                    { data: 'dep_name', name: 'emp_name' },
                    { data: 'leave_type', name: 'leave_type' },
                    { data: 'leave_from', name: 'leave_from' },
                    { data: 'leave_to', name: 'leave_to' },
                    { data: 'reson', name: 'reson' },
                    { data: 'covering_emp', name: 'covering_emp' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action',  className: 'text-right', orderable: false, searchable: false},
                    { data: 'id', name: 'id' },
                ],
                columnDefs: [
                    {
                        targets: -1,
                        className: 'd-none'
                    }
                ],
                "bDestroy": true,
                "order": [
                    [6, "desc"]
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

    var selectedRowIdsapprove = [];

    $('#allapproveel').click(function () {
        selectedRowIdsapprove = [];
        $('#divicestable tbody .selectCheck:checked').each(function () {
            var rowData = $('#divicestable').DataTable().row($(this).closest('tr')).data();

            if (rowData) {
                selectedRowIdsapprove.push({
                    empid: rowData.emp_id,     
                    emp_name: rowData.emp_name, 
                    laeaveid: rowData.id     
                });
            }
        });
        if (selectedRowIdsapprove.length > 0) {
            console.log(selectedRowIdsapprove);
            $('#approveconfirmModal').modal('show');
        } else {
            
            alert('Select Rows to Final Approve!!!!');
        }
    });

    $('#approveall').click(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            let comment = $("#allcomment").val();
             let status = $("input[name='allstatus']:checked").val();
            $.ajax({
                url: '{!! route("leaveapprove_batch") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    dataarry: selectedRowIdsapprove,
                    comment: comment,
                    status:status
                },
                success: function (data) {
                    setTimeout(function () {
                        $('#approveconfirmModal').modal('hide');
                        location.reload();
                    }, 500);

                    $('#selectAll').prop('checked', false);
                   
                }
            })
        });

    $('#selectAll').click(function (e) {
            $('#divicestable').closest('table').find('td input:checkbox').prop('checked', this.checked);
        });

});
</script>


@endsection