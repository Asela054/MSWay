<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class PromotionletterPDFController extends Controller
{
    public function printdata(Request $request)
    {
        $id =  $request->input('id');

        $html = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Promotion Letter</title>
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
                                    <p>{{ old_jobtitle }},</p>
                                    <p>{{ old_department }},</p>
                                    <p>{{ company_name }}.</p>

                                    <p><strong>Subject: Promotion Letter</strong></p>
                                    
                                    <p>Dear {{ employee_name }},</p>

                                    <p>We are pleased to inform you that, in recognition of your exceptional performance and dedication, you have been promoted to the position of <strong>{{ new_jobtitle }}</strong> in the <strong>{{ new_department }}</strong>. Your promotion will be effective from <strong>{{ date }}</strong>.</p>

                                    <p>Over the course of your employment at {{ company_name }} Company, you have demonstrated remarkable skills, leadership, and a strong commitment to our company goals. We are confident that you will continue to excel in your new role and contribute even more to the success of the organization.</p>
                                    
                                    <p>Your new salary package will reflect your new position, and the details will be shared with you by the HR department.</p>
                                    
                                    <p>We congratulate you on this well-deserved promotion and wish you continued success in your career with {{ company_name }} Company.</p>

                                    <p>Should you have any questions or require further information regarding your new role and responsibilities, please feel free to contact the HR department.</p>

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


                    
        $data = $data = DB::table('promotion_letter')
        ->leftJoin('companies', 'promotion_letter.company_id', '=', 'companies.id')
        ->leftJoin('departments as old_dept', 'promotion_letter.old_department_id', '=', 'old_dept.id')
        ->leftJoin('departments as new_dept', 'promotion_letter.new_department_id', '=', 'new_dept.id')
        ->leftJoin('employees', 'promotion_letter.employee_id', '=', 'employees.id')
        ->leftJoin('job_titles As old_job', 'promotion_letter.old_jobtitle', '=', 'old_job.id')
        ->leftJoin('job_titles As new_job', 'promotion_letter.new_jobtitle', '=', 'new_job.id')
        ->select('promotion_letter.*', 'employees.*', 'old_job.title As old_jobtitle', 'new_job.title As new_jobtitle', 'companies.*','old_dept.name AS old_department' ,'new_dept.name AS new_department')
        ->where('promotion_letter.id', $id)
        ->get(); 



        if ($data->isNotEmpty()) {
            $promotion = $data->first(); 
            $html = str_replace('{{ company_name }}', $promotion->name, $html);
            $html = str_replace('{{ company_address }}', $promotion->address, $html);
            $html = str_replace('{{ current_date }}', date('F j, Y'), $html);
            $html = str_replace('{{ employee_name }}', $promotion->emp_name_with_initial, $html);
            $html = str_replace('{{ employee_address }}', $promotion->emp_address, $html);
            $html = str_replace('{{ old_jobtitle }}', $promotion->old_jobtitle, $html);
            $html = str_replace('{{ new_jobtitle }}', $promotion->new_jobtitle, $html);
            $html = str_replace('{{ date }}', date('F j, Y', strtotime($promotion->date)), $html);
            $html = str_replace('{{ old_department }}', $promotion->old_department, $html);
            $html = str_replace('{{ new_department }}', $promotion->new_department, $html);
            $html = str_replace('{{ comment1 }}', $promotion->comment1, $html);
            $html = str_replace('{{ comment2 }}', $promotion->comment2, $html);
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
