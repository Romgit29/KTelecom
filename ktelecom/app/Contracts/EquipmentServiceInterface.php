<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Services\MaskValidationService;
use Illuminate\Pagination\LengthAwarePaginator;

interface EquipmentServiceInterface
{
    /**
     * @param array $data
     * @param MaskValidationService $maskValidationService
     * @return array
     */
    public function storeEquipment(array $data, MaskValidationService $maskValidationService): array;

    /**
     * @param array $data
     * @param int $id
     * @param MaskValidationService $maskValidationService
     * @return array
     */
    public function updateEquipment(array $data, int $id, MaskValidationService $maskValidationService): array;

    /**
     * @param int $id
     * @param MaskValidationService $maskValidationService
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
