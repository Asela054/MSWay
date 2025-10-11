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
                    <span>Late Deduction Approval</span>
                </h1>
            </div>
        </div>
    </div>  
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2 main_card">
                <div class="row">
                    <div class="col-md-12">

                        <div class="row align-items-center mb-4">
                            <div class="col-md-12">
                                <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                    data-toggle="offcanvas" data-target="#offcanvasRight"
                                    aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                    Records</button>
                            </div>
                            <div class="col-12">
                                <hr class="border-dark">
                            </div>
                            <div class="col-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Select All Records</label>
                                </div>
                            </div>
                            <div class="col-6 text-right">
                                <button id="approve_att" class="btn btn-primary btn-sm"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Approve All</button>
                            </div>
                        </div>

                         <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%"  id="attendtable">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>EMPLOYEE ID</th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>LATE MINITES TOTAL</th>
                                    <th>NOPAY AMOUNT</th>
                                    <th>TOTAL AMOUNT</th>
                                    <th class="d-none">EMPLOYEE auto ID</th>
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

          <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
              <div class="offcanvas-header">
                  <h2 class="offcanvas-title font-weight-bolder" id="offcanvasRightLabel">Records Filter Options</h2>
                  <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                      <span aria-hidden="true" class="h1 font-weight-bolder">&times;</span>
                  </button>
              </div>
              <div class="offcanvas-body">
                  <ul class="list-unstyled">
                      <form class="form-horizontal" id="formFilter">
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">Company *</label>
                                  <select name="company" id="company" class="form-control form-control-sm" required>
                                  </select>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">Department *</label>
                                  <select name="department" id="department" class="form-control form-control-sm"
                                      required>
                                  </select>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">Month *</label>
                                  <input type="month" id="month" name="month" class="form-control form-control-sm"
                                      placeholder="yyyy-mm" required>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">Close Date *</label>
                                  <input type="date" id="closedate" name="closedate"
                                      class="form-control form-control-sm" required>
                              </div>
                          </li>
                          <li>
                              <div class="col-md-12 d-flex justify-content-between">
                                 
                                  <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
                                      <i class="fas fa-redo mr-1"></i> Reset
                                  </button>
                                   <button type="submit" class="btn btn-primary btn-sm filter-btn px-3" id="btn-filter">
                                      <i class="fas fa-search mr-2"></i>Search
                                  </button>
                              </div>
                          </li>
                      </form>
                  </ul>
              </div>
          </div>
    </div>




    <!-- Modal Area End -->
</main>
              
@endsection


@section('script')

<script>
$(document).ready(function () {

    $('#attendant_menu_link').addClass('active');
    $('#attendant_menu_link_icon').addClass('active');
    $('#attendantmaster').addClass('navbtnactive');

    let company = $('#company');
    let department = $('#department');

    showInitialMessage()
    
    company.select2({
        placeholder: 'Select...',
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

    department.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("department_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company.val()
                }
            },
            cache: true
        }
    });

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        var department = $('#department').val();
        var company = $('#company').val();
        var month = $('#month').val();
        var closedate = $('#closedate').val();
         closeOffcanvasSmoothly();
        $.ajax({
                url: "{{url('/getlateminitesapprovel')}}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    department: department,
                    month: month,
                    closedate: closedate
                },
                dataType: "json",
                success: function (data) {
                    if ($.fn.DataTable.isDataTable('#attendtable')) {
                        $('#attendtable').DataTable().clear().destroy();
                    }
                    
                    $('#attendtable tbody').empty();
                    let dataRows = '';
                    $.each(data.data, function (index, item) {
                        dataRows += `
                                    <tr>
                                        <td><input type="checkbox" class="row-checkbox selectCheck removeIt"></td>
                                        <td>${item.emp_id}</td>
                                        <td>${item.emp_name_with_initial}</td>
                                        <td>${item.late_hours_total}</td>
                                        <td>${item.nopayAmount}</td>
                                        <td>${item.late_day_amount}</td>
                                        <td class="d-none">${item.emp_autoid}</td>
                                    </tr>`;
                    });
                    $('#attendtable tbody').html(dataRows);
                    $('#attendtable').DataTable({
                        destroy: true,
                        responsive: true,
                         dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                            "buttons": [{
                                    extend: 'csv',
                                    className: 'btn btn-success btn-sm',
                                    title: 'Late Deduction Approval Information',
                                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                                },
                                { 
                                    extend: 'pdf', 
                                    className: 'btn btn-danger btn-sm', 
                                    title: 'Late Deduction Approval Information', 
                                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                    orientation: 'landscape', 
                                    pageSize: 'legal', 
                                    customize: function(doc) {
                                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                    }
                                },
                                {
                                    extend: 'print',
                                    title: 'Late Deduction Approval  Information',
                                    className: 'btn btn-primary btn-sm',
                                    text: '<i class="fas fa-print mr-2"></i> Print',
                                    customize: function(win) {
                                        $(win.document.body).find('table')
                                            .addClass('compact')
                                            .css('font-size', 'inherit');
                                    },
                                },
                            ],
                        columnDefs: [{
                            orderable: false,
                            targets: [0, 6]
                        }, ]

                      
                    });
                    $('#btn-filter').html('Filter').prop('disabled', false);
                }
            });
     
    });

    var selectedRowIdsapprove = [];

        $('#approve_att').click(async function () {
            var r = await Otherconfirmation("You want to Edit this ? ");
            if (r == true) {

                selectedRowIdsapprove = [];
                $('#attendtable tbody .selectCheck:checked').each(function () {
                    var rowData = $('#attendtable').DataTable().row($(this).closest('tr')).data();

                    if (rowData) {
                        selectedRowIdsapprove.push({
                            empid: rowData[1],
                            emp_name: rowData[2],
                            late_hourstotal: rowData[3],
                            nopayamount: rowData[4],
                            total_amount: rowData[5],
                            autoid: rowData[6],
                        });
                    }
                });

                if (selectedRowIdsapprove.length > 0) {
                    console.log(selectedRowIdsapprove);


                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })

                    var department = $('#department').val();
                    var company = $('#company').val();
                    var month = $('#month').val();
                    var closedate = $('#closedate').val();

                    $.ajax({
                        url: '{!! route("approvelatemintes") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {
                            dataarry: selectedRowIdsapprove,
                            department: department,
                            month: month,
                            closedate: closedate
                        },
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
                                actionreload(actionJSON);
                            }

                        }
                    })
                } else {

                    Swal.fire({
                        position: "top-end",
                        icon: 'warning',
                        title: 'Select Rows to Final Approve!',
                        showConfirmButton: false,
                        timer: 2500
                        });
                }
            }
        });


    $('#selectAll').click(function (e) {
        $('#attendtable').closest('table').find('td input:checkbox').prop('checked', this.checked);
    });

      // Offcanvas toggle functionality - UPDATED
        $('[data-toggle="offcanvas"]').on('click', function () {
            var target = $(this).data('target');
            $(target).addClass('show');
            $('body').addClass('offcanvas-open');

            // Add backdrop
            $('<div class="offcanvas-backdrop fade show"></div>').appendTo('body');
        });

        // Close offcanvas when clicking on backdrop
        $(document).on('click', '.offcanvas-backdrop', function () {
            closeOffcanvasSmoothly();
        });
        // Close offcanvas when clicking on close button
        $('[data-dismiss="offcanvas"]').on('click', function () {
            closeOffcanvasSmoothly();
        });

        $('#btn-reset').on('click', function () {
            $('#formFilter')[0].reset();
            $('#company').val(null).trigger('change');
            $('#department').val(null).trigger('change');
            $('#employee').val(null).trigger('change');
            $('#location').val(null).trigger('change');
        });
              
        $('#month').on('change', function() {
            updateClosingDateConstraints();
        });


});
     function showInitialMessage() {
        $('#attendtable tbody').html(
            '<tr>' +
            '<td colspan="7" class="text-center py-5">' + // Changed colspan to 9 to match your columns
            '<div class="d-flex flex-column align-items-center">' +
            '<i class="fas fa-filter fa-3x text-muted mb-2"></i>' +
            '<h4 class="text-muted mb-2">No Records Found</h4>' +
            '<p class="text-muted">Use the filter options to get records</p>' +
            '</div>' +
            '</td>' +
            '</tr>'
        );
    }

    function closeOffcanvasSmoothly(offcanvasId = '#offcanvasRight') {
                const offcanvas = $(offcanvasId);
                const backdrop = $('.offcanvas-backdrop');

                // Add hiding class to trigger reverse animation
                offcanvas.addClass('hiding');
                backdrop.addClass('fading');

                // Remove elements after animation completes
                setTimeout(() => {
                    offcanvas.removeClass('show hiding');
                    backdrop.remove();
                    $('body').removeClass('offcanvas-open');
                }, 900); // Match this with your CSS transition duration
    }

    function updateClosingDateConstraints() {
        const monthInput = $('#month').val();
        const closeDateInput = $('#closedate');

        if (monthInput) {
            // Extract year and month from the month input
            const [year, month] = monthInput.split('-');

            // Calculate first and last day of the selected month
            const firstDay = `${year}-${month}-01`;
            const lastDay = new Date(year, month, 0).getDate(); // Last day of the month
            const lastDate = `${year}-${month}-${lastDay}`;

            // Set min and max attributes to restrict dates to the selected month
            closeDateInput.attr('min', firstDay);
            closeDateInput.attr('max', lastDate);

            // Update placeholder to show the valid date range
            closeDateInput.attr('placeholder', `${firstDay} to ${lastDate}`);

            // If current close date is outside the selected month, clear it
            const currentCloseDate = closeDateInput.val();
            if (currentCloseDate && (currentCloseDate < firstDay || currentCloseDate > lastDate)) {
                closeDateInput.val('');
            }

            // Enable the close date input
            closeDateInput.prop('disabled', false);

            // Auto-set close date to last day of month if not set
            if (!closeDateInput.val()) {
                closeDateInput.val(lastDate);
            }
        } else {
            // If no month selected, disable and clear close date
            closeDateInput.val('');
            closeDateInput.prop('disabled', true);
            closeDateInput.removeAttr('min');
            closeDateInput.removeAttr('max');
            closeDateInput.attr('placeholder', 'Select month first');
        }
    }

</script>

@endsection