<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class IncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => $this->isMethod('post') ? 'required' : '',
            'evidence' => $this->isMethod('post') ? 'required|min:15' : 'min:15',
            'criticality' => $this->isMethod('post') ? 'required' : '',
            'host' => $this->isMethod('post') ? 'required|min:5' : 'min:5',
            'user_id' => $this->isMethod('post') ? 'required' : ''
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo name é obrigatório!',
            'evidence.required' => 'Campo evidence é obrigatório!',
            'evidence.min' => 'Evidence com no mínimo :min caracteres!',
            'criticality.required' => 'Campo criticality é obrigatório!',
            'host.required' => 'Campo host é obrigatório!',
            'host.min' => 'Host com no mínimo :min caracteres!',
            'user_id.required' => 'Campo userId é obrigatório!',
        ];
    }
}
