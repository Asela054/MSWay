<?php

namespace App\Services\ActivityManagement;

use App\ActivityManagement\VisitPlace;

class VisitPlaceService
{
    /**
     * Retrieve all active visit places with related employee data.
     */
    public function getAllPlaces()
    {
        return VisitPlace::join('employees', 'visit_places.emp_id', '=', 'employees.emp_id')
            ->where('visit_places.status', '!=', 3)
            ->orderBy('visit_places.id', 'desc')
            ->select(
                'visit_places.*',
                'employees.emp_name_with_initial',
                'employees.calling_name'
            )
            ->get();
    }

    /**
     * Store a newly created visit place.
     */
    public function store(array $data)
    {
        $visitPlace = new VisitPlace;
        $visitPlace->emp_id = $data['employee'] ?? null;
        $visitPlace->place = $data['place'] ?? null;
        $visitPlace->date = $data['date'] ?? null;
        $visitPlace->time = $data['time'] ?? null;
        $visitPlace->latitude = $data['latitude'] ?? null;
        $visitPlace->longitude = $data['longitude'] ?? null;
        $visitPlace->remark = $data['remark'] ?? null;
        $visitPlace->status = 1;
        $visitPlace->save();

        return $visitPlace;
    }

    /**
     * Find an visit place by ID, including employee display name.
     */
    public function find($id)
    {
        return VisitPlace::join('employees', 'visit_places.emp_id', '=', 'employees.emp_id')
            ->where('visit_places.id', $id)
            ->select(
                'visit_places.*',
                'employees.emp_name_with_initial',
                'employees.calling_name'
            )
            ->firstOrFail();
    }

    /**
     * Update an existing visit place.
     */
    public function update($id, array $data)
    {
        VisitPlace::whereId($id)->update([
            'emp_id' => $data['employee'] ?? null,
            'place' => $data['place'] ?? null,
            'date' => $data['date'] ?? null,
            'time' => $data['time'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'remark' => $data['remark'] ?? null,
        ]);
    }

    /**
     * Soft-delete an visit place by setting its status to 3.
     */
    public function destroy($id)
    {
        $visitPlace = VisitPlace::findOrFail($id);
        $visitPlace->status = 3;
        $visitPlace->save();
    }
}
