@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.employee_nav_bar')
           
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-9">
                        @if(session('success'))
                            <div class="alert alert-success">{{session('success')}}</div>
                        @endif
                        <form id="PdetailsForm" class="form-horizontal" method="POST" action="{{ route('dependentInsert') }}">
                            {{ csrf_field() }}
                            <div class="form-row">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Name</label>
                                    <input required class="form-control form-control-sm @if ($errors->has('dep_name')) border-danger-soft @endif"
                                           id="dep_name" name="dep_name" type="text" value="{{old('dep_name')}}">
                                    @if ($errors->has('dep_name')) <p class="text-danger">{{ $errors->first('dep_name') }}</p> @endif
                                </div>
                                <div class="col">
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
                                    </select>
                                    @if ($errors->has('relationship')) <p class="text-danger">{{ $errors->first('relationship') }}</p> @endif
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Date of Birth</label>
                                    <input required class="form-control form-control-sm @if ($errors->has('birthday')) border-danger-soft @endif" id="birthday" name="birthday" type="text" value="{{old('birthday')}}"
                                    placeholder="yyyy-mm-dd">
                                    @if ($errors->has('birthday')) <p class="text-danger">{{ $errors->first('birthday') }}</p> @endif
                                </div>
                            </div>
                            <div class="form-group mt-3 text-right">
                                @can('employee-edit')
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                    <button type="reset" class="btn btn-outline-danger btn-sm mr-2"><i class="far fa-trash-alt"></i>&nbsp;Clear</button>
                                @endcan
                            </div>
                            <input type="hidden" class="form-control form-control-sm" id="emp_id" name="emp_id" value="{{$id}}">
                        </form>
                        <hr class="border-dark">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Relation</th>
                                    <th>Date of Birth</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>                            
                            <tbody>
                            @foreach($dependent as $dependents)
                                <tr>
                                    <td><a href="">{{$dependents->emp_dep_name}}</a></td>
                                    <td>{{$dependents->emp_dep_relation}}</td>
                                    <td>{{$dependents->emp_dep_birthday}}</td>
                                    <td class="text-right">
                                        @can('employee-edit')
                                            <a href="#" class="btn btn-outline-primary btn-sm btn-edit mr-1 mt-1" data-id="{{$dependents->id}}"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="#" class="btn btn-outline-danger btn-sm btn-delete mr-1 mt-1" data-id="{{$dependents->id}}"><i class="far fa-trash-alt"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <hr class="border-dark">
                        <form class="form-horizontal" method="POST" action="{{ route('dependetAttachment') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-row">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Select File</label>
                                    <input type="file" class="form-control form-control-sm" id="empattachment" name="empattachment">
                                    @if ($errors->has('empattachment'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('empattachment') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Comment</label>
                                    <textarea class="form-control form-control-sm" id="empcomment" name="empcomment" rows="3"></textarea>
                                    @if ($errors->has('empcomment'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('empcomment') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                @can('employee-edit')
                                    <button type="submit" name="" id="" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-save"></i>&nbsp;Save</button>
                                @endcan
                            </div>
                            <input type="hidden" class="form-control" id="id" name="id" value="{{$id}}">
                        </form>
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
                <h5 class="modal-title" id="staticBackdropLabel">Edit Dependent</h5>
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
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Name</label>
                                    <input class="form-control form-control-sm"
                                           id="edit_dep_name" name="dep_name" type="text">
                                </div>
                                <div class="col">
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
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Date of Birth</label>
                                    <input class="form-control form-control-sm" id="edit_birthday" name="birthday" type="text" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            <input class="btn btn-primary btn-sm float-right mt-2" type="submit" value="Update"/>
                            <input type="hidden" name="dependent_id" id="dependent_id"/>
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
$('#dataTable').DataTable();

    $('#employee_menu_link').addClass('active');
    $('#employee_menu_link_icon').addClass('active');
    $('#employeeinformation').addClass('navbtnactive');
	$('#view_dependent_link').addClass('navbtnactive');

$("#birthday").datetimepicker({
    pickTime: false,
    minView: 2,
    format: 'yyyy-mm-dd',
    autoclose: true,
});

$("#edit_birthday").datetimepicker({
    pickTime: false,
    minView: 2,
    format: 'yyyy-mm-dd',
    autoclose: true,
});

$(document).on('click', '.btn-edit', function () {
    var id = $(this).data('id');
    $('#form_result').html('');
    $.ajax({
        url: "../getDependentDetail/"+id,
        dataType: "json",
        success: function (data) {
            $('#edit_dep_name').val(data.result.emp_dep_name);
            $('#edit_relationship').val(data.result.emp_dep_relation);
            $('#edit_birthday').val(data.result.emp_dep_birthday);
            $('#dependent_id').val(data.result.id);
            $('#modelDependent').modal('show');
        }
    })
});

$('#formDependent').on('submit', function(event){
    event.preventDefault();
    var action_url = '../dependentUpdate';

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
        url: "../dependent_delete/"+dependent_id,
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
