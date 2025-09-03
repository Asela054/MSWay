<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class ApointmentletterPDFController extends Controller
{

    private function numberToWords($number) {
        $ones = array(
            '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
            'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
            'seventeen', 'eighteen', 'nineteen'
        );
        
        $tens = array(
            '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
        );
        
        if ($number == 0) return 'zero';
        
        $words = '';
        
        // Handle millions
        if ($number >= 1000000) {
            $millions = intval($number / 1000000);
            $words .= $this->convertHundreds($millions, $ones, $tens) . ' million ';
            $number %= 1000000;
        }
        
        // Handle thousands
        if ($number >= 1000) {
            $thousands = intval($number / 1000);
            $words .= $this->convertHundreds($thousands, $ones, $tens) . ' thousand ';
            $number %= 1000;
        }
        
        // Handle hundreds
        if ($number >= 100) {
            $hundreds = intval($number / 100);
            $words .= $ones[$hundreds] . ' hundred ';
            $number %= 100;
        }
        
        // Handle tens and ones
        if ($number >= 20) {
            $tensDigit = intval($number / 10);
            $onesDigit = $number % 10;
            $words .= $tens[$tensDigit];
            if ($onesDigit > 0) {
                $words .= '-' . $ones[$onesDigit];
            }
        } elseif ($number > 0) {
            $words .= $ones[$number];
        }
        
        return trim($words);
    }

    private function convertHundreds($number, $ones, $tens) {
        $result = '';
        
        if ($number >= 100) {
            $hundreds = intval($number / 100);
            $result .= $ones[$hundreds] . ' hundred ';
            $number %= 100;
        }
        
        if ($number >= 20) {
            $tensDigit = intval($number / 10);
            $onesDigit = $number % 10;
            $result .= $tens[$tensDigit];
            if ($onesDigit > 0) {
                $result .= '-' . $ones[$onesDigit];
            }
        } elseif ($number > 0) {
            $result .= $ones[$number];
        }
        
        return trim($result);
    }

    public function printdata(Request $request)
    {
        $id = $request->input('id');

        $html = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Letter of Appointment</title>
                        <style>
                            body {
                                font-family: "Times New Roman", serif;
                                font-size: 12px;
                                line-height: 1.4;
                                color: #000;
                                margin: 0;
                                padding: 0;
                            }
                            .container {
                                width: 100%;
                                max-width: 210mm;
                                margin: 0 auto;
                                padding: 15mm;
                            }
                            .letter-date {
                                text-align: right;
                                margin-bottom: 20px;
                                font-size: 12px;
                            }
                            .recipient-details {
                                margin-bottom: 20px;
                                font-size: 12px;
                            }
                            .subject-line {
                                text-align: center;
                                font-weight: bold;
                                margin: 20px 0;
                                text-decoration: underline;
                                font-size: 13px;
                            }
                            .section-heading {
                                font-weight: bold;
                                margin-top: 15px;
                                margin-bottom: 8px;
                                font-size: 12px;
                            }
                            .section-content {
                                margin-bottom: 10px;
                                text-align: justify;
                                font-size: 12px;
                            }
                            .numbered-section {
                                margin-bottom: 15px;
                                page-break-inside: avoid;
                            }
                            .subsection {
                                margin-left: 20px;
                                margin-bottom: 10px;
                                text-align: justify;
                                page-break-inside: avoid;
                            }
                            .signature-section {
                                margin-top: 30px;
                                margin-bottom: 20px;
                                /* Keep signature section together */
                                page-break-inside: avoid;
                            }
                            .acceptance-section {
                                border-top: 2px dotted #000;
                                padding-top: 15px;
                                margin-top: 20px;
                                page-break-inside: avoid;
                            }
                            .indent {
                                margin-left: 20px;
                            }
                            .leave-table {
                                margin-top: 10px;
                                line-height: 1.5;
                                margin-bottom: 10px;
                            }
                            .leave-summary {
                                margin-top: 10px;
                                line-height: 1.5;
                                margin-bottom: 10px;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-bottom: 10px;
                            }
                            td {
                                padding: 2px 0;
                                vertical-align: top;
                            }
                            .bold {
                                font-weight: bold;
                            }
                            .underline {
                                text-decoration: underline;
                            }
                            ol {
                                margin-left: 20px;
                                padding-left: 0;
                            }
                            ul {
                                margin-left: 20px;
                                padding-left: 0;
                            }
                            .page-break {
                                page-break-before: always;
                            }
                            .sub-indent {
                                margin-left: 40px;
                                margin-bottom: 8px;
                                page-break-inside: avoid;
                            }
                            .section-content {
                                orphans: 3;
                                widows: 3;
                            }
                            .subsection {
                                orphans: 2;
                                widows: 2;
                            }
                            .subsection-group {
                                page-break-inside: avoid;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="letter-date">
                                {{ current_date }}
                            </div>
                            
                            <div class="recipient-details">
                                {{ employee_name }},<br>
                                {{ employee_address }}.
                            </div>
                            
                            <div style="margin-bottom: 15px;">Dear {{ calling_name }},</div>
                            
                            <div class="subject-line">LETTER OF APPOINTMENT</div>
                            
                            <div class="numbered-section">
                                <div class="section-heading">1. Position</div>
                                <div class="section-content">
                                    {{ company_name }} is pleased to offer you the position of {{ job_title }} with effect from 
                                    {{ start_date }}, in the regular establishment of the Company.
                                </div>
                                <div class="section-content">
                                    However, it is expressly agreed that the Company reserves to itself the right to transfer you to 
                                    any other post of equivalent status and responsibility permanently, temporarily or on secondment 
                                    within the Company or any of its Associate, subsidiary or holding companies at the sole and 
                                    unfettered discretion of the Company.
                                </div>
                                <div class="section-content">
                                    You are liable to be called upon to serve in any part of the Republic of Sri Lanka with no 
                                    variation in these terms and conditions.
                                </div>
                            </div>
                            
                            <div class="numbered-section">
                                <div class="section-heading">2. Duties</div>
                                <div class="section-content">
                                    You will be responsible for all duties delegated to you by the Management of the Company.
                                </div>
                            </div>
                            
                            <div class="numbered-section">
                                <div class="section-heading">3. Remuneration</div>
                                <div class="section-content">
                                    You will be paid a Gross Monthly Salary of <strong>Sri Lankan Rupees {{ monthly_salary }} Only (Rs. 
                                    {{ monthly_salary_amount }}/=)</strong> inclusive of all allowances, statutory or otherwise. Salary increments will be at the 
                                    sole discretion of the Management of the Company.
                                </div>
                            </div>
                            
                            <div class="numbered-section">
                                <div class="section-heading">4. Probationary Period</div>
                                <div class="section-content">
                                    You will be on probation for a period of {{ probation_months }} months. The Management reserves the right to extend 
                                    the Probation Period if deemed necessary at the sole and absolute discretion of the Company.
                                </div>
                                <div class="section-content">
                                    Your service may be terminated at any time during the Probation Period without assigning 
                                    reasons or paying any compensation or any notice of termination. If your work and conduct 
                                    during this Probationary Period are found to be satisfactory in all aspects of employment and if 
                                    the Company decides that you are a fit and proper person to continue to be in the regular 
                                    employment of the Company, a letter of confirmation will be provided confirming your 
                                    employment with us.
                                </div>
                            </div>

                            <div class="page-break"></div>
                            
                            <div class="numbered-section">
                                <div class="section-heading">5. Statutory Deductions</div>
                                <div class="section-content">
                                    You will throughout your employment continue to be a member of the Employees\' Provident Fund. You 
                                    will contribute eight percent (8%) of your Gross Monthly Salary to this Fund and the Company will 
                                    make a contribution of twelve percent (12%) of your Basic Monthly Salary to the credit of the 
                                    Company\'s account in this Fund. Furthermore, the Company will contribute three percent (3%) of your 
                                    Basic Monthly Salary to the Employees\' Trust Fund.
                                </div>
                                <div class="section-content">
                                    Should the prevailing rates of contribution to the above mentioned Funds or the rates of taxation 
                                    applicable to employees be varied, or should new taxes (withholding or otherwise) be imposed by the 
                                    Government of Sri Lanka at a future date, the Company and you shall be required to comply with all 
                                    such statutory provisions or regulations, and you shall be required to contribute at such changed rates 
                                    and/or towards the new taxes.
                                </div>
                            </div>
                            
                            <div class="numbered-section">
                                <div class="section-heading">6. Leave</div>
                                <div class="section-content">
                                    Entitlement to leave shall be governed by the Shop and Office Employees Act.
                                </div>
                                <div class="subsection">
                                    1. In the <strong>first twelve months</strong> of employment, you will be entitled to ½ day of leave for every month 
                                    in which you have completed service.
                                </div>
                                <div class="subsection">
                                    2. After completion of 12 months in employment to 31<sup>st</sup> December of that year, you shall be entitled to 
                                    annual leave on a <strong>proportionate basis</strong> as stipulated in the Shop and Office Employees Act, and as 
                                    detailed below:
                                </div>
                                <div class="indent leave-table">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span><strong>Commencement of service between –</strong></span>
                                    </div>
                                    <div style="justify-content: space-between; align-items: center;">
                                        <span> 1<sup>st</sup> January and 31<sup>st</sup> March </span>
                                        <span style="position: absolute; left: 26.2%; transform: translateX(-50%);">- 14 days</span>
                                    </div>
                                    <div style="justify-content: space-between; align-items: center;">
                                        <span>1<sup>st</sup> April and 30<sup>th</sup> June</span>
                                        <span style="position: absolute; left: 30.2%; transform: translateX(-50%);">- 10 days</span>
                                    </div>
                                    <div style="justify-content: space-between; align-items: center;">
                                        <span>1<sup>st</sup> July and 30<sup>th</sup> September</span>
                                        <span style="position: absolute; left: 26%; transform: translateX(-50%);">- 07 days</span>
                                    </div>
                                    <div style="justify-content: space-between; align-items: center;">
                                        <span>1<sup>st</sup> October and 31<sup>st</sup> December</span>
                                        <span style="position: absolute; left: 23.3%; transform: translateX(-50%);">- 04 days</span>
                                    </div>
                                </div>
                                <div class="subsection">
                                    3. Thereafter, (from 1<sup>st</sup> January of the following year) you will be entitled to Annual Leave and 
                                    Casual Leave as follows:
                                </div>
                                <div class="indent leave-summary">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Annual Leave </span>
                                        <span style="position: absolute; left: 50%; transform: translateX(-50%);">- 14 days</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Casual Leave </span>
                                        <span style="position: absolute; left: 50%; transform: translateX(-50%);">- 07 days</span>
                                    </div>
                                </div>
                                <div class="subsection">
                                    4. The leave should be availed of in accordance with the rules and practices of the Company.
                                </div>
                            </div>
                            
                            <div class="numbered-section">
                                <div class="section-heading">7. Working Hours</div>
                                <div class="section-content">
                                    As per the Employer\'s policy, working hours consist of a minimum of 45 hours per week. Your normal 
                                    working hours will be from {{ work_start_time }} to {{ work_end_time }}, Monday to Friday (both days inclusive) and {{ saturday_start_time }} 
                                    to {{ saturday_end_time }} on Saturday. The Employer reserves the right to alter the working hours without 
                                    notice. You may be required to work in excess of the normal working hours aforementioned without 
                                    notice depending on the exigencies of your duties and without payment of any overtime or any overtime 
                                    allowances or extra remuneration.
                                </div>
                            </div>
                            
                            <div class="numbered-section">
                                <div class="section-heading">8. Other Employment</div>
                                <div class="subsection-group">
                                    <div class="subsection">
                                        1. During your period of employment, you shall not either directly or indirectly engage or be 
                                        concerned in any other employment or in any commercial, trade or business pursuit or 
                                        undertaking either in your own name as principal or as agent or otherwise which is in 
                                        competition with or similar to any business carried on by the Company or its Associate, 
                                        subsidiary or holding companies at present or at any time in the future, nor shall you receive or 
                                        accept directly or indirectly any profit or commission or other gain from any business, 
                                        enterprise or undertaking or any other contract in relation to the Company.
                                    </div>

                                    <div class="page-break"></div>

                                    <div class="subsection">
                                        2. You shall not engage in any other trade, business, employment or commercial activity without 
                                        the prior written consent of the Management.
                                    </div>
                                    <div class="subsection">
                                        3. At all times during the continuance of your employment and for a period of three (3) years 
                                        thereafter, you will not canvass or solicit personally or through other means or by letter, 
                                        advertisement or otherwise, any of the Company\'s or its Associate, Subsidiary or Holding 
                                        Companies\' customers, clients or principals (and where you are required to serve any 
                                        Associate, subsidiary or holding Company, such Company\'s customers, clients or principals) 
                                        local or foreign, and/or any person, firm or Company, local or foreign, with whom you have 
                                        had any dealings for or on behalf of the employer and/or its Associate, subsidiary or holding 
                                        companies in the course of your employment.
                                    </div>
                                </div>
                            </div>

                            
                            <div class="numbered-section">
                                <div class="section-heading">9. Secrecy of Information</div>
                                <div class="subsection-group">
                                    <div class="subsection">
                                        1. At all times during your employment and thereafter, you will not use either for yourself or for 
                                        others any information concerning the Company\'s business affairs and its customers and/or of 
                                        its Associate, subsidiary or holding companies which you may have acquired in the course of 
                                        your employment.
                                    </div>
                                    <div class="subsection">
                                        2. You will not divulge to any person, firm or Company any trade or other secrets or any 
                                        information of the Company and/or of its Associate, subsidiary or holding companies which 
                                        you may have acquired in the course of your employment or otherwise.
                                    </div>
                                    <div class="subsection">
                                        3. In the event of a breach by you of any of the foregoing stipulations or covenants, it is 
                                        specifically agreed and understood that the Company and/or its Associate, subsidiary or 
                                        holding companies shall be entitled inter-alia to obtain an injunction from a court of law 
                                        restraining you from continuing to commit any such breach and/or claim and recover from you 
                                        all losses, damages, costs and expenses incurred or sustained by the Company by reason of 
                                        such breach.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="numbered-section">
                                <div class="section-heading">10. Retirement</div>
                                <div class="section-content">
                                    Retirement is at the age of {{ retirement_age }}. Any extension shall be at the sole and absolute discretion of the 
                                    Management.
                                </div>
                            </div>
                            
                            <div class="numbered-section">
                                <div class="section-heading">11. Termination of Employment</div>
                                <div class="subsection-group">
                                    <div class="subsection">
                                        1) The Company reserves the right to:
                                        <div class="sub-indent">
                                            a) Terminate your employment should you be absent without authority or incapacitated 
                                            from properly performing your duties owing to ill health or accident.
                                        </div>
                                        <div class="sub-indent">
                                            b) Terminate your employment should it deem it necessary to do so as a consequence of 
                                            the re-organization of the Company.
                                        </div>
                                        <div class="sub-indent">
                                            c) Suspend your employment, without pay for a specified period on disciplinary 
                                            grounds.
                                        </div>
                                        <div class="sub-indent">
                                            d) Notwithstanding anything in the preceding paragraphs, the Company will be at liberty 
                                            to determine this contract and terminate your service without notice or compensation 
                                            if you commit any breach of your obligations under this contract or in the event of 
                                            disobedience, insobriety, insubordination, dishonesty, fraud, neglect of duty, breach 
                                            of any regulations adopted by the Company or the committing of any kind of 
                                            misconduct which is likely to bring disrepute or discredit to yourself or the Company.
                                        </div>
                                    </div>
                                    <div class="subsection">
                                        2) Either party may terminate this contract of employment without ascribing any reason 
                                        whatsoever by either party giving {{ notice_period }} ({{ notice_period_text }}) months\' notice to the other party or by payment of 
                                        {{ notice_period }} ({{ notice_period_text }}) months\' salary in lieu of notice.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="numbered-section">
                                <div class="section-heading">12. Return of Company Property</div>
                                <div class="section-content">
                                    The Company shall have the right, at its option, to require that you vacate your offices prior to the 
                                    effective date of termination and to cease all activities on the Company\'s behalf. Upon the termination 
                                    of your employment in any manner, you shall immediately surrender to the Company all lists, 
                                    equipment, computers, books and records having a connection with the Company\'s business, and 
                                    shall return all other property belonging to the Company, it being distinctly understood that all such 
                                    lists, books, records, and other documents are the property of the Company.
                                </div>
                            </div>

                            
                            <div class="numbered-section">
                                <div class="section-heading">13. Inventions & Improvements</div>
                                <div class="subsection-group">
                                    <div class="subsection">
                                        1. If, during your employment with the Company, you accomplish or conceive any invention, 
                                        creation, works of intellectual property in any form as a result of your employment with the 
                                        Company, the proprietary right to such property, including but not limited to patent, copyright, 
                                        trade secrets and other related rights, shall be vested in the Company.
                                    </div>
                                    <div class="subsection">
                                        2. You shall promptly give to the Company the full details of any invention or improvement 
                                        which you may from time to time make or discover in the course of your employment. Any 
                                        such invention or improvement shall be the property of the Company without any additional 
                                        compensation to you and you shall take all steps and execute such documents as may be 
                                        necessary and reasonably required by the Company, at the expense of the Company, to obtain 
                                        complete and exclusive legal title to any such invention or improvement.
                                    </div>
                                    <div class="subsection">
                                        3. You shall assist the Company in obtaining, securing and enforcing the above mentioned 
                                        intellectual property rights as is needed by the Company.
                                    </div>
                                </div>
                            </div>

                            <div class="numbered-section">
                                <div class="section-heading">14. Miscellaneous</div>
                                <div class="subsection-group">
                                    <div class="subsection">
                                        1. <strong>Modification; Prior claims:</strong> This agreement sets forth the entire understanding of the parties 
                                        with respect to the subject matter hereof, supersedes all existing agreements, whether written 
                                        or oral between the parties concerning such subject matter, and may be modified only by a 
                                        written instrument duly executed by each party. The employee hereby waives any claims that 
                                        may exist on the date hereof arising from his prior employment, if any, with the Company with 
                                        the exception of the benefits properly accrued on behalf of the employee but not paid for.
                                    </div>
                                    <div class="subsection">
                                        2. <strong>Survival:</strong> The covenants, agreements, representations and warranties contained in or made 
                                        pursuant to this agreement shall survive employee\'s termination of employment, irrespective 
                                        of any investigation made by or on behalf of any party.
                                    </div>
                                    <div class="subsection">
                                        3. <strong>Waiver:</strong> The failure of either party hereto at any time to enforce performance by the other 
                                        party of any provision of this agreement shall in no way affect such party\'s rights thereafter to 
                                        enforce the same, nor shall the waiver by either party of any breach of any provision hereof be 
                                        deemed to be a waiver by such party of any other breach of the same or any other provision 
                                        hereof.
                                    </div>
                                </div>
                            </div>

                            <div class="page-break"></div>
                            
                            <div class="numbered-section">
                                <div class="section-heading">15. Other Conditions</div>
                                <div class="subsection-group">
                                    <div class="subsection">
                                        1. You shall devote your time, attention and abilities exclusively to the performance of your 
                                        duties hereunder and shall in all respects obey and conform to the Company\'s orders and 
                                        regulations and well and faithfully serve the Company and make your best endeavors to 
                                        promote the interest thereof and of the business in which you will for the time being be 
                                        engaged. During such time as you may be engaged in connection with the business of any of 
                                        the Associate companies, subsidiary or holding companies, you shall at all times readily 
                                        observe all rules and regulations of such companies and conform to obey and execute all 
                                        orders which may be issued to you by the management of such Associate companies, 
                                        subsidiary and holding companies.
                                    </div>
                                    <div class="subsection">
                                        2. You may be subject to search by Company security staff. This includes the search of vehicles 
                                        and the contents of packets or parcels when taken into or out of the Company premises.
                                    </div>
                                    <div class="subsection">
                                        3. The Company may require you to consult a medical practitioner named by the Company if for 
                                        any reason the Management is concerned with your health.
                                    </div>
                                    <div class="subsection">
                                        4. You shall undertake not to pledge the Company\'s credit at any time except as is specifically 
                                        authorized or otherwise empowered under the authority of your office.
                                    </div>
                                    <div class="subsection">
                                        5. You are prohibited from entering into any type of private transactions with other staff of the 
                                        Company or the staff of any of its Associate companies, subsidiary or holding companies and 
                                        shall have no authority to purchase any goods or services or incur any liability with outsiders 
                                        who have business dealings with the Company or any private financial dealings with 
                                        employees of the Company, Associate companies, subsidiary or holding companies without 
                                        prior consent in writing from the Company.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="section-content" style="margin-top: 20px;">
                                This letter of appointment constitutes the entire contract of employment between you and the Company. In order 
                                to signify acceptance of the terms and conditions of employment contained herein, you are requested to place 
                                your signature on each page and return the duplicate.
                            </div>
                            
                            <div class="signature-section">
                                <table>
                                    <tr>
                                        <td style="width: 50%;">Yours faithfully,</td>
                                        <td style="width: 50%;"></td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 5px;">{{ company_name }}</td>
                                        <td></td>
                                    </tr>
                                </table>
                                <div style="margin-top: 30px;">
                                    <table>
                                        <tr>
                                            <td style="width: 50%; padding-top: 80px; padding-right: 350px;">.......................................</td>
                                            <td></td>
                                            <td style="width: 50%; padding-top: 80px; padding-right: 350px;">........................................</td>
                                        </tr>
                                        <tr>
                                            <td><strong>CEO, {{ company_name }}</strong></td>
                                            <td></td>
                                            <td style="padding-left: 50px;">Date</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="acceptance-section">
                                <div class="section-content">
                                    I, {{ employee_name }}, hereby accept the terms and conditions of 
                                    employment set out in the above paragraphs. I have also accepted the original of the letter of appointment.
                                </div>
                                <div style="margin-top: 30px;">
                                    <table>
                                        <tr>
                                            <td style="width: 50%; padding-top: 40px; padding-right: 350px;">.......................................</td>
                                            <td></td>
                                            <td style="width: 50%; padding-top: 40px; padding-right: 350px;">........................................</td>
                                        </tr>
                                        <tr>
                                            <td>Signature of Employee</td>
                                            <td></td>
                                            <td style="padding-left: 53px;">Date</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </body>
                    </html>';

        $data = DB::table('appointment_letter')
            ->leftjoin('employees', 'appointment_letter.employee_id', '=', 'employees.id')
            ->leftjoin('job_titles', 'appointment_letter.jobtitle', '=', 'job_titles.id')
            ->leftjoin('companies', 'appointment_letter.company_id', '=', 'companies.id')
            ->select('appointment_letter.*', 'employees.*', 'job_titles.title As emptitle', 'companies.*')
            ->where('appointment_letter.id', $id)
            ->get();
        
        if ($data->isNotEmpty()) {
            $appointment = $data->first();
           
            // Date formatting
            $currentDate = date('jS F Y');
            $startDate = date('jS F Y', strtotime($appointment->date));
           
            // Time formatting - Convert 24-hour to 12-hour format with AM/PM
            $workStartTime = $appointment->on_time ?? '08:30';
            $workEndTime = $appointment->off_time ?? '17:30';
            
            // Format times to 12-hour format with AM/PM
            $formattedStartTime = date('g:i A', strtotime($workStartTime));
            $formattedEndTime = date('g:i A', strtotime($workEndTime));
           
            // Calculate probation months from dates
            $probationStart = new \DateTime($appointment->probation_from);
            $probationEnd = new \DateTime($appointment->probation_to);
            $probationMonths = $probationStart->diff($probationEnd)->m + ($probationStart->diff($probationEnd)->y * 12);
           
            // Notice period text conversion
            $noticePeriod = $appointment->notice_period ?? 3;
            $noticePeriodText = '';
            switch($noticePeriod) {
                case 1: $noticePeriodText = 'one'; break;
                case 2: $noticePeriodText = 'two'; break;
                case 3: $noticePeriodText = 'three'; break;
                case 4: $noticePeriodText = 'four'; break;
                case 5: $noticePeriodText = 'five'; break;
                case 6: $noticePeriodText = 'six'; break;
                default: $noticePeriodText = 'three'; break;
            }

            // Convert salary to words
            $salaryAmount = $appointment->compensation ?? 0;
            $salaryInWords = $this->numberToWords($salaryAmount);
            $salaryInWords = ucfirst($salaryInWords);
           
            // Replace placeholders with actual data
            $html = str_replace('{{ current_date }}', $currentDate, $html);
            $html = str_replace('{{ employee_name }}', $appointment->emp_name_with_initial ?? 'Employee Name', $html);
            $html = str_replace('{{ calling_name }}', $appointment->calling_name ?? 'Calling Name', $html);
            $html = str_replace('{{ employee_address }}', $appointment->emp_address ?? 'Employee Address', $html);
            $html = str_replace('{{ company_name }}', $appointment->name ?? 'Company Name', $html);
            $html = str_replace('{{ job_title }}', $appointment->emptitle ?? 'Job Title', $html);
            $html = str_replace('{{ start_date }}', $startDate, $html);
            $html = str_replace('{{ monthly_salary }}', $salaryInWords, $html);
            $html = str_replace('{{ monthly_salary_amount }}', number_format($appointment->compensation ?? 0), $html);
            $html = str_replace('{{ probation_months }}', $probationMonths > 0 ? $probationMonths : 6, $html);
            $html = str_replace('{{ work_start_time }}', $formattedStartTime, $html);
            $html = str_replace('{{ work_end_time }}', $formattedEndTime, $html);
            $html = str_replace('{{ saturday_start_time }}', '8:30 AM', $html);
            $html = str_replace('{{ saturday_end_time }}', '12:30 PM', $html);
            $html = str_replace('{{ notice_period }}', $noticePeriod, $html);
            $html = str_replace('{{ notice_period_text }}', $noticePeriodText, $html);
            $html = str_replace('{{ retirement_age }}', $appointment->retirement_age ?? 55, $html);
        }
        
        $pdf = PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
       
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }
}