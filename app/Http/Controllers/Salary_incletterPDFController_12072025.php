<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class Salary_incletterPDFController extends Controller
{
    public function printdata(Request $request)
    {
        $id =  $request->input('id');

        $html = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Salary Increment Letter</title>
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
                                    <h3>{{ company_name }}.</h3>
                                    <p>{{ company_address }},</p>
                                    <p>Date: {{ current_date }}.</p>
                                    <hr>
                                </div>
                                <div class="letter-body">
                                    <p>To:</p>
                                    <p>{{ employee_name }},</p>
                                    <p>{{ job_title }},</p>
                                    <p>{{ department }},</p>
                                    <p>{{ company_name }}.</p>

                                    <p><strong>Subject: Salary Increment Notification</strong></p>

                                    <p>Dear {{ employee_name }},</p>

                                    <p>We are pleased to inform you that in recognition of your consistent hard work, dedication, and contributions to  <strong>{{ company_name }}</strong> Company Pvt. Ltd., your salary has been revised effective from  <strong>{{ date }}</strong>.</p>

                                    <p>Your new salary will be  <strong>Rs.{{ new_salary }}</strong> per [month/year], an increase from your previous salary of <strong>Rs.{{ pre_salary }}</strong>. This increment reflects our appreciation for your performance and our commitment to rewarding employees who help the company achieve its goals.</p>
                                    
                                    <p>We trust that this increase will serve as motivation for you to continue delivering exceptional results and contribute to the ongoing success of the company. Should you have any questions regarding this salary adjustment, please feel free to contact the HR department.</p>
                                    
                                    <p>Thank you once again for your valuable service. We look forward to your continued contribution to the growth of <strong>{{ company_name }}</strong> Company.</p>

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


                    
        $data = $data = DB::table('salary_inc_letter')
        ->leftJoin('companies', 'salary_inc_letter.company_id', '=', 'companies.id')
        ->leftJoin('departments', 'salary_inc_letter.department_id', '=', 'departments.id')
        ->leftJoin('employees', 'salary_inc_letter.employee_id', '=', 'employees.id')
        ->leftJoin('job_titles', 'salary_inc_letter.jobtitle', '=', 'job_titles.id')
        ->select('salary_inc_letter.*', 'employees.*', 'job_titles.title As job_title', 'companies.*','departments.name AS department')
        ->where('salary_inc_letter.id', $id)
        ->get(); 



        if ($data->isNotEmpty()) {
            $salary_inc = $data->first(); 
            $html = str_replace('{{ company_name }}', $salary_inc->name, $html);
            $html = str_replace('{{ company_address }}', $salary_inc->address, $html);
            $html = str_replace('{{ current_date }}', date('F j, Y'), $html);
            $html = str_replace('{{ employee_name }}', $salary_inc->emp_name_with_initial, $html);
            $html = str_replace('{{ employee_address }}', $salary_inc->emp_address, $html);
            $html = str_replace('{{ job_title }}', $salary_inc->job_title, $html);
            $html = str_replace('{{ pre_salary }}', $salary_inc->pre_salary, $html);
            $html = str_replace('{{ new_salary }}', $salary_inc->new_salary, $html);
            $html = str_replace('{{ date }}', date('F j, Y', strtotime($salary_inc->date)), $html);
            $html = str_replace('{{ department }}', $salary_inc->department, $html);
            $html = str_replace('{{ comment1 }}', $salary_inc->comment1, $html);
            $html = str_replace('{{ comment2 }}', $salary_inc->comment2, $html);
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
