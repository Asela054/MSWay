

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
                <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                        data-toggle="offcanvas" data-target="#offcanvasRight"
                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                        Options</button>
                </div>
                <div class="col-12">
                    <hr class="border-dark">
                </div>
                </div>
                <div id="info_msg"></div>
                <form id="shiftForm">
                    <div class="center-block fix-width scroll-inner my-2">
                    <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="shiftTable">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                    </div>
                    <br>
                    <button type="submit" id="save-roster" class="btn btn-sm btn-primary float-right d-none">Save Roster</button>
                </form>
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
                                      <select name="department" id="department" class="form-control form-control-sm"
                                          required></select>
                                  </div>
                              </li>
                              <li class="mb-2">
                                  <div class="col-12">
                                      <label class="small font-weight-bolder text-dark">Select Month:</label>
                                      <select id="month" class="form-control form-control-sm" required>
                                          @foreach ($months as $month)
                                          <option value="{{ $month->format('Y-m') }}"
                                              {{ $month->isSameMonth($currentMonth) ? 'selected' : '' }}>
                                              {{ $month->format('F Y') }}
                                          </option>
                                          @endforeach
                                      </select>
                                  </div>
                              </li>
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
    $('#monthlyshifts').addClass('navbtnactive');

    let department = $('#department');
    let employees = [];
    let shiftOptions = [];

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
        
    // Load employees and generate table
    $('#formFilter').on('submit', function (event) {
        event.preventDefault();
        let departmentId = $('#department').val();
        let selectedMonth = $('#month').val();

         closeOffcanvasSmoothly();


        $.ajax({
            url: '{{ url("/get-employees-by-department") }}',
            data: { department_id: departmentId },
            success: function(data) {
                employees = data;
                loadRosterData(departmentId, selectedMonth).then(rosterData => {
                generateTable(selectedMonth, rosterData);
                });
                $('#save-roster').removeClass('d-none');
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
        closeOffcanvasSmoothly();
        });
    });

       // Load shift options first
    fetch('getrostershifts')
        .then(response => response.json())
        .then(data => {
            shiftOptions = data.filter(opt => opt.id !== '' && opt.id !== null);
        })
        .catch(error => {
            console.error('Error loading shift options:', error);
    });
    
    function loadRosterData(departmentId, month) {
        return fetch(`get-roster-data?department_id=${departmentId}&month=${month}`)
            .then(response => response.json());
    }

   
    // Close dropdowns on outside click 
    document.addEventListener('click', function() {
        document.querySelectorAll('.ms-dropdown.open').forEach(d => {
            d.classList.remove('open');
            d.closest('.ms-wrap').querySelector('.ms-box').classList.remove('is-open');
        });
    });

     // refresh the tag display inside .ms-box 
    function refreshMsBox(wrap) {
        const box     = wrap.querySelector('.ms-box');
        const checked = wrap.querySelectorAll('input[type=checkbox]:checked');
        const ph      = wrap.querySelector('.ms-placeholder');

        wrap.querySelectorAll('.ms-tag').forEach(t => t.remove());

        if (checked.length === 0) {
            ph.style.display = '';
            box.classList.remove('has-value');
        } else {
            ph.style.display = 'none';
            box.classList.add('has-value');
            checked.forEach(cb => {
                const tag = document.createElement('span');
                tag.className  = 'ms-tag';
                tag.textContent = cb.dataset.code;
                box.appendChild(tag);
            });
        }
    }


    function generateTable(month, existingData = {}) {
        const [year, monthNum] = month.split('-');
        const daysInMonth = new Date(year, monthNum, 0).getDate();
        const thead = document.querySelector('#shiftTable thead');
        const tbody = document.querySelector('#shiftTable tbody');
        thead.innerHTML = '';
        tbody.innerHTML = '';

        // Header row
        let headerRow = `<tr><th nowrap>NO</th><th nowrap>NAME OF EMPLOYEE</th>`;

        for (let d = 1; d <= daysInMonth; d++) headerRow += `<th class="text-center">${d}</th>`;
        headerRow += `</tr>`;
        thead.innerHTML = headerRow;

        // Employee rows
        employees.forEach(emp => {
            let row = `<tr><td>${emp.id}</td><td class="name-col nowrap">${emp.fullname}</td>`;

        for (let d = 1; d <= daysInMonth; d++) {
                const raw = (existingData[emp.id] && existingData[emp.id][d]) || [];
                
                // raw is now always an array e.g. [3, 5] or [3] or []
                const selected = (Array.isArray(raw) ? raw : (raw ? [raw] : [])).map(String);
                
                const optItems = shiftOptions.map(opt => {
                    const isSel = selected.includes(String(opt.id));
                    return `
                        <label class="ms-opt${isSel ? ' selected' : ''}">
                            <input type="checkbox"
                                data-emp="${emp.id}"
                                data-day="${d}"
                                data-month="${month}"
                                data-code="${opt.code}"
                                value="${opt.id}"
                                ${isSel ? 'checked' : ''}>
                            ${opt.code}
                        </label>`;
                }).join('');

                const initTags = shiftOptions
                    .filter(opt => selected.includes(String(opt.id)))
                    .map(opt => `<span class="ms-tag">${opt.code}</span>`)
                    .join('');

                const hasVal = selected.length > 0;

                row += `
                    <td style="padding:0">
                        <div class="ms-wrap">
                            <div class="ms-box${hasVal ? ' has-value' : ''}">
                                ${initTags}
                                <span class="ms-placeholder"${hasVal ? ' style="display:none"' : ''}>—</span>
                            </div>
                            <div class="ms-dropdown">${optItems}</div>
                        </div>
                    </td>`;
            }

            row += `</tr>`;
            tbody.innerHTML += row;
        });

        // ── Attach delegated events ONCE by cloning tbody (removes old listeners) ──
        const newTbody = tbody.cloneNode(true);
        tbody.parentNode.replaceChild(newTbody, tbody);
        const tb = document.querySelector('#shiftTable tbody');

        // Open / close dropdown
        tb.addEventListener('click', function (e) {
            const box = e.target.closest('.ms-box');
            if (!box) return;

            document.querySelectorAll('.ms-dropdown.open').forEach(d => {
                d.classList.remove('open');
                d.closest('.ms-wrap').querySelector('.ms-box').classList.remove('is-open');
            });

            const wrap = box.closest('.ms-wrap');
            const dropdown = wrap.querySelector('.ms-dropdown');
            const rect = box.getBoundingClientRect();

            dropdown.style.top = (rect.bottom + window.scrollY + 2) + 'px';
            dropdown.style.left = (rect.left + window.scrollX) + 'px';
            dropdown.classList.add('open');
            box.classList.add('is-open');
            e.stopPropagation();
        });

        // Checkbox toggle → refresh tags
        tb.addEventListener('change', function (e) {
            const cb = e.target.closest('input[type=checkbox]');
            if (!cb) return;
            cb.closest('.ms-opt').classList.toggle('selected', cb.checked);
            refreshMsBox(cb.closest('.ms-wrap'));
        });

        // Keep dropdown open on internal click
        tb.addEventListener('click', function (e) {
            if (e.target.closest('.ms-dropdown')) e.stopPropagation();
        });
    }


    // Handle form submit
    $('#shiftForm').on('submit', function(e) {
        e.preventDefault();

        const month   = $('#month').val();
        const payload = [];

        // Read every checked checkbox in the table
        document.querySelectorAll('#shiftTable tbody input[type=checkbox]:checked').forEach(cb => {
            const empId = cb.dataset.emp;
            const day   = String(cb.dataset.day).padStart(2, '0');
            const date  = `${month}-${day}`;

            payload.push({
                emp_id : empId,
                shift  : cb.value,   // shift id
                date   : date        // YYYY-MM-DD  (matches backend work_date)
            });
        });

        let action_url = "{{ url('/fullrosterstore') }}";
         $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $.ajax({
            url: action_url,
            type: 'POST',
            data:{ shifts: payload },
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


// function generateTable(month, existingData = {}) {
    //     const [year, monthNum] = month.split('-');
    //     const daysInMonth = new Date(year, monthNum, 0).getDate();

    //     const thead = document.querySelector('#shiftTable thead');
    //     const tbody = document.querySelector('#shiftTable tbody');
    //     thead.innerHTML = '';
    //     tbody.innerHTML = '';

    //     let headerRow = `<tr><th nowrap>NO</th><th nowrap>NAME OF EMPLOYEE</th>`;
    //     for (let d = 1; d <= daysInMonth; d++) {
    //         headerRow += `<th class="text-center">${d}</th>`;
    //     }
    //     headerRow += `</tr>`;
    //     thead.innerHTML = headerRow;

    //     employees.forEach((emp, index) => {
    //         let row = `<tr><td>${emp.id}</td><td class="name-col nowrap">${emp.fullname}</td>`;
    //         for (let d = 1; d <= daysInMonth; d++) {
    //             const existingShift = (existingData[emp.id] && existingData[emp.id][d]) || '';
    //            row += `<td style="padding: 0px;">
    //                 <select name="shifts[${emp.id}][${d}]" class="form-control form-control-sm shiftcode" style="width: 55px;" muptiple>
                        
    //                     ${shiftOptions.map(opt =>{
    //                          const selectedClass = opt.id == existingShift ? 'selected-option' : '';
    //                          return `<option value="${opt.id}" ${opt.id == existingShift ? 'selected' : ''} class="${selectedClass}">${opt.code}</option>`;
    //                       }).join('')}
    //                 </select>
    //             </td>`;

    //         }
    //         row += `</tr>`;
    //         tbody.innerHTML += row;
    //     });

    //      $('.shiftcode').on('change', function() {
    //             $(this).find('option').removeClass('selected-option bg-success');
                
    //             $(this).find('option:selected').addClass('selected-option bg-success');
                
    //             if ($(this).val()) {
    //                 $(this).addClass('border-success');
    //             } else {
    //                 $(this).removeClass('border-success');
    //             }
    //         });

    //          $('.shiftcode').trigger('change');
    // }
</script>

<style>
    /* Style for selected option in dropdown */
        select.form-control-sm option.selected-option {
            background-color: #007bff !important;
            color: white !important;
        }

        /* Optional: Style for the select when a value is selected */
        select.form-control-sm.border-success {
            border-color: #007bff !important;
            border-width: 2px;
        }

        /* For Firefox compatibility */
        select.form-control-sm option:checked {
            background-color: #007bff !important;
            color: white !important;
        }
        .ms-wrap {
            position: relative;
            width: 80px;
        }
        .ms-box {
            min-height: 28px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background: #fff;
            cursor: pointer;
            padding: 2px 3px;
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
            align-items: center;
            transition: border-color .15s;
        }
        .ms-box.has-value { border-color: #0d6efd; background: #fff; }
        .ms-box.is-open   { border-color: #0d6efd; box-shadow: 0 0 0 2px rgba(13,110,253,.2); }
        .ms-tag {
            background: #0d6efd;
            color: #fff;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            padding: 1px 4px;
            white-space: nowrap;
            line-height: 1.4;
        }
        .ms-placeholder {
            color: #aaa;
            font-size: 10px;
            padding: 2px;
        }
        .ms-dropdown {
            display: none;
            position: fixed;
            z-index: 99999;
            background: #fff;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-shadow: 0 4px 14px rgba(0,0,0,.15);
            min-width: 110px;
            padding: 3px 0;
            max-height: 200px;
            overflow-y: auto;
        }
        .ms-dropdown.open { display: block; }
        .ms-opt {
            display: block;
            width: 100%;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 400;
            color: #212529;
            cursor: pointer;
            user-select: none;
            background: transparent;
            border-radius: 0;
            box-sizing: border-box;
            margin: 0;
            line-height: 1.4;
            appearance: none;
            -webkit-appearance: none;
        }
        .ms-opt:hover {
            background: #e8f0fe;
            color: #0d6efd;
        }
        .ms-opt.selected {
            background: #0d6efd;
            color: #fff;
            font-weight: 600;
        }
        .ms-opt.selected:hover {
            background: #0b5ed7;
            color: #fff;
        }
        .ms-opt input[type="checkbox"] {
            display: none !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            width: 0 !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            position: absolute !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
</style>


@endsection

