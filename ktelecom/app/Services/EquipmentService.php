<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\EquipmentServiceInterface;
use App\Models\Equipment;
use App\Models\EquipmentType;
use Illuminate\Pagination\LengthAwarePaginator;

class EquipmentService implements EquipmentServiceInterface
{
    public function storeEquipment(array $data, MaskValidationService $maskValidationService): array
    {
        $insertedDataIds = [];
        $failedInsertions = [];
        $time = time();

        foreach ($data as $key => $value) {
            try {
                $equipmentTypeMask = EquipmentType::where('id', $value['equipment_type_id'])
                    ->first()
                    ->serial_number_mask;

                if (!$maskValidationService->maskMatch($value['serial_number'], $equipmentTypeMask)) {
                    array_push($failedInsertions, [
                        'requestFieldNumber' => $key,
                        'errors' => ["Serial number {$value['serial_number']} doesn't match equipment type mask"]
                    ]);
                    continue;
                }

                $id = Equipment::insertGetId([
                    'equipment_type_id' => $value['equipment_type_id'],
                    'serial_number' => $value['serial_number'],
                    'comment' => $value['comment'],
                    'created_at' => date('Y-m-d H:i:s', $time),
                    'updated_at' => date('Y-m-d H:i:s', $time)
                ]);
                $insertedDataIds[$key] = $id;
            } catch (\Throwable $th) {
                array_push($failedInsertions, [
                    'requestFieldNumber' => $key,
                    'errors' => [$th->getMessage()]
                ]);
            }
        }

        return [
            'insertedDataIds' => $insertedDataIds,
            'failedInsertions' => $failedInsertions
        ];
    }

    public function updateEquipment(array $data, int $id, MaskValidationService $maskValidationService): array
    {
        try {
            if (array_key_exists('serial_number', $data) || array_key_exists('equipment_type_id', $data)) {
                $maskValidationParameters = $maskValidationService->getValidationParameters($data, $id);
                if (!$maskValidationService->maskMatch($maskValidationParameters['serialNumber'], $maskValidationParameters['equipmentMask'])) return [
                    'success' => false,
                    'error' => ["Serial number {$maskValidationParameters['serialNumber']} doesn't match given or existing serial number mask"]
                ];
            }
            Equipment::where('id', $id)
                ->update($data);
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'error' => [$th->getMessage()]
            ];
        }

        return [
            'success' => true,
            'data' => Equipment::find($id)
        ];
    }

    public function deleteEquipment(int $id): array
    {
        try {
            Equipment::where('id', $id)
                ->delete();
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'error' => [$th->getMessage()]
            ];
        }

        return [
            'success' => true,
            'data' => Equipment::onlyTrashed()->find($id)
        ];
    }

    public function getEquipment(array $params): LengthAwarePaginator
    {
        $query = Equipment::where(function ($where) use ($params) {
            if (array_key_exists('searchById', $params)) $where = $where->where('id', $params['searchById']);
            if (array_key_exists('searchByEquipmentTypeId', $params)) $where = $where->where('equipment_type_id', $params['searchByEquipmentTypeId']);
            if (array_key_exists('searchBySerialNumber', $params)) $where = $where->where('serial_number', 'like', "%{$params['searchBySerialNumber']}%");
            if (array_key_exists('searchByComment', $params)) $where = $where->where('comment', 'like', "%{$params['searchByComment']}%");
            if (array_key_exists('q', $params)) {
                $where = $where->where('comment', 'like', "%{$params['q']}%")
                    ->orWhere('serial_number', 'like', "%{$params['q']}%")
                    ->orWhere('equipment_type_id', $params['q'])
                    ->orWhere('id', $params['q']);
            }
        });
        if (array_key_exists('paginate', $params)) $query = $query->paginate($params['paginate']);
        else $query = $query->paginate(10);

        return $query;
    }

    public function getEquipmentTypes(array $params): LengthAwarePaginator
    {
        $query = EquipmentType::where(function ($where) use ($params) {
            if (array_key_exists('searchById', $params)) $where = $where->where('id', $params['searchById']);
            if (array_key_exists('searchByName', $params)) $where = $where->where('name', 'like', "%{$params['searchByName']}%");
            if (array_key_exists('searchBySerialNumberMask', $params)) $where = $where->where('serial_number_mask', 'like', "%{$params['searchBySerialNumberMask']}%");
            if (array_key_exists('q', $params)) {
                $where = $where->where('id', $params['q'])
                    ->orWhere('name', 'like', "%{$params['q']}%")
                    ->orWhere('serial_number_mask', 'like', "%{$params['q']}%");
            }
        });
        if (array_key_exists('paginate', $params)) $query = $query->paginate($params['paginate']);
        else $query = $query->paginate(10);

        return $query;
    }
}
