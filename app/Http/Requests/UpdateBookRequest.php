<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
            'Titulo' => ['required', 'string', 'max:40'],
            'Editora' => ['required', 'string', 'max:40'],
            'Edicao' => ['required', 'integer', 'min:1'],
            'AnoPublicacao' => ['required', 'integer', 'min:1000', 'max:' . (date('Y'))],
            'valor' => ['required', 'numeric', 'min:0'],
            'authors' => ['required', 'array', 'min:1'],
            'authors.*' => ['required', 'exists:Autor,CodAu'],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['required', 'exists:Assunto,CodAs'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Titulo.required' => 'O título do livro é obrigatório.',
            'Titulo.max' => 'O título não pode ter mais de 40 caracteres.',
            'Titulo.min' => 'O título deve ter pelo menos 3 caracteres.',
            'Editora.required' => 'A editora é obrigatória.',
            'Editora.max' => 'A editora não pode ter mais de 40 caracteres.',
            'Editora.min' => 'O nome da editora deve ter pelo menos 3 caracteres.',
            'Edicao.required' => 'A edição é obrigatória.',
            'Edicao.min' => 'A edição deve ser maior que zero.',
            'AnoPublicacao.required' => 'O ano de publicação é obrigatório.',
            'AnoPublicacao.min' => 'O ano de publicação deve ser maior que 1800.',
            'AnoPublicacao.max' => 'O ano de publicação não pode ser maior que o ano atual.',
            'valor.required' => 'O valor é obrigatório.',
            'valor.min' => 'O valor não pode ser negativo.',
            'authors.required' => 'Selecione pelo menos um autor.',
            'authors.min' => 'Selecione pelo menos um autor.',
            'authors.*.exists' => 'Um dos autores selecionados não existe.',
            'subjects.required' => 'Selecione pelo menos um assunto.',
            'subjects.min' => 'Selecione pelo menos um assunto.',
            'subjects.*.exists' => 'Um dos assuntos selecionados não existe.',
        ];
    }
}
