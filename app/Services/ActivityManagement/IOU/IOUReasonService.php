<?php

namespace App\Services\ActivityManagement\IOU;

use App\ActivityManagement\IOU\IOUReason;

class IOUReasonService
{
    /**
     * Retrieve all active IOU reasons.
     */
    public function getAllReasons()
    {
        return IOUReason::orderBy('id', 'asc')
            ->where('status', '!=', 3)
            ->get();
    }

    /**
     * Store a newly created IOU reason.
     */
    public function store(array $data)
    {
        $iouReason = new IOUReason;
        $iouReason->reason          = $data['reason'];
        $iouReason->estimate_amount = $data['estimate_amount'] ?? null;
        $iouReason->remark          = $data['remark'] ?? null;
        $iouReason->status          = 1;
        $iouReason->save();

        return $iouReason;
    }

    /**
     * Find an IOU reason by ID.
     */
    public function find($id)
    {
        return IOUReason::findOrFail($id);
    }

    /**
     * Update an existing IOU reason.
     */
    public function update($id, array $data)
    {
        IOUReason::whereId($id)->update([
            'reason'          => $data['reason'],
            'estimate_amount' => $data['estimate_amount'] ?? null,
            'remark'          => $data['remark'] ?? null,
        ]);
    }

    /**
     * Soft-delete an IOU reason by setting its status to 3.
     */
    public function destroy($id)
    {
        $iouReason = IOUReason::findOrFail($id);
        $iouReason->status = 3;
        $iouReason->save();
    }
}