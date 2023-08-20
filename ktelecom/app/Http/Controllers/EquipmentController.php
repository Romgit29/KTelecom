<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetEquipmentRequest;
use App\Http\Requests\GetEquipmentTypesRequest;
use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use App\Models\Equipment;
use App\Services\EquipmentService;

class EquipmentController extends Controller
{

    public function __construct( protected EquipmentService $equipmentService )
    {
    }

    public function store(StoreEquipmentRequest $request){
        $requestArray = $request->all();
        if( count( $requestArray ) === 0 ) return [ 'success' => false, 'errors' => 'Request array is empty'];
        count( $request->errors ) === 0 ? $validatedArray = $requestArray : $validatedArray = $request->passed;
        $result = $this->equipmentService->storeEquipment($validatedArray);

        return [
            'success' => $result['insertedData'],
            'errors' => array_merge($request->errors, $result['failedInsertions'])
        ];
    }

    public function update(UpdateEquipmentRequest $request, $id){
        if( ! filter_var($id, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1))) ) return ['success' => false, 'errors' => 'ID must be of type integer'];
        if( Equipment::where('id', $id)->first() === null ) return [ 'success' => false, 'errors' => 'Record with such ID does not exists'];
        if( count( $request->all() ) === 0 ) return [ 'success' => false, 'errors' => 'Request array is empty'];
        if( count( $request->errors ) !== 0 ) return [
            'success' => false, 
            'errors' => $request->errors
        ];

        return $this->equipmentService->updateEquipment($request->only('serial_number', 'equipment_type_id', 'comment'), $id);
    }

    public function delete($id){
        if( ! filter_var($id, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1))) ) return ['success' => false, 'errors' => 'ID must be of type integer'];
        if( Equipment::where('id', $id)->first() === null ) return ['success' => false, 'errors' => 'Record with such ID does not exists'];

        return $this->equipmentService->deleteEquipment($id);
    }

    public function getEquipment(GetEquipmentRequest $request){
        $params = request()->all();

        if( count( $request->errors ) !== 0 ) return [
            'success' => false, 
            'errors' => $request->errors
        ];

        return $this->equipmentService->getEquipment($params);
    }

    public function getEquipmentTypes(GetEquipmentTypesRequest $request){
        $params = request()->all();

        if( count( $request->errors ) !== 0 ) return [
            'success' => false, 
            'errors' => $request->errors
        ];

        return $this->equipmentService->getEquipmentTypes($params);
    }
}
