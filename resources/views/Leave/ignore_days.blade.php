@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.attendant&leave_nav_bar')
           
        </div>
    </div>

    <div class="container-fluid mt-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        @can('IgnoreDay-create')
                            <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Ignore Days</button>
                        @endcan
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="jobtable">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Date</th>
                                    <th class="text-right">Action</th>   
                                </tr>
                            </thead>                            
                            <tbody>
                            @foreach($IgnoreDays as $ignore_days)
                                <tr>
                                <td>
                                    @php
                                        $month = new DateTime($ignore_days->month); 
                                        echo $month->format('F'); 
                                    @endphp
                                </td>
                                    <td>{{$ignore_days->date}}</td>
                                    <td class="text-right">
                                        @can('IgnoreDay-delete')
                                            <button type="submit" name="delete" id="{{$ignore_days->id}}" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>
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
    </div>

<!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Ignore Days</h5>
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
                                    <label class="small font-weight-bold text-dark">Month</label>
                                    <input type="month" id="month" name="month" class="form-control form-control-sm" placeholder="yyyy-mm" required>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Dates</label>
                                    <div id="date-picker-container">
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="selected_dates" id="selected_dates" value="" />
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js"></script>

<script>
    let fp;
    $(document).ready(function(){

    $('#attendant_menu_link').addClass('active');
    $('#attendant_menu_link_icon').addClass('active');
    $('#leavemaster').addClass('navbtnactive');

    $(document).ready(function () {
        $('#jobtable').DataTable();
    });


    $('#create_record').click(function () {
    $('.modal-title').text('Add Ignore Days');
    $('#action_button').html('Add');
    $('#action').val('Add');
    $('#form_result').html('');
    $('#formTitle')[0].reset();
    $('#selected_dates').val('');
    $('#date-picker-container').empty();
    

        let monthInput = $('#month');
        let datePickerContainer = $('#date-picker-container');

        monthInput.off('change').on('change', function () {
            let selectedMonth = $(this).val();
            if (selectedMonth) {
                let [year, month] = selectedMonth.split('-');
                let daysInMonth = new Date(year, month, 0).getDate();

                let startDate = new Date(year, month - 1, 1);
                let endDate = new Date(year, month - 1, daysInMonth);

                datePickerContainer.empty().append('<input type="text" id="date-picker" class="form-control form-control-sm" placeholder="Select Dates" required>');
                
                flatpickr("#date-picker", {
                    mode: "multiple",
                    dateFormat: "Y-m-d", 
                    minDate: startDate,
                    maxDate: endDate,
                    disableMobile: true,
                    onChange: function (selectedDates) {
                        let formattedDates = selectedDates.map(date => 
                            date.toISOString().split('T')[0]
                        ).join(',');
                        $('#selected_dates').val(formattedDates);
                    }
                });
            } else {
                datePickerContainer.empty();
            }
        });

        $('#formModal').modal('show');
    });



    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addIgnoreDay') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "";
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

    var user_id;

    $(document).on('click', '.delete', function () {
        user_id = $(this).attr('id');
        $('#confirmModal').modal('show');
    });

    $('#ok_button').click(function () {
        $.ajax({
            url: "IgnoreDay/destroy/" + user_id,
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