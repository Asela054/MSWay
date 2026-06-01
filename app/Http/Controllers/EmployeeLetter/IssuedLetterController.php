<?php

namespace App\Http\Controllers\EmployeeLetter;

use App\Http\Controllers\Controller;
use App\EmployeeLetter\IssuedLetter;
use App\EmployeeLetter\LetterTemplate;
use App\EmployeeLetter\LetterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;
use Dompdf\Dompdf;
use Dompdf\Options;

class IssuedLetterController extends Controller
{
    const PLACEHOLDERS = [
        '{emp_name_with_initial}',
        '{calling_name}',
        '{company_name}',
        '{job_title}',
        '{department}',
    ];

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('issuedLetter-list');
        if (!$permission) {
            abort(403);
        }

        $letter_types = LetterType::whereHas('letterTemplates', function ($q) {
            $q->where('is_active', 1);
        })->orderBy('letter_type')->get();

        $issued_letters = IssuedLetter::with([])
            ->select(
                'issued_letters.*',
                'employees.emp_name_with_initial',
                'letter_types.letter_type',
                'letter_templates.name as template_name'
            )
            ->leftJoin('employees',        'issued_letters.employee_id',   '=', 'employees.emp_id')
            ->leftJoin('letter_types',     'issued_letters.letter_type_id', '=', 'letter_types.id')
            ->leftJoin('letter_templates', 'issued_letters.template_id',   '=', 'letter_templates.id')
            ->orderBy('issued_letters.id', 'desc')
            ->get();

        return view('EmployeeLetter.issueLetter', compact('letter_types', 'issued_letters'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('issuedLetter-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $error = Validator::make($request->all(), [
            'letter_type_id' => 'required|integer|exists:letter_types,id',
            'template_id'    => 'required|integer|exists:letter_templates,id',
            'employee_id'    => 'required|string|max:50',
            'issued_date'    => 'required|date',
            'content'        => 'required|string',
        ]);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        // Sanitize content before saving
        $content = $this->sanitizeContent($request->content);

        $issued_letter = new IssuedLetter;
        $issued_letter->letter_type_id = (int) $request->letter_type_id;
        $issued_letter->template_id    = (int) $request->template_id;
        $issued_letter->employee_id    = strip_tags(trim($request->employee_id));
        $issued_letter->content        = $this->sanitizeContent($request->content);
        $issued_letter->issued_date    = $request->issued_date;
        $issued_letter->issued_by      = Auth::id();
        $issued_letter->save();

        return response()->json(['success' => 'Letter issued successfully']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('issuedLetter-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = IssuedLetter::where('issued_letters.id', $id)
                ->leftJoin('employees', 'issued_letters.employee_id', '=', 'employees.emp_id')
                ->select('issued_letters.*', 'employees.emp_name_with_initial')
                ->firstOrFail();
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('issuedLetter-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $error = Validator::make($request->all(), [
            'content'     => 'required|string',
            'issued_date' => 'required|date',
        ]);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        // sanitize content before saving
        $content = $this->sanitizeContent($request->content);

        $issued_letter = IssuedLetter::findOrFail($request->hidden_id);
        $issued_letter->content     = $this->sanitizeContent($request->content);
        $issued_letter->issued_date = $request->issued_date;
        $issued_letter->save();
        return response()->json(['success' => 'Letter updated successfully']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('issuedLetter-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            IssuedLetter::findOrFail($id)->delete();
            return response()->json(['success' => 'Letter deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete record'], 500);
        }
    }

    // Load letter template
    public function loadTemplate(Request $request)
    {
        $error = Validator::make($request->all(), [
            'letter_type_id' => 'required|integer|exists:letter_types,id',
            'employee_id'    => 'required|string|max:50',
        ]);

        if ($error->fails()) {
            return response()->json(['success' => false, 'message' => $error->errors()->first()], 422);
        }
        $letterTypeId = (int) $request->letter_type_id;
        $employeeId   = strip_tags(trim($request->employee_id));

        // Get active template for this letter type
        $template = LetterTemplate::where('letter_type_id', $letterTypeId)
            ->where('is_active', 1)
            ->first();

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'No active template found for this letter type.'
            ], 404);
        }

        // Get employee details
        $employee = DB::table('employees')
            ->leftJoin('job_titles',   'employees.emp_job_code', '=', 'job_titles.id')
            ->leftJoin('departments',  'employees.emp_department', '=', 'departments.id')
            ->leftJoin('companies',    'employees.emp_company',    '=', 'companies.id')
            ->where('employees.emp_id', $employeeId)
            ->select(
                'employees.emp_name_with_initial',
                'employees.calling_name',
                'companies.name      as company_name',
                'job_titles.title    as job_title',
                'departments.name    as department'
            )
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        // Replace placeholders
        $replacements = [
            '{emp_name_with_initial}' => $employee->emp_name_with_initial ?? '',
            '{calling_name}'          => $employee->calling_name           ?? '',
            '{company_name}'          => $employee->company_name           ?? '',
            '{job_title}'             => $employee->job_title              ?? '',
            '{department}'            => $employee->department             ?? '',
        ];

        // Replace in template content — sanitize each value
        $content = $template->content;
        foreach ($replacements as $placeholder => $value) {
            $safe    = htmlspecialchars(strip_tags((string) $value), ENT_QUOTES, 'UTF-8');
            $content = str_replace($placeholder, $safe, $content);
        }

        return response()->json([
            'success'     => true,
            'content'     => $content,
            'template_id' => $template->id,
        ]);
    }

    public function printLetter($id)
    {
        $letter = IssuedLetter::findOrFail($id);

        // Sanitize content before PDF
        $content = $this->sanitizeContent($letter->content);

        $html = '<!DOCTYPE html>
        <html><head><meta charset="UTF-8">
        <style>
            body { font-family: "Times New Roman", serif; font-size: 13px;
                   line-height: 1.6; color: #000; margin: 0; padding: 20mm; }
            p { margin: 0 0 10px 0; }
            table { width: 100%; border-collapse: collapse; }
            td { padding: 4px; vertical-align: top; }
        </style>
        </head><body>' . $content . '</body></html>';

        // Load HTML to Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);

        // Set font directory
        $options->set('fontDir',   storage_path('fonts/'));
        $options->set('fontCache', storage_path('fonts/'));

        // Create Dompdf
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Build filename from letter_type and employee_name
        $letterType  = DB::table('letter_types')->where('id', $letter->letter_type_id)->value('letter_type') ?? 'Letter';
        $empName     = DB::table('employees')->where('emp_id', $letter->employee_id)->value('emp_name_with_initial') ?? $letter->employee_id;
        $filename    = preg_replace('/[^A-Za-z0-9_\-]/', '_', $letterType . '_' . $empName) . '.pdf';

        return response()->json(['pdf' => base64_encode($dompdf->output()), 'filename' => $filename]);
    }

    // Sanitize letter content
    private function sanitizeContent(string $content): string
    {
        // Remove any unresolved placeholders
        $content = preg_replace('/\{[a-z_]+\}/', '', $content);

        // Allow only safe HTML tags
        $allowed = '<p><br><strong><em><u><ul><ol><li><table><thead>'
            . '<tbody><tr><th><td><h1><h2><h3><h4><span><div>'
            . '<b><i><a>';

        return strip_tags($content, $allowed);
    }
}
