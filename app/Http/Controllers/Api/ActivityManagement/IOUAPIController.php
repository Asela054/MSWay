<?php

namespace App\Http\Controllers\Api\ActivityManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ActivityManagement\IOU\IOUReason;
use App\Http\Controllers\Api\BaseController;
use App\Services\ActivityManagement\IOU\IOURequestService;

class IOUAPIController extends Controller
{
    protected $iouRequestService;

    public function __construct(IOURequestService $iouRequestService)
    {
        $this->iouRequestService = $iouRequestService;

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
     * List all active IOU requests.
     */
    public function GetIOURequestList(Request $request)
    {
        try {
            $data = array(
                'ioulist' => $this->iouRequestService->getAllRequests()
            );

            return (new BaseController)->sendResponse($data, 'ioulist');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    /**
     * Create a new IOU request.
     */
    public function StoreIOURequest(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'reason_id' => 'required',
            'employee'  => 'required',
            'amount'    => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        try {
            $data = array(
                'iourequest' => $this->iouRequestService->store($request->all())
            );

            return (new BaseController)->sendResponse($data, 'IOU request created successfully.');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    /**
     * Get a single IOU request by id.
     */
    public function GetIOURequest(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        try {
            $data = array(
                'iourequest' => $this->iouRequestService->find($request->id)
            );

            return (new BaseController)->sendResponse($data, 'iourequest');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return (new BaseController)->sendError('IOU request not found.', array(), '404');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    /**
     * Update an existing IOU request.
     */
    public function UpdateIOURequest(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id'        => 'required',
            'reason_id' => 'required',
            'employee'  => 'required',
            'amount'    => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        try {
            $this->iouRequestService->update($request->id, $request->all());

            return (new BaseController)->sendResponse(array(), 'IOU request updated successfully.');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    /**
     * Soft-delete an IOU request (status = 3).
     */
    public function DeleteIOURequest(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        try {
            $this->iouRequestService->destroy($request->id);

            return (new BaseController)->sendResponse(array(), 'IOU request deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return (new BaseController)->sendError('IOU request not found.', array(), '404');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    public function getAllReasons()
    {
         $query = IOUReason::orderBy('id', 'asc')
            ->where('status', '!=', 3)
            ->get();
         $data = array(
            'reasonlist' => $query
        );
        return (new BaseController)->sendResponse($data, 'reasonlist');
    }
    
}