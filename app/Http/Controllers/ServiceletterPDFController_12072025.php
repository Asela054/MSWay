<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class ServiceletterPDFController extends Controller
{
    public function printdata(Request $request)
    {
        $id =  $request->input('id');

        $html = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>service Letter</title>
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
                                    <p>{{ employee_name }}</p>
                                    <p>{{ employee_address }}</p>

                                    <p>Dear {{ employee_name }},</p>

                                    <p><strong>Subject: Service Letter for {{ employee_name }}</strong></p>

                                    <p>This is to certify that <strong>{{ employee_name }}</strong> was employed at <strong>{{ company_name }}</strong> Company Pvt. Ltd. as a <strong>{{ job_title }}</strong> in the <strong>{{ department }}</strong>. The employment period commenced on <strong>{{ join_date }}</strong> and ended on <strong>{{ end_date }}</strong>.</p>

                                    <p>During their tenure, <strong>{{ employee_name }}</strong> demonstrated professionalism, dedication, and performed their duties satisfactorily. <strong>{{ calling_name }}</strong> contributed positively to the company goals and upheld our values throughout their employment. </p>
                                    
                                    <p>If you require any further information regarding <strong>{{ employee_name }}</strong>, please feel free to contact us.</p>
                                    
                                    <p>We wish <strong>{{ employee_name }}</strong> all the best in your future endeavors.</p>

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


                    
        $data = $data = DB::table('service_letter')
        ->leftJoin('companies', 'service_letter.company_id', '=', 'companies.id')
        ->leftJoin('departments', 'service_letter.department_id', '=', 'departments.id')
        ->leftJoin('employees', 'service_letter.employee_id', '=', 'employees.id')
        ->leftJoin('job_titles', 'service_letter.jobtitle', '=', 'job_titles.id')
        ->select('service_letter.*', 'employees.*', 'job_titles.title As job_title', 'companies.*','departments.name AS department')
        ->where('service_letter.id', $id)
        ->get(); 



        if ($data->isNotEmpty()) {
            $service = $data->first(); 
            $html = str_replace('{{ company_name }}', $service->name, $html);
            $html = str_replace('{{ company_address }}', $service->address, $html);
            $html = str_replace('{{ current_date }}', date('F j, Y'), $html);
            $html = str_replace('{{ employee_name }}', $service->emp_name_with_initial, $html);
            $html = str_replace('{{ employee_address }}', $service->emp_address, $html);
            $html = str_replace('{{ job_title }}', $service->job_title, $html);
            $html = str_replace('{{ join_date }}', date('F j, Y', strtotime($service->join_date)), $html);
            $html = str_replace('{{ end_date }}', date('F j, Y', strtotime($service->end_date)), $html);
            $html = str_replace('{{ calling_name }}', $service->calling_name, $html);
            $html = str_replace('{{ department }}', $service->department, $html);
            $html = str_replace('{{ comment1 }}', $service->comment1, $html);
            $html = str_replace('{{ comment2 }}', $service->comment2, $html);
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
