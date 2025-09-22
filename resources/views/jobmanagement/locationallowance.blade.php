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
                        <span>Location Allowance Approval</span>
                    </h1>
                </div>
            </div>
        </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card mb-2">
                <div class="card-body p-0 p-2">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col-sm-12 col-md-4">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm">
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{$employee->emp_id}}">{{$employee->emp_name_with_initial}} - {{$employee->calling_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd" required>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm"> </span>
                                    </div>
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd" required>
                                </div>
                            </div>
                           <div class="col-sm-12 col-md-4">
                                <button type="submit" class="btn btn-primary btn-sm filter-btn float-right" id="btn-filter" style="margin-top: 25px;"> Filter</button>
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
                            <div class="col-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Select All Records</label>
                                </div>
                            </div>
                            <div class="col-6 text-right">
                                <button id="approve_att" class="btn btn-primary btn-sm">Approve All</button>
                            </div>
                        </div>

                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>EMPLOYEE ID</th>
                                    <th>EMPLOYEE</th>
                                    <th>LOCATION VISIT COUNT</th>
                                    <th>ALLOWANCE AMOUNT</th>
                                    <th class="d-none">EMPLOYEE AUTO ID</th>
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
              
@endsection


@section('script')

<script>
$(document).ready(function(){

   $('#attendant_menu_link').addClass('active');
    $('#attendant_menu_link_icon').addClass('active');
    $('#jobmanegment').addClass('navbtnactive');


        function load_dt(employee, from_date, to_date) {
            $('#dataTable').DataTable({
               "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Location Allowance Approve  Information',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Location Allowance Approve Information', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Location Allowance Approve  Information',
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
                        [1, "desc"]
                    ],
                ajax: {
                    url:  "{{url('/locationallwanceapprovegenerate')}}",
                    type: 'POST',
                    data: { 
                         _token: '{{ csrf_token() }}',
                        employee: employee, 
                        from_date: from_date,
                        to_date: to_date
                    },
                },
                columns: [
                     {
                        data: null,
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox"  class="row-checkbox selectCheck removeIt" data-id="' + row.emp_auto_id + '">';
                        }
                    },
                    { data: 'emp_id', name: 'emp_id' },
                    { data: 'emp_name_with_initial', name: 'emp_name_with_initial' },
                    { data: 'visit_count', name: 'visit_count' },
                    { data: 'allowance_amount', name: 'allowance_amount' },
                    {
                        data: 'emp_auto_id',
                        name: 'emp_auto_id',
                        visible: false
                    }
                ]
            });
        }

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let employee = $('#employee_f').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            load_dt(employee, from_date, to_date);
        });

         var selectedRowIdsapprove = [];

    $('#approve_att').click(async function () {
        var r = await Otherconfirmation("You want to Edit this ? ");
        if (r == true) {
            selectedRowIdsapprove = [];
            $('#dataTable tbody .selectCheck:checked').each(function () {
                var rowData = $('#dataTable').DataTable().row($(this).closest('tr')).data();
                if (rowData) {
                    selectedRowIdsapprove.push({
                        empid: rowData.emp_id,
                        emp_name: rowData.emp_name_with_initial,
                        visit_count: rowData.visit_count,
                        allowance_amount: rowData.allowance_amount,
                        emp_auto_id: rowData.emp_auto_id
                    });
                }
            });
            if (selectedRowIdsapprove.length > 0) {
                console.log(selectedRowIdsapprove);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })

                var employee_f = $('#employee_f').val();
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();

                $.ajax({
                    url: '{!! route("approvelocationallowance") !!}',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        dataarry: selectedRowIdsapprove,
                        employee_f: employee_f,
                        from_date: from_date,
                        to_date: to_date
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
                })

            } else {
                Swal.fire({
                    position: "top-end",
                    icon: 'warning',
                    title: 'Select Rows to Final Approve!',
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