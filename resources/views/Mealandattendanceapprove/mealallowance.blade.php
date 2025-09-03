@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.attendant&leave_nav_bar')
               
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col-2">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm" required>
                                </select>
                            </div>
                            <div class="col-2">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm" required>
                                   
                                </select>
                            </div>
                            <div class="col-2">
                                <label class="small font-weight-bold text-dark">Add/Deduct Type</label>
                                <select id="remuneration_name" name="remuneration_name" class="form-control form-control-sm" required>
                                <option value="">Select Remuneration</option>
                                @foreach ($remunerations as $remuneration){
                                    <option value="{{$remuneration->id}}" >{{$remuneration->remuneration_name}}</option>
                                }  
                                @endforeach
                            </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Type</label>
                                <select name="reporttype" id="reporttype" class="form-control form-control-sm">
                                    <option value="">Please Select Type</option>
                                    <option value="1">Month Wise</option>
                                    <option value="2">Date Range Wise</option>
                                </select>
                            </div>
                            <div class="col-md-3" id="div_date_range">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd">
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            <div class="col-md-2" id="div_month">
                                <label class="small font-weight-bold text-dark">Month</label>
                                <input type="month" id="selectedmonth" name="selectedmonth" class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                            </div>
                            <div class="col">
                                <br>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn px-3" id="btn-filter"><i class="fas fa-search mr-2"></i>Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                            <div class="col-12">
                                <div class="row align-items-center mb-4">
                                    <div class="col-6 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                            <label class="form-check-label" for="selectAll">Select All Records</label>
                                        </div>
                                    </div>
                                    <div class="col-6 text-right">
                                        <button id="approve" class="btn btn-primary btn-sm px-3">Approve All</button>
                                    </div>
                                </div>
                                
                                <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap display"
                                        style="width: 100%" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Emp ID </th>
                                                <th>Empolyee Name</th>
                                                <th>Total Working Days</th>
                                                <th>Allowance Amount</th>
                                                <th>Total Amount</th>
                                                <th>Remaining Amount</th>
                                                <th class="d-none">Payroll profile</th>
                                                <th class="d-none">Type</th>
                                                <th class="d-none">Remunition</th>
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
    </main>


    <div class="modal fade" id="approveconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Approve Meal Allowance </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col text-center">
                            <h4 class="font-weight-normal">Are you sure you want to Approve this data?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="approve_button" id="approve_button"
                        class="btn btn-warning px-3 btn-sm">Approve</button>
                    <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
<script>
    $(document).ready(function () {
            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#attendantmaster').addClass('navbtnactive');

            $('#div_date_range').addClass('d-none');
            $('#div_month').addClass('d-none');
            $('#reporttype').on('change', function () {
                let $type = $(this).val();
                if ($type == 1) {

                    $('#div_date_range').addClass('d-none');
                    $('#div_month').removeClass('d-none');

                } else {
                    $('#div_month').addClass('d-none');
                    $('#div_date_range').removeClass('d-none');
                }
            });

        let company = $('#company');
        let department = $('#department');

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

        $('#formFilter').on('submit', function (event) {
            event.preventDefault();

            var action_url = "{{ route('mealallowancecreate') }}";

            var department = $('#department').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var selectedmonth = $('#selectedmonth').val();
            var remunerationtype = $('#remuneration_name').val();
            $.ajax({
                url: action_url,
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    department: department,
                    from_date: from_date,
                    to_date: to_date,
                    selectedmonth: selectedmonth,
                    remunerationtype: remunerationtype
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
                                        <td>
                                            ${item.approvedallowancestatus == 1 
                                                ? '<i class="fa fa-check-circle text-success"></i>' 
                                                : '<input type="checkbox" class="row-checkbox selectCheck removeIt">'
                                            }
                                        </td>
                                        <td>${item.empid}</td>
                                        <td>${item.emp_name}</td>
                                        <td>${item.working_Days}</td>
                                        <td>${item.allowance_amount}</td>
                                        <td>${item.total_amount}</td>
                                        <td>${item.monthly_remain}</td>
                                        <td class="d-none">${item.payroll_Profile}</td>
                                        <td class="d-none">${item.allowance_type}</td>
                                        <td class="d-none">${item.remuneration_id}</td>
                                    </tr>
                                `;
                    });
                    $('#dataTable tbody').html(dataRows);
                    $('#dataTable').DataTable({
                        destroy: true,
                        responsive: true,
                        columnDefs: [{
                            orderable: false,
                            targets: [0, 9]
                        }, ]
                    });
                }
            });
        });

        var selectedRowIdsapprove = [];

        $('#approve').click(function () {
                    selectedRowIdsapprove = [];
                    $('#dataTable tbody .selectCheck:checked').each(function () {
                        var rowData = $('#dataTable').DataTable().row($(this).closest('tr')).data();

                        if (rowData) {
                            selectedRowIdsapprove.push({
                                empid: rowData[1],
                                emp_name: rowData[2], 
                                working_Days: rowData[3],
                                allowance_amount: rowData[4], 
                                total_amount: rowData[5], 
                                monthly_remain: rowData[6], 
                                payroll_Profile: rowData[7],
                                allowance_type: rowData[8],
                                remuneration_id: rowData[9]
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

        $('#approve_button').click(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            var selected_month = $('#selectedmonth').val();
            var department = $('#department').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            $.ajax({
                url: '{!! route("mealallowancecreateapprove") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    dataarry: selectedRowIdsapprove,
                    selectedmonth: selected_month,
                    from_date:from_date,
                    to_date:to_date
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
            $('#dataTable').closest('table').find('td input:checkbox').prop('checked', this.checked);
        });

    });
</script>
@endsection