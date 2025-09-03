<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class EndUserletterPDFController extends Controller
{
    public function printdata(Request $request)
    {
        $id = $request->input('id');

        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Agreement for Use of Company-Issued Laptops and Mobile Devices</title>
    <style>
        @page {
            margin: 1in;
            size: A4;
        }
        
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .title {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .agreement-info {
            margin-bottom: 15px;
            text-align: justify;
        }
        
        .section {
            margin-bottom: 12px;
        }
        
        .section-title {
            border-top: 2px solid #000;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .subsection {
            margin-left: 20px;
            margin-bottom: 5px;
        }
        
        .sub-item {
            margin-left: 40px;
            margin-bottom: 3px;
        }
        
        .device-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .device-table th,
        .device-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            font-size: 10pt;
        }
        
        .device-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .signature-section {
            margin-top: 40px;
        }

        .signature-block {
            margin-bottom: 40px;
            line-height: 1.5;
        }

        .signature-line {
            border-bottom: 1px dotted #000;
            width: 200px;
            display: inline-block;
            margin-left: 15px;
            height: 25px;
        }

        .date-line {
            border-bottom: 1px dotted #000;
            width: 180px;
            display: inline-block;
            margin-left: 15px;
            height: 25px;
        }

        .final-note {
            border-top: 2px solid #000;
            margin-top: 30px;
            text-align: justify;
            font-size: 12pt;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">
            Employee Agreement for Use of Company-Issued Laptops and Mobile Devices
        </div>
    </div>
    
    <div class="agreement-info">
        This Agreement is entered into on {{ effect_date }} by and between {{ company_name }} and {{ employee_name }} bearing National Identity Card number {{ emp_national_id }}. The purpose of this Agreement is to establish the terms and conditions under which the Employee may use Company-issued laptops, mobile devices, and related equipment.
    </div>
    
    <div class="section">
        <div class="section-title">1. Issuance of Devices</div>
        <div class="subsection">• The Company agrees to provide the Employee with the following Devices for business purposes:</div>
        
        <table class="device-table">
            <thead>
                <tr>
                    <th>DEVICE TYPE</th>
                    <th>MODEL NO</th>
                    <th>SERIAL NUMBER</th>
                    <th>OTHER REF. NUMBERS</th>
                </tr>
            </thead>
            <tbody>
                {{ device_table_rows }}
            </tbody>
        </table>
        
        <div class="subsection">• The Devices remain the sole property of the Company at all times.</div>
    </div>
    
    <div class="section">
        <div class="section-title">2. Permitted Use</div>
        <div class="subsection">• The Devices are to be used primarily for business-related activities. Limited personal use is permitted, provided it does not:</div>
        <div class="sub-item">o Interfere with the Employee\'s job responsibilities.</div>
        <div class="sub-item">o Violate Company policies or applicable laws.</div>
        <div class="sub-item">o Compromise the security or integrity of the Devices or Company data.</div>
    </div>
    
    <div class="section">
        <div class="section-title">3. Security and Confidentiality</div>
        <div class="subsection">• The Employee agrees to:</div>
        <div class="sub-item">o Not share login credentials or allow unauthorized individuals to use the Devices.</div>
        <div class="sub-item">o Immediately report any loss, theft, or damage of the Devices to the Company.</div>
        <div class="sub-item">o Protect confidential Company information stored on or accessed through the Devices.</div>
    </div>
    
    <div class="section">
        <div class="section-title">4. Prohibited Activities</div>
        <div class="subsection">• The Employee shall not:</div>
        <div class="sub-item">o Install unauthorized software or applications on the Devices.</div>
        <div class="sub-item">o Use the Devices for illegal, unethical, or inappropriate activities.</div>
        <div class="sub-item">o Jailbreak, root, or otherwise modify the Devices\' operating systems.</div>
        <div class="sub-item">o Transfer, sell, or lease the Devices to any third party.</div>
    </div>
    
    <div class="section">
        <div class="section-title">5. Maintenance and Updates</div>
        <div class="subsection">• The Employee agrees to:</div>
        <div class="sub-item">o Keep the Devices in good working condition.</div>
        <div class="sub-item">o Install all software updates and security patches promptly.</div>
        <div class="sub-item">o Notify the Company of any technical issues or required repairs.</div>
    </div>
    
    <div class="section">
        <div class="section-title">6. Monitoring and Compliance</div>
        <div class="subsection">• The Company reserves the right to:</div>
        <div class="sub-item">o Monitor the use of the Devices, including internet activity, emails, and file transfers, to ensure compliance with this Agreement and Company policies.</div>
        <div class="sub-item">o Access, inspect, or retrieve the Devices and their contents at any time, with or without notice.</div>
    </div>
    
    <div class="section">
        <div class="section-title">7. Return of Devices</div>
        <div class="subsection">• Upon termination of employment or at the Company\'s request, the Employee agrees to:</div>
        <div class="sub-item">o Return all Devices and related accessories in good working condition.</div>
        <div class="sub-item">o Delete any personal data from the Devices before returning them.</div>
        <div class="sub-item">o Cooperate with the Company to ensure a smooth transition of data and accounts.</div>
    </div>
    
    <div class="section">
        <div class="section-title">8. Liability</div>
        <div class="subsection">• The Employee is responsible for:</div>
        <div class="sub-item">o The cost of repairing or replacing the Devices if lost, stolen, or damaged due to negligence or misuse.</div>
        <div class="sub-item">o Any fines, penalties, or legal liabilities resulting from unauthorized or illegal use of the Devices.</div>
    </div>
    
    <div class="section">
        <div class="section-title">9. Acknowledgment</div>
        <div class="subsection">• The Employee acknowledges that they have read, understood, and agree to comply with the terms of this Agreement. Failure to comply may result in disciplinary action, up to and including termination of employment.</div>
    </div>

    <div class="page-break"></div>
    
    <div class="section">
        <div class="section-title">10. Amendments</div>
        <div class="subsection">• The Company reserves the right to modify this Agreement at any time. The Employee will be notified of any changes and is expected to comply with the updated terms.</div>
    </div>
    
    <div class="signature-section">
        <div class="section-title">Signatures</div>
        
        <div class="signature-block">
            <strong>Employee:</strong><br>
            Name: {{ employee_name }}<br>
            Signature: <span style="padding-top: 60px;" class="signature-line"></span><br>
            Date: <span style="padding-top: 20px;" class="date-line"></span>
        </div>
        
        <div class="signature-block">
            <strong>Company Representative:</strong><br>
            Name: {{ rep_employee_name }}<br>
            Title: {{ rep_emptitle }}<br>
            Signature: <span style="padding-top: 80px;" class="signature-line"></span><br>
            Date: <span style="padding-top: 20px;" class="date-line"></span>
        </div>
    </div>
    
    <div class="final-note">
        This Agreement ensures the proper use and protection of Company-issued Devices while clarifying the responsibilities of both the Company and the Employee.
    </div>
</body>
</html>';

        // Get the main end user letter data
        $data = DB::table('end_user_letter')
            ->leftJoin('companies', 'end_user_letter.company_id', '=', 'companies.id')
            ->leftJoin('departments', 'end_user_letter.department_id', '=', 'departments.id')
            ->leftJoin('employees as emp', 'end_user_letter.emp_id', '=', 'emp.id')
            ->leftJoin('employees as rep_emp', 'end_user_letter.rep_emp_id', '=', 'rep_emp.id')
            ->leftJoin('job_titles', 'rep_emp.emp_job_code', '=', 'job_titles.id')
            ->select('end_user_letter.*',
                    'emp.emp_name_with_initial AS employee_name',
                    'rep_emp.emp_name_with_initial AS rep_employee_name',
                    'emp.emp_national_id AS emp_national_id',
                    'emp.emp_address AS emp_address',
                    'job_titles.title AS rep_emptitle',
                    'companies.*',
                    'departments.name AS department')
            ->where('end_user_letter.id', $id)
            ->first();

        // Get all devices assigned to the employee
        $devices = [];
        if ($data) {
            $devices = DB::table('employee_assigned_devices')
                ->select('device_type', 'model_number', 'serial_number', 'other_ref_number')
                ->where('emp_id', $data->emp_id)
                ->get();
        }

        if ($data) {
            $service = $data;
            
            // Build device table rows
            $deviceRows = '';
            if ($devices->isNotEmpty()) {
                foreach ($devices as $device) {
                    $deviceRows .= '<tr>
                        <td>' . ($device->device_type ?? '') . '</td>
                        <td>' . ($device->model_number ?? '') . '</td>
                        <td>' . ($device->serial_number ?? '') . '</td>
                        <td>' . ($device->other_ref_number ?? '') . '</td>
                    </tr>';
                }
            } else {
                // If no devices found, show empty row
                $deviceRows = '<tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>';
            }
            
            // Replace device table placeholder
            $html = str_replace('{{ device_table_rows }}', $deviceRows, $html);
             
            // Replace other placeholders with actual data
            $html = str_replace('{{ company_name }}', $service->name ?? 'MS WAY LOGISTICS COMPANY (PVT) LTD', $html);
            $html = str_replace('{{ company_address }}', $service->address ?? 'NO. 40/4, METRO HOMES RESIDENCES, MALAY STREET, COLOMBO 02', $html);
            $html = str_replace('{{ employee_name }}', $service->employee_name ?? '[EMPLOYEE NAME]', $html);
            $html = str_replace('{{ emp_national_id }}', $service->emp_national_id ?? '[NIC NUMBER]', $html);
            $html = str_replace('{{ employee_address }}', $service->emp_address ?? '[EMPLOYEE ADDRESS]', $html);
            $html = str_replace('{{ effect_date }}', $service->effect_date ?? '[EFFECTIVE DATE]', $html);
            $html = str_replace('{{ rep_employee_name }}', $service->rep_employee_name ?? '[REPRESENTATIVE NAME]', $html);
            $html = str_replace('{{ rep_emptitle }}', $service->rep_emptitle ?? '[REPRESENTATIVE TITLE]', $html);
        }

        $pdf = PDF::loadHTML($html);

        // Set page orientation to portrait with proper margins matching the original PDF
        $pdf->setPaper('A4', 'portrait');
        
        // Return the PDF as base64-encoded data
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }
}