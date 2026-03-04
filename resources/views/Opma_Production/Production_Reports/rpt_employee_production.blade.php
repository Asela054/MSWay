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
                                        <th>ID</th>
                                        <th>EMPLOYEE</th>
                                        <th>DATE</th>
                                        <th>MACHINE</th>
                                        <th>PRODUCT</th>
                                        <th>QTY</th>
                                        <th>Unit Price</th>
                                        <th>AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>   
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align: right">Totals:</th>
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
                                        <option value="{{ $product->id }}">{{ $product->productname }}</option>
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
                            .find('td:nth-child(6), td:nth-child(7), td:nth-child(8), th:nth-child(6), th:nth-child(7), th:nth-child(8)')
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
                var produceQtyTotal = api
                    .column( 5, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        var aNum = parseFloat(a) || 0;
                        var bNum = parseFloat(b) || 0;
                        return aNum + bNum;
                    }, 0 );
                
                // Calculate total for amount (column 7)
                var amountTotal = api
                    .column( 7, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        var aNum = parseFloat(a) || 0;
                        var bNum = parseFloat(b) || 0;
                        return aNum + bNum;
                    }, 0 );
                
                // Calculate total for unit_price (column 6)
                var unitPriceTotal = api
                    .column( 6, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        var aNum = parseFloat(a) || 0;
                        var bNum = parseFloat(b) || 0;
                        return aNum + bNum;
                    }, 0 );
                
                // Format the totals
                var formattedProduceQty = '';
                var formattedAmount = '';
                var formattedUnitPriceTotal = '';
                
                if (!isNaN(produceQtyTotal) && produceQtyTotal !== 0) {
                    formattedProduceQty = produceQtyTotal.toLocaleString('en-US', {
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
                
                if (!isNaN(unitPriceTotal) && unitPriceTotal !== 0) {
                    formattedUnitPriceTotal = unitPriceTotal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
                
                // Update footer
                $( api.column( 5 ).footer() ).html(
                    formattedProduceQty ? '<strong>' + formattedProduceQty + '</strong>' : ''
                );
                
                $( api.column( 6 ).footer() ).html(
                    formattedUnitPriceTotal ? '<strong>' + formattedUnitPriceTotal + '</strong>' : ''
                );
                
                $( api.column( 7 ).footer() ).html(
                    formattedAmount ? '<strong>' + formattedAmount + '</strong>' : ''
                );
            },
            "order": [
                [0, "desc"]
            ],
            ajax: {
                url: scripturl + '/employee_production_list.php',
                type: 'POST',
                data : 
                    {machine :machine, 
                    employee :employee, 
                    product: product,
                    from_date: from_date,
                    to_date: to_date},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'emp_name', name: 'emp_name' },
                { data: 'date', name: 'date' },
                { data: 'machine', name: 'machine' },
                { data: 'product', name: 'product' },
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
                    data: 'unit_price', 
                    name: 'unit_price',
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
                    "targets": [5, 6, 7],
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
    const currentDate = new Date().toLocaleDateString();
    
    // Get DataTable instance
    const table = $('#dataTable').DataTable();
    const tableData = table.rows({ filter: 'applied' }).data();
    
    // Initialize PDF in portrait mode
    const doc = new jsPDF('p', 'mm', 'a4'); // portrait, millimeters, A4 size
    

    // Assuming you have these variables available in your Blade template
    const companyName = '{{ Session::get("company_name") }}' || 'Eco Agro Pvt Ltd';
    
    // Add company name and address at the top
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text(companyName, doc.internal.pageSize.getWidth() / 2, 10, { align: 'center' });
    
    // Add report title
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('Employee Production Report', doc.internal.pageSize.getWidth() / 2, 20, { align: 'center' });
    
    // Add filter information
    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    
    let yPos = 35;
    doc.text(`Date Range: ${fromDate} to ${toDate}`, 15, yPos);
    doc.text(`Generated on: ${currentDate}`, doc.internal.pageSize.getWidth() - 15, yPos, { align: 'right' });
    
    // Add a line separator
    yPos += 7;
    doc.setLineWidth(0.3);
    doc.line(15, yPos, doc.internal.pageSize.getWidth() - 15, yPos);
    yPos += 5;
    
    // Prepare table data and calculate totals
    const headers = [
        ['ID', 'EMPLOYEE', 'DATE', 'MACHINE', 'PRODUCT', 'QTY', 'UNIT PRICE', 'AMOUNT']
    ];
    
    const body = [];
    let totalQty = 0;
    let totalUnitPrice = 0;
    let totalAmount = 0;
    let rowCount = 0;
    
    // Get all data from the current page (or all filtered data)
    tableData.each(function(value, index) {
        const qty = parseFloat(value.Produce_qty) || 0;
        const unitPrice = parseFloat(value.unit_price) || 0;
        const amount = parseFloat(value.amount) || 0;
        
        totalQty += qty;
        totalUnitPrice += unitPrice;
        totalAmount += amount;
        rowCount++;
        
        const row = [
            value.id || '',
            value.emp_name || '',
            value.date || '',
            value.machine || '',
            value.product || '',
            qty !== 0 ? qty.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '',
            unitPrice !== 0 ? unitPrice.toLocaleString('en-US', {
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
        // Calculate average unit price
        const avgUnitPrice = rowCount > 0 ? totalUnitPrice / rowCount : 0;
        
        // Format totals with comma separators
        const formattedTotalQty = totalQty.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        const formattedTotalUnitPrice = totalUnitPrice.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        const formattedTotalAmount = totalAmount.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        const formattedAvgUnitPrice = avgUnitPrice.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        // Add totals row
        body.push([
            '', '', '', '', 'TOTALS:',
            formattedTotalQty,
            formattedTotalUnitPrice,
            formattedTotalAmount
        ]);
    }
    
    // Calculate table width (100% of page width minus margins)
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 15;
    const tableWidth = pageWidth - (2 * margin);
    
    // Generate table using autoTable with 100% width
    doc.autoTable({
        startY: yPos,
        head: headers,
        body: body,
        theme: 'grid',
        styles: {
            fontSize: 8,
            cellPadding: 2,
            overflow: 'linebreak',
            textAlign: 'left'
        },
        headStyles: {
            fillColor: [41, 128, 185],
            textColor: 255,
            fontStyle: 'bold',
            halign: 'center',
            fontSize: 9
        },
        columnStyles: {
            0: { cellWidth: 'auto', halign: 'center' }, // ID
            1: { cellWidth: 'auto', halign: 'left' },    // EMPLOYEE
            2: { cellWidth: 'auto', halign: 'center' },  // DATE
            3: { cellWidth: 'auto', halign: 'left' },    // MACHINE
            4: { cellWidth: 'auto', halign: 'left' },    // PRODUCT
            5: { cellWidth: 'auto', halign: 'right' },   // QTY
            6: { cellWidth: 'auto', halign: 'right' },   // Unit Price
            7: { cellWidth: 'auto', halign: 'right' }    // AMOUNT
        },
        bodyStyles: {
            textAlign: 'left',
            fontSize: 8
        },
        alternateRowStyles: {
            fillColor: [245, 245, 245]
        },
        margin: { left: margin, right: margin },
        pageBreak: 'auto',
        tableWidth: tableWidth,
        showHead: 'everyPage',
        didParseCell: function(data) {
            if (data.row.index === body.length - 1) {
                data.cell.styles.fontStyle = 'bold';
                data.cell.styles.fillColor = [220, 220, 220]; 
                data.cell.styles.textColor = [0, 0, 0]; 
                data.cell.styles.fontSize = 9;
            }
        },
        willDrawPage: function(data) {
            // Add company name and page number on each page
            doc.setFontSize(9);
            doc.setFont('helvetica', 'normal');
            doc.text(companyName, 15, 10);
            doc.text(`Page ${data.pageNumber}`, doc.internal.pageSize.getWidth() - 15, 10, { align: 'right' });
            
            // Add report title on subsequent pages
            if (data.pageNumber > 1) {
                doc.setFontSize(12);
                doc.setFont('helvetica', 'bold');
                doc.text('Employee Production Report (Continued)', doc.internal.pageSize.getWidth() / 2, 20, { align: 'center' });
            }
        }
    });
    
    // Add summary on last page
    const totalPages = doc.internal.getNumberOfPages();
    if (totalPages > 0) {
        doc.setPage(totalPages);
        const finalY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 10 : 150;
        
        // Add summary section
        doc.setFontSize(9);
        doc.setFont('helvetica', 'bold');
        doc.text('Report Summary:', 15, finalY);
        
        doc.setFont('helvetica', 'normal');
        doc.text(`Total Records: ${rowCount}`, 15, finalY + 5);
        doc.text(`Total Quantity: ${totalQty.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, 15, finalY + 10);
        doc.text(`Total Amount: ${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, 15, finalY + 15);
        
        // Add footer with company info
        doc.setFontSize(8);
        doc.text(`Generated by: {{ Session::get('emp_name_with_initial') }}`, 15, doc.internal.pageSize.getHeight() - 15);
        doc.text(`Company: ${companyName}`, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 15, { align: 'center' });
        doc.text(`Page ${totalPages} of ${totalPages}`, doc.internal.pageSize.getWidth() - 15, doc.internal.pageSize.getHeight() - 15, { align: 'right' });
    }
    
    // Save the PDF
    const fileName = `Employee_Production_Report_${fromDate.replace(/-/g, '_')}_${toDate.replace(/-/g, '_')}.pdf`;
    doc.save(fileName);
}


//  // Custom PDF generation function
//         function generatePDF() {
//             // Get current filter values for PDF header
//             const fromDate = $('#from_date').val() || 'Not specified';
//             const toDate = $('#to_date').val() || 'Not specified';
//             const currentDate = new Date().toLocaleDateString();
            
//             // Get DataTable instance
//             const table = $('#dataTable').DataTable();
//             const tableData = table.rows({ filter: 'applied' }).data();
            
//             // Initialize PDF in portrait mode
//             const doc = new jsPDF('p', 'mm', 'a4'); // portrait, millimeters, A4 size
            
//             // Add report title
//             doc.setFontSize(16);
//             doc.setFont('helvetica', 'bold');
//             doc.text('Employee Production Report', doc.internal.pageSize.getWidth() / 2, 15, { align: 'center' });
            
//             // Add filter information
//             doc.setFontSize(9);
//             doc.setFont('helvetica', 'normal');
            
//             let yPos = 25;
//             doc.text(`Date Range: ${fromDate} to ${toDate}`, 15, yPos);
//             doc.text(`Generated on: ${currentDate}`, doc.internal.pageSize.getWidth() - 15, yPos, { align: 'right' });
            
//             // Add a line separator
//             yPos += 7;
//             doc.setLineWidth(0.3);
//             doc.line(15, yPos, doc.internal.pageSize.getWidth() - 15, yPos);
//             yPos += 5;
            
//             // Prepare table data and calculate totals
//             const headers = [
//                 ['ID', 'EMPLOYEE', 'DATE', 'MACHINE', 'PRODUCT', 'QTY', 'UNIT PRICE', 'AMOUNT']
//             ];
            
//             const body = [];
//             let totalQty = 0;
//             let totalUnitPrice = 0;
//             let totalAmount = 0;
//             let rowCount = 0;
            
//             // Get all data from the current page (or all filtered data)
//             tableData.each(function(value, index) {
//                 const qty = parseFloat(value.Produce_qty) || 0;
//                 const unitPrice = parseFloat(value.unit_price) || 0;
//                 const amount = parseFloat(value.amount) || 0;
                
//                 totalQty += qty;
//                 totalUnitPrice += unitPrice;
//                 totalAmount += amount;
//                 rowCount++;
                
//                 const row = [
//                     value.id || '',
//                     value.emp_name || '',
//                     value.date || '',
//                     value.machine || '',
//                     value.product || '',
//                     qty !== 0 ? qty.toLocaleString('en-US', {
//                         minimumFractionDigits: 2,
//                         maximumFractionDigits: 2
//                     }) : '',
//                     unitPrice !== 0 ? unitPrice.toLocaleString('en-US', {
//                         minimumFractionDigits: 2,
//                         maximumFractionDigits: 2
//                     }) : '',
//                     amount !== 0 ? amount.toLocaleString('en-US', {
//                         minimumFractionDigits: 2,
//                         maximumFractionDigits: 2
//                     }) : ''
//                 ];
//                 body.push(row);
//             });
            
//             // Add footer row with totals
//             if (body.length > 0) {
//                 // Calculate average unit price
//                 const avgUnitPrice = rowCount > 0 ? totalUnitPrice / rowCount : 0;
                
//                 // Format totals with comma separators
//                 const formattedTotalQty = totalQty.toLocaleString('en-US', {
//                     minimumFractionDigits: 2,
//                     maximumFractionDigits: 2
//                 });
                
//                 const formattedTotalUnitPrice = totalUnitPrice.toLocaleString('en-US', {
//                     minimumFractionDigits: 2,
//                     maximumFractionDigits: 2
//                 });
                
//                 const formattedTotalAmount = totalAmount.toLocaleString('en-US', {
//                     minimumFractionDigits: 2,
//                     maximumFractionDigits: 2
//                 });
                
//                 const formattedAvgUnitPrice = avgUnitPrice.toLocaleString('en-US', {
//                     minimumFractionDigits: 2,
//                     maximumFractionDigits: 2
//                 });
                
//                 // Add totals row (you can choose to show total or average for unit price)
//                 body.push([
//                     '', '', '', '', 'TOTALS:',
//                     formattedTotalQty,
//                     formattedTotalUnitPrice, // Show total unit price
//                     formattedTotalAmount
//                 ]);
                
//             }
            
//             // Calculate table width (100% of page width minus margins)
//             const pageWidth = doc.internal.pageSize.getWidth();
//             const margin = 15;
//             const tableWidth = pageWidth - (2 * margin);
            
//             // Calculate column widths for 100% width
//             const columnWidths = [
//                 'auto',  // ID (5%)
//                 'auto',  // EMPLOYEE (15%)
//                 'auto',  // DATE (10%)
//                 'auto',  // MACHINE (15%)
//                 'auto',  // PRODUCT (15%)
//                 'auto',  // QTY (10%)
//                 'auto',  // Unit Price (15%)
//                 'auto'   // AMOUNT (15%)
//             ];
            
//             // Generate table using autoTable with 100% width
//             doc.autoTable({
//                 startY: yPos,
//                 head: headers,
//                 body: body,
//                 theme: 'grid',
//                 styles: {
//                     fontSize: 8,
//                     cellPadding: 2,
//                     overflow: 'linebreak',
//                     textAlign: 'left'
//                 },
//                 headStyles: {
//                     fillColor: [41, 128, 185],
//                     textColor: 255,
//                     fontStyle: 'bold',
//                     halign: 'center',
//                     fontSize: 9
//                 },
//                 columnStyles: {
//                     0: { cellWidth: 'auto', halign: 'center' }, // ID
//                     1: { cellWidth: 'auto', halign: 'left' },    // EMPLOYEE
//                     2: { cellWidth: 'auto', halign: 'center' },  // DATE
//                     3: { cellWidth: 'auto', halign: 'left' },    // MACHINE
//                     4: { cellWidth: 'auto', halign: 'left' },    // PRODUCT
//                     5: { cellWidth: 'auto', halign: 'right' },   // QTY
//                     6: { cellWidth: 'auto', halign: 'right' },   // Unit Price
//                     7: { cellWidth: 'auto', halign: 'right' }    // AMOUNT
//                 },
//                 bodyStyles: {
//                     textAlign: 'left',
//                     fontSize: 8
//                 },
//                 alternateRowStyles: {
//                     fillColor: [245, 245, 245]
//                 },
//                 margin: { left: margin, right: margin },
//                 pageBreak: 'auto',
//                 tableWidth: tableWidth, // Set table to 100% width
//                 showHead: 'everyPage',
//                 didParseCell: function(data) {
//                     if (data.row.index === body.length - 1) {
//                         data.cell.styles.fontStyle = 'bold';
//                         data.cell.styles.fillColor = [220, 220, 220]; 
//                         data.cell.styles.textColor = [0, 0, 0]; 
//                         data.cell.styles.fontSize = 9;
//                     }
//                 },
//                 willDrawPage: function(data) {
//                     doc.setFontSize(9);
//                     doc.setFont('helvetica', 'normal');
//                     doc.text(`Page ${data.pageNumber}`, doc.internal.pageSize.getWidth() - 15, 10);
                    
//                     // Add report title on subsequent pages
//                     if (data.pageNumber > 1) {
//                         doc.setFontSize(12);
//                         doc.setFont('helvetica', 'bold');
//                         doc.text('Employee Production Report (Continued)', doc.internal.pageSize.getWidth() / 2, 10, { align: 'center' });
//                     }
//                 }
//             });
            
//             // Add final page numbers
//             const totalPages = doc.internal.getNumberOfPages();
//             doc.setFontSize(8);
            
//             // Save the PDF
//             const fileName = `Employee_Production_Report_${fromDate.replace(/-/g, '_')}_${toDate.replace(/-/g, '_')}.pdf`;
//             doc.save(fileName);
//         }


});
</script>

@endsection