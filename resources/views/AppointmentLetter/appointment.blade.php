
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
                        <span>Appointment Letter</span>
                    </h1>
                </div>
            </div>
        </div>    

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body">
                    <form method="POST" action="{{ route('appoinementletterinsert') }}" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="form-row mb-1">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm">
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
                                        class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd" required>
                                </div>
                            </div>

                        </div>
                        <div class="form-row mb-1">
                            <div class="col-3">
                                <label class="small font-weight-bold text-dark">Compensation Amount</label>
                                <input type="number" class="form-control form-control-sm" id="compensation"
                                    name="compensation" value="" required>
                            </div>
                            <div class="col-md-3 ">
                                <label class="small font-weight-bold text-dark">Probation Period : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date"
                                        class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd" required>
                                    <input type="date" id="to_date" name="to_date" class="form-control"
                                        placeholder="yyyy-mm-dd" required>
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
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-sm fa-pull-right px-4"><i
                                    class="fas fa-plus"></i>&nbsp;&nbsp;Add
                            </button>
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
                                        <th>EMPLOYEE NAME</th>
                                        <th>DATE</th>
                                        <th>JOB TITLE</th>
                                        <th>COMPANY</th>
                                        <th class="text-right">ACTION</th>
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


@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#employee_menu_link').addClass('active');
            $('#employee_menu_link_icon').addClass('active');
            $('#appointmentletter').addClass('navbtnactive');

            let company_f = $('#company_f');
            let employee_f = $('#employee_f');

            company_f.select2({
                placeholder: 'Select a Company',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("company_list_sel2")}}',
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

            employee_f.select2({
                placeholder: 'Select a Employee',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("employee_list_letter")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company_f.val()
                        }
                    },
                    cache: true
                }
            });

            $('#dataTable').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Appointment Letter',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'Appointment Letter', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'portrait', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Appointment Letter',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-print mr-2"></i> Print',
                        customize: function(win) {
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        },
                    },
                    // 'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                "order": [
                    [0, "desc"]
                ],
                ajax: {
                    "url": "{!! route('appoinementletterlist') !!}",
                   
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'employee_display', name: 'employee_display' },
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
            });

    
              // edit function
              $(document).on('click', '.edit',async function () {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
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
                       // Set Company
                        if (data.result.company_id && data.result.company_name) {
                            var companyOption = new Option(data.result.company_name, data.result.company_id, true, true);
                            $('#company_f').append(companyOption).trigger('change');
                        }
                        // Set Employee
                        if (data.result.employee_id && data.result.emp_name) {
                            var employeeOption = new Option(data.result.emp_name, data.result.emp_id, true, true);
                            $('#employee_f').append(employeeOption).trigger('change');
                        }
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
                }
            });

            var user_id;

            $(document).on('click', '.delete',async function () {
                 var r = await Otherconfirmation("You want to remove this ? ");
                if (r == true) {
                     user_id = $(this).attr('id');
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
                        success: function (data) {
                             const actionObj = {
                                icon: 'fas fa-trash-alt',
                                title: '',
                                message: 'Record Remove Successfully',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            actionreload(actionJSON);
                        }
                    })
                }
               
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
                    // Get the selected option's job ID from Select2 data
                    var selectedData = $(this).select2('data')[0];
                    if (selectedData && selectedData.jobid) {
                        $('#jobtitle').val(selectedData.jobid);
                    }

                    $.ajax({
                        url: '{!! route("getshiftdetails") !!}',
                        type: 'GET',
                        data: { emp_id: employeeId },
                        success: function(response) {
                            $('#ontime').val(response.shift.onduty_time);
                            $('#offtime').val(response.shift.offduty_time);
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to fetch shift details:', error);
                        }
                    });
                } else {
                    $('#ontime').val('');
                    $('#offtime').val('');
                    $('#jobtitle').val('');
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

