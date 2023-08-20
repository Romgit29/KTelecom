<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Http\Resources\EquipmentCollection;
use App\Http\Resources\EquipmentTypeCollection;

interface EquipmentServiceInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function storeEquipment(array $data): array;

    /**
     * @param array $data
     * @param int $id
     * @return array
     */
    public function updateEquipment(array $data, int $id): array;

    /**
     * @param int $id
     * @return array
     */
    public function deleteEquipment(int $id): array;

    /**
     * @return EquipmentCollection
     */
    public function getEquipment(array $params): EquipmentCollection;

    /**
     * @return EquipmentTypeCollection
     */
    public function getEquipmentTypes(array $params): EquipmentTypeCollection;
}