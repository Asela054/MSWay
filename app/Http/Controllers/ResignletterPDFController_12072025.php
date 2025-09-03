<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class ResignletterPDFController extends Controller
{
    public function printdata(Request $request)
    {
        $id =  $request->input('id');

        $html = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Resignation letter</title>
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
                                    <h3>{{ employee_name }}</h3>
                                    <p>{{ employee_address }}</p>
                                    <p>Date: {{ current_date }}</p>
                                    <hr>
                                </div>
                                <div class="letter-body">
                                    <p>To:</p>
                                    <p>{{ ceo_name }},</p>
                                    <p>CEO,</p>
                                    <p>{{ company_name }},</p>
                                    <p>{{ company_address }}.</p>

                                    <p><strong>Subject: Resignation Letter</strong></p>

                                    <p>Dear {{ ceo_name }},</p>

                                    <p>I am writing to formally inform you of my resignation from my position as <strong>{{ job_title }}</strong> at <strong>{{ company_name }}</strong> Company Pvt. Ltd., effective <strong>{{ last_date }}</strong>.</p>

                                    <p>I have thoroughly enjoyed working at {{ company_name }} Company and appreciate the opportunities for personal and professional development during my time here. However, after careful consideration, I have decided to pursue a new direction that better aligns with my long-term career goals.</p>
                                    
                                    <p>I am committed to ensuring a smooth transition during my notice period and will gladly assist in training my replacement or handing over my responsibilities in an orderly manner. Please let me know if there is anything further I can do to aid in this process.</p>
                                    
                                    <p>Thank you again for your guidance and support. I am grateful for the experiences I have gained during my tenure at {{ company_name }} Company and hope to maintain a positive relationship moving forward.</p>

                                    <p><strong>{{ comment1 }}</strong></P>
                                    <p><strong>{{ comment2 }}</strong></P>
                                </div>
                                <div class="letter-footer">
                                    <p>Yours sincerely,</p>
                                    <p>{{ employee_name }}</p>
                                    <p>{{ job_title }}</p>
                                    <p>{{ company_name }} company.</p>
                                </div>
                            </div>
                        </div>
                    </body>
                    </html>';


                    
        $data = $data = DB::table('resign_letter')
        ->leftJoin('companies', 'resign_letter.company_id', '=', 'companies.id')
        ->leftJoin('departments', 'resign_letter.department_id', '=', 'departments.id')
        ->leftJoin('employees', 'resign_letter.employee_id', '=', 'employees.id')
        ->leftJoin('job_titles', 'resign_letter.jobtitle', '=', 'job_titles.id')
        ->select('resign_letter.*', 'employees.*', 'job_titles.title As job_title', 'companies.*','departments.name AS department')
        ->where('resign_letter.id', $id)
        ->get(); 



        if ($data->isNotEmpty()) {
            $resign = $data->first(); 
            $html = str_replace('{{ company_name }}', $resign->name, $html);
            $html = str_replace('{{ company_address }}', $resign->address, $html);
            $html = str_replace('{{ current_date }}', date('F j, Y'), $html);
            $html = str_replace('{{ employee_name }}', $resign->emp_name_with_initial, $html);
            $html = str_replace('{{ employee_address }}', $resign->emp_address, $html);
            $html = str_replace('{{ job_title }}', $resign->job_title, $html);
            $html = str_replace('{{ join_date }}', date('F j, Y', strtotime($resign->join_date)), $html);
            $html = str_replace('{{ last_date }}', date('F j, Y', strtotime($resign->last_date)), $html);
            $html = str_replace('{{ calling_name }}', $resign->calling_name, $html);
            $html = str_replace('{{ department }}', $resign->department, $html);
            $html = str_replace('{{ comment1 }}', $resign->comment1, $html);
            $html = str_replace('{{ comment2 }}', $resign->comment2, $html);
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
