<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $array = parent::toArray($request);
        $validation = $this->validateParameters($array['success'], $array['errors']);
        if ($validation['success'] !== true) return [
            'success' => [],
            'errors' => $validation['errors'],
        ];

        return [
            'success' => $array['success'],
            'errors' => $array['errors'],
        ];
    }

    public function validateParameters($success, $errors)
    {
        if (!(gettype($success) == 'array' && count($success) == 0)) {
            if (gettype($success) !== 'object' || !(gettype($success) == 'object' && (get_parent_class(get_class($success)) == 'Illuminate\Http\Resources\Json\JsonResource' || get_parent_class(get_class($success)) == 'Illuminate\Http\Resources\Json\ResourceCollection'))) {
                return [
                    'success' => false,
                    'errors' => ["'success' property in GeneralResponseResource return has inappropriate type"]
                ];
            }
        }
        if (!gettype($errors) == 'array') return [
            'success' => false,
            'errors' => ["'errors' property in GeneralResponseResource class return has inappropriate type"],
        ];

        return ['success' => true];
    }
}
