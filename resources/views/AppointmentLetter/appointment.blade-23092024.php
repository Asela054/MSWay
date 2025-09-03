
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.employee_nav_bar')
               
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <form method="POST" action="{{ route('appoinementletterinsert') }}"  class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="form-row mb-1">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                    @foreach ($companies as $company){
                                        <option value="{{$company->id}}">{{$company->name}}</option>
                                    }  
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                    @foreach ($employees as $employee){
                                        <option value="{{$employee->id}}" data-jobid="{{$employee->emp_job_code}}">{{$employee->emp_name_with_initial}}</option>
                                    }  
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Job Title</label>
                                <select name="jobtitle" id="jobtitle" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                    @foreach ($jobtitles as $jobtitle){
                                        <option value="{{$jobtitle->id}}">{{$jobtitle->title}}</option>
                                    }  
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 ">
                                <label class="small font-weight-bold text-dark">Date</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="letterdate" name="letterdate" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                           
                        </div>
                        <div class="form-row mb-1">
                            <div class="col-3">
                                <label class="small font-weight-bold text-dark">Compensation</label>
                                <input type="text" class="form-control form-control-sm" id="compensation"name="compensation" value="">
                            </div>
                            <div class="col-md-3 ">
                                <label class="small font-weight-bold text-dark">Probation Period : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd">
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            <div class="col-3">
                                <label class="small font-weight-bold text-dark">Working Hours</label>
                                <input type="text" class="form-control form-control-sm" id="workinghours"name="workinghours" value="">
                            </div>
                            <div class="col-3">
                                <label class="small font-weight-bold text-dark">Leave Policy</label>
                                <input type="text" class="form-control form-control-sm" id="leaves"name="leaves" value="">
                            </div>
                            <div class="col-3">
                                <label class="small font-weight-bold text-dark">Termination Clause</label>
                                <input type="text" class="form-control form-control-sm" id="termination"name="termination" value="">
                            </div>

                        </div>
                        <input type="hidden" name="recordOption" id="recordOption" value="1">
                        <input type="hidden" name="recordID" id="recordID" value="">
                        <div class="form-group mt-4 text-center">
                            @can('Appointment-letter-create')
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-sm px-5"><i class="far fa-save"></i>&nbsp;&nbsp;Add</button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2 main_card">
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%" id="dataTable">
                                <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>Employee Name</th>
                                    <th>Date</th>
                                    <th>Job Title</th>
                                    <th>Company</th>
                                    <th class="text-right">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        </div>
                </div>
            </div>

    </main>

   <!-- Bootstrap Modal -->
<div class="modal fade" id="view_more_modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <div class="modal-header p-2">
            <h5 class="modal-title" id="staticBackdropLabel">Appointment Letter</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>


        <div class="letter-content" id="letterContent">
            <div class="letter-header">
                <h3 class="companyname"></h3>
                <p class="companyaddress"></p>
                <p>Date: <p class="printletterdate"></p></p>
            </div>
            <div class="letter-body">
                <p class="empoyeename"></p>
                <p class="empaddress"></p>
                Dear <p class="empoyeename"></p>,

                <p><strong>Subject: Appointment Letter for the Position of <p class="position"></p> </strong></p>

                <p>We are pleased to inform you that you have been selected for the position of <strong><p class="position"></p></strong> at <strong><p class="companyname"></p></strong>. 
                Your appointment is effective from <strong<p><p class="'printletterdate"></p></p></strong>, subject to the following terms and conditions:</p>

                <ul>
                    <li><strong>Job Title:</strong> You will be appointed as <p class="position"></p>.</li>
                    <li><strong>Compensation:</strong> You will receive an annual salary of <p id="salary"></p>  with a breakdown of the components mentioned in the attached salary structure.</li>
                    <li><strong>Probation Period:</strong> You will be on probation for a period of <p id="probationfrom"></p> to <p id="probationto"></p>, during which your performance will be reviewed,
                         and your employment may be confirmed or terminated based on the outcome.</li>
                    <li><strong>Working Hours:</strong> The standard working hours are <p id="workinghours"></p>, Monday to Friday.</li>
                    <li><strong>Leave Policy:</strong> You are entitled to <p id="leavecount"></p> of paid leave per year, in accordance with company policies.</li>
                    <li><strong>Termination Clause:</strong> Either party can terminate this contract with 30 days' notice or salary in lieu of the notice period.</li>
                </ul>

                <p>Please sign and return the attached copy of this letter as your formal acceptance of this offer. We are excited to have you on our team and look forward to your valuable contributions to the success of ABC Corporation.</p>
                <p>Welcome aboard!</p>
            </div>

            <div class="letter-footer">
                <p>Sincerely,</p>
                <p>James White</p>
                <p>CEO, <p class="companyname"></p></p>
            </div>
        </div>

        <div class="modal-footer p-2">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="printLetter()">Print</button>
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
        $(document).ready(function () {

            $('#employee_menu_link').addClass('active');
            $('#employee_menu_link_icon').addClass('active');
            $('#appointmentletter').addClass('navbtnactive');

            $('#employee_f').select2({ width: '100%' });

            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{!! route('appoinementletterlist') !!}",
                   
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'emp_name', name: 'emp_name' },
                    { data: 'date', name: 'date' },
                    { data: 'emptitle', name: 'emptitle' },
                    { data: 'companyname', name: 'companyname' },
                    {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return '<div style="text-align: right;">' + data + '</div>';
                    }
                },
                ],
                "bDestroy": true,
                "order": [
                    [0, "desc"]
                ]
            });

    
              // edit function
              $(document).on('click', '.edit', function () {
                var id = $(this).attr('id');
               
                $('#form_result').html('');
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })

                $.ajax({
                    url: '{!! route("appoinementletteredit") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                    success: function (data) {
                        $('#company').val(data.result.company_id);
                        $('#employee_f').val(data.result.employee_id);
                        $('#jobtitle').val(data.result.jobtitle);
                        $('#letterdate').val(data.result.date);
                        $('#compensation').val(data.result.compensation);
                        $('#from_date').val(data.result.probation_from);
                        $('#to_date').val(data.result.probation_to);
                        $('#workinghours').val(data.result.working_hours);
                        $('#leaves').val(data.result.leaves);
                        $('#termination').val(data.result.terminations);
                        $('#recordID').val(id);
                        $('#recordOption').val(2);
                    }
                })
            });

            var user_id;

            $(document).on('click', '.delete', function () {
                user_id = $(this).attr('id');
                $('#confirmModal').modal('show');
            });
          
            $('#ok_button').click(function () {
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                $.ajax({
                    url: '{!! route("appoinementletterdelete") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: user_id },
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {//alert(data);
                        setTimeout(function () {
                            $('#confirmModal').modal('hide');
                            $('#dataTable').DataTable().ajax.reload();
                        }, 2000);
                        location.reload()
                    }
                })
            });


            $(document).on('click', '.print', function (e) {
                var id = $(this).attr('id');
                $('#form_result').html('');
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                $.ajax({
                    url: '{!! route("appoinementletterprintdata") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                    success: function (data) {
                        $('3companyname').html(data.result.name);
                        $('.companyaddress').html(data.result.address);
                        $('.printletterdate').html(data.result.date);
                        $('.empoyeename').html(data.result.emp_fullname);
                        $('.empaddress').html(data.result.emp_address);
                        $('.position').html(data.result.emptitle);
                        $('#salary').html(data.result.compensation);
                        $('#probationfrom').html(data.result.probation_from);
                        $('#probationto').html(data.result.probation_to);
                        $('#workinghours').html(data.result.working_hours);
                        $('#leavecount').html(data.result.leaves);
                        $('#view_more_modal').modal('show');
                    }
                })
            });


            $('#employee_f').change(function () {
                var jobid = $('#employee_f option:selected').data('jobid');
                $('#jobtitle').val(jobid);
            });

        });

          function deactive_confirm() {
        return confirm("Are you sure you want to deactive this?");
    }

    function active_confirm() {
        return confirm("Are you sure you want to active this?");
    }
    function printLetter() {
        var content = document.getElementById('letterContent').innerHTML;
        var originalContent = document.body.innerHTML;

        document.body.innerHTML = content;
        window.print();

        document.body.innerHTML = originalContent;
        window.location.reload();
    }
    </script>

@endsection

