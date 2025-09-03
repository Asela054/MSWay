<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Laravel PDF</title>
    <!--link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"-->
    <style type="text/css">
	@page { 
		size: 29.5cm 21cm portrait; 
		font-size:10px;
		margin:0.5cm;
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
	/*
	table.pdf_data td{
		padding-left:5px;
		padding-right:5px;
		text-align:right;
	}
	table.pdf_data td:nth-child(1),
	table.pdf_data td:nth-child(2){
		text-align:left;
	}
	*/
	table.pdf_data thead th,
	table.pdf_data tbody td{
		border:1px solid grey;
	}
	/*
	table.pdf_data tbody tr:last-child td{
		border:none !Important;
		border-bottom:solid double grey;
	}
	*/
	table.pdf_data tfoot td{
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
    <div id="" style="padding-left:20px;padding-bottom:20px;">
        <table width="100%" style="" border="0" cellpadding="2" cellspacing="0">
            <tr>
                <td><strong>ANSEN AGRICULTURE (PVT) LTD</strong></td>
                <td align="right">HELD PAYMENTS</td>
            </tr>
            <tr>
                <td>NO. 16, THALADUWA ROAD, NEGOMBO</td>
                <td align="right">{{$sect_name}}</td>
            </tr>
            <tr>
            	<td colspan="2">TEL: 031 4873714, FAX: 031 2226365</td>
            </tr>
            <tr>
            	<td colspan="2">{{$more_info}}</td>
            </tr>
        </table>
        
    </div>
    <div id="footer">
      <div class="page-number"></div>
    </div>
    @php $check=0 @endphp
    
        
        	<table width="100%" border="0" cellpadding="2" cellspacing="0" class="pdf_data">
                <thead>
                    <tr class="col_border">
                        <th width="10%">Employee Name</th>
                        <th>Section</th>
                        <th>Net Amount</th>
                        <th>Status</th>
                        <th width="25%">Held Remarks</th>
                        <th width="25%">Release Remarks</th>
                        
                    </tr>
                </thead>
             	
                <tbody class="">
                @foreach ($emp_array as $row)
                    @if( $check > 0 )
                    <tr class="col_border">
                        <td>{{ $row['Name'] }}</td>
                        <td>{{ $row['Office'] }}</td>
                        <td align="right">{{ number_format((float)$row['NETSAL'], 2, '.', '') }}</td>
                        <td>{{ $row['payslip_held'] }}</td>
                        <td>{{ $row['payheld_reason'] }}</td>
                        <td>{{ $row['payrelease_reason'] }}</td>
                        
                  </tr>
                  @endif
                  @php $check++ @endphp
                @endforeach
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
                        <td colspan="11">&nbsp;</td>
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
                        <td colspan="11">&nbsp;</td>
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
                        <td colspan="11">&nbsp;</td>
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
                        <td colspan="11">&nbsp;</td>
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
                        <td colspan="11">&nbsp;</td>
                    </tr>
                </tfoot-->
            </table>
            
            
            
            
        	
        
      
  </body>
</html>