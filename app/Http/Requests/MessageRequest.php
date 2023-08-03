<?php

namespace App\Http\Requests;

use App\Builder\ReturnApi;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
        $rulesMessage = [
            'title' => 'required',
            'content' => 'required',
            'usersId' => 'required|array',
        ];
        return $rulesMessage;
    }

    public function messages()
    {
        $messages = [
            'title.required' => 'Informe o título da mensagem',
            'content.required' => 'Informe o conteúdo da mensagem',
            'usersId.required' => 'Informe os usuários que receberão a mensagem',
            'usersId.array' => 'Informe os usuários que receberão a mensagem em formato de array',
        ];
        return $messages;
    }

    public function failedValidation(Validator $validator)
    {
        throw new Exception($validator->errors()->first());
        // return ReturnApi::Error($validator->errors()->first(), 400);
    }
}
