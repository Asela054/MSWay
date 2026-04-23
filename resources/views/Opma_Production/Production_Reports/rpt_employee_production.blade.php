@extends('layouts.app')

@section('content')

<main> 
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.reports_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-file-contract"></i></div>
                    <span>Employee Production Report</span>
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
                            Records</button>
                    </div><br><br>

                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>EMPLOYEE ID</th>
                                        <th>EMPLOYEE</th>
                                        <th>DATE</th>
                                        <th>MACHINE</th>
                                        <th>PRODUCT</th>
                                        <th>PRECENTAGE</th>
                                        <th>TARGET</th>
                                        <th>PRODUCE</th>
                                        <th>DIFFRENCE</th>
                                        <th>DAMAGE QTY</th>
                                        <th>AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>   
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" style="text-align: right">Totals:</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Offcanvas -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h2 class="offcanvas-title font-weight-bolderer" id="offcanvasRightLabel">Records Filter Options</h2>
                <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                    <span aria-hidden="true" class="h1 font-weight-bolderer">&times;</span>
                </button>
            </div>
            <div class="offcanvas-body">
                <ul class="list-unstyled">
                    <form class="form-horizontal" id="formFilter">
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Machine</label>
                                <select name="machine" id="machine" class="form-control form-control-sm">
                                    <option value="">Select Machine</option>
                                    @foreach ($machines as $machine)
                                        <option value="{{ $machine->id }}">{{ $machine->machine }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Product</label>
                                <select name="product" id="product" class="form-control form-control-sm">
                                    <option value="">Select Product</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->title }}</option>
                                    @endforeach
                                </select>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- autoTable plugin for jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
// Make jsPDF available globally
window.jsPDF = window.jspdf.jsPDF;

$(document).ready(function(){

    $('#report_menu_link').addClass('active');
    $('#report_menu_link_icon').addClass('active');
    $('#productionreport').addClass('navbtnactive');

    let employee_f = $('#employee_f');

    employee_f.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("employee_list_production")}}',
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


    // Add custom PDF button to DataTable
    function load_dt(machine, employee, product, from_date, to_date){
        $('#dataTable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [
                {
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Employee Production Report',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    footer: true
                },
                {
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    action: function (e, dt, node, config) {
                        generatePDF();
                    }
                },
                {
                    extend: 'print',
                    title: 'Employee Production Report',
                    className: 'btn btn-primary btn-sm',
                    text: '<i class="fas fa-print mr-2"></i> Print',
                    footer: true,
                    customize: function(win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit')
                            .find('td:nth-child(7), td:nth-child(8), td:nth-child(9), th:nth-child(7), th:nth-child(8), th:nth-child(9)')
                            .css('text-align', 'right');
                        
                        // Style the footer in print
                        $(win.document.body).find('tfoot tr').css({
                            'background-color': '#f8f9fa',
                            'font-weight': 'bold'
                        });
                    },
                },
            ],
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api();
                
                // Calculate total for Produce_qty (column 5)
                var Targetqty = api
                    .column( 6, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        var aNum = parseFloat(a) || 0;
                        var bNum = parseFloat(b) || 0;
                        return aNum + bNum;
                    }, 0 );

                    var produceTotal = api
                    .column( 7, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        var aNum = parseFloat(a) || 0;
                        var bNum = parseFloat(b) || 0;
                        return aNum + bNum;
                    }, 0 );


                    var diffrenceTotal = api
                    .column( 8, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        var aNum = parseFloat(a) || 0;
                        var bNum = parseFloat(b) || 0;
                        return aNum + bNum;
                    }, 0 );

                    var damageTotal = api
                    .column( 9, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        var aNum = parseFloat(a) || 0;
                        var bNum = parseFloat(b) || 0;
                        return aNum + bNum;
                    }, 0 );

                    var amountTotal = api
                    .column( 10, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        var aNum = parseFloat(a) || 0;
                        var bNum = parseFloat(b) || 0;
                        return aNum + bNum;
                    }, 0 );

               
                
                // Format the totals
                var formattedtargetQty = '';
                var formattedproduced = '';
                var formatteddifferenceTotal = '';
                var formattedDamage = '';
                var formattedAmount = '';

                
                if (!isNaN(Targetqty) && Targetqty !== 0) {
                    formattedtargetQty = Targetqty.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
                
                if (!isNaN(produceTotal) && produceTotal !== 0) {
                    formattedproduced = produceTotal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
                
                if (!isNaN(diffrenceTotal) && diffrenceTotal !== 0) {
                    formatteddifferenceTotal = diffrenceTotal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
                 if (!isNaN(damageTotal) && damageTotal !== 0) {
                    formattedDamage = damageTotal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
                 if (!isNaN(amountTotal) && amountTotal !== 0) {
                    formattedAmount = amountTotal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
                
                // Update footer
                $( api.column( 6 ).footer() ).html(
                    formattedtargetQty ? '<strong>' + formattedtargetQty + '</strong>' : ''
                );
                
                $( api.column( 7 ).footer() ).html(
                    formattedproduced ? '<strong>' + formattedproduced + '</strong>' : ''
                );
                
                $( api.column( 8 ).footer() ).html(
                    formatteddifferenceTotal ? '<strong>' + formatteddifferenceTotal + '</strong>' : ''
                );
                 $( api.column( 9 ).footer() ).html(
                    formattedDamage ? '<strong>' + formattedDamage + '</strong>' : ''
                );
                 $( api.column( 10 ).footer() ).html(
                    formattedAmount ? '<strong>' + formattedAmount + '</strong>' : ''
                );
            },
            "order": [
                [0, "desc"]
            ],
            ajax: {
                url: scripturl + "/Opma_Production/employee_production_list.php",
                type: 'POST',
                data : 
                    {machine :machine, 
                    employee :employee, 
                    product: product,
                    from_date: from_date,
                    to_date: to_date},
            },
            columns: [
                { data: 'emp_id', name: 'emp_id' },
                { data: 'emp_name', name: 'emp_name' },
                { data: 'date', name: 'date' },
                { data: 'machine', name: 'machine' },
                { data: 'product', name: 'product' },
                { 
                    data: 'precentage', 
                    name: 'precentage',
                    className: 'text-right',
                    render: function(data, type, row) {
                        if (data === null || data === undefined || data === '' || isNaN(data)) {
                            if (type === 'display' || type === 'filter') {
                                return '';
                            }
                            return 0;
                        }
                        
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + '%';
                        }
                        if (type === 'sort' || type === 'type') {
                            return parseFloat(data);
                        }
                        return data;
                    }
                },
                { 
                    data: 'target', 
                    name: 'target',
                    className: 'text-right',
                    render: function(data, type, row) {
                        if (data === null || data === undefined || data === '' || isNaN(data)) {
                            if (type === 'display' || type === 'filter') {
                                return '';
                            }
                            return 0;
                        }
                        
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                        if (type === 'sort' || type === 'type') {
                            return parseFloat(data);
                        }
                        return data;
                    }
                },
                { 
                    data: 'Produce_qty', 
                    name: 'Produce_qty',
                    className: 'text-right',
                    render: function(data, type, row) {
                        if (data === null || data === undefined || data === '' || isNaN(data)) {
                            if (type === 'display' || type === 'filter') {
                                return '';
                            }
                            return 0;
                        }
                        
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                        if (type === 'sort' || type === 'type') {
                            return parseFloat(data);
                        }
                        return data;
                    }
                },
                { 
                    data: 'difference', 
                    name: 'difference',
                    className: 'text-right',
                    render: function(data, type, row) {
                        if (data === null || data === undefined || data === '' || isNaN(data)) {
                            if (type === 'display' || type === 'filter') {
                                return '';
                            }
                            return 0;
                        }
                        
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                        if (type === 'sort' || type === 'type') {
                            return parseFloat(data);
                        }
                        return data;
                    }
                },
                { 
                    data: 'damage_qty', 
                    name: 'damage_qty',
                    className: 'text-right',
                    render: function(data, type, row) {
                        if (data === null || data === undefined || data === '' || isNaN(data)) {
                            if (type === 'display' || type === 'filter') {
                                return '';
                            }
                            return 0;
                        }
                        
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                        if (type === 'sort' || type === 'type') {
                            return parseFloat(data);
                        }
                        return data;
                    }
                },
                { 
                    data: 'amount', 
                    name: 'amount',
                    className: 'text-right',
                    render: function(data, type, row) {
                        if (data === null || data === undefined || data === '' || isNaN(data)) {
                            if (type === 'display' || type === 'filter') {
                                return '';
                            }
                            return 0;
                        }
                        
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                        if (type === 'sort' || type === 'type') {
                            return parseFloat(data);
                        }
                        return data;
                    }
                }
            ],
            "columnDefs": [
                {
                    "targets": [6, 7,8,9,10],
                    "type": "num",
                    "render": function(data, type, row) {
                        if (data === null || data === undefined || data === '' || isNaN(data)) {
                            if (type === 'display') {
                                return '';
                            }
                            return 0;
                        }
                        
                        if (type === 'display') {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                        return parseFloat(data);
                    }
                }
            ],
            "initComplete": function() {
                this.api().columns().every(function() {
                    var column = this;
                    $(column.footer()).addClass('text-right');
                });
            }
        });
    }

    load_dt('', '', '', '', '');

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let machine = $('#machine').val();
        let employee = $('#employee_f').val();
        let product = $('#product').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt(machine, employee, product, from_date, to_date);
        closeOffcanvasSmoothly();
    });

    // Also add a standalone PDF button outside DataTable if needed
    $('#btn-pdf').on('click', function() {
        generatePDF();
    });


    // Custom PDF generation function
function generatePDF() {
    // Get current filter values for PDF header
    const fromDate = $('#from_date').val() || 'Not specified';
    const toDate = $('#to_date').val() || 'Not specified';
    const machine = $('#machine').val() || 'All';
    const employee = $('#employee').val() || 'All';
    const product = $('#product').val() || 'All';
    const currentDate = new Date().toLocaleDateString();
    
    // Get DataTable instance
    const table = $('#dataTable').DataTable();
    const tableData = table.rows({ filter: 'applied' }).data();
    
    // Initialize PDF in portrait mode
    const doc = new jsPDF('p', 'mm', 'a4');
    
    // Company name
    const companyName = 'OPMA EMBROIDERY ( PVT ) LTD';
    
    // Add company name at the top
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text(companyName, doc.internal.pageSize.getWidth() / 2, 10, { align: 'center' });
    
    // Add report title
    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text('Employee Production Report', doc.internal.pageSize.getWidth() / 2, 20, { align: 'center' });
    
    // Add filter information
    doc.setFontSize(7);
    doc.setFont('helvetica', 'normal');
    
    let yPos = 35;
    doc.text(`Date Range: ${fromDate} to ${toDate}`, 15, yPos);
    doc.text(`Machine: ${machine}`, 15, yPos + 5);
    doc.text(`Employee: ${employee}`, 15, yPos + 10);
    doc.text(`Product: ${product}`, 15, yPos + 15);
    doc.text(`Generated on: ${currentDate}`, doc.internal.pageSize.getWidth() - 15, yPos, { align: 'right' });
    
    // Add a line separator
    yPos += 22;
    doc.setLineWidth(0.3);
    doc.line(15, yPos, doc.internal.pageSize.getWidth() - 15, yPos);
    yPos += 5;
    
    // Prepare table data and calculate totals
    const headers = [
        ['ID', 'EMPLOYEE', 'DATE', 'MACHINE', 'PRODUCT', 'PERCENTAGE', 'TARGET', 'PRODUCE', 'DIFFERENCE', 'DAMAGE QTY', 'AMOUNT']
    ];
    
    const body = [];
    let totalTarget = 0;
    let totalProduce = 0;
    let totalDifference = 0;
    let totalDamage = 0;
    let totalAmount = 0;
    let rowCount = 0;
    
    // Get all data from filtered rows
    tableData.each(function(value, index) {
        const target = parseFloat(value.target) || 0;
        const produce = parseFloat(value.Produce_qty) || 0;
        const difference = parseFloat(value.difference) || 0;
        const damage = parseFloat(value.damage_qty) || 0;
        const amount = parseFloat(value.amount) || 0;
        const percentage = parseFloat(value.precentage) || 0;
        
        totalTarget += target;
        totalProduce += produce;
        totalDifference += difference;
        totalDamage += damage;
        totalAmount += amount;
        rowCount++;
        
        const row = [
            value.id || '',
            value.emp_name || '',
            value.date || '',
            value.machine || '',
            value.product || '',
            percentage !== 0 ? percentage.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + '%' : '0%',
            target !== 0 ? target.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '',
            produce !== 0 ? produce.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '',
            difference !== 0 ? difference.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '',
            damage !== 0 ? damage.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '',
            amount !== 0 ? amount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : ''
        ];
        body.push(row);
    });
    
    // Add footer row with totals
    if (body.length > 0) {
        // Format totals
        const formattedTotalTarget = totalTarget.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const formattedTotalProduce = totalProduce.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const formattedTotalDifference = totalDifference.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const formattedTotalDamage = totalDamage.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const formattedTotalAmount = totalAmount.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        // Add totals row
        body.push([
            '', '', '', '', '', 'TOTALS:',
            formattedTotalTarget,
            formattedTotalProduce,
            formattedTotalDifference,
            formattedTotalDamage,
            formattedTotalAmount
        ]);
    }
    
    // Calculate table width
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 10;
    const tableWidth = pageWidth - (2 * margin);
    
    // Generate table using autoTable
    doc.autoTable({
        startY: yPos,
        head: headers,
        body: body,
        theme: 'grid',
        styles: {
            fontSize: 5,
            cellPadding: 1.5,
            overflow: 'linebreak',
            textAlign: 'left'
        },
        headStyles: {
            fillColor: [41, 128, 185],
            textColor: 255,
            fontStyle: 'bold',
            halign: 'center',
            fontSize: 6
        },
        columnStyles: {
            0: { cellWidth: 'auto', halign: 'center' },  // ID
            1: { cellWidth: 'auto', halign: 'left' },    // EMPLOYEE
            2: { cellWidth: 'auto', halign: 'center' },  // DATE
            3: { cellWidth: 'auto', halign: 'left' },    // MACHINE
            4: { cellWidth: 'auto', halign: 'left' },    // PRODUCT
            5: { cellWidth: 'auto', halign: 'right' },   // PERCENTAGE
            6: { cellWidth: 'auto', halign: 'right' },   // TARGET
            7: { cellWidth: 'auto', halign: 'right' },   // PRODUCE
            8: { cellWidth: 'auto', halign: 'right' },   // DIFFERENCE
            9: { cellWidth: 'auto', halign: 'right' },   // DAMAGE QTY
            10: { cellWidth: 'auto', halign: 'right' }   // AMOUNT
        },
        bodyStyles: {
            textAlign: 'left',
            fontSize: 5
        },
        alternateRowStyles: {
            fillColor: [245, 245, 245]
        },
        margin: { left: margin, right: margin },
        pageBreak: 'auto',
        tableWidth: tableWidth,
        showHead: 'everyPage',
        didParseCell: function(data) {
            // Style the totals row
            if (data.row.index === body.length - 1) {
                data.cell.styles.fontStyle = 'bold';
                data.cell.styles.fillColor = [220, 220, 220]; 
                data.cell.styles.textColor = [0, 0, 0]; 
                data.cell.styles.fontSize = 6;
                // Right align the "TOTALS:" text
                if (data.column.index === 5) {
                    data.cell.styles.halign = 'right';
                }
            }
        },
        willDrawPage: function(data) {
            // Add company name and page number on each page
            doc.setFontSize(6);
            doc.setFont('helvetica', 'normal');
            doc.text(companyName, margin, 10);
            doc.text(`Page ${data.pageNumber}`, doc.internal.pageSize.getWidth() - margin, 10, { align: 'right' });
            
            // Add report title on subsequent pages
            if (data.pageNumber > 1) {
                doc.setFontSize(9);
                doc.setFont('helvetica', 'bold');
                doc.text('Employee Production Report (Continued)', doc.internal.pageSize.getWidth() / 2, 18, { align: 'center' });
            }
        }
    });
    
    // Add summary on last page
    const totalPages = doc.internal.getNumberOfPages();
    if (totalPages > 0) {
        doc.setPage(totalPages);
        const finalY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 10 : 150;
        
        // Only add summary if there's enough space
        if (finalY < doc.internal.pageSize.getHeight() - 40) {
            doc.setFontSize(7);
            doc.setFont('helvetica', 'bold');
            doc.text('Report Summary:', margin, finalY);
            
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(6);
            doc.text(`Total Records: ${rowCount}`, margin, finalY + 6);
            doc.text(`Total Target: ${totalTarget.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin, finalY + 12);
            doc.text(`Total Produce: ${totalProduce.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin, finalY + 18);
            doc.text(`Total Difference: ${totalDifference.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin, finalY + 24);
            doc.text(`Total Damage: ${totalDamage.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin, finalY + 30);
            doc.text(`Total Amount: ${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin, finalY + 36);
        }
        
        // Add footer
        doc.setFontSize(5);
        const generatedBy = $('#emp_name').val() || 'System';
        doc.text(`Generated by: ${generatedBy}`, margin, doc.internal.pageSize.getHeight() - 10);
        doc.text(`Date: ${currentDate}`, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
        doc.text(`${companyName}`, doc.internal.pageSize.getWidth() - margin, doc.internal.pageSize.getHeight() - 10, { align: 'right' });
    }
    
    // Save the PDF
    const fileName = `Employee_Production_Report_${fromDate.replace(/-/g, '_')}_${toDate.replace(/-/g, '_')}.pdf`;
    doc.save(fileName);
}



});
</script>

@endsection