<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'Nome' => 'required|string|max:40|min:3|unique:Autor,Nome',
        ];
    }

    public function messages(): array
    {
        return [
            'Nome.required' => 'O nome do autor é obrigatório.',
            'Nome.max' => 'O nome do autor não pode ter mais de 40 caracteres.',
            'Nome.min' => 'O nome do autor deve ter pelo menos 3 caracteres.',
            'Nome.unique' => 'O nome do autor já existe.',
        ];
    }
}
