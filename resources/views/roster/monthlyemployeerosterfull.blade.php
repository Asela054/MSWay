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
                            data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                class="fas fa-filter mr-1"></i> Filter
                            Options</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                </div>
                <div id="info_msg"></div>
                <form id="shiftForm">
                    <!-- <div class="center-block fix-width scroll-inner my-2"> -->
                        <div id="viewroster"></div>
                        <!-- <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="shiftTable">
                            <thead></thead>
                            <tbody></tbody>
                        </table> -->
                    <!-- </div> -->
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
    $(document).ready(function () {

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
                data: function (params) {
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
                data: function (params) {
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

            Swal.fire({
                title: '',
                html: '<div class="div-spinner"><div class="custom-loader"></div></div>',
                allowOutsideClick: false,
                showConfirmButton: false, // Hide the OK button
                backdrop: `
                    rgba(255, 255, 255, 0.5) 
                `,
                customClass: {
                    popup: 'fullscreen-swal'
                },
                didOpen: () => {
                    document.body.style.overflow = 'hidden';

                    $.ajax({
                        url: '{{ url("/get-employees-roster-info") }}',
                        data: {
                            department_id: departmentId,
                            selectedMonth: selectedMonth
                        },
                        success: function (data) {
                            Swal.close();
                            $('#viewroster').html(data);
                            initializeNewSelect2();
                            $('#save-roster').removeClass('d-none');
                        },
                        error: function () {
                            Swal.close();
                            alert('Failed to load employees.');
                        }
                    });
                    // $.ajax({
                    //     url: '{{ url("/get-employees-by-department") }}',
                    //     data: {
                    //         department_id: departmentId
                    //     },
                    //     success: function (data) {
                    //         employees = data;
                    //         loadRosterData(departmentId, selectedMonth).then(rosterData => {
                    //             generateTable(selectedMonth, rosterData);
                    //         });
                    //         $('#save-roster').removeClass('d-none');
                    //     },
                    //     error: function () {
                    //         alert('Failed to load employees.');
                    //     }
                    // });

                    document.body.style.overflowY = 'visible';
                }
            });
        });

        $('#month').on('change', function () {
            const departmentId = department.val();
            if (!departmentId) return;

            loadRosterData(departmentId, this.value).then(rosterData => {
                generateTable(this.value, rosterData);
            });
        });

        // Load shift options first
        fetch('getrostershifts').then(response => response.json()).then(data => {
            shiftOptions = data.filter(opt => opt.id !== '' && opt.id !== null);
        }).catch(error => {
            console.error('Error loading shift options:', error);
        });

        function loadRosterData(departmentId, month) {
            return fetch(`get-roster-data?department_id=${departmentId}&month=${month}`).then(response => response.json());
        }


        // Close dropdowns on outside click 
        document.addEventListener('click', function () {
            document.querySelectorAll('.ms-dropdown.open').forEach(d => {
                d.classList.remove('open');
                d.closest('.ms-wrap').querySelector('.ms-box').classList.remove('is-open');
            });
        });

        // refresh the tag display inside .ms-box 
        function refreshMsBox(wrap) {
            const box = wrap.querySelector('.ms-box');
            const checked = wrap.querySelectorAll('input[type=checkbox]:checked');
            const ph = wrap.querySelector('.ms-placeholder');

            wrap.querySelectorAll('.ms-tag').forEach(t => t.remove());

            if (checked.length === 0) {
                ph.style.display = '';
                box.classList.remove('has-value');
            } else {
                ph.style.display = 'none';
                box.classList.add('has-value');
                checked.forEach(cb => {
                    const tag = document.createElement('span');
                    tag.className = 'ms-tag';
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


                // Make sure the wrap has position relative for absolute positioning to work
                wrap.style.position = 'relative';

                // Reset any inline styles that might interfere
                dropdown.style.top = '';
                dropdown.style.left = '';

                // Toggle current dropdown
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
        // $('#shiftForm').on('submit', function (e) {
        //     e.preventDefault();

        //     const month = $('#month').val();
        //     const payload = [];

        //     // Track which emp+date combos have at least one checked shift
        //     const checkedKeys = new Set();

        //     document.querySelectorAll('#shiftTable tbody input[type=checkbox]:checked').forEach(cb => {
        //         const empId = cb.dataset.emp;
        //         const day = String(cb.dataset.day).padStart(2, '0');
        //         const date = `${month}-${day}`;
        //         const key = `${empId}_${date}`;

        //         checkedKeys.add(key);

        //         payload.push({
        //             emp_id: empId,
        //             shift: cb.value,
        //             date: date
        //         });
        //     });

        //     // send a sentinel entry with shift = null so backend knows to delete everything
        //     document.querySelectorAll('#shiftTable tbody input[type=checkbox]:not(:checked)').forEach(
        //         cb => {
        //             const empId = cb.dataset.emp;
        //             const day = String(cb.dataset.day).padStart(2, '0');
        //             const date = `${month}-${day}`;
        //             const key = `${empId}_${date}`;

        //             if (!checkedKeys.has(key)) {
        //                 checkedKeys.add(key); // prevent duplicate sentinel entries
        //                 payload.push({
        //                     emp_id: empId,
        //                     shift: null, // sentinel — means "delete all shifts for this emp+date"
        //                     date: date
        //                 });
        //             }
        //         });

        //     let action_url = "{{ url('/fullrosterstore') }}";
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });

        //     $.ajax({
        //         url: action_url,
        //         type: 'POST',
        //         data: JSON.stringify({
        //             shifts: payload
        //         }),
        //         contentType: 'application/json',
        //         dataType: 'json',
        //         success: function (response) {
        //             if (response.errors) {
        //                 const actionObj = {
        //                     icon: 'fas fa-warning',
        //                     title: '',
        //                     message: 'Record Error',
        //                     url: '',
        //                     target: '_blank',
        //                     type: 'danger'
        //                 };
        //                 action(JSON.stringify(actionObj));
        //             }
        //             if (response.success) {
        //                 const actionObj = {
        //                     icon: 'fas fa-save',
        //                     title: '',
        //                     message: response.success,
        //                     url: '',
        //                     target: '_blank',
        //                     type: 'success'
        //                 };
        //                 $('#shiftForm')[0].reset();
        //                 actionreload(JSON.stringify(actionObj));
        //             }
        //         },
        //         error: function (xhr, status, error) {
        //             console.error('Error:', error);
        //             const actionObj = {
        //                 icon: 'fas fa-times',
        //                 title: '',
        //                 message: 'Something went wrong!',
        //                 url: '',
        //                 target: '_blank',
        //                 type: 'danger'
        //             };
        //             action(JSON.stringify(actionObj));
        //         }
        //     });
        // });

        $('#shiftForm').on('submit', function (e) {
            e.preventDefault();
            $('#save-roster').prop('disabled', true);

            Swal.fire({
                title: '',
                html: '<div class="div-spinner"><div class="custom-loader"></div></div>',
                allowOutsideClick: false,
                showConfirmButton: false, // Hide the OK button
                backdrop: `
                    rgba(255, 255, 255, 0.5) 
                `,
                customClass: {
                    popup: 'fullscreen-swal'
                },
                didOpen: () => {
                    document.body.style.overflow = 'hidden';

                    let rosterData = [];

                    // Loop each select2shift select
                    $('.select2shift').each(function () {
                        let select      = $(this);
                        let selectedIds = select.val(); // array of selected shift IDs

                        if (selectedIds && selectedIds.length > 0) {
                            // Get emp_id and work_date from first option's data attributes
                            let firstOption = select.find('option').first();
                            let empId       = firstOption.data('empid');
                            let workDate    = firstOption.data('rosterdate');

                            $.each(selectedIds, function (i, shiftId) {
                                rosterData.push({
                                    shift_id  : shiftId,
                                    emp_id    : empId,
                                    work_date : workDate
                                });
                            });
                        }
                    });

                    if (rosterData.length === 0) {
                        // alert('No roster data to save.');
                        Swal.fire('Error!', 'No roster data to save..', 'error');
                        return;
                    }
                    // console.log(rosterData);

                    $.ajax({
                        url : '{{ url("/fullrosterstore") }}',
                        type: 'POST',
                        data: {
                            _token      : '{{ csrf_token() }}',
                            roster_data : JSON.stringify(rosterData)
                        },
                        success: function (response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: "Saved!",
                                    text: response.message,
                                    showConfirmButton: true, // 1. Ensure the OK button is visible
                                    confirmButtonText: 'OK'  // Optional: You can customize the button text here
                                }).then((result) => {
                                    // 2. Check if the user clicked the confirm ("OK") button
                                    if (result.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                                $('#save-roster').prop('disabled', true);
                            }
                        },
                        error: function () {
                            Swal.close();
                            Swal.fire('Error!', 'Failed to save roster.', 'error');
                            $('#save-roster').prop('disabled', true);
                        }
                    });

                    document.body.style.overflow = 'visible';
                }
            });
        });
    });

    // Example: When you dynamically add a new row or load roster data
    function initializeNewSelect2() {
        $('.select2shift:not(.select2-hidden-accessible)').each(function() {
            $(this).select2({
                width: '100%',
                placeholder: '-',
                dropdownAutoWidth: true
            });
        });

        $(document).on('select2:select select2:unselect', '.select2shift', function () {
            let container = $(this).next('.select2-container');
            let count = $(this).val() ? $(this).val().length : 0;

            if (count > 0) {
                container.find('.select2-selection--multiple')
                        .css('border', '1.5px solid #1a73e8');
            } else {
                container.find('.select2-selection--multiple')
                        .css('border', '1px solid #ced4da');
            }
        });
    }
</script>
@endsection