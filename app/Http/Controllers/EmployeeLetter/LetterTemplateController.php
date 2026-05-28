<?php

namespace App\Http\Controllers\EmployeeLetter;

use App\Http\Controllers\Controller;
use App\EmployeeLetter\LetterTemplate;
use App\EmployeeLetter\LetterType;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class LetterTemplateController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('letterTemplate-list');
        if (!$permission) {
            abort(403);
        }

        $letter_types     = LetterType::orderBy('letter_type')->get();
        $letter_templates = LetterTemplate::with('letterType')
            ->whereIn('is_active', [0, 1])
            ->orderBy('id', 'desc')
            ->get();

        // Allowed placeholders list ->for the UI hint panel
        $placeholders = [
            '{emp_name_with_initial}' => 'Employee Full Name',
            '{calling_name}'          => 'Calling Name',
            '{company_name}'          => 'Company Name',
            '{job_title}'             => 'Job Title',
            '{department}'            => 'Department',
        ];

        return view('EmployeeLetter.letterTemplate', compact('letter_types', 'letter_templates', 'placeholders'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('letterTemplate-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $error = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'letter_type_id' => 'required|exists:letter_types,id',
            'content'        => 'required|string',
        ]);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        // Deactivate previous template for this letter type
        LetterTemplate::where('letter_type_id', $request->letter_type_id)
            ->update(['is_active' => 0]);

        $letter_template = new LetterTemplate;
        $letter_template->name = $request->name;
        $letter_template->letter_type_id = $request->letter_type_id;
        $letter_template->content = $request->content;
        $letter_template->is_active = 1;
        $letter_template->created_by = Auth::id();
        $letter_template->save();

        return response()->json(['success' => 'Letter Template Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('letterTemplate-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = LetterTemplate::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('letterTemplate-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $error = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'letter_type_id' => 'required|exists:letter_types,id',
            'content'        => 'required|string',
        ]);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $letter_template = LetterTemplate::findOrFail($request->hidden_id);
        $letter_template->letter_type_id = $request->letter_type_id;
        $letter_template->name           = $request->name;
        $letter_template->content        = $request->content;
        $letter_template->created_by     = Auth::id();
        $letter_template->save();

        return response()->json(['success' => 'Letter Template successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('letterTemplate-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            $tpl = LetterTemplate::findOrFail($id);
            $tpl->is_active = 3;
            $tpl->save();
            return response()->json(['success' => 'Letter Template deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete record'], 500);
        }
    }

    // status button handler
    public function status($id)
    {
        $user = auth()->user();
        $permission = $user->can('letterTemplate-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            $tpl = LetterTemplate::findOrFail($id);
            
            if ($tpl->is_active == 1) {
                $tpl->is_active = 0;
                $tpl->save();
                return response()->json(['success' => 'Letter Template deactivated successfully.']);
            } else {
                LetterTemplate::where('letter_type_id', $tpl->letter_type_id)
                    ->update(['is_active' => 0]);
                
                $tpl->is_active = 1;
                $tpl->save();
                return response()->json(['success' => 'Letter Template activated successfully.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update status'], 500);
        }
    }
}
