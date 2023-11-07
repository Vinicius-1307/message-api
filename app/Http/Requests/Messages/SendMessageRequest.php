<?php

namespace App\Http\Requests\Messages;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'string|required',
            'content' => 'string|required',
            'users' => 'required|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Título',
            'content' => 'Conteúdo'
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'O campo (title) deve ser uma string.',
            'title.required' => 'O campo (title) é obrigatório.',
            'content.string' => 'O campo (content) deve ser uma string.',
            'content.required' => 'O campo (content) é obrigatório.',
            'users.required' => 'Informe os usuários que receberão a mensagem',
            'users.array' => 'Informe os usuários que receberão a mensagem em formato de array',
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new ApiException($validator->errors()->first());
    }
}
