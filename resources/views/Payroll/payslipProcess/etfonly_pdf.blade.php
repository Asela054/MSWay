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
		font-size:11px;
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
	table.pdf_data tbody tr td:nth-child(2){
		width:125px !Important; max-width:125px;
	}
	table.pdf_data tbody tr:last-child td{
		border:none !Important;
		border-bottom:solid double grey;
		font-weight:bold;
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
                <td><strong>MULTI OFFSET (PVT) LTD</strong></td>
                <td align="right">E.T.F.<!-- (3%)--></td>
            </tr>
            <tr>
                <td>NEGOMBO</td>
                <td align="right">Department: {{$sect_name}}</td>
            </tr>
            <tr>
            	<td colspan="2">TEL: 031 0000000, FAX: 031 0000000</td>
            </tr>
            <tr>
            	<td colspan="2">Month of {{$paymonth_name}} <!--{{$more_info}}--></td>
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
                        <th style="text-align:center;">EPF No.</th>
                        <th style="text-align:center;">Name</th>
                        <th style="text-align:center; border-right:none;">NIC</th>
                        <th style="border-left:none; border-right:none; width:5px;">&nbsp;</th>
                        <th style="text-align:center; width:100px;" colspan="">Tax Total</th>
                        <th style="border-left:none; border-right:none; width:5px;">&nbsp;</th>
                        <th style="text-align:center; border-left:none;" colspan="">E.T.F. Total</th>
                    </tr>
                </thead>
             	
                <tbody class="">
                @foreach ($emp_array as $row)
                    @if( $check > 0 )
                    <tr class="col_border">
                        <td>{{ $row['emp_epfno'] }}</td>
                        <td>{{ $row['emp_first_name'] }}</td>
                        <td style="border-right:none;">{{ $row['emp_nicno'] }}</td>
                        <td style="border-left:none; border-right:none;">&nbsp;</td>
                        <td style="" colspan="">{{ number_format((float)$row['tot_fortax'], 2, '.', ',') }}</td>
                        <td style="border-left:none; border-right:none;">&nbsp;</td>
                        <td style="border-left:none;" colspan="">{{ number_format((float)$row['ETF3'], 2, '.', ',') }}</td>
                  </tr>
                  @endif
                  @php $check++ @endphp
                @endforeach
                </tbody>
                
                <tfoot>
                	<tr class="foot_row">
                    	<td colspan="">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="">&nbsp;</td>
                    </tr>
                    <tr>
                    	<td colspan="">Prepared By</td>
                        <td>&nbsp;</td>
                        <td colspan="">Checked By</td>
                        <td>&nbsp;</td>
                        <td colspan="">Approved By</td>
                        <td>&nbsp;</td>
                        <td colspan="">Print Date</td>
                    </tr>
                    <tr class="sign_row">
                    	<td colspan="" class="sign_col">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="" class="sign_col">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="" class="sign_col">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="" class="sign_col" style="vertical-align:bottom;">{{\Carbon\Carbon::now()->format('Y-m-d')}}</td>
                    </tr>
                    <tr>
                        <td colspan="">System Administrator</td>
                    	<td>&nbsp;</td>
                        <td colspan="">Accountant</td>
                        <td>&nbsp;</td>
                        <td colspan="">Managing Director</td>
                        <td>&nbsp;</td>
                        <td colspan="">&nbsp;</td>
                    </tr>
                    <!--tr>
                        <td colspan="">&nbsp;</td>
                    	<td>&nbsp;</td>
                        <td colspan="">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="">Managing Director</td>
                        <td>&nbsp;</td>
                        <td colspan="">&nbsp;</td>
                    </tr-->
                </tfoot>
            </table>
            
            
            
            
        	
        
      
  </body>
</html>