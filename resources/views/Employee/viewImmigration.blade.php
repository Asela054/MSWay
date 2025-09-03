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
                        <form id="PdetailsForm" class="form-horizontal" method="POST" action="{{ route('immigrationInsert') }}">
							{{ csrf_field() }}
                            <div class="form-row">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Issued Date</label>
                                    <input required class="form-control form-control-sm" id="issue_date" name="issue_date" type="date">
                                    @if ($errors->has('issue_date'))
                                    <span class="help-block text-danger">
                                        <strong >{{ $errors->first('issue_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Expire Date</label>
                                    <input required class="form-control form-control-sm" id="expire_date" name="expire_date" type="date">
                                    @if ($errors->has('expire_date'))
                                    <span class="help-block text-danger">
                                        <strong>{{ $errors->first('expire_date') }}</strong>
                                    </span>
                                    @endif
                                </div>                         
                            </div>
                            <div class="form-row">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Eligible Status</label>
                                    <input required class="form-control form-control-sm" id="eligible" name="eligible" type="text">
                                    @if ($errors->has('eligible'))
                                    <span class="help-block text-danger">
                                        <strong>{{ $errors->first('eligible') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Eligible Review Date</label>
                                    <input required class="form-control form-control-sm" id="review_date" name="review_date" type="date">
                                    @if ($errors->has('review_date'))
                                    <span class="help-block text-danger">
                                        <strong>{{ $errors->first('review_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark" >Issued By</label>
                                    <select required class="custom-select form-control form-control-sm" name="issueed_by">
                                        <option>Select</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                    @if ($errors->has('issueed_by'))
                                    <span class="help-block text-danger">
                                        <strong>{{ $errors->first('issueed_by') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Comments</label>
                                    <textarea required class="form-control form-control-sm form-control-solid" id="comments" name="comments" rows="3"></textarea>
                                    @if ($errors->has('comments'))
                                    <span class="help-block text-danger">
                                        <strong>{{ $errors->first('comments') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group mt-3 text-right">
                                @can('employee-edit')
                                    <button type="submit" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-save"></i>&nbsp;Save</button>
                                    <button type="reset" class="btn btn-outline-danger btn-sm mr-2"><i class="far fa-trash-alt"></i>&nbsp;Clear</button>
                                @endcan
                            </div>
                            <input class="form-control" id="emp_id" name="emp_id" type="hidden" value="{{$id}}" readonly>
                        </form>
                        <hr class="border-dark">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Issued Date</th>
                                    <th>Expire Date</th>
                                    <th>Eligible Status</th>
                                    <th>Issued By</th>
                                    <th>Eligible Review Date</th>
                                    <th>Comments</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>                            
                            <tbody>
                            @foreach($immigration as $immigrations)
                                <tr>
                                    <td>{{$immigrations->emp_imm_issue_date}}</td>
                                    <td>{{$immigrations->emp_imm_expire_date}}</td>
                                    <td>{{$immigrations->emp_imm_eligible}}</td>
                                    <td>{{$immigrations->emp_imm_issueed_by}}</td>
                                    <td>{{$immigrations->emp_imm_review_date}}</td>
                                    <td>{{$immigrations->emm_imm_comments}}</td>
                                    <td class="text-right">
                                        @can('employee-edit')
                                            <a href="#" class="btn btn-outline-primary btn-sm btn-edit mr-1 mt-1" data-id="{{$immigrations->id}}"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="#" class="btn btn-outline-danger btn-sm btn-delete mr-1 mt-1" data-id="{{$immigrations->id}}"><i class="far fa-trash-alt"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach 
                            </tbody>
                        </table>
                        </div>
                        <hr class="border-dark">
                        <form class="form-horizontal" method="POST" action="{{ route('immigrationAttachment') }}"
                            enctype="multipart/form-data">
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
                                    <button type="submit" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-save"></i>&nbsp;Save</button>
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

    <div class="modal fade" id="modelEdit" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Edit Immigration</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form method="post" id="formEdit" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Issued Date</label>
                                        <input required class="form-control form-control-sm" id="edit_issue_date" name="issue_date" type="date">

                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Expire Date</label>
                                        <input required class="form-control form-control-sm" id="edit_expire_date" name="expire_date" type="date">

                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Eligible Status</label>
                                        <input required class="form-control form-control-sm" id="edit_eligible" name="eligible" type="text">

                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Eligible Review Date</label>
                                        <input required class="form-control form-control-sm" id="edit_review_date" name="review_date" type="date">

                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark" >Issued By</label>
                                        <select required class="custom-select form-control form-control-sm" id="edit_issued_by" name="issueed_by">
                                            <option>Select</option>
                                            <option value="Admin">Admin</option>
                                        </select>

                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Comments</label>
                                        <textarea required class="form-control form-control-sm form-control-solid" id="edit_comments" name="comments" rows="3"></textarea>
                                        @if ($errors->has('comments'))
                                            <span class="help-block text-danger">
                                        <strong>{{ $errors->first('comments') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <input class="btn btn-primary btn-sm float-right mt-2" type="submit" value="Update"/>
                                <input type="hidden" name="edit_id" id="edit_id"/>
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
</main>
              
@endsection
@section('script')
<script>
    $('#employee_menu_link').addClass('active');
    $('#employee_menu_link_icon').addClass('active');
    $('#employeeinformation').addClass('navbtnactive');
	$('#view_immigration_link').addClass('navbtnactive');

    $(document).on('click', '.btn-edit', function () {
        var id = $(this).data('id');
        $('#form_result').html('');
        $.ajax({
            url: "../getImmigrationDetail/"+id,
            dataType: "json",
            success: function (data) {
                $('#edit_issue_date').val(data.result.emp_imm_issue_date);
                $('#edit_expire_date').val(data.result.emp_imm_expire_date);
                $('#edit_eligible').val(data.result.emp_imm_eligible);
                $('#edit_review_date').val(data.result.emp_imm_review_date);
                $('#edit_issued_by').val(data.result.emp_imm_issueed_by);
                $('#edit_comments').val(data.result.emm_imm_comments);
                $('#edit_id').val(data.result.id);
                $('#modelEdit').modal('show');
            }
        })
    });

    $('#formEdit').on('submit', function(event){
        event.preventDefault();
        var action_url = '../immigrationUpdate';

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
                    $('#formEdit')[0].reset();
                    //$('#titletable').DataTable().ajax.reload();
                    location.reload()
                }
                $('#form_result').html(html);
            }
        });
    });

    let delete_id = 0;
    $(document).on('click', '.btn-delete', function () {
        delete_id = $(this).data('id');
        $('#confirmModal').modal('show');
    });

    $('#ok_button').click(function () {
        $.ajax({
            url: "../immigration_delete/"+delete_id,
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
