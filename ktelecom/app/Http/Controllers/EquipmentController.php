<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetEquipmentRequest;
use App\Http\Requests\GetEquipmentTypesRequest;
use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use App\Http\Resources\EquipmentCollection;
use App\Http\Resources\EquipmentResource;
use App\Http\Resources\EquipmentTypeCollection;
use App\Http\Resources\GeneralResponseResource;
use App\Models\Equipment;
use App\Services\EquipmentService;
use App\Services\ErrorWrapperService;
use App\Services\MaskValidationService;

class EquipmentController extends Controller
{

    private $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }

    public function store(StoreEquipmentRequest $request)
    {
        $requestArray = $request->all();
        count($request->errors) === 0 ? $validatedArray = $requestArray : $validatedArray = $request->passed;
        $result = $this->equipmentService->storeEquipment($validatedArray);
        $ids = $result['insertedDataIds'];
        $collection = new EquipmentCollection(
            Equipment::whereIn('id', $ids)->get()->mapWithKeys(function ($value) use ($ids) {
                foreach ($ids as $subKey => $subValue) {
                    if ($value->id == $subValue) return ["input_$subKey" => $value];
                }
            })
        );
        
        return new GeneralResponseResource([
            'success' => $collection,
            'errors' => (new ErrorWrapperService([$result['failedInsertions'], $request->errors]))->errors
        ]);
    }

    public function update(UpdateEquipmentRequest $request, $id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {
            return getError(['ID must be of type integer']);
        }
        if (!Equipment::where('id', $id)->exists()) {
            return getError(['Record with such ID does not exists']);
        }
        if (count($request->validated()) === 0) {
            return getError(['Request array is empty']);
        }
        if (count($request->errors) !== 0) {
            return getError($request->errors);
        }
        $result = $this->equipmentService->updateEquipment($request->validated(), $id);
        
        if ($result["success"] == true) {
            return getSuccess(new EquipmentResource($result["data"]));
        } else {
            return getError($result['error']);
        }
    }

    public function destroy($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {
            return getError(['ID must be of type integer']);
        }
        if (!Equipment::where('id', $id)->exists()) {
            return getError(['Record with such ID does not exists']);
        }
        $result = $this->equipmentService->deleteEquipment($id);
        if ($result["success"] == true) {
            return getSuccess(new EquipmentResource($result["data"]));
        } else {
            return getError($result['error']);
        }
    }

    public function index(GetEquipmentRequest $request)
    {
        $params = request()->all();
        if (count($request->errors) !== 0) {
            return getError($request->errors);
        }
        $result = $this->equipmentService->getEquipment($params);

        return getSuccess(new EquipmentCollection($result));
    }

    public function getEquipmentTypes(GetEquipmentTypesRequest $request)
    {
        $params = request()->all();
        if (count($request->errors) !== 0) return getError($request->errors);
        $result = $this->equipmentService->getEquipmentTypes($params);

        return getSuccess(new EquipmentTypeCollection($result));
    }
}
