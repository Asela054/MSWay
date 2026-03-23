<?php

namespace App\Http\Controllers;

use App\Company;
use App\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('company-list');

        if(!$permission) {
            abort(403);
        }

        $company = Company::orderBy('id', 'asc')->paginate(10);
        return view('Organization.company', compact('company'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('company-create');
        if(!$permission) {
            return response()->json(['errors' => array('You do not have permission to create company.')]);
        }

        $rules = array(
            'name' => 'required',
            'code' => 'required',
            'address' => 'required',
            'email' => 'required',
            'land' => 'required|Numeric',
            'mobile' => 'required|Numeric'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $company = new Company;
        $company->name = $request->input('name');
        $company->code = $request->input('code');
        $company->address = $request->input('address');
        $company->mobile = $request->input('mobile');
        $company->land = $request->input('land');
        $company->email = $request->input('email');
        $company->domain_name = $request->input('domain_name');
        $company->epf = $request->input('epf');
        $company->etf = $request->input('etf');
        $company->bank_account_name = $request->input('account_name');
        $company->bank_account_number = $request->input('account_no');
        $company->bank_account_branch_code = $request->input('account_branchcode');
        $company->employer_number = $request->input('employeeno');
        $company->zone_code = $request->input('zone_code');
        $company->ref_no = $request->input('ref_no');
        $company->vat_reg_no = $request->input('vat_reg_no');
        $company->svat_no = $request->input('svat_no');

        if ($request->hasFile('logo')) {
            $companyName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->input('name'));
            $extension = $request->file('logo')->getClientOriginalExtension();
            $fileName = $companyName . '.' . $extension;
            $request->file('logo')->move(public_path('images'), $fileName);
            $company->logo = 'images/' . $fileName;
        }
    
        $company->save();

        return response()->json(['success' => 'Company Added successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Company $branch
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('company-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = Company::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $user = Auth::user();
        $permission = $user->can('company-edit');
        if(!$permission) {
            return response()->json(['errors' => array('You do not have permission to update company.')]);
        }

        $rules = array(
            'name' => 'required',
            'code' => 'required',
            'mobile' => 'required|Numeric'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name' => $request->name,
            'code' => $request->code,
            'address' => $request->address,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'domain_name' => $request->domain_name,
            'land' => $request->land,
            'etf' => $request->etf,
            'epf' => $request->epf,
            'bank_account_name' => $request->account_name,
            'bank_account_number' => $request->account_no,
            'bank_account_branch_code' => $request->account_branchcode,
            'employer_number' => $request->employeeno,
            'zone_code' => $request->zone_code,
            'ref_no' => $request->ref_no,
            'vat_reg_no' => $request->vat_reg_no,
            'svat_no' => $request->svat_no
        );

        if ($request->hasFile('logo')) {
            $companyName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->name);
            $extension = $request->file('logo')->getClientOriginalExtension();
            $fileName = $companyName . '.' . $extension;
            $request->file('logo')->move(public_path('images'), $fileName);
            $form_data['logo'] = 'images/' . $fileName;

        } elseif ($request->input('remove_logo') == '1') {
            $existing = Company::find($request->hidden_id);
            if ($existing && $existing->logo) {
                $oldPath = public_path($existing->logo);
                if (file_exists($oldPath)) {
                    unlink($oldPath); 
                }
            }
            $form_data['logo'] = null;

        } else {
            $existing = Company::find($request->hidden_id);
            if ($existing && $existing->logo) {
                $form_data['logo'] = $existing->logo;
            }
        }

        Company::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Company is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('company-delete');
        if(!$permission) {
            return response()->json(['errors' => array('You do not have permission to remove company.')]);
        }

        $data = Company::findOrFail($id);
        $data->delete();
    }

  
}
