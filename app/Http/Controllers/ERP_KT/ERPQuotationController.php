<?php

namespace App\Http\Controllers\ERP_KT;

use App\Http\Controllers\Controller;
use App\ERP_KT\Inquiry;
use App\ERP_KT\InquiryDetail;
use App\ERP_KT\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ERPQuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('kt-quotation-list');
        if (!$permission) {
            abort(403);
        }

        $inquiries = Inquiry::where('status', '!=', 3)
                              ->orderBy('id', 'asc')
                              ->get();
        return view('ERP_KT.quotation', compact('inquiries'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('kt-quotation-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'customer_id'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'customer_id'=>$request->customer_id,
            'date'=>$request->date,
            'remarks'=>$request->remarks,
        );

        $inquiry=new Inquiry;
        $inquiry->customer_id=$request->input('customer_id');
        $inquiry->date=$request->input('date');
        $inquiry->remarks=$request->input('remarks');       
        $inquiry->status='1';       
        $inquiry->save();

        if($request->has('detail_inquiry')) {
            foreach($request->detail_inquiry as $index => $detail) {
                if(!empty($detail)) {
                    InquiryDetail::create([
                        'inquiry_id' => $inquiry->id,
                        'inquiry'    => $detail,
                        'quotation'  => $request->detail_quotation[$index] ?? 0,
                    ]);
                }
            }
        }

        return response()->json(['success' => 'Quotation Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-quotation-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = Inquiry::findOrFail($id);
            $details = InquiryDetail::where('inquiry_id', $id)->get();
            return response()->json(['result' => $data, 'details' => $details]);
        }
    }

    public function update(Request $request)   
    {
        $user = auth()->user();
        $permission = $user->can('kt-quotation-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
          'customer_id'    =>  'nullable',
        );

        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'date'=>$request->date,
            'remarks'=>$request->remarks,
        );

        if($request->has('customer_id')) {
            $form_data['customer_id'] = $request->customer_id;
        }

        Inquiry::whereId($request->hidden_id)->update($form_data);

        $existing_detail_ids = [];

        if($request->has('detail_inquiry')) {
            foreach($request->detail_inquiry as $index => $detail) {
                if(!empty($detail)) {
                    $detail_id = $request->detail_id[$index] ?? null;
                    if ($detail_id) {
                        $inqDet = InquiryDetail::where('id', $detail_id)->where('inquiry_id', $request->hidden_id)->first();
                        if ($inqDet) {
                            $inqDet->inquiry = $detail;
                            $inqDet->quotation = $request->detail_quotation[$index] ?? 0;
                            $inqDet->save();
                            $existing_detail_ids[] = $detail_id;
                        }
                    } else {
                        $newInqDet = InquiryDetail::create([
                            'inquiry_id' => $request->hidden_id,
                            'inquiry'    => $detail,
                            'quotation'  => $request->detail_quotation[$index] ?? 0,
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

        return response()->json(['success' => 'Quotation Data Updated Successfully ']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-quotation-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = Inquiry::findOrFail($id);
        $data->status = 3;
        $data->save();

        return response()->json(['success' => 'Quotation Deleted Successfully.']);
    }

    //searches customers by name or ID
        public function customerList(Request $request)
    {
        $search = $request->term;
        $page = $request->get('page', 1);
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;

        $query = DB::table('kt_customer')
            ->select('id', DB::raw('name as text'));

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('id', 'LIKE', '%' . $search . '%');
            });
        }

        $customers = $query->orderBy('name')
            ->skip($offset)
            ->take($resultCount)
            ->get();

        return response()->json([
            'results' => $customers,
            'pagination' => [
                'more' => $customers->count() == $resultCount
            ]
        ]);
    }
}