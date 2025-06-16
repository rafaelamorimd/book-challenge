<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
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
            'descricao' => 'required|string|max:40|min:3|unique:Assunto,Descricao',
        ];
    }

    public function messages(): array
    {
        return [
            'descricao.required' => 'A descrição do assunto é obrigatória.',
            'descricao.max' => 'A descrição do assunto não pode ter mais de 40 caracteres.',
            'descricao.min' => 'A descrição do assunto deve ter pelo menos 3 caracteres.',
            'descricao.unique' => 'A descrição do assunto já existe.',
        ];
    }
}
