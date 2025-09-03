@extends('layouts.app')

@section('content')
<main>
    <div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            <div class="page-header-content py-3">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="fas fa-user"></i></div>
                    <span>Immigration Detail</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-9">
                        <form id="PdetailsForm" class="form-horizontal" method="POST" action="{{ route('immigrationInsert') }}">
							{{ csrf_field() }}
                            <div class="form-row">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Issued Date</label>
                                    <input class="form-control form-control-sm" id="issue_date" name="issue_date" type="text">
                                    @if ($errors->has('issue_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('issue_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Expire Date</label>
                                    <input class="form-control form-control-sm" id="expire_date" name="expire_date" type="text">
                                    @if ($errors->has('expire_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('expire_date') }}</strong>
                                    </span>
                                    @endif
                                </div>                         
                            </div>
                            <div class="form-row">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Eligible Status</label>
                                    <input class="form-control form-control-sm" id="eligible" name="eligible" type="text">
                                    @if ($errors->has('eligible'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('eligible') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Eligible Review Date</label>
                                    <input class="form-control form-control-sm" id="review_date" name="review_date" type="text">
                                    @if ($errors->has('review_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('review_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Issued By</label>
                                    <select class="custom-select" name="issueed_by">
                                        <option>Select</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                    @if ($errors->has('issueed_by'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('issueed_by') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Comments</label>
                                    <textarea class="form-control form-control-sm form-control-solid" id="comments" name="comments" rows="3"></textarea>
                                    @if ($errors->has('comments'))
                                    <span class="help-block">
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
                        <table class="table table-striped table-bordered table-sm small" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Issued Date</th>
                                    <th>Expire Date</th>
                                    <th>Eligible Status</th>
                                    <th>Issued By</th>
                                    <th>Eligible Review Date</th>
                                    <th>Comments</th>
                                    <!--th>Action</th-->
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
                                    <!--td>
                                        <a href="dependentUpdate/{{$immigrations->id}}" class="btn btn-datatable btn-icon btn-transparent-dark mr-2"><i data-feather="edit"></i></a>
                                        <a href="dependentDelete/{{$immigrations->id}}" class="btn btn-datatable btn-icon btn-transparent-dark"><i data-feather="trash-2"></i></a>
                                    </td-->
                                </tr>
                                @endforeach 
                            </tbody>
                        </table>
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
                    
                </main>
              
@endsection
@section('script')
<script>
    $('#employee_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
    $('#employee_collapse').addClass('show');
    $('#employee_add_link').addClass('active');

    $('#dataTable').DataTable();
  $('#issue_date').datepicker({
        format: "yyyy/mm/dd",
        autoclose: true
    });
	$('#expire_date').datepicker({
        format: "yyyy/mm/dd",
        autoclose: true
    });
	$('#review_date').datepicker({
        format: "yyyy/mm/dd",
        autoclose: true
    });
	</script>
@endsection
