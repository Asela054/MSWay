@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
             <div class="container-fluid d-none d-sm-block shadow">
                 @include('layouts.attendant&leave_nav_bar')
             </div>
             <div class="container-fluid">
                 <div class="page-header-content py-3 px-2">
                     <h1 class="page-header-title ">
                         <div class="page-header-icon"><i class="fa-light fa-calendar-pen"></i></div>
                         <span>Holidays</span>
                     </h1>
                 </div>
             </div>
         </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                            <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Holidays</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%" id="jobtable">
                            <thead>
                                <tr>
                                    <th>HOLIDAY NAME</th>
                                    <th>HOLIDAY TYPE</th>
                                    <th>HALF DAY / SHORT</th>
                                    <th>DATE</th> 
                                    <th>WORK LEVEL</th> 
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
        </div>
    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Holiday</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}	
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Holiday Name</label>
                                    <input type="text" name="holiday_name" id="holiday_name" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Holiday Type</label>
                                    <select name="type" class="form-control form-control-sm">
                                        <option value="1">Poya Holiday</option>
                                        <option value="2">Public & Bank Holiday</option>
                                        <option value="3">Public,Bank,Mercantile Holiday</option>
                                    </select>
                                </div>

                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Half Day/ Short</label>
                                    <select name="half_short" id="half_short"
                                            class="form-control form-control-sm">
                                        <option value="0.00">Select</option>
                                        <option value="0.25">Short Leave</option>
                                        <option value="0.5">Half Day</option>
                                        <option value="1.00" selected>Full Day</option>
                                    </select>
                                </div>

                                <div class="half_short_time">

                                </div>

                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Date</label>
                                    <input type="date" name="date" id="date" class="form-control form-control-sm" placeholder="YYYY-MM-DD" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Work Level</label>
                                    <select name="work_level" id="work_level" class="form-control form-control-sm">
                                        <option value="">Select</option>
                                        @foreach($worklevel as $worklevels)
                                        <option value="{{$worklevels->id}}"
                                                @if($worklevels->level == "Double O.T.")
                                                    selected
                                                @endif
                                        >{{$worklevels->level}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Area End -->
</main>
              
@endsection


@section('script')

<script>

    //initialize_calendar();
    //cal2();

    function cal2(){
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: this.events,

            eventClick: function(info) {
                info.jsEvent.preventDefault(); // don't let the browser navigate
            }

        });
        calendar.render();
    }

    function initialize_calendar(){
        document.addEventListener('DOMContentLoaded', function() {

            let events = [];
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                url: '{{route('get_holidays_for_calendar')}}',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    response.forEach(function(event) {
                        events.push({
                            id: event.id,
                            title: event.holiday_name,
                            start: event.date,
                            end: event.date,
                            url:'#',
                            //color: '#560319',
                            //textColor: event.textColor,
                            //allDay: event.allDay
                        });
                    });

                    var calendarEl = document.getElementById('calendar');
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        },
                        events: events,
                        eventClick: function(info) {
                            info.jsEvent.preventDefault(); // don't let the browser navigate

                            //get service_res
                            // $.ajax({
                            //     url: base_url + 'MachineServicesCalendar/getServiceRes',
                            //     type: 'POST',
                            //     data: {
                            //         service_id: info.event.id
                            //     },
                            //     dataType: 'json',
                            //     success: function(response) {
                            //         $('#service_res').html(response);
                            //     }
                            // });
                        }

                    });


                    calendar.render();

                }
            });

        });
    }

$(document).ready(function(){

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#leavemaster').addClass('navbtnactive');

            $('#jobtable').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Holiday Details',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'Holiday Details', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'landscape', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Holiday Details',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-print mr-2"></i> Print',
                        customize: function(win) {
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        },
                    },
                ],
                
            ajax: {
                url: scripturl + '/holidays_list.php',
                type: 'POST',
            },
            columns: [
                { data: 'holiday_name', name: 'holiday_name' },
                { data: 'holiday_type_name', name: 'holiday_type_name' },
                { 
                    data: 'half_short', 
                    name: 'half_short',
                    render: function(data, type, row) {
                        if (data == 1) {
                            return "Full Day";
                        } else if (data == 0.5) {
                            return "Half Day";
                        } else if (data == 0.25) {
                            return "Short Day";
                        } else {
                            return data;
                        }
                    }
                },
                { data: 'date', name: 'date' },
                { data: 'level', name: 'level' },
                {
                data: 'id',
                name: 'action',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';

                        buttons += '<button name="edit"  id="'+row.id+'" class="edit btn btn-primary btn-sm"   style="margin:1px;" type="submit" data-toggle="tooltip"  title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                        buttons += '<button type="submit" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm"  style="margin:1px;" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';
                    

                    return buttons;
                }
            }
            ],
            "bDestroy": true,
            "order": [
                [4, "desc"]
            ],
             drawCallback: function(settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    //#half_short select change
    $('#half_short').change(function(){
        var half_short = $(this).val();
        if(half_short == 0.25 || half_short == 0.5){
            // $('.half_short_time').html(`
            //     <div class="form-group mb-1">
            //         <label class="small font-weight-bolder text-dark">Start Time</label>
            //         <input type="time" name="start_time" id="start_time" class="form-control form-control-sm" />
            //     </div>
            //     <div class="form-group mb-1">
            //         <label class="small font-weight-bolder text-dark">End Time</label>
            //         <input type="time" name="end_time" id="end_time" class="form-control form-control-sm" />
            //     </div>
            // `);
        }else{
            $('.half_short_time').html('');
        }
    });

    $('#create_record').click(function () {
        $('.modal-title').text('Add Holiday');
        $('#action_button').html('Add');
        $('#action').val('Add');
        $('#form_result').html('');

        //form reset
        $('#formTitle')[0].reset();
        $('#holiday_name').val('');
        $('#type').val('');
        $('#half_short').val('1.00');
        $('#date').val('');
        $('#work_level').val('2');
        $('.half_short_time').html('');


        $('#formModal').modal('show');
    });

    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addHoliday') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('Holiday.update') }}";
        }


        $.ajax({
            url: action_url,
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (data) {
                 if (data.errors) {
                    const actionObj = {
                        icon: 'fas fa-warning',
                        title: '',
                        message: 'Record Error',
                        url: '',
                        target: '_blank',
                        type: 'danger'
                    };
                    const actionJSON = JSON.stringify(actionObj, null, 2);
                    action(actionJSON);
                }
                if (data.success) {
                    const actionObj = {
                        icon: 'fas fa-save',
                        title: '',
                        message: data.success,
                        url: '',
                        target: '_blank',
                        type: 'success'
                    };
                    const actionJSON = JSON.stringify(actionObj, null, 2);
                    $('#formTitle')[0].reset();
                    actionreload(actionJSON);
                }
            }
        });
    });

    $(document).on('click', '.edit',async function () {
        var r = await Otherconfirmation("You want to Edit this ? ");
        if (r == true) {

            $('#action_button').html('Edit');
            $('#action').val('Edit');

            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "Holiday/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    $('#holiday_name').val(data.result.holiday_name);
                    $('#holiday_type').val(data.result.holiday_type);

                    let half_short = data.result.half_short;
                    if (half_short == '1') {
                        half_short = '1.00';
                    }

                    $('#half_short').val(half_short);

                    if (data.result.half_short == 0.25 || data.result.half_short == 0.5) {
                        // $('.half_short_time').html(`
                        //     <div class="form-group mb-1">
                        //         <label class="small font-weight-bolder text-dark">Start Time</label>
                        //         <input type="time" name="start_time" id="start_time" class="form-control form-control-sm" value="` + data.result.start_time + `" />
                        //     </div>
                        //     <div class="form-group mb-1">
                        //         <label class="small font-weight-bolder text-dark">End Time</label>
                        //         <input type="time" name="end_time" id="end_time" class="form-control form-control-sm" value="` + data.result.end_time + `" />
                        //     </div>
                        // `);
                    } else {
                        $('.half_short_time').html('');
                    }

                    $('#date').val(data.result.date);
                    $('#work_level').val(data.result.work_level);
                    $('#hidden_id').val(id);

                    $('.modal-title').text('Edit Holiday');
                    $('#action_button').val('Edit');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
            })
        }
    });

    var user_id;

    $(document).on('click', '.delete', async function () {
        user_id = $(this).attr('id');
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            $.ajax({
                url: "Holiday/destroy/" + user_id,
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

    
});
</script>

@endsection