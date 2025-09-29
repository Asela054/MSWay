

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
                    <span>Monthly Shift Roster</span>
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
                            <select name="department" id="department" class="form-control form-control-sm" required>
                            </select>
                        </div>
                        
                    </div>

                </form>
                


                <div id="info_msg"></div>

                <form id="shiftForm">
                    <div class="center-block fix-width scroll-inner">
                    <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="shiftTable">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-sm btn-primary float-right">Save Roster</button>
                </form>
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
    let shiftOptions = [];

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

    // Load shift options first
    fetch('getrostershifts')
        .then(response => response.json())
        .then(data => {
            shiftOptions = [{ id: '', code: 'NA' }, ...data];
        })
        .catch(error => {
            console.error('Error loading shift options:', error);
        });

    // Load employees and generate table
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
                    generateTable(selectedMonth, rosterData);
                });
            },
            error: function() {
                alert('Failed to load employees.');
            }
        });
    });

    $('#month').on('change', function() {
        const departmentId = department.val();
        if (!departmentId) return;

        loadRosterData(departmentId, this.value).then(rosterData => {
            generateTable(this.value, rosterData);
        });
    });

    function loadRosterData(departmentId, month) {
        return fetch(`get-roster-data?department_id=${departmentId}&month=${month}`)
            .then(response => response.json());
    }

    function generateTable(month, existingData = {}) {
        const [year, monthNum] = month.split('-');
        const daysInMonth = new Date(year, monthNum, 0).getDate();

        const thead = document.querySelector('#shiftTable thead');
        const tbody = document.querySelector('#shiftTable tbody');
        thead.innerHTML = '';
        tbody.innerHTML = '';

        let headerRow = `<tr><th>No</th><th>Name of Employee</th>`;
        for (let d = 1; d <= daysInMonth; d++) {
            headerRow += `<th>${d}</th>`;
        }
        headerRow += `</tr>`;
        thead.innerHTML = headerRow;

        employees.forEach((emp, index) => {
            let row = `<tr><td>${emp.id}</td><td class="name-col">${emp.name}</td>`;
            for (let d = 1; d <= daysInMonth; d++) {
                const existingShift = (existingData[emp.id] && existingData[emp.id][d]) || '';
               row += `<td style="padding: 0px;">
                    <select name="shifts[${emp.id}][${d}]" class="form-control form-control-sm " style="width: 55px;">
                        
                        ${shiftOptions.map(opt =>
                            `<option value="${opt.id}" ${opt.id == existingShift ? 'selected' : ''}>${opt.code}</option>`
                        ).join('')}
                    </select>
                </td>`;

            }
            row += `</tr>`;
            tbody.innerHTML += row;
        });
    }

    // Handle form submit
    $('#shiftForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const month = $('#month').val();
        const shifts = {};

        for (const [key, value] of formData.entries()) {
            if (key.startsWith("shifts") && value !== "") {
                const matches = key.match(/shifts\[(\d+)\]\[(\d+)\]/);
                if (matches) {
                    const empId = matches[1];
                    const day = matches[2];
                    const date = new Date(`${month}-${String(day).padStart(2, '0')}`);
                    const formattedDate = date.toISOString().split('T')[0];

                    if (!shifts[empId]) shifts[empId] = [];

                    shifts[empId].push({
                        emp_id: empId,
                        shift: value,
                        date: formattedDate
                    });
                }
            }
        }

        const payload = Object.values(shifts).flat();
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        fetch('{{ url("/fullrosterstore") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ shifts: payload })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(result => {
            $('#info_msg').html(
                '<div class="alert alert-success"><p>' + result.message + '</p></div>'
            );

            // Hide after 5 seconds
            setTimeout(() => {
                $('#info_msg').fadeOut('slow', function () {
                    $(this).html('').show(); // clear content and reset visibility
                });
            }, 1000);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong!');
        });
    });

});
</script>


@endsection

