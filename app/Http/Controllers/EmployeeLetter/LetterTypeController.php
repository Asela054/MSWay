<?php

namespace App\Http\Controllers\EmployeeLetter;

use App\Http\Controllers\Controller;
use App\EmployeeLetter\LetterType;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class LetterTypeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('letterType-list');
        if (!$permission) {
            abort(403);
        }

        $letter_type = LetterType::where('status', '!=', 3)->orderBy('id', 'asc')->get();
        return view('EmployeeLetter.letterType', compact('letter_type'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('letterType-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'letter_type'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $letter_type = new LetterType;
        $letter_type->letter_type = $request->input('letter_type');
        $letter_type->remarks = $request->input('remarks');
        $letter_type->save();

        return response()->json(['success' => 'Letter Type Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('letterType-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = LetterType::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, LetterType $letter_type)
    {
        $user = auth()->user();
        $permission = $user->can('letterType-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'letter_type'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'letter_type'    =>  $request->letter_type,
            'remarks'    =>  $request->remarks
        );
        LetterType::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('letterType-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            $data = LetterType::findOrFail($id);
            $data->status = 3;
            $data->save();

            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete record'], 500);
        }
    }
}
