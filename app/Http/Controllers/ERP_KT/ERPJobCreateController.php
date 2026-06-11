<?php

namespace App\Http\Controllers\ERP_KT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ERPJobCreateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('kt-job-list');
        if (!$permission) {
            abort(403);
        }
        return view('ERP_KT.job_create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('kt-job-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $tableData = $request->input('tableData');
        if (empty($tableData) || !is_array($tableData)) {
            return response()->json(['errors' => ['No data provided.']]);
        }

        DB::beginTransaction();
        try {
            $firstRow = $tableData[0];
            $jobId = DB::table('kt_job_inquiry')->insertGetId([
                'customer_id' => $firstRow['col_1'],
                'inquiry_id'  => $firstRow['col_4'],
                'start_from'  => $firstRow['col_2'],
                'end_at'      => $firstRow['col_3'],
                'reading_hours' => round(Carbon::parse($firstRow['col_2'])->diffInMinutes(Carbon::parse($firstRow['col_3'])) / 60, 2),
                'job_description' => !empty($firstRow['col_8']) ? trim($firstRow['col_8']) : null,
                'remarks'         => !empty($firstRow['col_9']) ? trim($firstRow['col_9']) : null,
                'created_at'  => Carbon::now()->toDateTimeString(),
                'updated_at'  => Carbon::now()->toDateTimeString(),
            ]);
            foreach ($tableData as $row) {
                DB::table('kt_job_details')->insert([
                    'job_id'     => $jobId,
                    'emp_id'     => !empty($row['col_6']) ? trim($row['col_6']) : null,
                    'job_title'  => !empty($row['col_7']) ? trim($row['col_7']) : null,
                    'machine_id' => !empty($row['col_5']) ? trim($row['col_5']) : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
            DB::commit();
            return response()->json(['success' => 'Job Created Successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => ['Failed: ' . $e->getMessage()]]);
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-job-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $job = DB::table('kt_job_inquiry AS j')
            ->leftJoin('kt_inquiry_details AS d', 'd.id', '=', 'j.inquiry_id')
            ->leftJoin('kt_inquiries AS i',  'i.id', '=', 'j.inquiry_id')
            ->leftJoin('kt_customer AS c',  'c.id', '=', 'j.customer_id')
            ->where('j.id', $id)
            ->select('j.id', 'j.customer_id', 'c.name as customer_name', 'j.inquiry_id', 'd.inquiry', 'j.start_from', 'j.end_at','j.job_description', 'j.remarks')
            ->first();

        $details = DB::table('kt_job_details AS jd')
            ->leftJoin('employees AS e', 'e.emp_id', '=', 'jd.emp_id')
            ->leftJoin('kt_machines AS m', 'm.id', '=', 'jd.machine_id')
            ->leftJoin('job_titles AS jt', 'jt.id', '=', 'jd.job_title')
            ->where('jd.job_id', $id)
            ->select(
                'jd.id',
                'jd.machine_id',
                'm.machine_name',
                'jd.emp_id',
                DB::raw("CONCAT(e.emp_name_with_initial,' - ',e.calling_name) as emp_name"),
                'jd.job_title',
                'jt.title as job_title_name'
            )->get();
        return response()->json(['result' => $job, 'details' => $details]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('kt-job-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $tableData = $request->input('tableData');

        if (empty($tableData) || !is_array($tableData)) {
            return response()->json(['errors' => ['No data provided.']]);
        }

        $firstRow = $tableData[0];
        $jobId = $request->input('hidden_id');

        DB::beginTransaction();
        try {
            DB::table('kt_job_inquiry')->where('id', $jobId)->update([
                'customer_id'     => $firstRow['col_1'],
                'inquiry_id'      => $firstRow['col_4'],
                'start_from'      => $firstRow['col_2'],
                'end_at'          => $firstRow['col_3'],
                'reading_hours'   => round(Carbon::parse($firstRow['col_2'])->diffInMinutes(Carbon::parse($firstRow['col_3'])) / 60, 2),
                'job_description' => !empty($firstRow['col_8']) ? trim($firstRow['col_8']) : null,
                'remarks'         => !empty($firstRow['col_9']) ? trim($firstRow['col_9']) : null,
                'updated_at'      => Carbon::now()->toDateTimeString(),
            ]);

            DB::table('kt_job_details')->where('job_id', $jobId)->delete();

            foreach ($tableData as $detailRow) {   
                DB::table('kt_job_details')->insert([
                    'job_id'     => $jobId,
                    'emp_id'     => !empty($detailRow['col_6']) ? trim($detailRow['col_6']) : null,
                    'job_title'  => !empty($detailRow['col_7']) ? trim($detailRow['col_7']) : null,
                    'machine_id' => !empty($detailRow['col_5']) ? trim($detailRow['col_5']) : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            DB::commit();
            return response()->json(['success' => 'Job Updated Successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => ['Failed: ' . $e->getMessage()]]);
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-job-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        DB::table('kt_job_inquiry')->where('id', $id)->update(['status' => 3]);

        return response()->json(['success' => 'Job Deleted Successfully.']);
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
                'more' => count($customers) == $resultCount
            ]
        ]);
    }

    //searches induiry list and filter approved inquires only, optionally filtered by customer
    public function inquiryList(Request $request)
    {
        $search     = $request->term;
        $customerId = $request->customer_id;
        $page = $request->get('page', 1);
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;

        $query = DB::table('kt_inquiry_details as d')
            ->join('kt_inquiries as i', 'i.id', '=', 'd.inquiry_id')
            ->where('d.approve_status', 1)
            ->select('d.id', DB::raw('d.inquiry as text'));

        if (!empty($search)) {
            $query->where('d.inquiry', 'like', '%' . $search . '%');
        }

        if (!empty($customerId)) {
            $query->where('i.customer_id', $customerId);
        }

        $inquiries = $query->orderBy('d.inquiry')
            ->skip($offset)
            ->take($resultCount)
            ->get();

        return response()->json([
            'results' => $inquiries,
            'pagination' => ['more' => count($inquiries) == $resultCount]
        ]);
    }

    public function machineList(Request $request)
    {
        $search = $request->term;
        $page = $request->get('page', 1);
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;

        $query = DB::table('kt_machines')
            ->where('status', 1)
            ->select('id', DB::raw('machine_name as text'));

        if (!empty($search)) {
            $query->where('machine_name', 'like', '%' . $search . '%');
        }

        $machines = $query->orderBy('machine_name')
            ->skip($offset)
            ->take($resultCount)
            ->get();

        return response()->json([
            'results' => $machines,
            'pagination' => ['more' => count($machines) == $resultCount]
        ]);
    }

    public function jobTitleList(Request $request)
    {
        $search = $request->term;
        $page = $request->get('page', 1);
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;

        $query = DB::table('job_titles')->select('id', DB::raw('title as text'));

        if (!empty($search)) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $titles = $query->orderBy('title')
            ->skip($offset)
            ->take($resultCount)
            ->get();

        return response()->json([
            'results' => $titles,
            'pagination' => ['more' => count($titles) == $resultCount]
        ]);
    }

    //job_title_id filter by employee
    public function employeeListByTitle(Request $request)
    {
        $search       = $request->term;
        $jobTitleId   = $request->job_title_id;
        $page = $request->get('page', 1);
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;

        $query = DB::table('employees AS e')
            ->join('job_titles AS jt', 'jt.id', '=', 'e.emp_job_code')
            ->where('e.deleted', 0)
            ->where('e.is_resigned', 0)
            ->select('e.emp_id as id', DB::raw("CONCAT(e.emp_name_with_initial,' - ',e.calling_name) as text"));

        if (!empty($jobTitleId)) {
            $query->where('e.emp_job_code', $jobTitleId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('e.emp_name_with_initial', 'like', '%' . $search . '%')
                    ->orWhere('e.calling_name', 'like', '%' . $search . '%');
            });
        }

        $employees = $query->orderBy('e.emp_name_with_initial')
            ->skip($offset)
            ->take($resultCount)
            ->get();

        return response()->json([
            'results' => $employees,
            'pagination' => ['more' => count($employees) == $resultCount]
        ]);
    }
}
