<?php

namespace App\Http\Controllers\Api\ActivityManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use App\Services\ActivityManagement\VisitPlaceService;

class VistplaceAPIController extends Controller
{
    protected $visitPlaceService;

    public function __construct(VisitPlaceService $visitPlaceService)
    {
        $this->visitPlaceService = $visitPlaceService;

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
     * List all active visit places.
     */
    public function GetVisitPlaceList(Request $request)
    {
        try {
            $data = array(
                'visitplacelist' => $this->visitPlaceService->getAllPlaces()
            );

            return (new BaseController)->sendResponse($data, 'visitplacelist');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    /**
     * Create a new visit place.
     */
    public function StoreVisitPlace(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee'  => 'required',
            'place'     => 'required',
            'date'      => 'required',
            'time'      => 'required',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        try {
            $data = array(
                'visitplace' => $this->visitPlaceService->store($request->all())
            );

            return (new BaseController)->sendResponse($data, 'Visit place created successfully.');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    /**
     * Get a single visit place by id.
     */
    public function GetVisitPlace(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        try {
            $data = array(
                'visitplace' => $this->visitPlaceService->find($request->id)
            );

            return (new BaseController)->sendResponse($data, 'visitplace');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return (new BaseController)->sendError('Visit place not found.', array(), '404');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    /**
     * Update an existing visit place.
     */
    public function UpdateVisitPlace(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id'        => 'required',
            'employee'  => 'required',
            'place'     => 'required',
            'date'      => 'required',
            'time'      => 'required',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        try {
            $this->visitPlaceService->update($request->id, $request->all());

            return (new BaseController)->sendResponse(array(), 'Visit place updated successfully.');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }

    /**
     * Soft-delete a visit place (status = 3).
     */
    public function DeleteVisitPlace(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        try {
            $this->visitPlaceService->destroy($request->id);

            return (new BaseController)->sendResponse(array(), 'Visit place deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return (new BaseController)->sendError('Visit place not found.', array(), '404');
        } catch (\Exception $e) {
            return (new BaseController)->sendError('Server Error.', $e->getMessage(), '500');
        }
    }
}