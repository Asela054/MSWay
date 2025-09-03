<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class NDAletterPDFController extends Controller
{
    public function printdata(Request $request)
    {
        $id = $request->input('id');

        $html = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Non-Disclosure Agreement</title>
                        <style>
                            @page {
                                margin: 1in 1.25in;
                                size: A4;
                            }
                            body {
                                font-family: "Times New Roman", Times, serif;
                                font-size: 11pt;
                                line-height: 1.4;
                                color: #000;
                                margin: 0;
                                padding: 0;
                                text-align: justify;
                            }
                            .container {
                                width: 100%;
                                margin: 0;
                                padding: 0;
                            }
                            .header {
                                text-align: center;
                                font-weight: bold;
                                font-size: 14pt;
                                text-decoration: underline;
                                margin-bottom: 20pt;
                                letter-spacing: 0.5pt;
                            }
                            .intro-paragraph {
                                margin-bottom: 15pt;
                                text-align: justify;
                                text-indent: 0;
                                line-height: 1.4;
                            }
                            .witnesseth {
                                font-weight: bold;
                                text-align: center;
                                margin: 20pt 0 15pt 0;
                                font-size: 11pt;
                            }
                            .whereas {
                                margin-bottom: 12pt;
                                text-align: justify;
                                line-height: 1.4;
                            }
                            .now-therefore {
                                margin: 15pt 0;
                                text-align: justify;
                                line-height: 1.4;
                            }
                            .section-title {
                                font-weight: bold;
                                text-decoration: underline;
                                margin: 15pt 0 8pt 0;
                                font-size: 11pt;
                            }
                            .subsection {
                                margin-left: 0;
                                margin-bottom: 10pt;
                                text-align: justify;
                                line-height: 1.4;
                                padding-left: 25pt;
                                text-indent: -25pt;
                            }
                            .numbered-section {
                                margin-bottom: 12pt;
                                text-align: justify;
                                line-height: 1.4;
                            }
                            .signature-section {
                                margin-top: 25pt;
                                page-break-inside: avoid;
                            }
                            .signature-intro {
                                text-align: justify;
                                margin-bottom: 20pt;
                                line-height: 1.4;
                            }
                            .signature-block {
                                margin-bottom: 30pt;
                            }
                            .signature-line {
                                border-bottom: 1px dotted #000;
                                width: 200pt;
                                display: inline-block;
                                margin-left: 10pt;
                            }
                            .signature-text {
                                margin: 2pt 0;
                            }
                            .company-info {
                                margin-top: 5pt;
                            }
                            strong {
                                font-weight: bold;
                            }
                            .block-quote {
                                margin-left: 25pt;
                                margin-right: 25pt;
                                font-style: italic;
                            }
                            .page-break {
                                page-break-before: always;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="header">NON-DISCLOSURE AGREEMENT</div>
                            
                            <div class="intro-paragraph">
                                This Non-Confidentiality Agreement also knows as Non-Disclosure (the "Agreement") is made and entered into in Colombo by and between <strong>{{ company_name }}</strong> (hereafter known as "<strong>the Disclosing Party</strong>"), located at <strong>{{ company_address }}</strong> and <strong>{{ employee_name }}</strong> bearing National Identity card number <strong>{{ emp_national_id }}</strong> (hereinafter known as "<strong>the Receiving Party</strong>") at <strong>{{ employee_address }}</strong>
                            </div>
                            
                            <div class="witnesseth">WITNESSETH</div>
                            
                            <div class="whereas">
                                <strong>WHEREAS</strong> conducting such discussion requires the Disclosing Party and the Receiving Party to exchange confidential business information; and
                            </div>
                            
                            <div class="whereas">
                                <strong>WHEREAS</strong> the Disclosing Party and the Receiving Party are willing to exchange confidential business information only based on mutual assurances that such information will not be used or disclosed by any person expect as expressly permitted under this Agreement:
                            </div>
                            
                            <div class="now-therefore">
                                <strong>NOW, THEREFORE;</strong> in consideration of the mutual covenants and promises contained in this Agreement and other good and valuable consideration, the sufficiency of which is hereby acknowledged, the Disclosing Party and the Receiving Party agree as follows:
                            </div>
                            
                            <div class="section-title">1. Confidential Information.</div>
                            
                            <div class="subsection">
                                a. The term "Confidential Information" as used in this Agreement means all nonpublic or proprietary information, including, but not limited to, trade secrets, technical information, concepts, financial information, production information, marketing information, customer information.
                            </div>
                            
                            <div class="subsection">
                                b. All such Confidential Information shall be deemed confidential, proprietary, and valuable business secret information which is the exclusive property of the Disclosing Party producing it pursuant to this Agreement.
                            </div>
                            
                            <div class="subsection">
                                c. The production or receipt of Confidential Information pursuant to this Agreement does not create any proprietary interest in favor of any Party or Party\'s employee receiving or obtaining access to such information.
                            </div>

                            <div class="page-break"></div>
                            
                            <div class="section-title">2. Protection, Nonuse, and Nondisclosure of Confidential Information.</div>
                            
                            <div class="subsection">
                                a. A Party receiving Confidential Information pursuant to this agreement shall take all reasonable measures to protect the secrecy of and to avoid disclosure and unauthorized use of Confidential Information received pursuant to this agreement, including, but not limited to, at least those measures that the receiving party takes to protect its own most highly Confidential Information.
                            </div>
                            
                            <div class="subsection">
                                b. Except as expressly provided in this Agreement, a Party receiving Confidential Information pursuant to this agreement shall not use and shall not disclose such Confidential Information to a third party, expect as expressly provided in this Agreement.
                            </div>
                            
                            <div class="subsection">
                                c. A Party receiving Confidential Information pursuant to this Agreement shall immediately notify the producing party in the event of any unauthorized use or disclosure of the producing party\'s Confidential Information.
                            </div>
                            
                            <div class="section-title">3. Procedures Relating to Confidential Information.</div>
                            
                            <div class="subsection">
                                a. An employee of a party receiving Confidential Information pursuant to this Agreement may obtain access to such Confidential Information, but only to the extent necessary to enable the receiving party to participate in the mutually beneficial business discussion contemplated by this Agreement; provided, however, a party providing an employee with access to Confidential Information shall not be released from its obligations under this agreement and shall be strictly liable for any disclosure or use would be a breach or violation of this agreement if made by the Party and further provided, however, a party\'s employee receiving access to Confidential Information pursuant to this agreement shall execute and agree to be bound by the terms of this Agreement.
                            </div>
                            
                            <div class="page-break"></div>

                            <div class="numbered-section">
                                <strong>4.</strong> If any party breach any term or condition of this agreement should be liable to pay the compensation to other party to cover the damage.
                            </div>
                            
                            <div class="numbered-section">
                                <strong>5.</strong> This Agreement and these terms and conditions are governed by and shall be construed in accordance with laws of Sri Lanka.
                            </div>
                            
                            <div class="signature-section">
                                <div class="signature-intro">
                                    IN WITNESS WHEREOF, the parties hereto have caused this Agreement to be duly executed this <strong>{{ effect_day }}</strong> day of <strong>{{ effect_month }}, {{ effect_year }}</strong> (the "<strong>Effective Date</strong>")
                                </div>
                                
                                <div class="signature-block">
                                    <div class="signature-text">On behalf of the Disclosing Party<br><br></div>
                                    <div class="signature-text" style="padding-top: 80px;">By: <span class="signature-line"></span></div>
                                    <div class="signature-text">(Signature)</div>
                                    <div class="company-info"><strong>CEO, {{ company_name }}</strong></div>
                                </div>
                                
                                <div class="signature-block">
                                    <div class="signature-text">On behalf of the Receiving Party<br><br></div>
                                    <div class="signature-text" style="padding-top: 80px;">By: <span class="signature-line"></span></div>
                                    <div class="signature-text">(Signature)</div>
                                </div>
                            </div>
                        </div>
                    </body>
                    </html>';

        $data = DB::table('nda_letter')
            ->leftJoin('companies', 'nda_letter.company_id', '=', 'companies.id')
            ->leftJoin('departments', 'nda_letter.department_id', '=', 'departments.id')
            ->leftJoin('employees', 'nda_letter.employee_id', '=', 'employees.id')
            ->select('nda_letter.*',
            'employees.emp_name_with_initial AS emp_name_with_initial',
            'employees.emp_national_id AS emp_national_id',
            'employees.emp_address AS emp_address',
            'companies.*',
            'departments.name AS department')
            ->where('nda_letter.id', $id)
            ->get();

        if ($data->isNotEmpty()) {
            $service = $data->first();
            
            // Get date parts from effect_date
            $effectDate = date('j', strtotime($service->effect_date)); 
            $effectMonth = date('F', strtotime($service->effect_date)); 
            $effectYear = date('Y', strtotime($service->effect_date)); 
            
            // Replace placeholders with actual data
            $html = str_replace('{{ company_name }}', $service->name ?? 'MS WAY LOGISTICS (PRIVATE) LIMITED', $html);
            $html = str_replace('{{ company_address }}', $service->address ?? 'NO. 40/4, METRO HOMES RESIDENCES, MALAY STREET, COLOMBO 02', $html);
            $html = str_replace('{{ employee_name }}', $service->emp_name_with_initial ?? '[EMPLOYEE NAME]', $html);
            $html = str_replace('{{ emp_national_id }}', $service->emp_national_id ?? '[NIC NUMBER]', $html);
            $html = str_replace('{{ employee_address }}', $service->emp_address ?? '[EMPLOYEE ADDRESS]', $html);
            $html = str_replace('{{ effect_day }}', $effectDate, $html);
            $html = str_replace('{{ effect_month }}', $effectMonth, $html);
            $html = str_replace('{{ effect_year }}', $effectYear, $html);
        }

        $pdf = PDF::loadHTML($html);

        // Set page orientation to portrait with proper margins
        $pdf->setPaper('A4', 'portrait');
        
        // Return the PDF as base64-encoded data
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }
}