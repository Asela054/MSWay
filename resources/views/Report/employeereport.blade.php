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
                    <span>Employee Report</span>
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
                            Records</button><br><br>
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner" >
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="emptable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>EMPLOYEE</th>
                                    <th>LOCATION</th>
                                    <th>DEPARTMENT</th>
                                    <th>DATE OF BIRTH</th>
                                    <th>MOBILE NO</th>
                                    <th>TELEPHONE</th>
                                    <th>NIC</th>
                                    <th>GENDER</th>
                                    <th>EMAIL</th>
                                    <th>PERMANENT ADDRESS</th>
                                    <th>TEMPORARY ADDRESS</th>
                                    <th>JOB CATEGORY</th>
                                    <th>JOB STATUS</th>
                                    <th>PERMANENT DATE</th>

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
                                 <label class="small font-weight-bolder text-dark">Company</label>
                                 <select name="company" id="company" class="form-control form-control-sm">
                                 </select>
                             </div>
                         </li>
                         <li class="mb-2">
                             <div class="col-md-12">
                                 <label class="small font-weight-bolder text-dark">Department</label>
                                 <select name="department" id="department" class="form-control form-control-sm"
                                     required>
                                 </select>
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
$(document).ready(function() {

    $('#report_menu_link').addClass('active');
    $('#report_menu_link_icon').addClass('active');
    $('#employeedetailsreport').addClass('navbtnactive');

    let company = $('#company');
    let department = $('#department');

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

    load_dt('');
    function load_dt(department){
        $('#emptable').DataTable({
                    "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Employee Reports',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Employee Reports', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Employee Reports',
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
                  url: scripturl + "/rpt_employee.php",
                  type: "POST",
                  data: {'department':department},
            },
            columns: [
                { data: 'id' },
                { data: 'employee_display' },
                { data: 'location' },
                { data: 'dept_name' },
                { data: 'emp_birthday' },
                { data: 'emp_mobile' },
                { data: 'emp_work_telephone' },
                { data: 'emp_national_id' },
                { data: 'emp_gender' },
                { data: 'emp_email' },
                { data: 'emp_address' },
                { data: 'emp_addressT' },
                { data: 'title' },
                { data: 'e_status' },
                { data: 'emp_permanent_date' }
            ],
            "bDestroy": true,
            "order": [[ 0, "desc" ]],
        });
    }

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department').val();

        load_dt(department);
        closeOffcanvasSmoothly();
    });


     $('#btn-reset').on('click', function () {
                 $('#formFilter')[0].reset();
                 $('#company').val(null).trigger('change');
                 $('#department').val(null).trigger('change');
             });

} );
</script>

@endsection