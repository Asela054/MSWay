@extends('layouts.app')

@section('content')

				<main>
                    <div class="page-header page-header-light bg-white shadow">
                        <div class="container-fluid">
                            @include('layouts.payroll_nav_bar')
                           
                        </div>
                    </div>
                    <div class="container-fluid mt-4">
                        <div class="row">
                       
                           
                            <div class="col-lg-12">
                                <div id="default">
                                    <form id="frmExport" method="post" action="{{ url('DownloadEpfEtf') }}">
                                    {{ csrf_field() }}
                                        <div class="card card-header-actions mb-4">
                                            <div class="card-header">
                                                Salary provided to Office employees
                                                <span id="lbl_duration" style="display:none; margin-right:auto; padding-left:10px;">
                                                    <span id="lbl_date_fr">&nbsp;</span> To <span id="lbl_date_to">&nbsp;</span>
                                                    (<span id="lbl_payroll_name">&nbsp;</span>)
                                                </span>
                                                <div>
                                                    <button type="button" name="find_employee" id="find_employee" class="btn btn-success btn-sm">Search</button>
                                                    {{-- <button type="submit" name="print_record" id="print_record" disabled="disabled" class="btn btn-secondary btn-sm btn-light">Download</button> --}}
                                                    <button type="button" name="pdfprint_record" id="pdfprint_record" disabled="disabled" class="btn btn-secondary btn-sm btn-light" onclick="generatePDF();">Download PDF</button>
                                                </div>
                                            </div>
                                            
                                            <div class="card-body">
                                                
                                                <div id="divPrint" class="datatable table-responsive" style="margin-top:0px;">
                                                    <table class="table table-bordered table-hover" id="emp_bank_table" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr>
                                                                <th style="">&nbsp;</th>
                                                                <th style="text-align:right; width:200px;">Debit</th>
                                                                <th style="text-align:right; width:200px;">Credit</th>
                                                            </tr>
                                                        </thead>
                                                     
                                                        <tbody class="">
                                                        	<tr data-figid="">
                                                            	<td>Salary & Wages - Administrative</td>
                                                                <td class="text-right" data-cap="amt" id="salary_and_wages"></td>
                                                                <td data-cap=""></td>
                                                            </tr>
                                                            <tr data-figid="add_transport">
                                                            	<td>Employee Travelling Expenses</td>
                                                                <td class="text-right" data-cap="amt" id="travelling"></td>
                                                                <td data-cap=""></td>
                                                            </tr>
                                                            <tr data-figid="INCNTV_EMP">
                                                            	<td>Employee Incentive</td>
                                                                <td class="text-right" data-cap="amt" id="incentive_emp"></td>
                                                                <td data-cap=""></td>
                                                            </tr>
                                                            <tr data-figid="">
                                                            	<td>Employee Incentive</td>
                                                                <td class="text-right" data-cap="amt" id="incentive_dir"></td>
                                                                <td data-cap=""></td>
                                                            </tr>
                                                            <tr data-figid="">
                                                            	<td>Employee Travelling Expenses Reserve</td>
                                                                <td data-cap=""></td>
                                                                <td class="text-right" data-cap="amt" id="travelling"></td>
                                                            </tr>
                                                            <tr data-figid="">
                                                            	<td>Employee Incentive Reserve</td>
                                                                <td data-cap=""></td>
                                                                <td class="text-right" data-cap="amt" id="incentive_reserve"></td>
                                                            </tr>
                                                            <tr data-figid="PAYE">
                                                            	<td>Payee Tax</td>
                                                                <td data-cap=""></td>
                                                                <td class="text-right" data-cap="amt" id="payee_tax"></td>
                                                            </tr>
                                                            <tr data-figid="sal_adv">
                                                            	<td>Salary Advance</td>
                                                                <td data-cap=""></td>
                                                                <td class="text-right" data-cap="amt" id="salary_advance"></td>
                                                            </tr>
                                                            <tr data-figid="epf_etf_res">
                                                            	<td>Employee Provident Fund Reserve</td>
                                                                <td data-cap=""></td>
                                                                <td class="text-right" data-cap="amt" id="emp_fund_reserve"></td>
                                                            </tr>
                                                            <tr data-figid="">
                                                            	<td>Salary Payment Suspense</td>
                                                                <td data-cap=""></td>
                                                                <td class="text-right" data-cap="amt" id="payment_suspense"></td>
                                                            </tr>
                                                            <tr data-figid="">
                                                            	<td>Total Payment:</td>
                                                                <td class="text-right" data-cap="amt" style="border-top:1px double; border-bottom:1px double;" id="debit_total"></td>
                                                                <td class="text-right" data-cap="amt" style="border-top:1px double; border-bottom:1px double;" id="credit_total"></td>
                                                            </tr>
                                                            
                                                        </tbody>
                                                        
                                                    </table>
                                                </div>
                                                
                                                <input type="hidden" name="payroll_profile_id" id="payroll_profile_id" value="" /><!-- edit loans -->
                                                <input type="hidden" name="payment_period_id" id="payment_period_id" value="" />
                                                <input type="hidden" name="payslip_process_type_id" id="payslip_process_type_id" value="" />
                                                
                                                <input type="hidden" name="rpt_period_id" id="rpt_period_id" value="" />
                                                <input type="hidden" name="rpt_info" id="rpt_info" value="-" />
                                                <input type="hidden" name="rpt_payroll_id" id="rpt_payroll_id" value="" />
                                                <input type="hidden" name="rpt_location_id" id="rpt_location_id" value="" />
                                                <input type="hidden" name="debit_total_text" id="debit_total_text" value="" />
                                                <input type="hidden" name="thousentseparate_debit_total" id="thousentseparate_debit_total" value="" />
                                                
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                                
                                
                              
                            </div>
                           
                        </div>
                        
                        
                    </div>
                    
                    <div id="formModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <form id="frmSearch" method="post">
                            {{ csrf_field() }}	
                                <div class="modal-content">
                                   <div class="modal-header">
                                       <h5 class="modal-title" id="formModalLabel"></h5>
                                       
                                       <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span class="btn-sm btn-danger" aria-hidden="true">X</span></button>
                                   </div>
                                   <div class="modal-body">
                                        <span id="search_result"></span>
                                        <div class="row">
                                          
                                            <div class="form-group col-md-6">
                                               <label class="control-label col">Location</label>
                                               <div class="col">
                                                   <select name="location_filter_id" id="location_filter_id" class="custom-select shipClass" style="pointer-events: none;" >
                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                        @foreach($branch as $branches)
                                                        
                                                        <option value="{{$branches->id}}">{{$branches->name}}</option>
                                                        @endforeach
                                                        
                                                   </select>
                                               </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label col">Department</label>
                                                <div class="col">
                                                    <select name="department_filter_id" id="department_filter_id" class="custom-select" style="" data-nestname="deptnest" >
                                                         <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                         <option value="All" >All</option>
                                                         @foreach($department as $section)
                                                         <option value="{{$section->id}}" >{{$section->name}}</option>
                                                         @endforeach
                                                    </select>
                                                </div>
                                             </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="control-label col" >Payroll type</label>
                                                <div class="col">
                                                  <select name="payroll_process_type_id" id="payroll_process_type_id" class="form-control" >
                                                     <option value="" disabled="disabled" selected="selected">Please select</option>
                                                     @foreach($payroll_process_type as $payroll)
                                                     
                                                     <option value="{{$payroll->id}}" data-totdays="{{$payroll->total_work_days}}">{{$payroll->process_name}}</option>
                                                     @endforeach
                                                     
                                                  </select>
                                                </div>
                                             </div>
                                        	<div class="form-group col">
                                            	<label class="control-label col">Working Period</label>
                                                <div class="col">
                                                   <select name="period_filter_id" id="period_filter_id" class="custom-select" style="" >
                                                        <option value="" disabled="disabled" selected="selected">Please Select</option>
                                                        @foreach($payment_period as $schedule)
                                                        
                                                        <option value="{{$schedule->id}}" disabled="disabled" data-payroll="{{$schedule->payroll_process_type_id}}" style="display:none;">{{$schedule->payment_period_fr}} to {{$schedule->payment_period_to}}</option>
                                                        @endforeach
                                                        
                                                   </select>
                                               </div>
                                            </div>
                                            <!--div class="form-group col-md-6">
                                            	<label class="control-label col">To</label>
                                                <div class="col">
                                                	<input type="date" class="form-control" name="work_date_to" id="work_date_to" value="" />
                                                </div>
                                            </div-->
                                        </div>
                                   </div>
                                   <div class="modal-footer" align="right">
                                       <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="View Report" />
                                       <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                   </div>
                                   
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    
                    
                    
                </main>
              
@endsection


@section('script')

<script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
<script src="https://unpkg.com/jspdf-autotable@3.8.3/dist/jspdf.plugin.autotable.js"></script>

<script>
$(document).ready(function(){
    $('#payroll_menu_link').addClass('active');
    $('#payroll_menu_link_icon').addClass('active');
    $('#payroll_statments').addClass('navbtnactive');

    var companyId = '{{ session("company_id") }}';
    var companyName = '{{ session("company_name") }}';

    if (companyId && companyName) {
        $('#location_filter_id').val(companyId).trigger('change');
    }
	/*var empTable=$("#emp_bank_table").DataTable({
			"columns":[{data:'fig_name'}, {data:'fig_value'}, {data:'emp_cnt'}],
			"ordering":false, "order":[],
			"columnDefs": [{
					"targets":1, 
					"className":"text-right",
					render: function(data, type, row){
						return parseFloat(data).toFixed(2);
					}
				}],
			"createdRow": function( row, data, dataIndex ){
				//$('td', row).eq(5).attr('data-colvalue', data.loan_installments); 
				//$('td', row).eq(0).attr('data-refemp', data.payroll_profile_id); 
				//$( row ).attr( 'id', 'row-'+data.id );//$( row ).data( 'refid', data[3] );
			}
		});*/
	
	//var loanTable=$("#loantable").DataTable();
	
	var _token = $('#frmSearch input[name="_token"]').val();;
	
	function findEmployee(){
		$('#formModalLabel').text('Find Employee');
		//$('#action_button').val('Add');
		//$('#action').val('Add');
		$('#search_result').html('');
		
		$('#formModal').modal('show');
	}
	
	$('#find_employee').click(function(){
		findEmployee();
	});
	
	$(".modal").on("shown.bs.modal", function(){
		var objinput=$(this).find('input[type="text"]:first-child');
		objinput.focus();
		objinput.select();
	});
	
	$("#payroll_process_type_id").on("change", function(){
		$('#period_filter_id').val('');
		$('#period_filter_id option').prop("disabled", true);
		$('#period_filter_id option:not(:first-child)').hide();
		$('#period_filter_id option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').prop("disabled", false);
		$('#period_filter_id option[data-payroll="'+$("#payroll_process_type_id").find(":selected").val()+'"]').show();
	});
	
	$("#frmSearch").on('submit', function(event){
	  event.preventDefault();
	  
	  $.ajax({
	   url:"previewPaySummary",
	   method:'POST',
	   data:$(this).serialize(),
	   dataType:"JSON",
	   beforeSend:function(){
		//$('#find_employee').prop('disabled', true);
	   },
	   success:function(data){
		//alert(JSON.stringify(data));
		var html = '';
		//empTable.clear();
		
		if(data.errors){
			html = '<div class="alert alert-danger">';
			for(var count = 0; count < data.errors.length; count++){
			  html += '<p>' + data.errors[count] + '</p>';
			}
			html += '</div>';
			$('#search_result').html(html);
		}else{
			// $("#emp_bank_table tbody tr").each(function(index, obj){
			// 	//console.log($(this).data('figid'));
			// 	if($(this).data('figid')!=''){
			// 		var disp_amt = parseFloat(Math.abs(data.payment_detail[$(this).data('figid')].amt)).toFixed(2);
			// 		$(this).children('td[data-cap="amt"]').html(disp_amt).addClass('text-right');
					
			// 	}
			// });

            $("#salary_and_wages").html(parseFloat(Math.abs(data.salary_and_wages.toFixed(2))));
			$("#travelling").html(parseFloat(Math.abs(data.travelling.toFixed(2))));
            $("#incentive_emp").html(parseFloat(Math.abs(data.incentive_emp.toFixed(2))));
			$("#incentive_dir").html(parseFloat(Math.abs(data.incentive_dir.toFixed(2))));
            $("#travelling").html(parseFloat(Math.abs(data.travelling.toFixed(2))));
			$("#incentive_reserve").html(parseFloat(Math.abs(data.incentive_reserve.toFixed(2))));
            $("#payee_tax").html(parseFloat(Math.abs(data.payee_tax.toFixed(2))));
			$("#salary_advance").html(parseFloat(Math.abs(data.salary_advance.toFixed(2))));
            $("#emp_fund_reserve").html(parseFloat(Math.abs(data.emp_fund_reserve.toFixed(2))));
			$("#payment_suspense").html(parseFloat(Math.abs(data.payment_suspense.toFixed(2))));
            $("#debit_total").html(parseFloat(Math.abs(data.debit_total.toFixed(2))));
			$("#credit_total").html(parseFloat(Math.abs(data.credit_total.toFixed(2))));

			//empTable.rows.add(data.payment_detail);
			//empTable.draw();
			
			
			$("#lbl_date_fr").html(data.work_date_fr);
			$("#lbl_date_to").html(data.work_date_to);
			$("#lbl_duration").show();
			$("#payment_period_id").val(data.payment_period_id);
			$("#payslip_process_type_id").val($("#payroll_process_type_id").find(":selected").val());
			$("#lbl_payroll_name").html($("#payroll_process_type_id").find(":selected").text());
			//$('#find_employee').prop('disabled', false);
			
			$("#rpt_payroll_id").val($("#payroll_process_type_id").find(":selected").val());
			$("#rpt_location_id").val($("#location_filter_id").find(":selected").val());
			$("#rpt_period_id").val($("#period_filter_id").find(":selected").val());
			$("#rpt_info").val(data.work_date_fr+" To "+data.work_date_to+" ("+$("#payroll_process_type_id").find(":selected").text()+")");
            $("#debit_total_text").val(data.debit_total_text);
            $("#thousentseparate_debit_total").val(data.thousentseparate_debit_total);
			
			$("#pdfprint_record").prop('disabled', false);
			$("#pdfprint_record").removeClass('btn-light');
			
			$('#formModal').modal('hide');
		}
	   }
	  })
	});
	
	
	$(".btn_back").on("click", function(){
		$(".show .frm_info").addClass('sect_bg');
		$(".show .frm_link").removeClass('sect_bg');
	});
	
	
	$(".modal").on("shown.bs.modal", function(e){
		if($(this).find(".frm_link")){
			$(".show .frm_info").addClass('sect_bg');
			$(".show .frm_link").removeClass('sect_bg');
		}
	});
	/*
	$(".modal").on("hide.bs.modal", function(e){
		$(this).removeClass('active');
	});
	*/
	
});

function centerText(text, y, doc) {
    var pageWidth = doc.internal.pageSize.getWidth();
    var textWidth = doc.getTextWidth(text);
    doc.text(text, (pageWidth - textWidth) / 2, y);
}

function rightAlignText(text, y, doc, margin = 22) {
    var pageWidth = doc.internal.pageSize.getWidth();
    var textWidth = doc.getTextWidth(text);
    var xCoordinate = pageWidth - textWidth - margin; 

    doc.text(text, xCoordinate, y);
}

function centerTextWithUnderline(text, y, doc) {
    var pageWidth = doc.internal.pageSize.getWidth();
    var textWidth = doc.getTextWidth(text);
    var startX = (pageWidth - textWidth) / 2;
    
    doc.text(text, startX, y);

    doc.line(startX, y + 1, startX + textWidth, y + 1); 
}


async function generatePDF() {
    const { jsPDF } = window.jspdf; 
    const { autoTable } = window.jspdf; 

    const doc = new jsPDF();
	const margins = { top: 35};

    var companyId = '{{ session("company_id") }}';
    var companyName = '{{ session("company_name") }}';
    var companyAddress = '{{ session("company_address") }}';
    
	var workfrom=$("#lbl_date_fr").text();
	var workto=$("#lbl_date_to").text();
    var debit_total_text=$("#debit_total_text").val();
    var thousentseparate_debit_total=$("#thousentseparate_debit_total").val();

    var date = new Date(workto);
    var formattedDate = date.toLocaleString('default', { month: 'long', year: 'numeric' });

	doc.autoTable({ 
		html: '#emp_bank_table',
		theme: 'grid',
		margin: margins,
		headStyles: {
        fillColor: [255, 255, 255], 
        textColor: [0, 0, 0],      
        fontStyle: 'bold'         
		},
		styles: {
			lineWidth: 0,             
			lineColor: [0, 0, 0]     
		},
        didParseCell: function (data) {
            if (data.column.index === 1 || data.column.index === 2) {
                data.cell.styles.halign = 'right';

                var plainText = $(data.cell.raw).text();
                if (!isNaN(plainText)) {  
                    if(plainText!=''){
                        let value = parseFloat(plainText).toFixed(2); 
                        data.cell.text = [value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")]; 
                    }
                }
            }
        },
        didDrawCell: function (data) {
            const table = data.table;
            
            const lastRowIndex = table.body.length - 1;
            
            if (data.row.index === lastRowIndex) {
                if (data.column.index === 1 || data.column.index === 2) {
                    doc.setLineWidth(0.5); 
                    doc.setDrawColor(0, 0, 0); 
                    doc.line(data.cell.x, data.cell.y, data.cell.x + data.cell.width, data.cell.y);      
        
                    doc.setLineWidth(0.5); 
                    doc.line(data.cell.x, data.cell.y + data.cell.height, data.cell.x + data.cell.width, data.cell.y + data.cell.height); 

                    doc.setLineWidth(0.5); 
                    doc.line(data.cell.x, data.cell.y + data.cell.height + 1, data.cell.x + data.cell.width, data.cell.y + data.cell.height + 1); 

                }
            }
        },
		didDrawPage: function (data) {

        doc.setFontSize(12);
        doc.setFont('Helvetica', 'normal');
        centerTextWithUnderline('Journal Voucher', 15, doc);

        doc.setFontSize(14);
        doc.setTextColor(40);
        doc.setFont('Helvetica', 'bold');
        centerText(companyName || "Your Company Name", 22, doc);

        doc.setFontSize(9);
        doc.setFont('Helvetica', 'normal');
        rightAlignText('NO : ....................', 22, doc);

        doc.setFontSize(9);
        doc.setFont('Helvetica', 'normal');
        centerText(companyAddress, 27, doc);

        doc.setFontSize(9);
        doc.setFont('Helvetica', 'normal');
        rightAlignText('Date : '+workto, 27, doc);

        doc.setFontSize(12);
        doc.setFont('Helvetica', 'bold');
        doc.text('With a sum of ', 14, 135);

        doc.setFontSize(11);
        doc.setFont('Helvetica', 'normal');
        const maxWidth = 150; 
        const splitText = doc.splitTextToSize(debit_total_text, maxWidth);
        const x = 43;
        const y = 135;
        doc.text(splitText, x, y);

        doc.setFontSize(12);
        doc.setFont('Helvetica', 'bold');
        doc.text('Being ', 14, 150);

        doc.setFontSize(11);
        doc.setFont('Helvetica', 'normal');
        const maxWidth2 = 150; 
        const splitText2 = doc.splitTextToSize('Salary provided to Office employees - '+formattedDate , maxWidth2);
        const x2 = 43;
        const y2 = 150;
        doc.text(splitText2, x2, y2);

        doc.setFontSize(12);
        doc.setFont('Helvetica', 'bold');
        doc.text('Rs. '+thousentseparate_debit_total, 14, 165);

        doc.setFontSize(11);
        doc.setFont('Helvetica', 'normal');
        const signpageWidth = doc.internal.pageSize.getWidth();
        const signcolWidth = signpageWidth / 4;
        const signtext1 = '..........................';
        const signtext2 = '..........................';
        const signtext3 = '..........................';
        const signtext4 = '..........................';
        const signyPosition = 180;
        doc.text(signtext1, 14, signyPosition); 
        doc.text(signtext2, 14 + signcolWidth, signyPosition); 
        doc.text(signtext3, 14 + 2 * signcolWidth, signyPosition); 
        doc.text(signtext4, 14 + 3 * signcolWidth, signyPosition); 

        doc.setFontSize(11);
        doc.setFont('Helvetica', 'normal');
        const pageWidth = doc.internal.pageSize.getWidth();
        const colWidth = pageWidth / 4;
        const text1 = 'Asst Accountant';
        const text2 = 'J L Folio';
        const text3 = 'Manager';
        const text4 = 'Director';
        const yPosition = 185;
        doc.text(text1, 14, yPosition); 
        doc.text(text2, 14 + colWidth, yPosition); 
        doc.text(text3, 14 + 2 * colWidth, yPosition); 
        doc.text(text4, 14 + 3 * colWidth, yPosition); 

      }
	 })
	doc.save('Journal Voucher'+workfrom+' to '+workto+'.pdf')

  }
</script>

@endsection