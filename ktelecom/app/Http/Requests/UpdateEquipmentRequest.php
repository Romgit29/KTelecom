<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipmentRequest extends FormRequest
{
    public $errors = [];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'equipment_type_id' => 'integer|exists:equipment_types,id',
            'serial_number' => 'max:100',
            'comment' => 'max:100'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->errors = $validator->errors()->toArray();
        getRidOfNestsed($this->errors);
    }
}
