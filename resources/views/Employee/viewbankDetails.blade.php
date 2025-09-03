@extends('layouts.app')

@section('content')
    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.employee_nav_bar')
               
            </div>
        </div>
        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-lg-9">
                    <div id="default">
                        <div class="card mb-4">
                            <div class="card-header">Add Bank Details</div>
                            <div class="card-body">
                                @if(Session::has('message'))
                                    <p class="alert {{ Session::get('alert-class', 'alert-info') }} col-lg-8">{{ Session::get('message') }}</p>
                                @endif
                                <form id="PdetailsForm" class="form-horizontal" method="POST"
                                      action="{{ route('BankInsert') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group col-lg-8">
                                        <label for="exampleFormControlInput1">
                                            Employee Id</label>
                                        <input class="form-control form-control-sm" id="emp_id" name="emp_id" type="text"
                                               value="{{$id}}" readonly>
                                    </div>

                                    <div class="form-group col-lg-8">
                                        <label for="exampleFormControlInput1">
                                            Bank Name</label>
                                        <select class="form-control-sm" id="bank_name" name="bank_code" required>
                                        </select>
                                        @if ($errors->has('bank_code'))
                                            <span class="help-block text-danger">
                                                                <strong>{{ $errors->first('bank_code') }}</strong>
                                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-lg-8">
                                        <label for="exampleFormControlInput1">
                                            Branch Name</label>
                                        <select class="form-control-sm" id="branch_name" name="branch_id" required>
                                        </select>
                                        @if ($errors->has('branch_id'))
                                            <span class="help-block text-danger">
                                                                <strong>{{ $errors->first('branch_id') }}</strong>
                                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-lg-8">
                                        <label for="exampleFormControlInput1">
                                            Bank Account No</label>
                                        <input class="form-control form-control-sm" id="bank_ac_no" name="bank_ac_no"
                                               type="text" required>
                                        @if ($errors->has('bank_ac_no'))
                                            <span class="help-block text-danger">
                                                                <strong>{{ $errors->first('bank_ac_no') }}</strong>
                                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group col">
                                        @can('employee-edit')
                                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                            <button type="reset" class="btn btn-success btn-sm">Clear</button>
                                        @endcan
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <div class="mt-3">
                        <div class="card mb-4">

                            <div class="card-body">
                                <div class="datatable table-responsive">
                                    <table class="table table-bordered table-hover" id="dataTable" width="100%"
                                           cellspacing="0">
                                        <thead>
                                        <tr>
                                            <th>Bank Name</th>
                                            <th>Bank Code</th>
                                            <th>Branch Name</th>
                                            <th>Branch Code</th>
                                            <th>Account No</th>
                                            <th class="text-right">Action</th>
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
                                                <td class="text-right">
                                                    @can('employee-edit')
                                                        <button type="submit" name="delete" id="{{$employeebanks->id}}"
                                                            class="delete btn btn-outline-danger btn-sm"><i
                                                                class="far fa-trash-alt"></i></button>
                                                    @endcan
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
                        <button type="button" name="ok_button" id="ok_button" class="btn btn-danger px-3 btn-sm">OK
                        </button>
                        <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
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
            $('#view_bank_link').addClass('navbtnactive');

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
