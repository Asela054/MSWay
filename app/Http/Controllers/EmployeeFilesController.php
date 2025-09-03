<?php

namespace App\Http\Controllers;

use App\EmployeeAttachment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeFilesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($id)
    {
        $permission = \Auth::user()->can('employee-list');
        if (!$permission) {
            abort(403);
        }

        $attachments = EmployeeAttachment::with('attachment_type_rel')
            ->where('emp_id',$id)
            ->where('emp_ath_type', null)
            ->get();

        return view('Employee.viewFiles', compact('id', 'attachments'));
    }
}
