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
		font-size:7px;/*8px;*/
		margin:0.4cm;/*0.8cm;*/
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
		padding-left:1px;/*10px*/
		padding-right:1px;/*10px*/
		text-align:right;
	}
	/**/
	table.pdf_data thead tr th{
		text-align:center !Important;
	}
	
	table.pdf_data tbody tr td:nth-child(1),
	table.pdf_data tbody tr td:nth-child(2){
		text-align:left;
	}
	table.pdf_data thead tr th,
	table.pdf_data tbody tr td{
		border:1px solid grey; width:35px !Important; max-width:35px;/*47px*/
	}
	table.pdf_data tbody tr td:nth-child(2){
		/*width:50px !Important; max-width:50px;*//*125px*/
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
                <td><strong>{{ $company_name }}</strong></td>
                <td align="right">SALARY ADVANCE PAY REGISTER</td>
            </tr>
            <tr>
                <td>{{ $company_addr }}</td>
                <td align="right">Department: {{$sect_name}}</td>
            </tr>
            <tr>
            	<td colspan="2">TEL: {{ $land_tp }}</td>
            </tr>
            <tr>
            	<td colspan="2">Month of {{$paymonth_name}} <!--{{$more_info}}--></td>
            </tr>
        </table>
        
    </div>
    <div id="footer">
      <div class="page-number"></div>
    </div>
    @php $check=1 @endphp
    
        
        	<table width="100%" border="0" cellpadding="2" cellspacing="0" class="pdf_data">
                <thead>
                    <tr class="col_border">
                        <th style="text-align:center;">EPF <br />NO</th>
                        <th style="text-align:center;">Employee Name</th>
                        <th style="text-align:center;">Advance</th>
                        <th style="text-align:center;">Dialog Bill</th>
                        
                        <th style="text-align:center;">Other <br />Deductions</th>
                        <th style="text-align:center;">Advance Pay (Net)</th>
                        <th style="text-align:center;">Signature</th>
                    </tr>
                </thead>
             	
                <tbody class="">
                @foreach ($employee_list as $row)
                    @if( $check > 0 )
                    <tr class="col_border">
                        <td>{{ $row['emp_epfno'] }}</td>
                        <td>{{ $row['emp_first_name'] }}</td>
                        <td>{{ number_format((float)$row['col_advance'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['col_dialogbill'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['col_otherdeduction'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['net_amount'], 2, '.', ',') }}</td>
                        <td align="center">&nbsp;<!--...................--></td>
                  </tr>
                  @endif
                  @php $check++ @endphp
                @endforeach
                </tbody>
                
                
            </table>
            
            
            
            
        	
        
      
  </body>
</html>