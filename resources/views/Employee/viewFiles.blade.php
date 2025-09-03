@extends('layouts.app')

@section('content')
    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.employee_nav_bar')
               
            </div>
        </div>
        <div class="container-fluid mt-4">
            {{-- Show global success/error messages --}}
            @if (session('success'))
                <div class="alert alert-success" id="att_msg">{{ session('success') }}</div>
            @elseif (session('error'))
                <div class="alert alert-danger" id="att_msg">{{ session('error') }}</div>
            @else
                <div id="att_msg"></div>
            @endif
            <div class="row">
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col">
                            <div class="card mb-2">
                                <div class="card-header">Add Employee Files</div>
                                <div class="card-body">

                                    <form class="form-horizontal" method="POST" action="{{ route('employeeAttachmentJson') }}" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="form-row">
                                            <div class="col">
                                                <label class="small font-weight-bold text-dark">Select File</label><br>
                                                <input type="file" class="form-control form-control-sm" id="empattachment" name="empattachment" required>
                                                @if ($errors->has('empattachment'))
                                                    <span class="help-block">
												<strong class="text-danger">{{ $errors->first('empattachment') }}</strong>
											</span>
                                                @endif
                                            </div>
                                            <div class="col">
                                                <label class="small font-weight-bold text-dark">Attachment Type*</label>
                                                <select name="attachment_type" class="form-control form-control-sm" id="attachment_type" >
                                                    <option value="1"> CV</option>
                                                    <option value="2"> Birth Certificate</option>
                                                    <option value="3"> O/L Certificates</option>
                                                    <option value="4"> A/L Certificates</option>
                                                    <option value="5"> Other Educational Certificates</option>
                                                    <option value="6"> Professional Qualification</option>
                                                    <option value="7"> Training Certificates</option>
                                                    <option value="8"> Service Letter</option>
                                                    <option value="9"> NIC</option>
                                                    <option value="10"> Driving License</option>
                                                    <option value="11"> Grama Niladhari Certificate</option>
                                                    <option value="12"> Police Clearance Certificate </option>
                                                </select>
                                                @if ($errors->has('attachment_type'))
                                                    <span class="help-block">
												<strong class="text-danger">{{ $errors->first('attachment_type') }}</strong>
											</span>
                                                @endif
                                            </div>
                                            <div class="col">
                                                <label class="small font-weight-bold text-dark">Comment</label>
                                                <textarea class="form-control form-control-sm" id="empcomment" name="empcomment" rows="1"></textarea>
                                                @if ($errors->has('empcomment'))
                                                    <span class="help-block">
												<strong class="text-danger">{{ $errors->first('empcomment') }}</strong>
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
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col">
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="center-block fix-width scroll-inner">
                                     <table class="table table-striped nowrap" style="width: 100%">
                                         <thead>
                                            <tr>
                                                <th>File Name</th>
                                                <th>File Type</th>
                                                <th>Comment</th>
                                                <th class="text-right">Action</th>
                                            </tr>
                                         </thead>
                                         <tbody>
                                            @php
                                                $typeNames = [
                                                    1 => 'CV',
                                                    2 => 'Birth Certificate',
                                                    3 => 'O/L Certificates',
                                                    4 => 'A/L Certificates',
                                                    5 => 'Other Educational Certificates',
                                                    6 => 'Professional Qualification',
                                                    7 => 'Training Certificates',
                                                    8 => 'Service Letter',
                                                    9 => 'NIC',
                                                    10 => 'Driving License',
                                                    11 => 'Grama Niladhari Certificate',
                                                    12 => 'Police Clearance Certificate',
                                                ];
                                            @endphp
                                            @foreach($attachments as $att)
                                                <tr>
                                                    <td> <a href="{{route('download_file', $att->emp_ath_file_name)}}">{{$att->emp_ath_file_name}}</a> </td>
                                                    <td>
                                                        @if($att->attachment_type_rel && isset($att->attachment_type_rel->name))
                                                            {{$att->attachment_type_rel->name}}
                                                        @else
                                                            {{ $typeNames[$att->attachment_type] ?? $att->attachment_type }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $att->empcomment }}</td>
                                                    <td class="text-right"> <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{$att->emp_ath_id}}"> <i class="fa fa-trash"></i> </button> </td>
                                                </tr>
                                            @endforeach
                                         </tbody>
                                     </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                @include('layouts.employeeRightBar')

            </div>
        </div>

    </main>

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
        $(document).ready(function() {

        $('#employee_menu_link').addClass('active');
        $('#employee_menu_link_icon').addClass('active');
        $('#employeeinformation').addClass('navbtnactive');
        $('#view_empfile_link').addClass('navbtnactive');

            let delete_id = 0;
            $(document).on('click', '.btn-delete', function () {
                delete_id = $(this).data('id');
                $('#confirmModal').modal('show');
                $('#ok_button').text('Delete');
            });

            $('#ok_button').click(function () {
                $.ajax({
                    url: "../attachment/destroy/" + delete_id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {//alert(data);
                        let html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#confirmModal').modal('hide');
                        $('#att_msg').html(html);
                        location.reload();
                    }
                })
            });

        });
    </script>
@endsection
