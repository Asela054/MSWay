<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class JobConfirmationLetterPDFController extends Controller
{
    public function printdata(Request $request)
    {
        $id =  $request->input('id');

        $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Job Confirmation Letter</title>
        <style>
            @media print {
                body {
                    margin: 0;
                    padding: 0;
                }
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.4;
                color: #000;
                margin: 0;
                padding: 20px 30px;
            }
            .container {
                width: 100%;
                max-width: 800px;
                margin: 0 auto;
            }
            .letter-header {
                margin-bottom: 8px;
            }
            .letter-header h2 {
                margin: 0 0 12px 0;
                font-size: 16px;
                font-weight: bold;
                text-align: center;
            }
            .letter-header p {
                margin: 2px 0;
                font-size: 12px;
            }
            .letter-info {
                margin: 15px 0;
            }
            .letter-info p {
                margin: 3px 0;
            }
            .letter-subject {
                margin: 12px 0;
                font-weight: bold;
            }
            .letter-body {
                margin-top: 5px;
                text-align: justify;
            }
            .letter-body p {
                margin-bottom: 8px;
            }
            .letter-footer {
                margin-top: 15px;
            }
            .letter-footer p {
                margin: 3px 0;
            }
            .acknowledgment {
                margin-top: 15px;
                padding-top: 8px;
                border-top: 1px solid #000;
            }
            .acknowledgment p {
                margin: 6px 0;
            }
            .signature-line {
                margin-top: 8px;
                border-bottom: 1px solid #000;
                width: 200px;
                display: inline-block;
            }
            strong {
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="container">

            <div class="letter-header">
                <h2>Job Confirmation Letter</h2>
                <p><strong>{{ company_name }},</strong></p>
                <p>{{ company_address }},</p>
                <p>{{ company_contact }}.</p>
            </div>

            <div class="letter-info">
                <p><strong>Date:</strong> {{ confirmation_date }}</p>
                <p style="margin-top: 10px;"><strong>Employee Name:</strong> {{ employee_name }}</p>
                <p><strong>Employee ID:</strong> {{ employee_id }}</p>
                <p><strong>Designation:</strong> {{ job_title }}</p>
                <p><strong>Department:</strong> {{ department_name }}</p>
            </div>

            <div class="letter-subject">
                <p><strong>Subject: Confirmation of Employment</strong></p>
            </div>

            <div class="letter-body">
                <p>Dear <strong>{{ calling_name }}</strong>,</p>

                <p>We are pleased to inform you that upon successful completion of your probationary period, your employment with <strong>{{ company_name }}</strong> is hereby <strong>confirmed</strong>, effective from <strong>{{ confirmation_date }}</strong>.</p>

                <p>Based on your performance, conduct, and contribution during the probation period from <strong>{{ start_date }}</strong> to <strong>{{ end_date }}</strong>, the management is satisfied with your work and dedication to the organization.</p>

                <p>You will continue in the position of <strong>{{ job_title }}</strong> under the <strong>{{ department_name }}</strong>, and all terms and conditions of employment will remain as per the company policies and your appointment letter.</p>

                <p>We appreciate your efforts and look forward to your continued commitment and valuable contribution to the growth and success of the organization.</p>

                <p>Please sign and return a copy of this letter as an acknowledgment of acceptance.</p>

                <p>We wish you every success in your career with <strong>{{ company_name }}</strong>.</p>
            </div>

            <div class="letter-footer">
                <p style="margin-top: 20px;">Yours sincerely,</p>
                <p style="margin-top: 15px;"><strong>CEO,</strong></p>
                <p><strong>{{ company_name }}.</strong></p>
            </div>

            <div class="acknowledgment">
                <p><strong>Employee Acknowledgment</strong></p>
                <p style="margin-top: 8px;">I hereby acknowledge and accept the confirmation of my employment as stated above.</p>
                <p style="margin-top: 40px;"><strong>Employee Signature:</strong> <span class="signature-line">&nbsp;</span></p>
                <p style="margin-top: 12px;"><strong>Date:</strong> <span class="signature-line">&nbsp;</span></p>
            </div>
        </div>
    </body>
    </html>';


                    
        $data = $data = DB::table('job_confirmation_letter')
        ->leftJoin('companies', 'job_confirmation_letter.company_id', '=', 'companies.id')
        ->leftJoin('departments', 'job_confirmation_letter.department_id', '=', 'departments.id')
        ->leftJoin('employees', 'job_confirmation_letter.employee_id', '=', 'employees.emp_id')
        ->leftJoin('job_titles', 'job_confirmation_letter.jobtitle', '=', 'job_titles.id')
        ->select('job_confirmation_letter.*', 'employees.*', 'job_titles.title As job_title', 'companies.*','departments.name AS department')
        ->where('job_confirmation_letter.id', $id)
        ->get(); 



        if ($data->isNotEmpty()) {
            $job = $data->first(); 
            $html = str_replace('{{ company_name }}', $job->name, $html);
            $html = str_replace('{{ company_address }}', $job->address, $html);
            $html = str_replace('{{ company_contact }}', $job->land, $html);
            $html = str_replace('{{ department_name }}', $job->department, $html);
            $html = str_replace('{{ current_date }}', date('F j, Y'), $html);
            $html = str_replace('{{ employee_name }}', $job->emp_name_with_initial, $html);
            $html = str_replace('{{ employee_id }}', $job->emp_id, $html);
            $html = str_replace('{{ employee_address }}', $job->emp_address, $html);
            $html = str_replace('{{ job_title }}', $job->job_title, $html);
            $html = str_replace('{{ start_date }}', date('F j, Y', strtotime($job->start_date)), $html);
            $html = str_replace('{{ end_date }}', date('F j, Y', strtotime($job->end_date)), $html);
            $html = str_replace('{{ confirmation_date }}', date('F j, Y', strtotime($job->confirmation_date)), $html);
            $html = str_replace('{{ calling_name }}', $job->calling_name, $html);
            $html = str_replace('{{ department }}', $job->department, $html);
            $html = str_replace('{{ comment }}', $job->comment, $html);
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
