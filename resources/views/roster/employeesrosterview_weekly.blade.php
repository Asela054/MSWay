

@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
             @include('layouts.shift_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-business-time"></i></div>
                    <span>Weekly Roster View</span>
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
                            Options</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                </div>
                <form id="shiftForm">
                    <button type="submit" id="saveroster" class="btn btn-sm btn-primary float-right d-none">Save Roster To Next Week</button>
                </form><br><br>
                <div class="center-block fix-width scroll-inner my-2">
                    <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%"
                        id="shiftTable">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>    
    </div>    
    
    <!-- Search Offcanvas End -->

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
                                <select name="company" id="company" class="form-control form-control-sm" required>
                                </select>
                            </div>
                        </li>
                        <li class="mb-2">
                             <div class="col-12 mt-2">
                                <label class="small font-weight-bolder text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm" required></select>
                            </div>
                        </li>
                         <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">From Date</label>
                                <input type="date" id="fromdate" name="fromdate" class="form-control form-control-sm" required>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">To Date</label>
                                <input type="date" id="todate" name="todate" class="form-control form-control-sm" required>
                            </div>
                        </li>
                        {{-- <li class="mb-2">
                             <div class="col-12">
                                <label class="small font-weight-bolder text-dark">Select Month:</label>
                                <input type="month" id="month" name="month" class="form-control form-control-sm" placeholder="yyyy-mm" required>
                            </div>
                        </li> --}}
                        <li class="mb-2">
                            <div class="col-md-12 d-flex justify-content-between">
                                <button type="button" class="btn btn-danger btn-sm filter-btn px-3"
                                    id="btn-reset">
                                    <i class="fas fa-redo mr-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn px-3"
                                    id="btn-filter">
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
    $('#shift_menu_link').addClass('active');
    $('#shift_menu_link_icon').addClass('active');
    $('#monthlyshifts_view').addClass('navbtnactive');

    let department = $('#department');
    let employees = [];
    let shiftCodeMap = {};

    let company = $('#company');

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
            url: '{{ url("department_list_sel2") }}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company.val()
                };
            },
            cache: true
        }
    });

    // Load shift codes
    fetch('getrostershifts')
        .then(response => response.json())
        .then(data => {
            shiftCodeMap = Object.fromEntries(data.map(s => [s.id, s.code]));
             shiftCodeMap[100] = 'SD';
             shiftCodeMap[101] = 'DO';
        });

    // Department change event
   
    $('#formFilter').on('submit', function (event){
        event.preventDefault();
        let departmentId = $('#department').val();
        let fromdate = $('#fromdate').val();
        let todate = $('#todate').val();

        if (!departmentId || !fromdate || !todate) return;

        if (fromdate > todate) {
            alert('From Date must be before To Date');
            return;
        }

        closeOffcanvasSmoothly();

        $.ajax({
            url: '{{ url("/get-employees-by-department") }}',
            data: { department_id: departmentId },
            success: function(data) {
                employees = data;
                loadRosterData(departmentId, fromdate, todate).then(rosterData => {
                    generateViewTable(fromdate, todate, rosterData);
                });
                $('#saveroster').removeClass('d-none');
            }
        });
    });

    // Date range change event
    $('#fromdate, #todate').on('change', function() {
        const departmentId = department.val();
        const fromdate = $('#fromdate').val();
        const todate = $('#todate').val();

        if (!departmentId || !fromdate || !todate) return;

        if (fromdate > todate) {
            alert('From Date must be before To Date');
            return;
        }

        loadRosterData(departmentId, fromdate, todate).then(rosterData => {
            generateViewTable(fromdate, todate, rosterData);
        });
    });

    function loadRosterData(departmentId, fromdate, todate) {
        return fetch(`get-view-roster-data-Weekly?department_id=${departmentId}&fromdate=${fromdate}&todate=${todate}`)
            .then(response => response.json());
    }

    function generateViewTable(fromdate, todate, rosterData = {}) {
            // Build dates list (manual parse/format to avoid timezone shifts)
            const [fy, fm, fd] = fromdate.split('-').map(Number);
            const [ty, tm, td] = todate.split('-').map(Number);
            const start = new Date(fy, fm - 1, fd);
            const end   = new Date(ty, tm - 1, td);

            const pad = n => String(n).padStart(2, '0');
            const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

            const dates = [];
            for (let dt = new Date(start); dt <= end; dt.setDate(dt.getDate() + 1)) {
                dates.push({
                    key: dt.getFullYear() + '-' + pad(dt.getMonth() + 1) + '-' + pad(dt.getDate()),
                    day: dt.getDate(),
                    month: monthNames[dt.getMonth()]
                });
            }

            const thead = document.querySelector('#shiftTable thead');
            const tbody = document.querySelector('#shiftTable tbody');
            thead.innerHTML = '';
            tbody.innerHTML = '';

            // Header
            let headerRow = `<tr><th>NO</th><th>NAME OF EMPLOYEE</th>`;
            dates.forEach(item => {
                headerRow += `<th>${item.day}<br><small>${item.month}</small></th>`;
            });
            headerRow += `</tr>`;
            thead.innerHTML = headerRow;

            // Rows
            employees.forEach((emp) => {
                let row = `<tr><td>${emp.id}</td><td class="name-col">${emp.fullname}</td>`;

                dates.forEach(item => {
                    const dayData = (rosterData[emp.id] && rosterData[emp.id][item.key]) || [];

                    const shiftIds = Array.isArray(dayData) ? dayData : [dayData];

                    const shiftText = shiftIds
                        .map(id => shiftCodeMap[id] || '')
                        .filter(code => code !== '')
                        .join(' / ');

                    row += `<td>${shiftText}</td>`;
                });

                row += `</tr>`;
                tbody.innerHTML += row;
            });
    }


     // Handle form submit for colne
    $('#shiftForm').on('submit', function(e) {
        e.preventDefault();
         let department = $('#department').val();
        let fromdate = $('#fromdate').val();
        let todate = $('#todate').val();
         $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $.ajax({
            url: '{!! route("colnerosterstoreWeekly") !!}',
            type: 'POST',
            data: { 
                department_id: department, 
                fromdate: fromdate,
                todate: todate
            },
            dataType: 'json',
            success: function(response) {
                if (response.errors) {
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
                if (response.success) {
                    const actionObj = {
                        icon: 'fas fa-save',
                        title: '',
                        message: response.success,
                        url: '',
                        target: '_blank',
                        type: 'success'
                    };
                    const actionJSON = JSON.stringify(actionObj, null, 2);
                    $('#shiftForm')[0].reset();
                    actionreload(actionJSON);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                const actionObj = {
                    icon: 'fas fa-times',
                    title: '',
                    message: 'Something went wrong!',
                    url: '',
                    target: '_blank',
                    type: 'danger'
                };
                const actionJSON = JSON.stringify(actionObj, null, 2);
                action(actionJSON);
            }
        });
    });

});
</script>


@endsection

