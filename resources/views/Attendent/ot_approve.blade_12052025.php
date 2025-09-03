<?php $page_stitle = 'Report on Employee O.T. Hours - Multi Offset'; ?>
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.attendant&leave_nav_bar')
               
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Location</label>
                                <select name="location" id="location" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee" class="form-control form-control-sm">
                                </select>
                            </div>

                            <div class="col-md-3 div_date_range">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd"
                                           required 
                                    >

                                    <input type="date" id="to_date" name="to_date" class="form-control"
                                           required
                                           placeholder="yyyy-mm-dd"
                                    >
                                </div>
                            </div>
                            <div class="col">
                                <br>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn float-right ml-2" id="btn-filter"><i class="fas fa-search mr-2"></i>Filter</button>
                                <button type="button" class="btn btn-danger btn-sm filter-btn float-right" id="btn-clear"><i class="far fa-trash-alt"></i>&nbsp;&nbsp;Clear</button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2">
                <div class="row">
                        <div class="col-6">
                            <div class="custom-control custom-checkbox ml-2 mb-2">
                                <input type="checkbox" class="custom-control-input checkallocate" id="selectAll">
                                <label class="custom-control-label" for="selectAll">Select All Records</label>
                            </div>
                        </div>
                        <div class="col-6 text-right">
                            <button type="button" class="btn btn-primary btn-sm mr-3 px-4" id="btn_approve_ot">Approve</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div id="info_msg"></div>
                            <hr>
                            <div class="center-block fix-width scroll-inner pb-3">
                                <table class="table table-striped table-bordered table-sm small nowrap" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center"></th>
                                            <th>Emp ID</th>
                                            <th>ETF NO</th>
                                            <th>Employee</th>
                                            <th>Date</th>
                                            <th>Day</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>OT Time</th>
                                            <th>D/OT Time</th>
                                            <th>T/OT Time</th>
                                            <th>Is Holiday</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
            let employee = $('#employee');
            let location = $('#location');

            $('#selectAll').click(function (e) {
                $('#dataTable').closest('table').find('td input:checkbox').prop('checked', this.checked);
            });

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
                    url: '{{url("department_list_sel2")}}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company.val()
                        }
                    },
                    cache: true
                }
            });

            employee.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("employee_list_sel2")}}',
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

            location.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("location_list_sel2")}}',
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

            $('#dataTable').DataTable({lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'All']
            ]});

            function load_table(){
                $('#btn-filter').html('<i class="fa fa-spinner fa-spin mr-2"></i> Searching').prop('disabled', true);
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                $.ajax({
                    url: "{{ route('get_ot_details') }}",
                    method: "POST",
                    data: {
                        department: department,
                        employee: employee,
                        location: location,
                        from_date: from_date,
                        to_date: to_date,
                        _token: '{{csrf_token()}}'
                    },
                    success: function (res) {
                        let ot_data = res.ot_data;
                        let ot_data_html = '';

                        if(ot_data.length > 0) {
                            let j=1;
                            ot_data.forEach(function(key, data) {
                                let is_approved = key.is_approved;
                                //iterate through key
                                for(let i in key.ot_breakdown) {
                                    let obj = key.ot_breakdown[i];

                                    let is_holiday = obj.is_holiday;

                                    if(is_holiday == 1) {
                                        is_holiday = 'Yes';
                                    } else {
                                        is_holiday = 'No';
                                    }

                                    let from_input = '<input type="datetime-local" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" value="'+ obj.from_24+'" readonly>';
                                    let to_input = '<input type="datetime-local" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" value="'+obj.to_24+'" readonly>';
                                    let hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.hours+'" step=".01" readonly>';
                                    let double_hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.double_hours+'" step=".01" readonly>';
                                    let triple_hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.triple_hours +'" step=".01" readonly>';
                                    //let one_point_five_hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.one_point_five_ot_hours+'" step=".01">';

                                    let h_class = '';
                                    let h_label = 'Evening';
                                    if(obj.is_morning){
                                        h_class = 'bg-teal-light';
                                        h_label = 'Morning';
                                    }

                                    ot_data_html += '<tr class="'+h_class+'" >';


                                    if(is_approved == false){
                                        ot_data_html += '<td class="text-center"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input cb" id="customCheck'+j+'" data-emp_id="'+obj.emp_id+'" data-date="'+obj.date+'"><label class="custom-control-label" for="customCheck'+j+'"></label></div></td>';
                                    }else{
                                        ot_data_html += '<td> <i class="fa fa-check text-success"> </i> </td>';
                                    }

                                    ot_data_html += '<td>'+obj.emp_id+'</td>';
                                    ot_data_html += '<td>'+obj.etf_no+'</td>';
                                    ot_data_html += '<td>'+obj.name+'</td>';
                                    ot_data_html += '<td>'+obj.date+'</td>';
                                    ot_data_html += '<td>'+obj.day_name+'</td>';
                                    //ot_data_html += '<td>'+h_label+'</td>';
                                    ot_data_html += '<td>'+from_input+'</td>';
                                    ot_data_html += '<td>'+to_input+'</td>';
                                    ot_data_html += '<td>'+ hours_input +'</td>';
                                    ot_data_html += '<td>'+ double_hours_input +'</td>';
                                    ot_data_html += '<td>'+ triple_hours_input+'</td>';
                                    ot_data_html += '<td>'+is_holiday+'</td>';
                                    ot_data_html += '</tr>';
                                }
                                j++;
                            });
                        }

                        if ($.fn.DataTable.isDataTable("#dataTable")) {
                            $('#dataTable').DataTable().destroy();
                        }

                        $('#dataTable > tbody').empty().append(ot_data_html);
                        $('#dataTable').DataTable( {
                            "destroy": true,
                            "processing": true,
                            "lengthMenu": [
                                [10, 25, 50, -1],
                                [10, 25, 50, 'All']
                            ]
                        });

                        $('#btn-filter').html('<i class="fas fa-search mr-2"></i>Filter').prop('disabled', false);
                    }
                });
            }

            $('#from_date').on('change', function() {
                let fromDate = $(this).val();
                $('#to_date').attr('min', fromDate); 
            });

            $('#to_date').on('change', function() {
                let toDate = $(this).val();
                $('#from_date').attr('max', toDate); 
            });

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                $('.info_msg').html('');
                load_table();

            });

            //document btn_approve_ot click
            $(document).on('click', '#btn_approve_ot', function(e) {
                $('#info_msg').html('');
                $('#btn_approve_ot').html('<i class="fa fa-spinner fa-spin mr-2"></i> Processing').prop('disabled', true);

                var tablelist = $("#dataTable tbody input[type=checkbox]:checked");
                if(tablelist.length>0){
                    ot_data = [];
                    tablelist.each(function() {
                        var row = $(this).closest("tr");

                        let emp_id = $(this).data('emp_id');
                        let date = $(this).data('date');
                        let from_input = row.find('td:nth-child(7) input');
                        let to_input = row.find('td:nth-child(8) input');
                        let hours_input = row.find('td:nth-child(9) input');
                        //let one_point_five_hours_input = row.find('td:nth-child(9) input');
                        let double_hours_input = row.find('td:nth-child(10) input');
                        let triple_hours_input = row.find('td:nth-child(11) input');

                        let from = from_input.val();
                        let to = to_input.val();
                        let hours = hours_input.val();
                        //let one_point_five_hours = one_point_five_hours_input.val();
                        let double_hours = double_hours_input.val();
                        let triple_hours = triple_hours_input.val();
                        let is_holiday = row.find('td:nth-child(12)');

                        let ot_data_obj = {
                            emp_id: emp_id,
                            date: date,
                            from: from,
                            to: to,
                            hours: hours,
                            //one_point_five_hours: one_point_five_hours,
                            double_hours: double_hours,
                            triple_hours: triple_hours,
                            is_holiday: is_holiday.text()
                        }

                        ot_data.push(ot_data_obj);
                    });

                    $.ajax({
                        url: "{{ route('ot_approve_post') }}",
                        method: "POST",
                        data: {
                            ot_data: ot_data,
                            _token: '{{csrf_token()}}'
                        },
                        success: function (res) {
                            if(res.success) {
                                $('#info_msg').html('<div class="alert alert-success">' + res.success + '</div>');
                                load_table();
                            }

                            $('#btn_approve_ot').html('Approve').prop('disabled', false);
                        }
                    });
                }
                else {
                    $('#info_msg').html('<div class="alert alert-danger">Please select at least one attendance</div>');
                    $('#btn_approve_ot').html('Approve').prop('disabled', false);
                }
            });

            document.getElementById('btn-clear').addEventListener('click', function() {
                $('#formFilter').reset();
                $('#company').val('').trigger('change');   
                $('#location').val('').trigger('change');
                $('#department').val('').trigger('change');
                $('#employee').val('').trigger('change');
                $('#from_date').val('');                     
                $('#to_date').val('');                       

                // load_dt('', '', '', '', '');
            });
        });

    </script>

@endsection