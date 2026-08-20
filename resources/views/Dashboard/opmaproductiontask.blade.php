@extends('layouts.app')

@section('content')

<main>
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
              @include('layouts.production&task_nav_bar_opma')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-ballot-check"></i></div>
                    <span>Production & Task</span>
                </h1>
            </div>
        </div>
    </div>


    <div class="container-fluid mt-2 p-0 p-2">
       <div class="card">
           <div class="card-body p-0 p-2">
               <div class="row">
                   <div class="col-md-12">
                       <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                           data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                               class="fas fa-filter mr-1"></i> Filter
                           Records</button>
                   </div><br><br>

                   <div class="col-12">
                       <div class="center-block fix-width scroll-inner">
                           <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%"
                               id="dataTable">
                               <thead>
                                   <tr>
                                       <th>ID</th>
                                       <th>EMPLOYEE</th>
                                       <th>DATE</th>
                                       <th>DAILY AVG</th>
                                       <th>BASIC SALARY</th>
                                       <th>O.T</th>
                                       <th>TRANSPORT ALLOWANCE</th>
                                       <th>PRODUCTION INCENTIVE</th>
                                       <th>NIGHT ALLOWANCE</th>
                                       <th>ATTENDANCE ALLOWANCE</th>
                                       <th>BONUS AMOUNT</th>
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
                      <span aria-hidden="true" class="h1 font-weight-bolderer">&times;</span>
                  </button>
              </div>
              <div class="offcanvas-body">
                  <ul class="list-unstyled">
                      <form class="form-horizontal" id="formFilter">
                        <li class="mb-2">
                            <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">Company</label>
                                <select name="company" id="company_f" class="form-control form-control-sm"></select>
                            </div>
                          </li>
                           <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">Department</label>
                                <select name="department" id="department_f" class="form-control form-control-sm"></select>
                            </div>
                          </li>
                           <li class="mb-2">
                              <div class="col-md-12">
                                   <label class="small font-weight-bolder text-dark">Employee</label>
                                    <select name="employee" id="employee_f" class="form-control form-control-sm">
                                    </select>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark"> From Date* </label>
                                  <input type="date" id="from_date" name="from_date"
                                      class="form-control form-control-sm" placeholder="yyyy-mm-dd"
                                      value="{{date('Y-m-d') }}" required>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark"> To Date*</label>
                                  <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                      placeholder="yyyy-mm-dd" value="{{date('Y-m-d') }}" required>
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
@endsection


@section('script')

<script>
$(document).ready(function(){
    $('#production_menu_link_opma').addClass('active');
    $('#production_menu_link_icon').addClass('active');

    let employee_f = $('#employee_f');
    let company_f = $('#company_f');
    let department_f = $('#department_f');

    employee_f.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("employee_list_sel2")}}',
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

    department_f.select2({
        placeholder: 'Select a Department',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("department_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company_f.val(),
                }
            },
            cache: true
        }
    });


     load_dt('', '', '', '');

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let department = $('#department_f').val();
            let employee = $('#employee_f').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            load_dt(department,employee,from_date, to_date);
             closeOffcanvasSmoothly();
        });

        function load_dt(department,employee, from_date, to_date){
                $('#dataTable').DataTable({
                   "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'DAILY PRODUCTION AVERAGE & BENEFITS DISPLAY Information',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'DAILY PRODUCTION AVERAGE & BENEFITS DISPLAY Information', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'DAILY PRODUCTION AVERAGE & BENEFITS DISPLAY Information',
                            className: 'btn btn-primary btn-sm',
                            text: '<i class="fas fa-print mr-2"></i> Print',
                            customize: function(win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    ajax: {
                         url: scripturl + '/Opma_Production/daily_approve_employee_productionsummary_list.php',
                         type: 'POST',
                         data : 
                            { department :department, 
                              employee :employee,
                            from_date: from_date,
                            to_date: to_date},
                    },
                    columns: [
                        { data: 'emp_id', name: 'emp_id' },
                        { data: 'emp_name', name: 'emp_name' },
                        { data: 'date', name: 'date' },
                        {
                            data: 'daily_average',
                            name: 'daily_average',
                            render: function(data, type, row) {
                                if (type !== 'display') return data;
                                var tier = getTier(data);
                                return '<span >' +
                                    parseFloat(data).toFixed(1) + '%</span>';
                            }
                        },
                        {
                            data: 'daily_average', name: 'basic_salary', orderable: false,
                            render: function(data) { return renderComponent(data, 'basic'); }
                        },
                        {
                            data: 'daily_average', name: 'ot', orderable: false,
                            render: function(data) { return renderComponent(data, 'ot'); }
                        },
                        {
                            data: 'daily_average', name: 'transport', orderable: false,
                            render: function(data) { return renderComponent(data, 'transport'); }
                        },
                        {
                            data: 'daily_average', name: 'incentive', orderable: false,
                            render: function(data) { return renderComponent(data, 'incentive'); }
                        },
                        {
                            data: 'daily_average', name: 'night', orderable: false,
                            render: function(data, type, row) {
                                if (type !== 'display') return data;
                                var tier = getTier(data);
                                var isNightPass = tier.allow.indexOf('night') !== -1 && parseInt(row.shift_id) === 4;
                                return isNightPass
                                    ? '<span>Pass</span>'
                                    : '<span class="text-muted">-</span>';
                            }
                        },
                        {
                            data: 'daily_average', name: 'attendance', orderable: false,
                            render: function(data) { return renderComponent(data, 'attendance'); }
                        },
                         {
                            data: 'daily_average', name: 'bonus', orderable: false,
                            render: function(data) { return renderComponent(data, 'bonus'); }
                        }
                    ],
                  rowCallback: function(row, data) {
    var tier = getTier(data.daily_average);

    var colors = {
        'red':    { bg: '#ff6b6b', color: '#000' },
        'orange': { bg: '#ff9900', color: '#000' },
        'yellow': { bg: '#ffff00', color: '#000' },
        'lgreen': { bg: '#99e64d', color: '#000' },
        'dgreen': { bg: '#2f4f2f', color: '#fff' },
        'na':     { bg: '', color: '' }
    };

    var c = colors[tier.key] || colors['na'];

    // apply to the row itself
    $(row).css('background-color', c.bg);
    $(row).css('color', c.color);

    // apply to every cell too (in case striping/hover CSS targets td directly)
    $(row).find('> td').css('background-color', c.bg).css('color', c.color);
}
                });
        }


});

function getTier(avg) {
    avg = parseFloat(avg);
    if (isNaN(avg)) return { key: 'na', label: '-', allow: [] };

    if (avg >= 90) {
        return { key: 'dgreen', label: 'Pass', allow: ['basic','ot','transport','incentive','night','attendance','bonus'] };
    } else if (avg >= 70) {
        return { key: 'lgreen', label: 'Pass', allow: ['basic','ot','transport','incentive','night','attendance'] };
    } else if (avg >= 60) {
        return { key: 'yellow', label: 'Pass', allow: ['basic','ot','attendance','night'] };
    } else if (avg >= 50) {
        return { key: 'orange', label: 'Pass', allow: ['basic','ot','attendance'] };
    } else {
        return { key: 'red', label: 'WORNING', allow: [] };
    }
}

function renderComponent(avg, key) {
    var tier = getTier(avg);
    return tier.allow.indexOf(key) !== -1
        ? '<span>Pass</span>'
        : '<span class="text-muted">-</span>';
}
</script>

@endsection