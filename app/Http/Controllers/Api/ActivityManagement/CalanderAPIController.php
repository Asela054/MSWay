<?php

namespace App\Http\Controllers\Api\ActivityManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use App\Services\ActivityManagement\EmpCalendarService;

class CalanderAPIController extends Controller
{
    protected $empCalendarService;

         public function __construct(EmpCalendarService $empCalendarService)
    {
            $this->empCalendarService = $empCalendarService;

            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token');
                header('Access-Control-Max-Age: 86400');
                header('content-type: application/json; charset=utf-8');
            }

            if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
                $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));
            }

            // Access-Control headers are received during OPTIONS requests
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
                }

                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                }

                exit(0);
            }
    }

    /**
     * List all active employee calendar schedules.
     */
    public function GetScheduleList(Request $request)
    {
        try {
            $data = array(
                'schedulelist' => $this->empCalendarService->getAllShedules()
            );

            return (new BaseController)->sendResponse($data, 'schedulelist');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    /**
     * Create a new employee calendar schedule.
     */
    public function StoreSchedule(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee' => 'required',
            'title'    => 'required',
            'date'     => 'required',
            'time'     => 'required'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        try {
            $schedule = $this->empCalendarService->store($request->all());

            $data = array(
                'schedule' => $schedule
            );

            return (new BaseController)->sendResponse($data, 'Schedule created successfully.');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    /**
     * Get a single employee calendar schedule by id.
     */
    public function GetSchedule(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        try {
            $data = array(
                'schedule' => $this->empCalendarService->find($request->id)
            );

            return (new BaseController)->sendResponse($data, 'schedule');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return (new BaseController)->sendError('Schedule not found.', array(), '404');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    /**
     * Update an existing employee calendar schedule.
     */
    public function UpdateSchedule(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id'       => 'required',
            'employee' => 'required',
            'title'    => 'required',
            'date'     => 'required',
            'time'     => 'required'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        try {
            $this->empCalendarService->update($request->id, $request->all());

            return (new BaseController)->sendResponse(array(), 'Schedule updated successfully.');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    /**
     * Soft-delete an employee calendar schedule (status = 3).
     */
    public function DeleteSchedule(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        try {
            $this->empCalendarService->destroy($request->id);

            return (new BaseController)->sendResponse(array(), 'Schedule deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return (new BaseController)->sendError('Schedule not found.', array(), '404');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }
}