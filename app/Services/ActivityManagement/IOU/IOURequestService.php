<?php

namespace App\Services\ActivityManagement\IOU;

use App\ActivityManagement\IOU\IOURequest;

class IOURequestService
{
    /**
     * Retrieve all active IOU requests with related reason and employee data.
     */
    public function getAllRequests()
    {
        return IOURequest::join('iou_reasons', 'iou_requests.reason_id', '=', 'iou_reasons.id')
            ->join('employees', 'iou_requests.emp_id', '=', 'employees.emp_id')
            ->where('iou_requests.status', '!=', 3)
            ->orderBy('iou_requests.id', 'desc')
            ->select(
                'iou_requests.*',
                'iou_reasons.reason',
                'employees.emp_name_with_initial',
                'employees.calling_name'
            )
            ->get();
    }

    /**
     * Store a newly created IOU request.
     */
    public function store(array $data)
    {
        $iouRequest = new IOURequest;
        $iouRequest->reason_id = $data['reason_id'];
        $iouRequest->emp_id    = $data['employee'] ?? null;
        $iouRequest->amount    = $data['amount'] ?? null;
        $iouRequest->remark    = $data['remark'] ?? null;
        $iouRequest->status    = 1;
        $iouRequest->save();

        return $iouRequest;
    }

    /**
     * Find an IOU request by ID, including employee display name.
     */
    public function find($id)
    {
        return IOURequest::join('employees', 'iou_requests.emp_id', '=', 'employees.emp_id')
            ->where('iou_requests.id', $id)
            ->select(
                'iou_requests.*',
                'employees.emp_name_with_initial',
                'employees.calling_name'
            )
            ->firstOrFail();
    }

    /**
     * Update an existing IOU request.
     */
    public function update($id, array $data)
    {
        IOURequest::whereId($id)->update([
            'reason_id' => $data['reason_id'],
            'emp_id'    => $data['employee'] ?? null,
            'amount'    => $data['amount'] ?? null,
            'remark'    => $data['remark'] ?? null,
        ]);
    }

    /**
     * Soft-delete an IOU request by setting its status to 3.
     */
    public function destroy($id)
    {
        $iouRequest = IOURequest::findOrFail($id);
        $iouRequest->status = 3;
        $iouRequest->save();
    }
}
