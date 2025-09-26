

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
                    <span>Roster View</span>
                </h1>
            </div>
        </div>
    </div>
      <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">


<form class="form-horizontal" id="formFilter">
    <div class="form-row mb-1">
        <div class="col">
            <label class="small font-weight-bold text-dark">Select Month:</label>
            <select id="month" class="form-control form-control-sm" required>
                @foreach ($months as $month)
                    <option value="{{ $month->format('Y-m') }}" {{ $month->isSameMonth($currentMonth) ? 'selected' : '' }}>
                        {{ $month->format('F Y') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label class="small font-weight-bold text-dark">Department</label>
            <select name="department" id="department" class="form-control form-control-sm" required></select>
        </div>
    </div>
</form>

<div class="center-block fix-width scroll-inner">
    <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="shiftTable">
        <thead></thead>
        <tbody></tbody>
    </table>
</div>


            </div>
        </div>
    </div>      

</main>
              
@endsection


@section('script')
<script>
$(document).ready(function() {

    let department = $('#department');
    let employees = [];
    let shiftCodeMap = {};

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
                    page: params.page || 1
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
        });

    // Department change event
    department.on('change', function() {
        const departmentId = $(this).val();
        const selectedMonth = $('#month').val();
        if (!departmentId) return;

        $.ajax({
            url: '{{ url("/get-employees-by-department") }}',
            data: { department_id: departmentId },
            success: function(data) {
                employees = data;
                loadRosterData(departmentId, selectedMonth).then(rosterData => {
                    generateViewTable(selectedMonth, rosterData);
                });
            }
        });
    });

    // Month change event
    $('#month').on('change', function() {
        const departmentId = department.val();
        if (!departmentId) return;

        loadRosterData(departmentId, this.value).then(rosterData => {
            generateViewTable(this.value, rosterData);
        });
    });

    function loadRosterData(departmentId, month) {
        return fetch(`get-view-roster-data?department_id=${departmentId}&month=${month}`)
            .then(response => response.json());
    }

    function generateViewTable(month, rosterData = {}) {
        const [year, monthNum] = month.split('-');
        const daysInMonth = new Date(year, monthNum, 0).getDate();

        const thead = document.querySelector('#shiftTable thead');
        const tbody = document.querySelector('#shiftTable tbody');
        thead.innerHTML = '';
        tbody.innerHTML = '';

        // Header
        let headerRow = `<tr><th>No</th><th>Name of Employee</th>`;
        for (let d = 1; d <= daysInMonth; d++) {
            headerRow += `<th>${d}</th>`;
        }
        headerRow += `</tr>`;
        thead.innerHTML = headerRow;

        // Rows
        employees.forEach((emp, index) => {
            let row = `<tr><td>${emp.id}</td><td class="name-col">${emp.name}</td>`;
            for (let d = 1; d <= daysInMonth; d++) {
                const shiftId = (rosterData[emp.id] && rosterData[emp.id][d]) || '';
                const shiftCode = shiftCodeMap[shiftId] || '';
                row += `<td>${shiftCode}</td>`;
            }
            row += `</tr>`;
            tbody.innerHTML += row;
        });
    }

});
</script>


@endsection

