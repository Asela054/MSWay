
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
                    <form method="POST" action="{{ route('appoinementletterinsert') }}" class="form-horizontal">
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
                                    <option value="{{$employee->id}}" data-jobid="{{$employee->emp_job_code}}">
                                        {{$employee->emp_name_with_initial}}</option>
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
                                    <input type="date" id="letterdate" name="letterdate"
                                        class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">
                                </div>
                            </div>

                        </div>
                        <div class="form-row mb-1">
                            <div class="col-3">
                                <label class="small font-weight-bold text-dark">Compensation</label>
                                <input type="text" class="form-control form-control-sm" id="compensation"
                                    name="compensation" value="">
                            </div>
                            <div class="col-md-3 ">
                                <label class="small font-weight-bold text-dark">Probation Period : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date"
                                        class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">
                                    <input type="date" id="to_date" name="to_date" class="form-control"
                                        placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="small font-weight-bold text-dark">No of Weeks</label>
                                <input type="text" class="form-control form-control-sm" id="noweeks"
                                    name="noweeks" value="">
                            </div>
                            <div class="col-4">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">On Time</label>
                                        <input type="time" class="form-control form-control-sm" id="ontime" name="ontime" value="">
                                    </div>
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Off Time</label>
                                        <input type="time" class="form-control form-control-sm" id="offtime"
                                            name="offtime" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-3" style="margin-right: 15px;">
                                <label class="small font-weight-bold text-dark">Leave Policy</label>
                                <input type="text" class="form-control form-control-sm" id="leaves" name="leaves"
                                    value="">
                            </div>
                            <div class="col-3">
                                <label class="form-control-label">Saturday</label>
                                <div class="row">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="saturdayfull"
                                            name="saturdayshift" value="Full Day">
                                        <label class="custom-control-label" for="saturdayfull">Full Day</label>
                                    </div>&nbsp;
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="saturdayhalf"
                                            name="saturdayshift" value="Half Day">
                                        <label class="custom-control-label" for="saturdayhalf">Half Day</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="recordOption" id="recordOption" value="1">
                        <input type="hidden" name="recordID" id="recordID" value="">
                        <div class="form-group mt-4 text-center">
                            @can('Appointment-letter-create')
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-sm px-5"><i
                                    class="far fa-save"></i>&nbsp;&nbsp;Add</button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2 main_card">
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display"
                                style="width: 100%" id="dataTable">
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
                        $('#noweeks').val(data.result.no_ofweeks);
                        $('#ontime').val(data.result.on_time);
                        $('#offtime').val(data.result.off_time);
                        $('#leaves').val(data.result.leaves);
                        var shiftValue = data.result.saturday_shift;

                        $('input[name="saturdayshift"][value="' + shiftValue + '"]').prop('checked', true);
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
    e.preventDefault(); // Prevent default behavior if it's a link
    
    var id = $(this).attr('id');
    $('#form_result').html('');

    // Open the tab or window first
    var newTab = window.open('', '_blank');
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: '{!! route("appoinementletterprintdata") !!}',
        type: 'POST',
        dataType: "json",
        data: {
            _token: '{{ csrf_token() }}',
            id: id
        },
        success: function (data) {
            var pdfBlob = base64toBlob(data.pdf, 'application/pdf');
            var pdfUrl = URL.createObjectURL(pdfBlob);

            // Write content to the opened tab
            newTab.document.write('<html><body><embed width="100%" height="100%" type="application/pdf" src="' + pdfUrl + '"></body></html>');
        },
        error: function () {
            console.log('PDF request failed.');
            newTab.document.write('<html><body><p>Failed to load PDF. Please try again later.</p></body></html>');
        }
    });
});


            $('#employee_f').on('change', function() {
                var employeeId = $(this).val();
                if (employeeId) {
                    $.ajax({
                        url: '{!! route("getshiftdetails") !!}',
                        type: 'GET',
                        data: { emp_id: employeeId },
                        success: function(response) {

                            $('#ontime').val(response.shift.onduty_time);
                            $('#offtime').val(response.shift.offduty_time);
                            var jobid = $('#employee_f option:selected').data('jobid');
                            $('#jobtitle').val(jobid);

                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to fetch shift details:', error);
                        }
                    });
                }
            });

        });

          function deactive_confirm() {
        return confirm("Are you sure you want to deactive this?");
    }

    function active_confirm() {
        return confirm("Are you sure you want to active this?");
    }
    
    function base64toBlob(base64Data, contentType) {
    var byteCharacters = atob(base64Data);
    var byteArrays = [];

    for (var offset = 0; offset < byteCharacters.length; offset += 512) {
        var slice = byteCharacters.slice(offset, offset + 512);

        var byteNumbers = new Array(slice.length);
        for (var i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        var byteArray = new Uint8Array(byteNumbers);
        byteArrays.push(byteArray);
    }

    return new Blob(byteArrays, { type: contentType });
}
    </script>

@endsection

