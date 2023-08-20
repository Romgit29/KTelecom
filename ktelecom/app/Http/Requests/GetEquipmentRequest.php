<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class GetEquipmentRequest extends FormRequest
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
            'searchById' => 'integer',
            'searchByEquipmentTypeId' => 'integer',
            'searchBySerialNumber' => 'max:100',
            'searchByComment' => 'max:100',
            'paginate' => 'integer'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->errors = $validator->errors()->toArray();
    }
}
