<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Services\MaskValidationService;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function getEquipment(array $params): LengthAwarePaginator;

    /**
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function getEquipmentTypes(array $params): LengthAwarePaginator;
}
