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
                        <span>Additional Timestamps Approval</span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="col-md-12">

                        <div class="row align-items-center mb-4">
                            <div class="col-md-12">
                                <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                    data-toggle="offcanvas" data-target="#offcanvasRight"
                                    aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
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
                            <div class="col-md-6">
                                <button id="approve" class="btn btn-primary btn-sm float-right px-3"><i
                                        class="fas fa-plus mr-2"></i>Approve Timestamps</button>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap w-100" style="width: 100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>EMP ID </th>
                                            <th>EMPLOYEE NAME</th>
                                            <th>Date</th>
                                            <th>FROM</th>
                                            <th>TO</th>
                                            <th>DURATION</th>
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

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
                aria-labelledby="offcanvasRightLabel">
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
                                    <label class="small font-weight-bolder text-dark">Employee</label>
                                    <select name="employee" id="employee" class="form-control form-control-sm">
                                    </select>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">From Date</label>
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm"placeholder="yyyy-mm-dd">
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">To Date</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"placeholder="yyyy-mm-dd">
                                </div>
                            </li>
                            <li>
                                <div class="col-md-12 d-flex justify-content-between">
                                    
                                    <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
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
        $('#attendant_menu_link').addClass('active');
        $('#attendant_menu_link_icon').addClass('active');
        $('#attendantmaster').addClass('navbtnactive');

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
                        department: department.val(),
                        location: location.val()
                    }
                },
                cache: true
            }
        });

        $('#formFilter').on('submit', function (event) {
            event.preventDefault();
            $('#btn-filter').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Searching...');

            var action_url = "{{ route('AdditionalTimesApprovallist') }}";

            var department = $('#department').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var employee = $('#employee').val();

            $.ajax({
                url: action_url,
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    department: department,
                    from_date: from_date,
                    to_date: to_date,
                    employee: employee
                },
                dataType: "json",
                success: function (data) {
                    if ($.fn.DataTable.isDataTable('#dataTable')) {
                        $('#dataTable').DataTable().clear().destroy();
                    }

                    $('#dataTable tbody').empty();

                    let dataRows = '';
                    $.each(data.data, function (index, item) {
                        dataRows += `
                                    <tr>
                                        <td>${item.status == '1' ? '<i class="fa fa-check text-success"></i>' : '<input type="checkbox" class="row-checkbox selectCheck removeIt">'}</td>
                                        <td>${item.emp_id}</td>
                                        <td>${item.emp_name}</td>
                                        <td>${item.date}</td>
                                        <td>${item.from_time}</td>
                                        <td>${item.to_time}</td>
                                        <td>${item.duration}</td>
                                    </tr>
                                    `;
                    });
                    closeOffcanvasSmoothly();
                    $('#btn-filter').prop('disabled', false).html('<i class="fas fa-search mr-2"></i> Search');
                    $('#dataTable tbody').html(dataRows);

                  $('#dataTable').DataTable({
                        destroy: true,
                        responsive: true,
                        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                        "buttons": [{
                                extend: 'csv',
                                className: 'btn btn-success btn-sm',
                                title: 'Absent Nopay Information',
                                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                            },
                            {
                                extend: 'pdf',
                                className: 'btn btn-danger btn-sm',
                                title: 'Absent Nopay Information',
                                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                orientation: 'landscape',
                                pageSize: 'legal',
                                customize: function (doc) {
                                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Absent Nopay  Information',
                                className: 'btn btn-primary btn-sm',
                                text: '<i class="fas fa-print mr-2"></i> Print',
                                customize: function (win) {
                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');
                                },
                            },
                        ],
                        columnDefs: [{
                            orderable: false,
                            targets: [0, 1]
                        }, ]
                    }); 
                }
            });
        });

        var selectedRowIdsapprove = [];

        $('#approve').click(async function () {
              var r = await Otherconfirmation("You want to Approve this ? ");
              if (r == true) {

                          selectedRowIdsapprove = [];
                          $('#dataTable tbody .selectCheck:checked').each(function () {
                              var rowData = $('#dataTable').DataTable().row($(this).closest('tr')).data();

                              if (rowData) {
                                selectedRowIdsapprove.push({
                                    empid: rowData[1],
                                    emp_name: rowData[2], 
                                    date: rowData[3],
                                    from_time: rowData[4],
                                    to_time: rowData[5],
                                    duration: rowData[6]
                                });
                            }
                          });
                          if (selectedRowIdsapprove.length > 0) {
                              console.log(selectedRowIdsapprove);
                              $.ajaxSetup({
                                  headers: {
                                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                  }
                              });

                              $.ajax({
                                  url: '{!! route("AdditionalTimesApprove") !!}',
                                  type: 'POST',
                                  dataType: "json",
                                   data: {
                                        dataarry: selectedRowIdsapprove
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
                                          actionreload(actionJSON);
                                      }
                                  }
                              });
                          } else {
                              Swal.fire({
                                  position: "top-end",
                                  icon: 'warning',
                                  title: 'Select Rows to Approve!',
                                  showConfirmButton: false,
                                  timer: 2500
                              });
                          }
              }
          });

        $('#selectAll').click(function (e) {
            $('#dataTable').closest('table').find('td input:checkbox').prop('checked', this.checked);
        });

    });
</script>
@endsection