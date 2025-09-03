@extends('layouts.app')

@section('content')

<main>
    <div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            <div class="page-header-content py-3">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="fas fa-calendar-week"></i></div>
                    <span>Holidays</span>
                </h1>
            </div>
        </div>
    </div>         
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        @can('holiday-create')
                            <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Holidays</button>
                        @endcan
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <table class="table table-striped table-bordered table-sm small" id="jobtable">
                            <thead>
                                <tr>
                                    <th>Holiday Name </th>
                                    <th>Holiday Type</th>
                                    <th>Half Day/ Short</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Date</th> 
                                    <th>Work Level</th> 
                                    <th class="text-right">Action</th>   
                                </tr>
                            </thead>                            
                            <tbody>
                                @foreach($holiday as $holidays)
                                <tr>
                                    <td>{{$holidays->holiday_name}}</td>
                                    <td>{{$holidays->holiday_type_name}}</td>
                                    <td>
                                    @php
                                    if($holidays->half_short == 1){
                                        echo "Full Day";
                                    }else if($holidays->half_short == 0.5){
                                        echo "Half Day";
                                    }else if($holidays->half_short == 0.25){
                                        echo "Short Day";
                                    }
                                    @endphp
                                    </td>
                                    <td>{{ ($holidays->start_time != '' ) ? date('h:i A', strtotime($holidays->start_time)) : '' }}</td>
                                    <td>{{ ($holidays->end_time != '' ) ? date('h:i A', strtotime($holidays->end_time)) : '' }}</td>
                                    <td>{{$holidays->date}}</td>
                                    <td>{{$holidays->level}}</td>
                                    <td class="text-right">
                                        @can('holiday-edit')
                                            <button name="edit" id="{{$holidays->id}}" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>
                                        @endcan
                                        @can('holiday-delete')
                                            <button type="submit" name="delete" id="{{$holidays->id}}" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
                                    <label class="small font-weight-bold text-dark">Holiday Name</label>
                                    <input type="text" name="holiday_name" id="holiday_name" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Holiday Type</label>
                                    <select name="type" class="form-control form-control-sm">
                                        <option value="1">Poya Holiday</option>
                                        <option value="2">Public & Bank Holiday</option>
                                        <option value="3">Public,Bank,Mercantile Holiday</option>
                                    </select>
                                </div>

                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Half Day/ Short</label>
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
                                    <label class="small font-weight-bold text-dark">Date</label>
                                    <input type="date" name="date" id="date" class="form-control form-control-sm" placeholder="YYYY-MM-DD" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Work Level</label>
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
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
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
    <!-- Modal Area End -->
</main>
              
@endsection


@section('script')

<script>
$(document).ready(function(){

    $('#leaves_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
    $('#leave_collapse').addClass('show');
    $('#holiday_link').addClass('active');

    //#half_short select change
    $('#half_short').change(function(){
        var half_short = $(this).val();
        if(half_short == 0.25 || half_short == 0.5){
            $('.half_short_time').html(`
                <div class="form-group mb-1">
                    <label class="small font-weight-bold text-dark">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-control form-control-sm" />
                </div>
                <div class="form-group mb-1">
                    <label class="small font-weight-bold text-dark">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="form-control form-control-sm" />
                </div>
            `);
        }else{
            $('.half_short_time').html('');
        }
    });

    $(document).ready(function () {
        $('#jobtable').DataTable();
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

                var html = '';
                if (data.errors) {
                    html = '<div class="alert alert-danger">';
                    for (var count = 0; count < data.errors.length; count++) {
                        html += '<p>' + data.errors[count] + '</p>';
                    }
                    html += '</div>';
                }
                if (data.success) {
                    html = '<div class="alert alert-success">' + data.message + '</div>';
                    $('#formTitle')[0].reset();
                    //$('#titletable').DataTable().ajax.reload();
                    location.reload()
                }
                $('#form_result').html(html);
            }
        });
    });

    $(document).on('click', '.edit', function () {
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
                if(half_short == '1' ){
                    half_short = '1.00';
                }

                $('#half_short').val(half_short);

                if(data.result.half_short == 0.25 || data.result.half_short == 0.5) {
                    $('.half_short_time').html(`
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Start Time</label>
                            <input type="time" name="start_time" id="start_time" class="form-control form-control-sm" value="` + data.result.start_time + `" />
                        </div>
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">End Time</label>
                            <input type="time" name="end_time" id="end_time" class="form-control form-control-sm" value="` + data.result.end_time + `" />
                        </div>
                    `);
                }else{
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
    });

    var user_id;

    $(document).on('click', '.delete', function () {
        user_id = $(this).attr('id');
        $('#confirmModal').modal('show');
    });

    $('#ok_button').click(function () {
        $.ajax({
            url: "Holiday/destroy/" + user_id,
            beforeSend: function () {
                $('#ok_button').text('Deleting...');
            },
            success: function (data) {
                setTimeout(function () {
                    $('#confirmModal').modal('hide');
                    $('#user_table').DataTable().ajax.reload();
                    alert('Data Deleted');
                }, 2000);
                location.reload()
            }
        })
    });
});
</script>

@endsection