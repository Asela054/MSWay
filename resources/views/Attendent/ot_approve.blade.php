<?php $page_stitle = 'Report on Employee O.T. Hours - Multi Offset'; ?>
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
                         <div class="page-header-icon"><i class="fa-light fa-file-signature"></i></div>
                         <span>OT Approve</span>
                     </h1>
                 </div>
             </div>
         </div>

        <div class="container-fluid mt-2 p-0 p-2">
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
                    <div class="info_msg"></div>
                    <div class="response">
                        <div class="alert alert-info" role="alert">
                            <span> Please select a date period to continue</span>
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
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company.val(),
                            department: department.val()
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

            function load_table() {
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                $('.response').html('');
                let btn = $('#btn-filter');
                btn.attr('disabled', true);
                btn.html('<i class="fa fa-spinner fa-spin"></i>');

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
                        btn.html('Filter');
                        btn.prop('disabled', false);

                        let ot_data = res.ot_data;
                        
                        // Create the table structure
                        let ot_data_html = '<div class="form-group mt-3 ">';
                        ot_data_html += '<button type="button" class="btn btn-primary btn-sm float-right" id="btn_approve_ot"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Approve OT</button><br>';
                        ot_data_html += '</div>';
                        
                        ot_data_html += '<div class="center-block fix-width scroll-inner">';
                        ot_data_html += '<table class="table table-striped table-bordered table-sm small nowrap" id="ot_table">';
                        ot_data_html += '<thead>';
                        ot_data_html += '<tr>';
                        ot_data_html += '<th><label> <input type="checkbox" name="selectAll" id="selectAllDomainList" /> Check All </label></th>';
                        ot_data_html += '<th>Emp ID</th>';
                        ot_data_html += '<th>ETF NO</th>';
                        ot_data_html += '<th>Employee</th>';
                        ot_data_html += '<th>Date</th>';
                        ot_data_html += '<th>Day</th>';
                        ot_data_html += '<th>From</th>';
                        ot_data_html += '<th>To</th>';
                        ot_data_html += '<th>OT Time</th>';
                        ot_data_html += '<th>D/OT Time</th>';
                        ot_data_html += '<th>T/OT Time</th>';
                        ot_data_html += '<th>Is Holiday</th>';
                        ot_data_html += '</tr>';
                        ot_data_html += '</thead>';
                        ot_data_html += '<tbody>';
                        
                        if(ot_data.length > 0) {
                            ot_data.forEach(function(key, data) {
                                let is_approved = key.is_approved;
                                let obj = key.ot_breakdown;
                                let is_holiday = obj.is_holiday == 1 ? 'Yes' : 'No';
                                
                                // let from_input = '<input type="datetime-local" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" value="'+ obj.from_24+'" readonly>';
                                // let to_input = '<input type="datetime-local" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" value="'+obj.to_24+'" readonly>';
                                // let hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.hours+'" step=".01" readonly>';
                                // let double_hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.double_hours+'" step=".01" readonly>';
                                // let triple_hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.triple_hours +'" step=".01" readonly>';
                                
                                let h_class = obj.is_morning ? 'bg-teal-light' : '';
                                
                                ot_data_html += '<tr class="'+h_class+'" >';
                                
                                if(is_approved == false){
                                    ot_data_html += '<td><input type="checkbox" class="cb" ' +
                                        'data-emp_id="'+obj.emp_id+'" ' +
                                        'data-date="'+obj.date+'" ' +
                                        ' /></td>';
                                }else{
                                    ot_data_html += '<td> <i class="fa fa-check text-success"> </i> </td>';
                                }
                                
                                ot_data_html += '<td>'+obj.emp_id+'</td>';
                                ot_data_html += '<td>'+obj.etf_no+'</td>';
                                ot_data_html += '<td>'+obj.name+'</td>';
                                ot_data_html += '<td>'+obj.date+'</td>';
                                ot_data_html += '<td>'+obj.day_name+'</td>';
                                ot_data_html += '<td>'+obj.from_24+'</td>';
                                ot_data_html += '<td>'+obj.to_24+'</td>';
                                ot_data_html += '<td>'+obj.hours +'</td>';
                                ot_data_html += '<td>'+obj.double_hours +'</td>';
                                ot_data_html += '<td>'+obj.triple_hours+'</td>';
                                ot_data_html += '<td>'+is_holiday+'</td>';
                                ot_data_html += '</tr>';
                            });
                        }
                        
                        ot_data_html += '</tbody>';
                        ot_data_html += '</table>';
                        ot_data_html += '</div>';
                        
                        $('.response').html(ot_data_html);
                        
                        // Initialize DataTable with export buttons
                        $('#ot_table').DataTable({
                              dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                            "buttons": [{
                                    extend: 'csv',
                                    className: 'btn btn-success btn-sm',
                                    title: 'OT Approve Information',
                                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                                },
                                { 
                                    extend: 'pdf', 
                                    className: 'btn btn-danger btn-sm', 
                                    title: 'OT Approve Information', 
                                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                    orientation: 'landscape', 
                                    pageSize: 'legal', 
                                    customize: function(doc) {
                                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                    }
                                },
                                {
                                    extend: 'print',
                                    title: 'OT Approve  Information',
                                    className: 'btn btn-primary btn-sm',
                                    text: '<i class="fas fa-print mr-2"></i> Print',
                                    customize: function(win) {
                                        $(win.document.body).find('table')
                                            .addClass('compact')
                                            .css('font-size', 'inherit');
                                    },
                                },
                            ],

                            responsive: true,
                            pageLength: 25,
                            order: [[1, 'desc']]
                        });
                        
                        $('.date_time').datetimepicker({
                            format:'Y-m-d H:i',
                            mask:false,
                        });
                        
                        $(':checkbox[name=selectAll]').click(function () {
                            $(':checkbox.cb').prop('checked', this.checked);
                        });
                    },
                    error: function(xhr, status, error) {
                        btn.html('Filter');
                        btn.prop('disabled', false);
                        console.error("Error:", error);
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
            $(document).on('click', '#btn_approve_ot', async function (e) {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {

                    let btn = $(this);

                    btn.attr('disabled', true);
                    btn.html('<i class="fa fa-spinner fa-spin"></i>');

                    let cb = $('.cb');
                    let ot_data = [];

                    cb.each(function (e1) {
                        let cb_obj = $(this);
                        if (cb_obj.is(':checked')) {
                            let emp_id = cb_obj.data('emp_id');
                            let date = cb_obj.data('date');

                            // Updated column references - now getting text content instead of input values
                            let from_cell = cb_obj.closest('tr').find('td:eq(6)');
                            let to_cell = cb_obj.closest('tr').find('td:eq(7)');
                            let hours_cell = cb_obj.closest('tr').find('td:eq(8)');
                            let double_hours_cell = cb_obj.closest('tr').find('td:eq(9)');
                            let triple_hours_cell = cb_obj.closest('tr').find('td:eq(10)');
                            let is_holiday_cell = cb_obj.closest('tr').find('td:eq(11)');

                            let from = from_cell.text();
                            let to = to_cell.text();
                            let hours = hours_cell.text();
                            let double_hours = double_hours_cell.text();
                            let triple_hours = triple_hours_cell.text();
                            let is_holiday = cb_obj.parent().parent().find('td:nth-child(12)');

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

                        }

                    });

                    if (ot_data.length > 0) {
                        $(btn).html('<i class="fa fa-spinner fa-spin"></i>');
                        $(btn).prop('disabled', true);

                        $.ajax({
                            url: "{{ route('ot_approve_post') }}",
                            method: "POST",
                            data: {
                                ot_data: ot_data,
                                _token: '{{csrf_token()}}'
                            },
                            success: function (res) {
                                if (res.errors) {
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
                                if (res.success) {
                                    const actionObj = {
                                        icon: 'fas fa-save',
                                        title: '',
                                        message: res.success,
                                        url: '',
                                        target: '_blank',
                                        type: 'success'
                                    };
                                    const actionJSON = JSON.stringify(actionObj, null, 2);
                                    actionreload(actionJSON);
                                }
                                btn.html('Approve');
                                btn.attr('disabled', false);
                            }
                        });
                    } else {
                        $('.info_msg').html('<div class="alert alert-danger">Please select at least one attendance</div>');
                        $('html, body').animate({
                            scrollTop: 100
                        }, 'fast');
                         btn.attr('disabled', false);
                    }
                }
            });

            document.getElementById('btn-clear').addEventListener('click', function() {
                document.getElementById('formFilter').reset();

                $('#company').val('').trigger('change');   
                $('#location').val('').trigger('change');
                $('#department').val('').trigger('change');
                $('#employee').val('').trigger('change');
                $('#from_date').val('');                     
                $('#to_date').val('');                       
                // load_dt('', '', '', '', '');
            });

        });



                    // function load_table(){
            //     let department = $('#department').val();
            //     let employee = $('#employee').val();
            //     let location = $('#location').val();
            //     let from_date = $('#from_date').val();
            //     let to_date = $('#to_date').val();

            //     $('.response').html('');
            //     let btn = $('#btn-filter');
            //     btn.attr('disabled',true);
            //     btn.html('<i class="fa fa-spinner fa-spin"></i>');

            //     $.ajax({
            //         url: "{{ route('get_ot_details') }}",
            //         method: "POST",
            //         data: {
            //             department: department,
            //             employee: employee,
            //             location: location,
            //             from_date: from_date,
            //             to_date: to_date,
            //             _token: '{{csrf_token()}}'
            //         },
            //         success: function (res) {
            //             // console.log(res);
            //             btn.html('Filter');
            //             btn.prop('disabled', false);

            //             let ot_data = res.ot_data;

            //             let ot_data_html = '<div class="alert alert-info">';
            //             ot_data_html += '<span> Please check each rows and click Approve button in bottom of the table.</span>';
            //             ot_data_html += '</div>';

            //             ot_data_html = '<div class="center-block fix-width scroll-inner">';
            //             ot_data_html += '<table class="table table-striped table-bordered table-sm small nowrap" id="ot_table">';
            //             ot_data_html += '<thead>';
            //             ot_data_html += '<tr>';
            //             ot_data_html += '<th><label> <input type="checkbox" name="selectAll" id="selectAllDomainList" /> Check All </label></th>';
            //             ot_data_html += '<th>Emp ID</th>';
            //             ot_data_html += '<th>ETF NO</th>';
            //             ot_data_html += '<th>Employee</th>';
            //             ot_data_html += '<th>Date</th>';
            //             ot_data_html += '<th>Day</th>';
            //             //ot_data_html += '<th>Type</th>';
            //             ot_data_html += '<th>From</th>';
            //             ot_data_html += '<th>To</th>';
            //             ot_data_html += '<th>OT Time</th>';
            //             ot_data_html += '<th>D/OT Time</th>';
            //             ot_data_html += '<th>T/OT Time</th>';
            //             ot_data_html += '<th>Is Holiday</th>';
            //             ot_data_html += '</tr>';
            //             ot_data_html += '</thead>';
            //             ot_data_html += '<tbody>';


            //             if(ot_data.length > 0) {
            //                 ot_data.forEach(function(key, data) {
            //                     let is_approved = key.is_approved;
            //                     //iterate through key
                                
            //                     let obj = key.ot_breakdown;

            //                     let is_holiday = obj.is_holiday;

            //                     if(is_holiday == 1) {
            //                         is_holiday = 'Yes';
            //                     } else {
            //                         is_holiday = 'No';
            //                     }

            //                     let from_input = '<input type="datetime-local" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" value="'+ obj.from_24+'" readonly>';
            //                     let to_input = '<input type="datetime-local" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" value="'+obj.to_24+'" readonly>';
            //                     let hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.hours+'" step=".01" readonly>';
            //                     let double_hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.double_hours+'" step=".01" readonly>';
            //                     let triple_hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.triple_hours +'" step=".01" readonly>';
            //                     //let one_point_five_hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.one_point_five_ot_hours+'" step=".01">';

            //                     let h_class = '';
            //                     let h_label = 'Evening';
            //                     if(obj.is_morning){
            //                         h_class = 'bg-teal-light';
            //                         h_label = 'Morning';
            //                     }

            //                     ot_data_html += '<tr class="'+h_class+'" >';

            //                     if(is_approved == false){
            //                         ot_data_html += '<td><input type="checkbox" class="cb" ' +
            //                             'data-emp_id="'+obj.emp_id+'" ' +
            //                             'data-date="'+obj.date+'" ' +
            //                             ' /></td>';
            //                     }else{
            //                         ot_data_html += '<td> <i class="fa fa-check text-success"> </i> </td>';
            //                     }

            //                     ot_data_html += '<td>'+obj.emp_id+'</td>';
            //                     ot_data_html += '<td>'+obj.etf_no+'</td>';
            //                     ot_data_html += '<td>'+obj.name+'</td>';
            //                     ot_data_html += '<td>'+obj.date+'</td>';
            //                     ot_data_html += '<td>'+obj.day_name+'</td>';
            //                     //ot_data_html += '<td>'+h_label+'</td>';
            //                     ot_data_html += '<td>'+from_input+'</td>';
            //                     ot_data_html += '<td>'+to_input+'</td>';
            //                     ot_data_html += '<td>'+ hours_input +'</td>';
            //                     ot_data_html += '<td>'+ double_hours_input +'</td>';
            //                     ot_data_html += '<td>'+ triple_hours_input+'</td>';
            //                     ot_data_html += '<td>'+is_holiday+'</td>';
            //                     ot_data_html += '</tr>';
            //                 });
            //             }

            //             ot_data_html += '</tbody>';
            //             ot_data_html += '</table>' +
            //                 '<div class="form-group mt-3 ">' +
            //                 '<button type="button" class="btn btn-primary btn-sm float-right" id="btn_approve_ot">Approve</button><br>' +
            //                 '</div>';
            //             ot_data_html += '</div>';

            //             $('.response').html(ot_data_html);

            //             $('#ot_table').DataTable();
            //             btn.html('Filter');
            //             btn.prop('disabled', false);

            //             $('.date_time').datetimepicker({
            //                 format:'Y-m-d H:i',
            //                 mask:false,
            //             });

            //             $(':checkbox[name=selectAll]').click(function () {
            //                 $(':checkbox').prop('checked', this.checked);
            //             });

            //         }

            //     });
            // }
    </script>

@endsection