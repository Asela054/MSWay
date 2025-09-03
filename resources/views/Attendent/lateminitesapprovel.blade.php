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
                        <div class="col">
                            <label class="small font-weight-bold text-dark">Company</label>
                            <select name="company" id="company" class="form-control form-control-sm" required>
                            </select>
                        </div>
                        <div class="col">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department" class="form-control form-control-sm" required>
                            </select>
                        </div>
                        <div class="col">
                            <label class="small font-weight-bold text-dark">Month</label>
                            <input type="month" id="month" name="month" class="form-control form-control-sm" placeholder="yyyy-mm" required>
                        </div>
                        <div class="col">
                            <label class="small font-weight-bold text-dark">Close Date</label>
                            <input type="date" id="closedate" name="closedate" class="form-control form-control-sm" required>
                        </div>
                        <div class="col">
                            <br>
                            <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"><i class="fas fa-search mr-2"></i>Filter</button>
                            <button type="button" class="btn btn-danger btn-sm filter-btn" id="btn-clear"><i class="far fa-trash-alt"></i>&nbsp;&nbsp;Clear</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0 p-2 main_card">
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
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%"  id="attendtable">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Employee ID</th>
                                    <th>Employee Name</th>
                                    <th>Late minites Total</th>
                                    <th>Nopay Amout</th>
                                    <th>Total Amount</th>
                                    <th class="d-none">Employee auto ID</th>
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

    <div class="modal fade" id="approveconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Approve Late Minites </h5>
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


    <!-- Modal Area End -->
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

    $('#formFilter').on('submit',function(e) {
        $('#btn-filter').html('<i class="fa fa-spinner fa-spin mr-2"></i> Processing').prop('disabled', true);
        e.preventDefault();
        var department = $('#department').val();
        var company = $('#company').val();
        var month = $('#month').val();
        var closedate = $('#closedate').val();

        $.ajax({
                url: "{{url('/getlateminitesapprovel')}}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    department: department,
                    month: month,
                    closedate: closedate
                },
                dataType: "json",
                success: function (data) {
                    if ($.fn.DataTable.isDataTable('#attendtable')) {
                        $('#attendtable').DataTable().clear().destroy();
                    }
                    
                    $('#attendtable tbody').empty();
                    let dataRows = '';
                    $.each(data.data, function (index, item) {
                        dataRows += `
                                    <tr>
                                        <td><input type="checkbox" class="row-checkbox selectCheck removeIt"></td>
                                        <td>${item.emp_id}</td>
                                        <td>${item.emp_name_with_initial}</td>
                                        <td>${item.late_hours_total}</td>
                                        <td>${item.nopayAmount}</td>
                                        <td>${item.late_day_amount}</td>
                                        <td class="d-none">${item.emp_autoid}</td>
                                    </tr>`;
                    });
                    $('#attendtable tbody').html(dataRows);
                    $('#attendtable').DataTable({
                        destroy: true,
                        responsive: true,
                        columnDefs: [{
                            orderable: false,
                            targets: [0, 6]
                        }, ]
                    });
                    $('#btn-filter').html('Filter').prop('disabled', false);
                }
            });
     
    });

    var selectedRowIdsapprove = [];

    $('#approve_att').click(function () {
        selectedRowIdsapprove = [];
        $('#attendtable tbody .selectCheck:checked').each(function () {
            var rowData = $('#attendtable').DataTable().row($(this).closest('tr')).data();

            if (rowData) {
                selectedRowIdsapprove.push({
                    empid: rowData[1],
                    emp_name: rowData[2], 
                    late_hourstotal: rowData[3],
                    nopayamount: rowData[4], 
                    total_amount: rowData[5], 
                    autoid: rowData[6], 
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
            $('#approve_button').html('<i class="fa fa-spinner fa-spin mr-2"></i> Processing').prop('disabled', true);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            var department = $('#department').val();
            var company = $('#company').val();
            var month = $('#month').val();
            var closedate = $('#closedate').val();

            $.ajax({
                url: '{!! route("approvelatemintes") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    dataarry: selectedRowIdsapprove,
                    department: department,
                    month: month,
                    closedate: closedate
                },
                success: function (data) {
                    $('#approve_button').html('Approve').prop('disabled', false);
                    setTimeout(function () {
                        $('#approveconfirmModal').modal('hide');
                        location.reload();
                    }, 500);

                    $('#selectAll').prop('checked', false);
                   
                }
            })
        });


    $('#selectAll').click(function (e) {
        $('#attendtable').closest('table').find('td input:checkbox').prop('checked', this.checked);
    });

});


</script>

@endsection