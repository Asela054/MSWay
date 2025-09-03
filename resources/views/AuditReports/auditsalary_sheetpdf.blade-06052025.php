<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Laravel PDF</title>
    <!--link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"-->
    <style type="text/css">
	@page { 
		size: 21cm 29.5cm portrait; 
		font-size:10px;
		margin:0.5cm;
	}
	/**/
	hr.pgbrk {
		page-break-after:always;
		border:0;
	}
	
	
	
	p{
		/*
		border-top:1px solid grey;
		*/
		margin-top:1px;
		padding-top:3px;
		margin-bottom:0px;
	}
	
	table.emp_info td:nth-child(1) hr{
		top:-2px;
	}
	table.emp_info td:nth-child(3) hr{
		top:1px;
	}
	
	table.emp_info hr.hr_thin{
		height:1px; border:none; color:grey; background-color:grey;
		margin-top:-1px;
	}
	table.emp_info hr.hr_stretch{
		width:115%;
	}
	
	table td{
		padding-left:5px;/*10px*/
		padding-right:5px;/*10px*/
	}
	
	table.emp_info tr.col_head td{
		border:1px solid grey;
	}
	table.emp_info tr.col_foot td{
		/*
		border-bottom:1px solid grey;
		*/
	}
	table.emp_info .col_foot td:nth-child(1),
	table.emp_info .col_foot td:nth-child(2){
		/*
		border-top:1px solid grey !Important;
		*/
	}
	table.emp_info tr td:first-child{
		border-left:1px solid grey;
	}
	table.emp_info td:last-child{
		border-right:1px solid grey;
		
	}
	table.emp_info tr:last-child td{
		border-bottom:1px solid grey;
		
	}
	
	table.sal_info, table.gen_info{
		border:1px solid grey;
	}
	table.sal_info td:nth-child(1), 
	table.sal_info td:nth-child(2){
		border-right:1px solid grey;
	}
	table.sal_info td.col_head,
	table.sal_info td.col_foot{
		border-top:1px solid grey;
		border-bottom:1px solid grey;
	}
	
	table.gen_info td.main_fig{
		border:1px solid grey;
		vertical-align:top;
	}
	table.gen_info td.left_border{
		border-left:1px solid grey;
	}
	table.emp_info td.right_border{
		border-right:1px solid grey;
	}
	table.emp_info td.top_border{
		border-top:1px solid grey;
	}
	
	
	table.emp_info tr td{
		height:16px;
	}
	
	tr.summary_sect td{
		border-top:1px solid grey; border-bottom:1px solid double;
	}
	
	span.fig_val{
		/*float:right;*/
		padding-left:5px;
	}
	</style>
  </head>
  <body>
    @php $check=1 @endphp
    
        @for ($slipcnt=0;$slipcnt<count($emp_array);$slipcnt++)
        	@if( (($check>1)&&($check%2)==1) ) 
            	@php echo '<hr class="pgbrk" />'; @endphp
            @elseif( ($check%2)==0 ) 
            	@php echo '<hr style="border:none; color:black; background-color:black; height:1px;" />'; @endphp
            @endif
            @if(isset($emp_array[$slipcnt]))
            @php $row=$emp_array[$slipcnt] @endphp
            <div id="" style="padding-left:20px;padding-bottom:5px;">
                <table width="100%" style="" border="0" cellpadding="2" cellspacing="0">
                    <tr>
                        <td><strong>MULTI OFFSET (PVT) LTD</strong></td>
                        <td align="right">PAY SLIP for {{$paymonth_name}}</td>
                    </tr>
                    <tr>
                        <td>NEGOMBO</td>
                        <td align="right">&nbsp;</td>
                    </tr>
                    
                </table>
                
            </div>
            <table width="100%" border="0" cellpadding="2" cellspacing="0" class="emp_info">
              <tr class="col_head">
                <td width="17%">EMP NO.:{{ $row['emp_epfno'] }}</td>
                <td width="17%" align="left">EPF NO:{{ $row['emp_epfno'] }}</td>
                <td width="65%" colspan="4">NAME:{{ $row['emp_first_name'] }}</td>
              </tr>
              <tr class="col_head">
                <!--td colspan="2" width="33%">SECTION: <span class="fig_val">{{ $row['Office'] }}</span></td-->
                <td width="33%" colspan="2">DEPARTMENT: <span class="fig_val">{{ $sect_name }}</span></td>
                <td width="33%" colspan="4">DESIGNATION<span class="fig_val">{{ $row['emp_designation'] }}</span></td>
              </tr>
			  <tr>
                <td colspan="6" style="border-left:none; border-right:none;">&nbsp;</td>
              </tr>
            <!--/table>
            <table width="100%" border="0" cellpadding="2" cellspacing="0" class="sal_info"-->
              <tr align="center" class="col_head">
                <td width="33%" colspan="2">SALARY AND ADDITIONS</td>
                <td width="33%" colspan="2">DEDUCTIONS</td>
                <td width="33%" colspan="2">NET SALARY</td>
              </tr>
              <tr>
                <td colspan="">BASIC</td>
                <td class="right_border" align="right"><!--&nbsp;--><span class="fig_val">{{ number_format((float)$row['BASIC'], 2, '.', ',') }}</span></td>
                <td colspan="">E.P.F. 8%:</td>
                <td class="right_border" align="right"><span class="fig_val">{{ number_format((float)$row['EPF8'], 2, '.', ',') }}</span><!--...--></td>
                <td colspan="">TOTAL ADDITIONS:</td>
                <td align="right"><span class="fig_val">{{ number_format((float)$row['tot_earn'], 2, '.', ',') }}</span><!--...--></td>
              </tr>
              <tr>
                <td colspan="">BRA I</td>
                <td class="right_border" align="right"><span class="fig_val">{{ number_format((float)$row['BRA_I'], 2, '.', ',') }}</span><!--&nbsp;--></td>
                <td colspan="">P.A.Y.E TAX:</td>
                <td class="right_border" align="right"><span class="fig_val">{{ number_format((float)$row['PAYES'], 2, '.', ',') }}</span><!--...--></td>
                <td colspan="">TOTAL DEDUCTIONS</td>
                <td align="right"><span class="fig_val">{{ number_format((float)$row['tot_ded'], 2, '.', ',') }}</span><!--&nbsp;--></td>
              </tr>
              <tr>
                <td colspan="">BRA II</td>
                <td class="right_border" align="right"><span class="fig_val">{{ number_format((float)$row['add_bra2'], 2, '.', ',') }}</span><!--&nbsp;--></td>
                <td colspan="">SALARY ADVANCE</td>
                <td class="right_border" align="right"><!--&nbsp;--></td>
                <td colspan=""><hr class="hr_thin hr_stretch" /><strong>NET SALARY:</strong></td>
                <td align="right"><hr class="hr_thin" /><span class="fig_val" style="margin-right:-5px;"><strong>{{ number_format((float)$row['NETSAL'], 2, '.', ',') }}</strong></span><!--<strong>&nbsp;</strong>--></td>
              </tr>
              <tr>
                <td colspan=""><hr class="hr_thin hr_stretch" />GROSS SALARY</td>
                <td class="right_border" align="right"><hr class="hr_thin" /><span class="fig_val">{{ number_format((float)$row['tot_bnp'], 2, '.', ',') }}</span><!--&nbsp;--></td>
                <td class="right_border" colspan="2">&nbsp;</td>
                <!--td align="right">&nbsp;</td-->
                <td colspan=""><hr class="hr_thin hr_stretch" />EMPLOYER E.P.F. 12%</td>
                <td align="right"><hr class="hr_thin" /><span class="fig_val">{{ number_format((float)$row['EPF12'], 2, '.', ',') }}</span><!--&nbsp;--></td>
              </tr>
              <tr>
                <td colspan=""><hr class="hr_thin hr_stretch" />NO PAY: ( {{ number_format((float)$row['NOPAYCNT'], 2, '.', '') }} DAYS):</td>
                <td class="right_border" align="right"><hr class="hr_thin" /><span class="fig_val">{{ number_format((float)$row['NOPAY'], 2, '.', ',') }}</span><!--...--></td>
                <td class="right_border" colspan="2">&nbsp;</td>
                <!--td align="right">&nbsp;</td-->
                <td colspan="">E.T.F. 3%</td>
                <td align="right"><span class="fig_val">{{ number_format((float)$row['ETF3'], 2, '.', ',') }}</span><!--&nbsp;--></td>
              </tr>
              <tr>
                <td colspan=""><hr class="hr_thin hr_stretch" />ARREARS</td>
                <td class="right_border" align="right"><hr class="hr_thin" />&nbsp;</td>
                <td class="right_border" colspan="2">&nbsp;</td>
                <!--td align="right">&nbsp;</td-->
                <td colspan=""><hr class="hr_thin hr_stretch" />OVERTIME (1.5) HOURS</td>
                <td align="right"><hr class="hr_thin" /><span class="fig_val">{{ number_format((float)$row['OTAMT1'], 2, '.', '') }}</span><!--&nbsp;--></td>
              </tr>
              <tr>
                <td colspan="">SALARY FOR E.P.F</td>
                <td class="right_border" align="right"><span class="fig_val">{{ number_format((float)$row['tot_fortax'], 2, '.', ',') }}</span><!--&nbsp;--></td>
                <td class="right_border" colspan="2">&nbsp;</td>
                <!--td align="right">&nbsp;</td-->
                <td colspan="">OVERTIME (2) HOURS</td>
                <td align="right"><span class="fig_val">{{ number_format((float)$row['OTAMT2'], 2, '.', '') }}</span><!--&nbsp;--></td>
              </tr>
              <tr>
                <td colspan=""><hr class="hr_thin hr_stretch" />OVERTIME (1.5):</td>
                <td class="right_border" align="right"><hr class="hr_thin" /><span class="fig_val">{{ number_format((float)$row['OTHRS1'], 2, '.', ',') }}</span><!--&nbsp;--></td>
                <td class="right_border" colspan="2">&nbsp;</td>
                <!--td align="right">&nbsp;</td-->
                <td colspan="2"><hr class="hr_thin" />&nbsp;</td>
                <!--td align="right">&nbsp;</td-->
              </tr>
              <tr>
                <td colspan="">OVERTIME (2)</td>
                <td class="right_border" align="right"><span class="fig_val">{{ number_format((float)$row['OTHRS2'], 2, '.', ',') }}</span><!--&nbsp;--></td>
                <td class="right_border" colspan="2">&nbsp;</td>
                <!--td align="right">&nbsp;</td-->
                <td colspan="2">&nbsp;</td>
                <!--td align="right">&nbsp;</td-->
              </tr>
              <tr>
                <td class="right_border" colspan="2">&nbsp;</td>
                <td class="right_border" colspan="2">&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="">ATTENDANCE</td>
                <td class="right_border" align="right"><span class="fig_val">{{ number_format((float)$row['ATTBONUS'], 2, '.', ',') }}</span><!--&nbsp;--></td>
                <td class="right_border" colspan="2">Salary Advance</td>
                <!--td align="right">&nbsp;</td-->
                <td colspan="2">&nbsp;</td>
                <!--td align="right">&nbsp;</td-->
              </tr>
              <tr>
                <td colspan="">TRANSPORT:</td>
                <td class="right_border" align="right"><span class="fig_val">{{ number_format((float)$row['add_transport'], 2, '.', ',') }}</span><!--...--></td>
                <td colspan="">TELEPHONE</td>
                <td class="right_border" align="right"><span class="fig_val">{{ number_format((float)$row['ded_tp'], 2, '.', ',') }}</span><!--&nbsp;--></td>
                <td colspan="2">&nbsp;</td>
                <!--td align="right">&nbsp;</td-->
              </tr>
              <tr>
                <td colspan="">OT Payment</td>
                <td class="right_border" align="right"><span class="fig_val">{{ number_format((float)($row['OTHRS1']+$row['OTHRS2']), 2, '.', ',') }}</span><!--&nbsp;--></td>
                <td colspan="">OTHER:</td>
                <td class="right_border" align="right"><span class="fig_val">{{ number_format((float)$row['ded_other'], 2, '.', ',') }}</span><!--...--></td>
                <td colspan="2">&nbsp;</td>
                <!--td align="right">&nbsp;</td-->
              </tr>
              <tr>
                <td class="right_border" height="50" colspan="2">&nbsp;</td>
                <td class="right_border" colspan="2">&nbsp;</td>
                <td colspan="2" align="center" style="vertical-align:bottom;">............................</td>
              </tr>
              <tr class="col_foot">
                <td colspan="" class="top_border"><strong>TOTAL ADDITIONS</strong></td>
                <td align="right" class="top_border right_border"><span class="fig_val">{{ number_format((float)$row['tot_earn'], 2, '.', ',') }}</span><!--<strong>&nbsp;</strong>--></td>
                <td colspan="" class="top_border"><strong>TOTAL DEDUCTIONS</strong></td>
                <td align="right" class="top_border right_border"><span class="fig_val">{{ number_format((float)$row['tot_ded'], 2, '.', ',') }}</span><!--<strong>&nbsp;</strong>--></td>
                <td colspan="2" align="center"><strong>SIGNATURE</strong></td>
              </tr>
            </table>
            <hr style="border:1px dashed grey;" />
            <table width="100%" border="0" cellpadding="2" cellspacing="0" style="font-size:8px;" class="gen_info">
              <tr>
                <td width="15%" class="main_fig">EMP NO: {{ $row['emp_epfno'] }}</td>
                <td width="15%" class="main_fig">E.P.F. NO: {{ $row['emp_epfno'] }}</td>
                <td width="30%" class="main_fig" colspan="4">NAME: {{ $row['emp_first_name'] }}</td>
                <td colspan="2" class="main_fig" width="26%">PAYSLIP FOR : {{ $paymonth_name }}</td>
                <td width="14%" rowspan="4" align="center" style="vertical-align:bottom; border-left:1px solid grey;">..................</td>
              </tr>
              <tr>
                <td colspan="2" class="main_fig" width="30%">SECTION <span class="fig_val">{{ $row['Office'] }}</span></td>
                <td width="10%">BASIC: </td>
                <td align="right"><span class="fig_val">{{ number_format((float)$row['BASIC'], 2, '.', ',') }}</span></td>
                <td width="8%" class="left_border">NO PAY</td>
                <td align="right"><span class="fig_val">{{ number_format((float)$row['NOPAY'], 2, '.', ',') }}</span></td>
                <td width="13%" class="left_border">TOTAL EARNED</td>
                <td width="15%" align="right"><span class="fig_val">{{ number_format((float)$row['tot_earn'], 2, '.', ',') }}</span></td>
                <!--td width="14%">&nbsp;</td-->
              </tr>
              <tr>
                <td colspan="2" class="main_fig">DEPARTMENT <span class="fig_val">{{ $sect_name }}</span></td>
                <td>BRA I</td>
                <td align="right"><span class="fig_val">{{ number_format((float)$row['BRA_I'], 2, '.', ',') }}</span></td>
                <td class="left_border">ARREARS</td>
                <td align="right"></td>
                <td class="left_border">TOTAL DEDUCTIONS</td>
                <td align="right"><span class="fig_val">{{ number_format((float)$row['tot_ded'], 2, '.', ',') }}</span></td>
                <!--td>&nbsp;</td-->
              </tr>
              <tr>
                <td colspan="2" rowspan="2" class="main_fig">DESIGNATION<span class="fig_val">{{ $row['emp_designation'] }}</span></td>
                <td>BRA II</td>
                <td align="right"><span class="fig_val">{{ number_format((float)$row['add_bra2'], 2, '.', ',') }}</span></td>
                <td class="left_border">&nbsp;</td>
                <td align="right"></td>
                <td class="left_border"><strong>NET SALARY</strong></td>
                <td align="right"><strong>{{ number_format((float)$row['NETSAL'], 2, '.', ',') }}</strong></td>
                <!--td>&nbsp;</td-->
              </tr>
              <tr>
                <td colspan="3" class="" style="border-top:1px solid grey;">SALARY FOR E.P.F</td>
                <td align="right" style="border-top:1px solid grey;"><span class="fig_val">{{ number_format((float)$row['tot_bnp'], 2, '.', ',') }}</span></td>
                <td colspan="2" class="main_fig">
                    <table width="100%" border="0" cellpadding="2" cellspacing="0" class="">
                      <tr>
                        <td width="33%">8%<span class="fig_val">{{ number_format((float)$row['EPF8'], 2, '.', ',') }}</span></td>
                        <td width="33%">12%<span class="fig_val">{{ number_format((float)$row['EPF12'], 2, '.', ',') }}</span></td>
                        <td width="33%">3%<span class="fig_val">{{ number_format((float)$row['ETF3'], 2, '.', ',') }}</span></td>
                      </tr>
                    </table>
                </td>
                <td align="center">SIGNATURE</td>
              </tr>
            </table>
            
            @endif
            
            @php $check++ @endphp
        	
        @endfor
      
  </body>
</html>