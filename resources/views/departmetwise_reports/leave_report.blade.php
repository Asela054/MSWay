
@extends('layouts.app')

@section('content')

    <main>
         <div class="page-header">
             <div class="container-fluid d-none d-sm-block shadow">
                 @include('layouts.reports_nav_bar')
             </div>
             <div class="container-fluid">
                 <div class="page-header-content py-3 px-2">
                     <h1 class="page-header-title ">
                         <div class="page-header-icon"><i class="fa-light fa-file-contract"></i></div>
                         <span>Department-Wise Leave Report</span>
                     </h1>
                 </div>
             </div>
         </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                    <div class="card-body p-0 p-2 main_card">
                        <div class="col-md-12">
                            <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                    class="fas fa-filter mr-1"></i> Filter
                                Records</button>
                        </div><br>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                        <div class="table_outer">
                            <div class="daily_table table-responsive center-block fix-width scroll-inner" id="tableContainer">
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
                                  <label class="small font-weight-bolder text-dark">Company*</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                    @foreach ($companies as $company){
                                        <option value="{{$company->id}}">{{$company->name}}</option>
                                    }  
                                    @endforeach
                                </select>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                 <label class="small font-weight-bolder text-dark">Department*</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                    <option value="All">All Departments</option>
                                </select>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">Type*</label>
                                  <select name="reporttype" id="reporttype" class="form-control form-control-sm">
                                    <option value="">Please Select Type</option>
                                    <option value="1">Month Wise</option>
                                    <option value="2">Date Range Wise</option>
                                </select>
                              </div>
                          </li>

                          <li class="div_date_range">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">From Date</label>
                                  <div class="input-group input-group-sm mb-2">
                                      <input type="date" id="from_date" name="from_date"
                                          class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                                  </div>
                              </div>
                          </li>
                          <li class="div_date_range">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">To Date  </label>
                                  <div class="input-group input-group-sm mb-2">
                                      <input type="date" id="to_date" name="to_date"  class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                                  </div>
                              </div>
                          </li>
                          <li id="div_month">
                              <div class="col-md-12">
                                 <label class="small font-weight-bolder text-dark">Month</label>
                                 <div class="input-group input-group-sm mb-2">
                                    <input type="month" id="selectedmonth" name="selectedmonth" class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                                </div>
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
    </main>

    <div class="modal fade" id="view_more_modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Leave Breakdown</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class=" table-responsive center-block fix-width scroll-inner">
                    </div>

                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-danger btn-sm px-3" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#report_menu_link').addClass('active');
            $('#report_menu_link_icon').addClass('active');
            $('#departmentvisereport').addClass('navbtnactive');
            $('#department').select2({ width: '100%' });
 
            showInitialMessage()

            $('.div_date_range').addClass('d-none');
            $('#div_month').addClass('d-none');
            $('#reporttype').on('change', function () {
                let $type = $(this).val();
                if ($type == 1) {

                    $('.div_date_range').addClass('d-none');
                    $('#div_month').removeClass('d-none');

                } else {
                    $('#div_month').addClass('d-none');
                    $('.div_date_range').removeClass('d-none');
                }
            });


            $('#formFilter').on('submit', function (e) {
                let department = $('#department').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                let reporttype = $('#reporttype').val();
                let selectedmonth = $('#selectedmonth').val();
                e.preventDefault();

                closeOffcanvasSmoothly();

                $.ajax({
                    url: '{{ route("departmentwise_generateleavereport") }}',
                    type: 'GET',
                    data: {
                        department: department,
                        from_date: from_date,
                        to_date: to_date,
                        reporttype: reporttype,
                        selectedmonth: selectedmonth
                    },
                    success: function (response) {
                        $('#tableContainer').html(response.table);
                        $('#leave_report').DataTable({});
                    }
                });
            });

            $(document).on('click', '.view_more', function (e) {
                var depid = $(this).attr('id');

                $.ajax({
                    url: '{{ route("departmentwise_gettotalleaveemployee") }}',
                    type: 'GET',
                    data: {
                        department: depid,
                        from_date: $('#from_date').val(),
                        to_date: $('#to_date').val(),
                        reporttype: $('#reporttype').val(),
                        selectedmonth: $('#selectedmonth').val()
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#view_more_modal .modal-body').html(response.table);
                            $('#view_more_modal').modal('show');
                            $('#leave_reportemployee').DataTable({});
                        } 
                    }
                });
            });

            $('#company').on('change', function () {
                var companyId = $(this).val();
                if (companyId) {
                    $.ajax({
                        url: '{{ route("getdepartments",["company_id" => "id_company"]) }}'.replace('id_company', companyId),
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $('#department').empty();
                            $('#department').append('<option value="">Please Select</option>');
                            $('#department').append('<option value="All">All Departments</option>');

                            $.each(data, function (key, department) {
                                $('#department').append('<option value="' + department.id + '">' + department.name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#department').empty();
                    $('#department').append('<option value="">Please Select</option>');
                }
            });

            $('#btn-reset').on('click', function () {
            $('#formFilter')[0].reset();
            $('#company').val(null).trigger('change');
            $('#department').val(null).trigger('change');
        });
    
        });

         function showInitialMessage() {
        $('#tableContainer').html(
            '<div class="d-flex flex-column align-items-center">' +
            '<i class="fas fa-filter fa-3x text-muted mb-2"></i>' +
            '<h4 class="text-muted mb-2">No Records Found</h4>' +
            '<p class="text-muted">Use the filter options to get records</p>' +
            '</div>'
        );
        }
    </script>

@endsection

