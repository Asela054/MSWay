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
                         <span>Ignore Days</span>
                     </h1>
                 </div>
             </div>
         </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                            <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Ignore Days</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="jobtable">
                            <thead>
                                <tr>
                                    <th>MONTH</th>
                                    <th>DATE</th>
                                    <th class="text-right">ACTION</th>   
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
                                            <button type="submit" name="delete" id="{{$ignore_days->id}}" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>
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
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
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

    $('#jobtable').DataTable({
              dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
              "<'row'<'col-sm-5'i><'col-sm-7'p>>",
          "buttons": [{
                  extend: 'csv',
                  className: 'btn btn-success btn-sm',
                  title: 'Ignore Days Details',
                  text: '<i class="fas fa-file-csv mr-2"></i> CSV',
              },
              {
                  extend: 'pdf',
                  className: 'btn btn-danger btn-sm',
                  title: 'Ignore Days Details',
                  text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                  orientation: 'landscape',
                  pageSize: 'legal',
                  customize: function (doc) {
                      doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                  }
              },
              {
                  extend: 'print',
                  title: 'Ignore Days Details',
                  className: 'btn btn-primary btn-sm',
                  text: '<i class="fas fa-print mr-2"></i> Print',
                  customize: function (win) {
                      $(win.document.body).find('table')
                          .addClass('compact')
                          .css('font-size', 'inherit');
                  },
              },
          ],
          "order": [
              [1, "desc"]
          ]
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

    var user_id;

    $(document).on('click', '.delete',async function () {
        user_id = $(this).attr('id');
       var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            $.ajax({
            url: "IgnoreDay/destroy/" + user_id,
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