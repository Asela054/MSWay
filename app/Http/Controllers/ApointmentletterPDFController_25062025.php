<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class ApointmentletterPDFController extends Controller
{

    public function printdata(Request $request)
    {
        $id =  $request->input('id');

        $html = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Appointment Letter</title>
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
                                </div>
                                <div class="letter-body">
                                    <p>{{ employee_name }}</p>
                                    <p>{{ employee_address }}</p>

                                    <p>Dear {{ employee_name }},</p>

                                    <p><strong>Subject: Appointment Letter for the Position of {{ job_title }}</strong></p>

                                    <p>We are pleased to inform you that you have been selected for the position of <strong>{{ job_title }}</strong> at <strong>{{ company_name }}</strong>. Your appointment is effective from <strong>{{ start_date }}</strong>, subject to the following terms and conditions:</p>

                                    <ul>
                                        <li><strong>Job Title:</strong> You will be appointed as {{ job_title }}, reporting to {{ reporting_manager }}.</li>
                                        <li><strong>Job Location:</strong> Your initial place of work will be at our office, though you may be transferred or assigned to other locations as per business requirements.</li>
                                        <li><strong>Compensation:</strong> You will receive an annual salary of {{ annual_salary }}, with a breakdown of the components mentioned in the attached salary structure.</li>
                                        <li><strong>Probation Period:</strong> You will be on probation {{ probationfrom }} to {{ probationto }}, during These {{ weekcount }} which your performance will be reviewed, and your employment may be confirmed or terminated based on the outcome.</li>
                                        <li><strong>Working Hours:</strong> The standard working hours are from {{ shiftontime }}AM to {{ shiftofftime }} PM, Monday to Friday.</li>
                                        <li><strong>Leave Policy:</strong> You are entitled to {{ leavedays }}of paid leave per year, in accordance with company policies.</li>
                                        <li><strong>Termination Clause:</strong> Either party can terminate this contract with 30 days notice or salary in lieu of the notice period.</li>
                                    </ul>

                                    <p>Please sign and return the attached copy of this letter as your formal acceptance of this offer. We are excited to have you on our team and look forward to your valuable contributions to the success of {{ company_name }}.</p>
                                    <p>Welcome aboard!</p>
                                </div>
                                <div class="letter-footer">
                                    <p>Sincerely,</p>
                                    <p>{{ ceo_name }}</p>
                                    <p>CEO, {{ company_name }}</p>
                                </div>
                            </div>
                        </div>
                    </body>
                    </html>';


                    
        $data = DB::table('appointment_letter')
        ->leftjoin('employees', 'appointment_letter.employee_id', '=', 'employees.id')
        ->leftjoin('job_titles', 'appointment_letter.jobtitle', '=', 'job_titles.id')
        ->leftjoin('companies', 'appointment_letter.company_id', '=', 'companies.id')
        ->select('appointment_letter.*','employees.*','job_titles.title As emptitle','companies.*')
        ->where('appointment_letter.id', $id)
        ->get(); 



        if ($data->isNotEmpty()) {
            $appointment = $data->first(); 
            $html = str_replace('{{ company_name }}', $appointment->name, $html);
            $html = str_replace('{{ company_address }}', $appointment->address, $html);
            $html = str_replace('{{ current_date }}', date('F j, Y'), $html);
            $html = str_replace('{{ employee_name }}', $appointment->emp_name_with_initial, $html);
            $html = str_replace('{{ employee_address }}', $appointment->emp_address, $html);
            $html = str_replace('{{ job_title }}', $appointment->emptitle, $html);
            $html = str_replace('{{ start_date }}', date('F j, Y', strtotime($appointment->date)), $html);
            $html = str_replace('{{ probationfrom }}', date('F j, Y', strtotime($appointment->probation_from)), $html);
            $html = str_replace('{{ probationto }}', date('F j, Y', strtotime($appointment->probation_to)), $html);
            $html = str_replace('{{ weekcount }}', $appointment->no_ofweeks, $html);
            $html = str_replace('{{ shiftontime }}', $appointment->on_time, $html);
            $html = str_replace('{{ shiftofftime }}', $appointment->off_time, $html);
            $html = str_replace('{{ leavedays }}',$appointment->leaves, $html);
            $html = str_replace('{{ reporting_manager }}', 'Jane Smith, Head of Engineering', $html);
            $html = str_replace('{{ annual_salary }}', $appointment->compensation, $html);
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
