<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class WarningletterPDFController extends Controller
{
    public function printdata(Request $request)
    {
        $id =  $request->input('id');

        $html = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Warning Letter</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                font-size: 14px;
                                line-height: 1.5;
                                color: #000;
                                margin: 0;
                                padding: 0;
                            }
                            .container {
                                width: 100%;
                                max-width: 800px;
                                margin: 0 auto;
                                padding: 10px;
                            }
                            .letter-content {
                                padding: 20px;
                                margin-top: 20px;
                            }
                            .letter-header,
                            .letter-footer {
                                text-align: center;
                                font-weight: bold;
                            }
                            .letter-header h3 {
                                margin: 0;
                                font-size: 18px;
                            }
                            .letter-header p {
                                margin: 5px 0;
                                font-size: 14px;
                            }
                            .letter-body {
                                margin-top: 20px;
                            }
                            .letter-body p {
                                margin-bottom: 1rem;
                            }
                            ul {
                                padding-left: 20px;
                                margin-bottom: 1rem;
                            }
                            ul li {
                                margin-bottom: 10px;
                            }
                            .letter-footer p {
                                margin: 5px 0;
                                font-size: 14px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="letter-content">
                                <div class="letter-header">
                                    <h3>{{ company_name }}</h3>
                                    <p>{{ company_address }}</p>
                                    <p>Date: {{ current_date }}</p>
                                    <hr>
                                </div>
                                <div class="letter-body">
                                    <p>To:</p>
                                    <p>{{ employee_name }},</p>
                                    <p>{{ job_title }},</p>
                                    <p>{{ department }},</p>
                                    <p>{{ company_name }}.</p>

                                    <p><strong>Subject: Warning Letter for {{ reason }}</strong></p>

                                    <p>Dear {{ employee_name }},</p>

                                    <p>This letter serves as an official warning regarding your conduct/performance in the company. It has come to our attention that on {{ date }}, you have been involved in {{ reason }}, which is a breach of company policies and procedures.</p>

                                    <p>As per our company policy, your actions/behavior are unacceptable, and we expect immediate corrective action on your part. The following are the specifics of the issue:</p>

                                    <ul>
                                        <li><strong>Violation:</strong>{{ description }}</li>
                                        <li><strong>Consequences:</strong> Failure to comply with this warning and demonstrate improvement may result in further disciplinary actions, including suspension or termination.</li>
                                        </ul>
                                    
                                    <p>We advise you to take this warning seriously and avoid any repetition of such behavior/performance issues in the future. We value your contribution to the company and hope you will take the necessary steps to improve and align with the company standards.</p>

                                    <p>Please consider this letter as a formal warning, and it will be placed in your personnel file.</p>

                                    <p>If you have any concerns or wish to discuss this matter, feel free to contact your immediate supervisor or the HR department.</p>

                                    <p><strong>{{ comment1 }}</strong></P>
                                    <p><strong>{{ comment2 }}</strong></P>
                                </div>
                                <div class="letter-footer">
                                    <p>Yours sincerely,</p>
                                    <p>{{ ceo_name }}</p>
                                    <p>CEO, {{ company_name }}</p>
                                </div>
                            </div>
                        </div>
                    </body>
                    </html>';


                    
        $data = $data = DB::table('warning_letter')
        ->leftJoin('companies', 'warning_letter.company_id', '=', 'companies.id')
        ->leftJoin('departments', 'warning_letter.department_id', '=', 'departments.id')
        ->leftJoin('employees', 'warning_letter.employee_id', '=', 'employees.id')
        ->leftJoin('job_titles', 'warning_letter.jobtitle', '=', 'job_titles.id')
        ->select('warning_letter.*', 'employees.*', 'job_titles.title As job_title', 'companies.*','departments.name AS department')
        ->where('warning_letter.id', $id)
        ->get(); 



        if ($data->isNotEmpty()) {
            $warning = $data->first(); 
            $html = str_replace('{{ company_name }}', $warning->name, $html);
            $html = str_replace('{{ company_address }}', $warning->address, $html);
            $html = str_replace('{{ current_date }}', date('F j, Y'), $html);
            $html = str_replace('{{ employee_name }}', $warning->emp_name_with_initial, $html);
            $html = str_replace('{{ employee_address }}', $warning->emp_address, $html);
            $html = str_replace('{{ job_title }}', $warning->job_title, $html);
            $html = str_replace('{{ date }}', date('F j, Y', strtotime($warning->date)), $html);
            $html = str_replace('{{ department }}', $warning->department, $html);
            $html = str_replace('{{ reason }}', $warning->reason, $html);
            $html = str_replace('{{ description }}', $warning->description, $html);
            $html = str_replace('{{ comment1 }}', $warning->comment1, $html);
            $html = str_replace('{{ comment2 }}', $warning->comment2, $html);
            $html = str_replace('{{ ceo_name }}', 'James White', $html);

        }

         $pdf = PDF::loadHTML($html);

        // Set page orientation to landscape
        $pdf->setPaper('A4', 'portrait');
        
        // Return the PDF as base64-encoded data
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }
}
