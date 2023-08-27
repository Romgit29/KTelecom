<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentType;

class MaskValidationService
{
    public $rulesDict = [
        'N' => "[0-9]",
        'A' => "[A-Z]",
        'a' => "[a-z]",
        'X' => "[A-Z0-9]",
        'Z' => "[-_@]"
    ];

    public function maskMatch($serialNumber, $mask)
    {
        $rules = "^";
        $split = str_split($mask);
        foreach ($split as $key => $value) {
            $subString = substr($mask, $key);
            $subSplit = str_split($subString);
            $rule = $subSplit[0];
            $count = 0;
            if (array_key_exists($key - 1, $split) && $split[$key - 1] === $split[$key]) continue;
            foreach ($subSplit as $subValue) {
                if ($subValue == $rule) $count = $count + 1;
                else break;
            }
            $rules .= $this->rulesDict[$rule] . '{' . $count . '}';
        }
        $rules .= "$";
        if (preg_match("/$rules/", $serialNumber)) {
            return true;
        } else {
            return false;
        }
    }

    public function getValidationParameters($data, $id)
    {
        $equipmentData = Equipment::where('equipment.id', $id)
            ->join('equipment_types', 'equipment_types.id', 'equipment.equipment_type_id')
            ->select('equipment_types.serial_number_mask', 'equipment.serial_number')
            ->first();

        if (!array_key_exists('equipment_type_id', $data)) $equipmentMask = $equipmentData['serial_number_mask'];
        else $equipmentMask = EquipmentType::where('id', $data['equipment_type_id'])
            ->first()
            ->serial_number_mask;

        if (!array_key_exists('serial_number', $data)) $serialNumber = $equipmentData['serial_number'];
        else $serialNumber = $data['serial_number'];
        $serialNumber;
        return [
            'equipmentMask' => $equipmentMask,
            'serialNumber' => $serialNumber,
        ];
    }
}
