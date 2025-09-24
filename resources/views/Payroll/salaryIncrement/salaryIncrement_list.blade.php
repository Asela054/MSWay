@extends('layouts.app')

@section('content')

<main>
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.payroll_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-money-check-dollar-pen"></i></div>
                    <span>Salary Increments</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12 text-right">
                        <button type="button" name="upload_record" id="upload_record" class="btn btn-secondary btn-sm px-3"><i class="fal fa-upload mr-2"></i>Upload</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card mt-3">
                            <div class="card-body p-3">
                                @if (\Session::has('success'))
                                <div class="alert alert-primary" role="alert">
                                    {{ \Session::get('success') }}
                                </div>
                                @endif
                                <div class="form-row">
                                    <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                                        <label class="font-weight-bolder small">Increment Type</label>
                                        <select name="remuneration_filter" id="remuneration_filter"
                                            class="form-control form-control-sm">
                                            <option value="" selected="selected">Select</option>
                                            <option value="0">Basic Salary</option>
                                            @foreach($remuneration as $payment)

                                            <option value="{{$payment->id}}">{{$payment->remuneration_name}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-2 col-xl-2">
                                        <label class="font-weight-bolder small">Effective Date</label>
                                        <input type="month" name="month_filter" id="month_filter" class="form-control form-control-sm" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="center-block fix-width scroll-inner mt-3">
                            <table class="table table-bordered table-striped table-sm small nowrap w-100" id="emptable" width="100%"
                                cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>EMPLOYEE NAME</th>
                                        <th>INCREMENT TYPE</th>
                                        <th>INCREMENT VALUE</th>
                                        <th>EFECTIVE DATE</th>
                                        <th>PAID VALUE</th>
                                        <th class="actlist_col">ACTIONS</th>
                                    </tr>
                                </thead>

                                <!--tbody>
                                                
                                                
                                                    <tr>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                    </tr>
                                                
                                                
                                                </tbody-->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>






    <div id="incrementCancelModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="incrementCancelModalLabel">Confirmation</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span
                            class="btn-sm btn-danger" aria-hidden="true">X</span></button>

                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div id="incrementUploadModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">            
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel">Confirmation</h5>&nbsp;
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('uploadSalaryIncrement') }}" method="post" target="_self" enctype="multipart/form-data" onsubmit="return colValidate();">
                        {{ csrf_field() }}
                        <div class="form-row mb-1">
                            <div class="col-12">
                                <label class="font-weight-bolder small">
                                    File Content :
                                    <a class="font-weight-normal" href="{{ url('/public/csvsample/salary_increments.csv') }}">
                                        CSV Format-Download Sample File
                                    </a>
                                </label>
                                <select name="remuneration_file" id="remuneration_file" class="form-control form-control-sm">
                                    <option value="0" selected="selected">Basic Salary</option>
                                    @foreach($remuneration as $payment)
                                    <option value="{{$payment->id}}">{{$payment->remuneration_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col-12">
                                <p id="lblstatus"></p>
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <div class="col-12">
                                <h6 class="title-style small"><span>Upload File</span></h6>
                                <div class="input-group input-group-sm">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="file" id="file" aria-describedby="inputGroupFileAddon04" required>
                                        <label class="custom-file-label" for="inputGroupFile04">Choose file</label>
                                    </div>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit" name="import_file" value="import" required="required">Upload</button>
                                    </div>
                                </div>
                                <!-- <input class="form-control col" type="file" name="file" id="file" style="padding-bottom:38px;">
                                <button type="submit" name="import_file" value="import" class="btn btn-primary" required="required">Upload</button> -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>

@endsection


@section('script')

<script>
    $(document).ready(function () {
        $('#payrollmenu').addClass('active');
        $('#payrollmenu_icon').addClass('active');
        $('#policymanagement').addClass('navbtnactive');

        var empTable = $("#emptable").DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('incrementData.getData') }}",
            "order": [
                [0, 'asc'],
                [1, 'asc']
            ],
            "columns": [{
                    "data": "emp_name_with_initial"
                },
                {
                    "data": "increment_type"
                },
                {
                    "data": "increment_value"
                },
                {
                    "data": "effective_month"
                },
                {
                    "data": "paid_value"
                },
                {
                    "data": "id"
                }
            ],
            "columnDefs": [{
                "targets": 1,
                render: function (data, type, row) {
                    // return '<div class="badge badge-primary badge-pill">' + row
                    //     .increment_desc + '</div>';
                    return row.increment_desc;
                }
            },{
                "targets": 2,
                "className": "text-right",
            }, {
                "targets": 3,
                render: function (data, type, row) {
                    // return '<div class="badge badge-primary badge-pill">' + row
                    //     .effective_date + '</div>';
                    return row.effective_date;
                }
            }, {
                "targets": 4,
                "className": "text-right",
                // render: function (data, type, row) {
                //     return '<div class="badge badge-primary badge-pill">' + data + '</div>';
                // }
            }, {
                "targets": 5,
                "orderable": false,
                "className": "actlist_col masked_col text-right",
                render: function (data, type, row) {
                    return '<button class="btn btn-danger btn-sm delete" data-refid="' +
                        data + '"><i class="fas fa-trash-alt"></i></button>';
                }
            }],
            "createdRow": function (row, data, dataIndex) {
                $('td', row).eq(5).removeClass('masked_col');
                $(row).attr('id', 'row-' + data.id); //data[5] //$( row ).data( 'refid', data[3] );
            }
        });
        $('#remuneration_filter').on('keyup change', function () {
            if (empTable.columns(1).search() !== this.value) {
                empTable.columns(1).search(this.value).draw();
            }
        });
        $('#month_filter').on('keyup change', function () {
            if (empTable.columns(3).search() !== this.value) {
                empTable.columns(3).search(this.value).draw();
            }
        });

        $('#upload_record').click(function () {
            //$('#formModalLabel').text('Find Employee');
            //$('#action_button').val('Add');
            //$('#action').val('Add');
            //$('#form_result').html('');

            $('#incrementUploadModal').modal('show');
        });

        var increment_id;

        $(document).on('click', '.delete', async function () {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (r == true) {
                increment_id = $(this).data('refid');

                $.ajax({
                    url: "SalaryIncrement/destroy/" + increment_id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        //alert(JSON.stringify(data));
                        // setTimeout(function () {
                        //     $('#incrementCancelModal').modal('hide');
                        //     //$('#user_table').DataTable().ajax.reload();
                        //     //alert('Data Deleted');
                        // }, 2000);
                        //location.reload()
                        if (data.errors) {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: 'Record Error',
                                message: data.errors,
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                        if (data.result == 'success') {
                            const actionObj = {
                                icon: 'fas fa-trash-alt',
                                title: '',
                                message: 'Record Remove Successfully',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);

                            empTable.row('#row-' + increment_id + '').remove().draw();
                        }
                    }
                });
            }
        });
    });

    function colValidate() {
        var remuneration_file = $('#remuneration_file').find(":selected").val();

        if (remuneration_file == '') {
            Swal.fire({
				icon: 'question',
				title: '',
				text: 'Select file content'
			});
            return false;
        } else {
            return true;
        }

    }
</script>

@endsection