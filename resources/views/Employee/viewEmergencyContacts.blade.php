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
                        <span>Emergency Contacts</span>
                    </h1>
                </div>
            </div>
    </div>    
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-lg-9 col-12">
                            @if(session('success'))
                                <div class="alert alert-success">{{session('success')}}</div>
                            @endif
                            <form id="PdetailsForm" class="form-horizontal" method="POST" action="{{ route('emergencyContactInsert') }}">
                                {{ csrf_field() }}
                                <div class="form-row">
                                    <div class="col-md-6 col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Name</label>
                                        <input required class="form-control form-control-sm @if ($errors->has('name')) border-danger-soft @endif"
                                            id="name" name="name" type="text" value="{{old('name')}}">
                                        @if ($errors->has('name')) <p class="text-danger">{{ $errors->first('name') }}</p> @endif
                                    </div>
                                    <div class="col-md-3 col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Relationship</label>
                                        <select required class="form-control form-control-sm @if ($errors->has('relationship')) border-danger-soft @endif"
                                                id="relationship" name="relationship">
                                            <option @if(old('relationship') == '') selected @endif value="">Select</option>
                                            <option @if(old('relationship') == 'Son') selected @endif value="Son">Son</option>
                                            <option @if(old('relationship') == 'Daughter') selected @endif value="Daughter">Daughter</option>
                                            <option @if(old('relationship') == 'Wife') selected @endif value="Wife">Wife</option>
                                            <option @if(old('relationship') == 'Husband') selected @endif value="Husband">Husband</option>
                                            <option @if(old('relationship') == 'Father') selected @endif value="Father">Father</option>
                                            <option @if(old('relationship') == 'Mother') selected @endif value="Mother">Mother</option>
                                            <option @if(old('relationship') == 'Brother') selected @endif value="Brother">Brother</option>
                                            <option @if(old('relationship') == 'Sister') selected @endif value="Sister">Sister</option>
                                            <option @if(old('relationship') == 'Friend') selected @endif value="Friend">Friend</option>
                                            <option @if(old('relationship') == 'Other') selected @endif value="Other">Other</option>
                                        </select>
                                        @if ($errors->has('relationship')) <p class="text-danger">{{ $errors->first('relationship') }}</p> @endif
                                    </div>
                                    
                                    <div class="col-md-3 col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Contact No</label>
                                        <input required class="form-control form-control-sm @if ($errors->has('contact_no')) border-danger-soft @endif"
                                            id="contact_no" name="contact_no" type="text" value="{{old('contact_no')}}">
                                        @if ($errors->has('contact_no')) <p class="text-danger">{{ $errors->first('contact_no') }}</p> @endif
                                    </div>
                                    <div class="col-md-6 col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Address</label>
                                        <textarea required class="form-control form-control-sm @if ($errors->has('address')) border-danger-soft @endif" name="address" rows="3">{{old('address')}}</textarea>
                                        @if ($errors->has('address')) <p class="text-danger">{{ $errors->first('address') }}</p> @endif
                                    </div>
                                </div>
                                <div class="form-group mt-3 text-right">
                                    @can('employee-edit')
                                        <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm px-4 mb-2 mb-sm-0"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                        <button type="reset" class="btn btn-danger btn-sm mr-2 mb-2 mb-sm-0"><i class="far fa-trash-alt"></i>&nbsp;Clear</button>
                                    @endcan
                                </div>
                                <input type="hidden" class="form-control form-control-sm" id="emp_id" name="emp_id" value="{{$id}}">
                            </form>
                            <hr class="border-dark">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small" id="dataTable">
                                    <thead>
                                    <tr>
                                        <th>NAME</th>
                                        <th>RELATION</th>
                                        <th class="d-none d-md-table-cell">ADDRESS</th>
                                        <th>CONTACT NO</th>
                                        <th class="text-right">ACTION</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($emergency_contact as $ec)
                                        <tr>
                                            <td><a href="">{{$ec->name}}</a></td>
                                            <td>{{$ec->relationship}}</td>
                                            <td class="d-none d-md-table-cell">{{$ec->address}}</td>
                                            <td>{{$ec->contact_no}}</td>
                                            <td class="text-right text-nowrap">
                                                @can('employee-edit')
                                                    <a href="#" class="btn btn-primary btn-sm btn-edit mr-1 mt-1" data-id="{{$ec->id}}"><i class="fas fa-pencil-alt"></i></a>
                                                    <a href="#" class="btn btn-danger btn-sm btn-delete mr-1 mt-1" data-id="{{$ec->id}}"><i class="far fa-trash-alt"></i></a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <hr class="border-dark">

                        </div>
                        @include('layouts.employeeRightBar')
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modelDependent" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Edit Emergency Contact</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form method="post" id="formDependent" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row">
                                    <div class="col-sm-4">
                                        <label class="small font-weight-bold text-dark">Name</label>
                                        <input required class="form-control form-control-sm"
                                               id="edit_name" name="name" type="text">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small font-weight-bold text-dark">Relationship</label>
                                        <select class="form-control form-control-sm"
                                                id="edit_relationship" name="relationship">
                                            <option >Select</option>
                                            <option value="Son">Son</option>
                                            <option value="Daughter">Daughter</option>
                                            <option value="Wife">Wife</option>
                                            <option value="Husband">Husband</option>
                                            <option value="Father">Father</option>
                                            <option value="Mother">Mother</option>
                                            <option value="Brother">Brother</option>
                                            <option value="Sister">Sister</option>
                                            <option value="Friend">Friend</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small font-weight-bold text-dark">Address</label>
                                        <textarea class="form-control form-control-sm" id="edit_address" name="address"></textarea>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small font-weight-bold text-dark">Contact No</label>
                                        <input class="form-control form-control-sm"
                                               id="edit_contact_no" name="contact_no" type="text">
                                    </div>
                                </div>
                                <input class="btn btn-primary btn-sm float-right mt-2" type="submit" value="Update"/>
                                <input type="hidden" name="ec_id" id="ec_id"/>
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

@endsection

@section('script')
    <script>
        $('#dataTable').DataTable({
        "destroy": true,
        "processing": true,
        
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Emergemcy  Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Emergemcy Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'portrait', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Emergemcy  Information',
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

        $('#employee_menu_link').addClass('active');
        $('#employee_menu_link_icon').addClass('active');
        $('#employeeinformation').addClass('navbtnactive');
	    $('#view_contact_link').addClass('active');

        $(document).on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            $('#form_result').html('');
            $.ajax({
                url: "../getEmergencyContactDetail/"+id,
                dataType: "json",
                success: function (data) {
                    $('#edit_name').val(data.result.name);
                    $('#edit_relationship').val(data.result.relationship);
                    $('#edit_address').val(data.result.address);
                    $('#edit_contact_no').val(data.result.contact_no);
                    $('#ec_id').val(data.result.id);
                    $('#modelDependent').modal('show');
                }
            })
        });

        $('#formDependent').on('submit', function(event){
            event.preventDefault();
            var action_url = '../emergencyContactUpdate';

            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {//alert(data);

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
                        $('#formDependent')[0].reset();
                        //$('#titletable').DataTable().ajax.reload();
                        location.reload()
                    }
                    $('#form_result').html(html);
                }
            });
        });

        let dependent_id = 0;
        $(document).on('click', '.btn-delete', function () {
            dependent_id = $(this).data('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function () {
            $.ajax({
                url: "../emergency_contact_delete/"+dependent_id,
                beforeSend: function () {
                    $('#ok_button').text('Deleting...');
                },
                success: function (data) {//alert(data);
                    setTimeout(function () {
                        let html = '<div class="alert alert-success"> Success </div>';
                        $('#form_result').html(html);
                    }, 2000);
                    $('#confirmModal').modal('hide');
                    location.reload()
                }
            })
        });

    </script>
@endsection
