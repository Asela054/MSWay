<?php

namespace App\Http\Controllers\ERP_KT;

use App\Http\Controllers\Controller;
use App\ERP_KT\Inquiry;
use App\ERP_KT\InquiryDetail;
use App\ERP_KT\Customer;
use Illuminate\Http\Request;
use Validator;

class ERPInquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('kt-inquiry-list');
        if (!$permission) {
            abort(403);
        }

        $inquiries = Inquiry::where('status', '!=', 3)
            ->orderBy('id', 'asc')
            ->get();
        return view('ERP_KT.inquiry', compact('inquiries'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('kt-inquiry-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'customer_id'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'customer_id' => $request->customer_id,
            'date' => $request->date,
            'remarks' => $request->remarks,
        );

        $inquiry = new Inquiry;
        $inquiry->customer_id = $request->customer_id; 
        $inquiry->date = $request->input('date');
        $inquiry->remarks = $request->input('remarks');
        $inquiry->status = '1';
        $inquiry->save();

        if ($request->has('detail_inquiry')) {
            foreach ($request->detail_inquiry as $detail) {
                if (!empty($detail)) {
                    InquiryDetail::create([
                        'inquiry_id' => $inquiry->id,
                        'inquiry'    => $detail,
                    ]);
                }
            }
        }

        return response()->json(['success' => 'Inquiry Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-inquiry-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = Inquiry::findOrFail($id);
            $details = InquiryDetail::where('inquiry_id', $id)->get();
            return response()->json(['result' => $data, 'details' => $details]);
        }
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('kt-inquiry-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'customer_id'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'customer_id' => $request->customer_id, 
            'date' => $request->date,
            'remarks' => $request->remarks,
        );

        Inquiry::whereId($request->hidden_id)->update($form_data);

        $existing_detail_ids = [];

        if ($request->has('detail_inquiry')) {
            foreach ($request->detail_inquiry as $index => $detail) {
                if (!empty($detail)) {
                    $detail_id = $request->detail_id[$index] ?? null;
                    if ($detail_id) {
                        $inqDet = InquiryDetail::where('id', $detail_id)->where('inquiry_id', $request->hidden_id)->first();
                        if ($inqDet) {
                            $inqDet->inquiry = $detail;
                            $inqDet->save();
                            $existing_detail_ids[] = $detail_id;
                        }
                    } else {
                        $newInqDet = InquiryDetail::create([
                            'inquiry_id' => $request->hidden_id,
                            'inquiry'    => $detail,
                        ]);
                        $existing_detail_ids[] = $newInqDet->id;
                    }
                }
            }
        }

        // Delete removed details
        InquiryDetail::where('inquiry_id', $request->hidden_id)
            ->whereNotIn('id', $existing_detail_ids)
            ->delete();

        return response()->json(['success' => 'Inquiry Data Updated Successfully ']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-inquiry-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = Inquiry::findOrFail($id);
        $data->status = 3;
        $data->save();

        return response()->json(['success' => 'Inquiry Deleted Successfully.']);
    }

    //searches customers by name or ID
    public function customerList(Request $request)
    {
        $search = $request->term;
        $customers = Customer::where('name', 'like', '%' . $search . '%')
            ->orWhere('id', 'like', '%' . $search . '%')
            ->get(['id', 'name']);

        $results = $customers->map(function ($c) {
            return ['id' => $c->id, 'text' => $c->name];
        });

        return response()->json(['results' => $results]);
    }
}
