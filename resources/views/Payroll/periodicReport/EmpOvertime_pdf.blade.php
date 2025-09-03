<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Laravel PDF</title>
    <!--link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"-->
    <style type="text/css">
	@page { 
		/*
		size: 50.5cm 21cm landspace;
		*/ 
		font-size:12px;
		margin:0.8cm;
	}
	/**/
	hr.pgbrk {
		page-break-after:always;
		border:0;
	}
	
	#header,
	#footer {
	  position: fixed;
	  left: 0;
		right: 0;
		color: #aaa;
		font-size: 0.9em;
	}
	#header {
	  top: 0;
		border-bottom: 0.1pt solid #aaa;
	}
	#footer {
	  bottom: 0;
	  border-top: 0.1pt solid #aaa;
	}
	.page-number:before {
	  content: "Page " counter(page);
	}
	
	table.pdf_data tbody tr td{
		padding-left:2px;/*10px*/
		padding-right:1px;/*10px*/
		text-align:right;
	}
	/**/
	table.pdf_data thead tr th{
		text-align:center !Important;
	}
	
	table.pdf_data tbody tr td:nth-child(1),
	table.pdf_data tbody tr td:nth-child(2),
	table.pdf_data tbody tr td:nth-child(3){
		text-align:left;
	}
	table.pdf_data thead tr th,
	table.pdf_data tbody tr td{
		border:1px solid grey; width:47px !Important; max-width:47px;
	}
	table.pdf_data tbody tr td:nth-child(3){
		width:125px !Important; max-width:125px;
	}
	table.pdf_data tbody tr:last-child td{
		border:none !Important;
		border-bottom:solid double grey;
		font-weight:bold;
	}
	
	table.pdf_data tbody tr:last-child td:nth-child(1),
	table.pdf_data tbody tr:last-child td:nth-child(2),
	table.pdf_data tbody tr:last-child td:nth-child(3),
	table.pdf_data tbody tr:last-child td:nth-child(4),
	table.pdf_data tbody tr:last-child td:nth-child(5){
		border-bottom:solid thin grey;
	}
	
	table.pdf_data tfoot tr td{
		text-align:center;
		
	}
	table.pdf_data tfoot tr.sign_row td{
		height:50px;
	}
	table.pdf_data tfoot tr.sign_row td.sign_col{/*td:nth-child(odd)*/
		border-bottom:1px dashed grey;
	}
	
	</style>
  </head>
  <body>
    <div id="" style="padding-left:20px;padding-bottom:20px; padding-right:10px;">
        <table width="100%" style="" border="0" cellpadding="2" cellspacing="0">
            <tr>
                <td><strong>ANSEN AGRICULTURE (PVT) LTD</strong></td>
                <td align="right">MONTHLY OVERTIME AMOUNT REPORT {{$paymonth_name}}</td>
            </tr>
            <tr>
                <td><!--NO. 16, THALADUWA ROAD, NEGOMBO-->&nbsp;</td>
                <td align="right">SECTION: {{$sect_name}}</td>
            </tr>
            <!--tr>
            	<td colspan="2">TEL: 031 4873714, FAX: 031 2226365</td>
            </tr>
            <tr>
            	<td colspan="2">Month of {{$paymonth_name}} <!-//-{{$more_info}}-//-></td>
            </tr-->
        </table>
        
    </div>
    <div id="footer">
      <div class="page-number"></div>
    </div>
    @php $check=1 @endphp
    
        
        	<table width="100%" border="0" cellpadding="2" cellspacing="0" class="pdf_data">
                <thead>
                    <tr class="col_border">
                        <th style="text-align:center;">#</th>
                        <th style="text-align:center;">EPF NO</th>
                        <th style="text-align:center;">Employee Name</th>
                        <th style="text-align:center;">NORMAL OT RATE</th>
                        <th style="text-align:center;">DOUBLE OT RATE</th>
                        <th style="text-align:center;">NORMAL OT AMOUNT</th>
                        <th style="text-align:center;">DOUBLE OT AMOUNT</th>
                        <th style="text-align:center;">TOTAL AMOUNT</th>
                        
                    </tr>
                </thead>
             	
                <tbody class="">
                @foreach ($employee_list as $row)
                    @if( $check > 0 )
                  <tr class="col_border">
                        <td>{{ $check }}</td>
                        <td>{{ $row['emp_etfno'] }}</td>
                        <td>{{ $row['emp_first_name'] }}</td>
                        <td>{{ number_format((float)$row['OTHRS1'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['OTHRS2'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['OTVAL1'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['OTVAL2'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)($row['OTVAL1']+$row['OTVAL2']), 2, '.', ',') }}</td>
                        
                  </tr>
                  @endif
                  @php $check++ @endphp
                @endforeach
                  <tr class="col_border">
                        <td>GRAND TOTAL</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>{{ number_format((float)$empot_paysums['OTVAL1'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$empot_paysums['OTVAL2'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)($empot_paysums['OTVAL1']+$empot_paysums['OTVAL2']), 2, '.', ',') }}</td>
                        
                  </tr>
                </tbody>
                
                <!--tfoot>
                	<tr class="foot_row">
                    	<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td colspan="10">&nbsp;</td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Prepared By</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Checked By</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Checked By</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Approved By</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Print Date</td>
                        <td colspan="7">&nbsp;</td>
                    </tr>
                    <tr class="sign_row">
                    	<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2" class="sign_col">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2" class="sign_col">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2" class="sign_col">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2" class="sign_col">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2" class="sign_col" style="vertical-align:bottom;">{{\Carbon\Carbon::now()->format('Y-m-d')}}</td>
                        <td colspan="7">&nbsp;</td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">System Administrator</td>
                        <td>&nbsp;</td>
                        <td colspan="2">HR Executive</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Accountant</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Angelo Wijesinghe</td>
                        <td colspan="10">&nbsp;</td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Managing Director</td>
                        <td colspan="10">&nbsp;</td>
                    </tr>
                </tfoot-->
            </table>
            
            
            
            
        	
        
      
  </body>
</html>