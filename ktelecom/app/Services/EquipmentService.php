<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\EquipmentServiceInterface;
use App\Http\Resources\EquipmentCollection;
use App\Http\Resources\EquipmentResource;
use App\Http\Resources\EquipmentTypeCollection;
use App\Models\Equipment;
use App\Models\EquipmentType;
use Exception;

class EquipmentService implements EquipmentServiceInterface
{
    public $rulesDict = [
        'N' => "[0-9]",
        'A' => "[A-Z]",
        'a' => "[a-z]",
        'X' => "[A-Z0-9]",
        'Z' => "[-_@]"
    ];

    public function storeEquipment(array $data): array
    {
        $insertedDataIds = [];
        $failedInsertions = [];
        $time = time();

        foreach($data as $value) {
            try {
                $equipmentTypeMask = EquipmentType::where('id', $value['equipment_type_id'])
                ->first()
                ->serial_number_mask;

                if( ! $this->maskMatch($value['serial_number'], $equipmentTypeMask) ) throw new Exception("Serial number {$value['serial_number']} doesn't match equipment type mask");

                $id = Equipment::insertGetId([
                    'equipment_type_id' => $value['equipment_type_id'],
                    'serial_number' => $value['serial_number'],
                    'comment' => $value['comment'],
                    'created_at' => date('Y-m-d H:i:s', $time),
                    'updated_at' => date('Y-m-d H:i:s', $time)
                ]);
                array_push($insertedDataIds, $id);
            } catch (\Throwable $th) {
                array_push($failedInsertions, [
                    'data' => $value, 
                    'errors' => [$th->getMessage()]
                ]);
            }
        }

        return [
            'insertedData' => new EquipmentCollection( Equipment::whereIn('id', $insertedDataIds)->get() ),
            'failedInsertions' => $failedInsertions
        ];
    }

    public function updateEquipment(array $data, int $id): array {
        try {
            if( array_key_exists('serial_number', $data) || array_key_exists('equipment_type_id', $data)){
                $equipmentData = Equipment::where('equipment.id', $id)
                ->join('equipment_types', 'equipment_types.id', 'equipment.equipment_type_id')
                ->select('equipment_types.serial_number_mask', 'equipment.serial_number')
                ->first();

                if( !array_key_exists('equipment_type_id', $data) ) $equipmentMask = $equipmentData['serial_number_mask'];
                else $equipmentMask = EquipmentType::where('id', $data['equipment_type_id'])
                ->first()
                ->serial_number_mask;

                if( !array_key_exists('serial_number', $data) ) $serialNumber = $equipmentData['serial_number'];
                else $serialNumber = $data['serial_number'];
                
                if( ! $this->maskMatch($serialNumber, $equipmentMask) ) throw new Exception("Serial number $serialNumber doesn't match given or existing serial number mask");
            }

            Equipment::where('id', $id)
            ->update($data);
        } catch (\Throwable $th) {
            return [
                'success' => false, 
                'data' => $th->getMessage()
            ];
        }

        return [
            'success' => true, 
            'data' => new EquipmentResource( Equipment::find($id) )
        ];
    }

    public function deleteEquipment(int $id): array {
        try {
            Equipment::where('id', $id)
            ->delete();
        } catch (\Throwable $th) {
            return [
                'success' => false, 
                'data' => $th->getMessage()
            ];
        }

        return [
            'success' => true,
            'data' => new EquipmentResource( Equipment::onlyTrashed()->find($id) )
        ];
        // Написано "все ответы API должны использовать API Resources & Resource Collections", не забыть вернуть тут удалённую запись
    }

    public function getEquipment(array $params): EquipmentCollection {
        $query = Equipment::where(function ($where) use ($params){
            if( array_key_exists('searchById', $params) ) $where = $where->where('id', $params['searchById']);
            if( array_key_exists('searchByEquipmentTypeId', $params) ) $where = $where->where('equipment_type_id', $params['searchByEquipmentTypeId']);
            if( array_key_exists('searchBySerialNumber', $params) ) $where = $where->where('serial_number', 'like', "%{$params['searchBySerialNumber']}%");
            if( array_key_exists('searchByComment', $params) ) $where = $where->where('comment', 'like', "%{$params['searchByComment']}%");
        });
        if( array_key_exists('paginate', $params) ) $query = $query->paginate($params['paginate']);
        else $query = $query->paginate(10);

        return new EquipmentCollection( $query );
    }

    public function getEquipmentTypes(array $params): EquipmentTypeCollection {
        $query = EquipmentType::where(function ($where) use ($params){
            if( array_key_exists('searchById', $params) ) $where = $where->where('id', $params['searchById']);
            if( array_key_exists('searchByName', $params) ) $where = $where->where('name', 'like', "%{$params['searchByName']}%");
            if( array_key_exists('searchBySerialNumberMask', $params) ) $where = $where->where('serial_number_mask', 'like', "%{$params['searchBySerialNumberMask']}%");
        });
        if( array_key_exists('paginate', $params) ) $query = $query->paginate($params['paginate']);
        else $query = $query->paginate(10);

        return new EquipmentTypeCollection( $query );
    }
    
    public function maskMatch($serialNumber, $mask) {
        $rules = "^";
        $split = str_split($mask);
        foreach($split as $key => $value) {
            $subString = substr($mask, $key);
            $subSplit = str_split($subString);
            $rule = $subSplit[0];
            $count = 0;
            if(array_key_exists($key-1, $split) && $split[$key-1] === $split[$key]) continue;
            foreach($subSplit as $subValue) {
                if($subValue == $rule) $count = $count + 1;
                else break;
            }
            $rules .= $this->rulesDict[$rule] . '{' . $count . '}';
        }
        $rules .= "$";
        if ( preg_match("/$rules/", $serialNumber) ) {
            return true;
        } else {
            return false;
        }
    }
}