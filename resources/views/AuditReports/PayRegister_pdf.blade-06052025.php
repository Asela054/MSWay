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
		font-size:8px;
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
	table.pdf_data tbody tr td:nth-child(2){
		text-align:left;
	}
	table.pdf_data thead tr th,
	table.pdf_data tbody tr td{
		border:1px solid grey; width:37px !Important; max-width:37px;/*47px*/
	}
	table.pdf_data tbody tr td:nth-child(2){
		width:75px !Important; max-width:75px;/*125px*/
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
                <td><strong>Tableau (PVT) LTD</strong></td>
                <td align="right">PAY REGISTER</td>
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
                        <th style="text-align:center;">EPF <br />NO</th>
                        <th style="text-align:center;">Employee Name</th>
                        <th style="text-align:center;">Working Day</th>
                        <th style="text-align:center;">Salary Per Day</th>
                        <th style="text-align:center;">OT1 Hrs.</th>
                        <th style="text-align:center;">OT2 Hrs.</th>
                        <th style="text-align:center;">OT1 Rate</th>
                        <th class="" style="text-align:center;">OT2 Rate</th>
                        <!--th class="" style="text-align:center;">Total for <br />Tax</th-->
                        <th style="text-align:center;">Basic+BRA</th><!--th style="text-align:center;">Attendance</th-->
                        <th style="text-align:center;">No Pay Day</th>
                        <th style="text-align:center;">No Pay Amount</th>
                        <!--th style="text-align:center;">Transport</th-->
                        <!--th>Other Addition</th-->
                        <th style="text-align:center;">TTL For E.P.F.</th>
                        <th style="text-align:center;">OT1 Amount</th>
                        <th style="text-align:center;">OT2 Amount</th>
                        <th style="text-align:center;">Attendance</th>
                        <th class="" style="text-align:center;">Transport Allowance</th>
                        <th style="text-align:center;">Production Insentive</th>
                        <th style="text-align:center;">Gross Salary</th>
                        <th style="text-align:center;">EPF-8</th>
                        <!--th style="text-align:center;">Telephone</th-->
                        <th style="text-align:center;">Late Deduction</th>
                        <th style="text-align:center;">Advance</th>
                        <th style="text-align:center;">Payee Tax</th>
                        <th style="text-align:center;">Bank Charges<!-- Deductions--></th>
                        <th style="text-align:center;">Loan</th>
                        
                        <th style="text-align:center;">Total <br />Deductions</th>
                        <th style="text-align:center;">Net Salary</th>
                        <th style="text-align:center;">EPF-12</th>
                        <th style="text-align:center;">ETF-3</th>
                        <th style="text-align:center;">Signature</th>
                    </tr>
                </thead>
             	
                <tbody class="">
                @foreach ($emp_array as $row)
                    @if( $check > 0 )
                    <tr class="col_border">
                        <td>{{ $row['emp_epfno'] }}</td>
                        <td>{{ $row['emp_first_name'] }}</td>
                        <td>{{ !empty($row['WK_ACT_DAYS'])?$row['WK_ACT_DAYS']:'' }}</td>
                        @php 
                        $basic_pay=$row['BASIC']+$row['BRA_I']+$row['add_bra2'];
                        $emp_daysal=($row['WK_MAX_DAYS']>0)?number_format((float)($basic_pay/$row['WK_MAX_DAYS']), 2, '.', ','):'';
                        @endphp
                        <td>{{ $emp_daysal }}</td>
                        <td>{{ !empty($row['OT1DURA'])?$row['OT1DURA']:'' }}</td>
                        <td>{{ !empty($row['OT2DURA'])?$row['OT2DURA']:'' }}</td>
                        @php
                        $normal_ot_rate = !empty($row['OT1DURA'])?number_format((float)($row['OTHRS1']/$row['OT1DURA']), 2, '.', ','):'';
                        $double_ot_rate = !empty($row['OT2DURA'])?number_format((float)($row['OTHRS2']/$row['OT2DURA']), 2, '.', ','):'';
                        @endphp
                        <td>{{ $normal_ot_rate }}</td>
                        <td class="">{{ $double_ot_rate }}</td>
                        
                        <td>{{ number_format((float)$basic_pay, 2, '.', ',') }}</td>
                        <td>{{ !empty($row['nopay_days'])?number_format((float)$row['nopay_days'], 2, '.', ','):'' }}</td>
                        <td>{{ number_format((float)$row['NOPAY'], 2, '.', ',') }}</td>
                        
                        <!--td>number_format((float)$row['add_transport'], 2, '.', ',')</td-->
                        <!--td>{{ number_format((float)$row['add_other'], 2, '.', '') }}</td-->
                        @php
                        $tot_forepf = $row['tot_bnp']-$row['NOPAY'];//$row['tot_fortax'];//
                        @endphp
                        <td>{{ number_format((float)$tot_forepf, 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['OTHRS1'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['OTHRS2'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['ATTBONUS_W'], 2, '.', ',') }}</td>
                        <td class="">{{ number_format((float)$row['add_transport'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['INCNTV_EMP'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['tot_earn'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['EPF8'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['ded_IOU'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['sal_adv'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['PAYES'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['ded_fund_1'], 2, '.', ',') }}</td>
                        
                        
                        <td>{{ number_format((float)$row['LOAN'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['tot_ded'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['NETSAL'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['EPF12'], 2, '.', ',') }}</td>
                        <td>{{ number_format((float)$row['ETF3'], 2, '.', ',') }}</td>
                        <td align="center">&nbsp;<!--...................--></td>
                  </tr>
                  @endif
                  @php $check++ @endphp
                @endforeach
                </tbody>
                
                <tfoot>
                	<tr class="foot_row">
                    	<td colspan="3">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td colspan="10">&nbsp;</td>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                    	<td colspan="3">&nbsp;</td>
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
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr class="sign_row">
                    	<td colspan="3">&nbsp;</td>
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
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                    	<td colspan="3">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">System Administrator</td>
                        <td>&nbsp;</td>
                        <td colspan="2">HR Executive</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Accountant</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Managing Director</td>
                        <td colspan="10">&nbsp;</td>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <!--tr>
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
                    </tr-->
                </tfoot>
            </table>
            
            
            
            
        	
        
      
  </body>
</html>