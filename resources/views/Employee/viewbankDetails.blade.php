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
                        <span>Bank Details</span>
                    </h1>
                </div>
            </div>
        </div>    
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="row">
                <div class="col-lg-9 col-12">
                    <div id="default">
                        <div class="card mb-4">
                            <div class="card-header">Add Bank Details</div>
                            <div class="card-body">
                                @if(Session::has('message'))
                                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                                @endif
                                <form id="PdetailsForm" class="form-horizontal" method="POST"
                                    action="{{ route('BankInsert') }}">
                                    {{ csrf_field() }}
                                    
                                    <div class="row">
                                        <!-- Employee ID - Full Width -->
                                        <div class="form-group col-12">
                                            <label for="emp_id">Employee Id</label>
                                            <input class="form-control form-control-sm" id="emp_id" name="emp_id" type="text"
                                                value="{{$id}}" readonly>
                                        </div>

                                        <!-- Bank Name - Half Width on Desktop -->
                                        <div class="form-group col-md-6 col-12">
                                            <label for="bank_name">Bank Name</label>
                                            <select class="form-control form-control-sm" id="bank_name" name="bank_code" required>
                                            </select>
                                            @if ($errors->has('bank_code'))
                                                <span class="help-block text-danger">
                                                    <strong>{{ $errors->first('bank_code') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Branch Name - Half Width on Desktop -->
                                        <div class="form-group col-md-6 col-12">
                                            <label for="branch_name">Branch Name</label>
                                            <select class="form-control form-control-sm" id="branch_name" name="branch_id" required>
                                            </select>
                                            @if ($errors->has('branch_id'))
                                                <span class="help-block text-danger">
                                                    <strong>{{ $errors->first('branch_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Bank Account No - Full Width -->
                                        <div class="form-group col-12">
                                            <label for="bank_ac_no">Bank Account No</label>
                                            <input class="form-control form-control-sm" id="bank_ac_no" name="bank_ac_no"
                                                type="text" required>
                                            @if ($errors->has('bank_ac_no'))
                                                <span class="help-block text-danger">
                                                    <strong>{{ $errors->first('bank_ac_no') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Buttons -->
                                        <div class="form-group col-12 text-right">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i>&nbsp;Add
                                            </button>
                                            <button type="reset" class="btn btn-danger btn-sm">
                                                <i class="far fa-trash-alt"></i>&nbsp;Clear
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="mt-3">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-sm small" id="dataTable">
                                        <thead>
                                        <tr>
                                            <th>BANK</th>
                                            <th>BANK CODE</th>
                                            <th>BRANCH</th>
                                            <th>BRANCH CODE</th>
                                            <th>ACCOUNT NO</th>
                                            <th>STATUS</th>
                                            <th class="text-right">ACTION</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @foreach($employeebank as $employeebanks)
                                            <tr>
                                                <td>{{$employeebanks->bank}}</td>
                                                <td>{{$employeebanks->bankcode}}</td>
                                                <td>{{$employeebanks->branch}}</td>
                                                <td>{{$employeebanks->branchCode}}</td>
                                                <td>{{$employeebanks->bank_ac_no}}</td>
                                                <td class="text-center text-nowrap">
                                                    @if($employeebanks->status == 1)
                                                        <button 
                                                            class="btn btn-success btn-sm btn-status mr-1 mt-1" 
                                                            data-id="{{ $employeebanks->id }}" 
                                                            data-status="1">
                                                            Active
                                                        </button>
                                                    @elseif($employeebanks->status == 2)
                                                        <button 
                                                            class="btn btn-secondary btn-sm btn-status mr-1 mt-1" 
                                                            data-id="{{ $employeebanks->id }}" 
                                                            data-status="2">
                                                            Deactive
                                                        </button>
                                                    @endif
                                                </td>
                                                <td class="text-right text-nowrap">
                                                    <button type="submit" name="delete" id="{{$employeebanks->id}}"
                                                        class="delete btn btn-danger btn-sm">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    @include('layouts.employeeRightBar')
            </div>
        </div>

        <!-- Confirmation Modal -->
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

        <div class="modal fade" id="statusModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
            aria-labelledby="statusModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header p-2 border-0">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body px-3 py-4">
                        <div class="row">
                            <div class="col text-center">
                                <i class="fas fa-question-circle text-primary mb-3" style="font-size: 3rem;"></i>
                                <h5 class="font-weight-normal">Change Bank status?</h5>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-2 border-0 justify-content-center">
                        <button type="button" class="btn btn-dark btn-sm px-4" data-dismiss="modal">No</button>
                        <button type="button" id="status_ok_button" class="btn btn-primary btn-sm px-4">Yes</button>
                    </div>
                </div>
            </div>
        </div>


    </main>

@endsection
@section('script')
    <script>
        $(document).ready(function() {

            $('#employee_menu_link').addClass('active');
            $('#employee_menu_link_icon').addClass('active');
            $('#employeeinformation').addClass('navbtnactive');
            $('#view_bank_link').addClass('active');

            $('#dataTable').DataTable({
                "destroy": true,
                "processing": true,
                
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Bank  Information',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'Bank Information', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'portrait', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Bank  Information',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-print mr-2"></i> Print',
                        customize: function(win) {
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        },
                    },
                ],
                "order": [
                    [0, "desc"]
                ],
                }
                );

            let bank_name = $('#bank_name');
            let branch_name = $('#branch_name');

            bank_name.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("bank_list")}}',
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

            branch_name.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("branch_list")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            bank: bank_name.val()
                        }
                    },
                    cache: true
                }
            });


            let status = 0;
            $(document).on('click', '.btn-status', function () {
                status = $(this).data('id');
                $('#statusModal').modal('show');
            });

            $('#status_ok_button').click(function () {
                $.ajax({
                    url: "{{ url('bankAccountStatusUpdate') }}",
                    method: "POST",
                    data: {
                        id: status,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            let button = $('.btn-status[data-id="' + status + '"]');
                            if (response.new_status == 1) {
                                button.removeClass('btn-secondary').addClass('btn-success').text('Active');
                            } else {
                                button.removeClass('btn-success').addClass('btn-secondary').text('Deactive');
                            }
                            $('#statusModal').modal('hide');
                        }
                    }
                });
            });

            var user_id;

            $(document).on('click', '.delete', function () {
                user_id = $(this).attr('id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function () {
                $.ajax({
                    url: "../empBank/destroy/" + user_id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        setTimeout(function () {
                            $('#confirmModal').modal('hide');
                            alert('Data Deleted');
                        }, 2000);
                        location.reload();
                    }
                })
            });

        });
    </script>
@endsection
