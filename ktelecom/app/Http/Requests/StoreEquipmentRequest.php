<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentRequest extends FormRequest
{
    public $errors = [];
    public $passed = [];

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
            '*' => 'array',
            '*.equipment_type_id' => 'required|integer|exists:equipment_types,id',
            '*.serial_number' => 'required|max:100',
            '*.comment' => 'required|max:100'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        foreach (request()->all() as $key => $value) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $errorKey => $error) {
                if (str_starts_with($errorKey, $key)) array_push($errors, $error);
            }
            if (count($errors) > 0) array_push($this->errors, [
                'requestFieldNumber' => $key,
                'errors' => $errors
            ]);
            else $this->passed[$key] = $value;
        }
    }
}
