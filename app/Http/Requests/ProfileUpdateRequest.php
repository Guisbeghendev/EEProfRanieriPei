<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            // Regras para o Avatar
            // 'nullable' porque o avatar pode não ser enviado (se não for alterado ou se for removido)
            // 'image' para garantir que é um arquivo de imagem
            // 'mimes' para tipos específicos de imagem (jpeg, png, gif)
            // 'max:2048' para um tamanho máximo de 2MB (2048 KB)
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,gif', 'max:2048'],
            // 'boolean' para a flag de remoção do avatar
            'remove_avatar' => ['boolean'],

            // Regras para os campos da tabela 'profiles'
            // Assumindo que os campos podem ser opcionais, usamos 'nullable'.
            // Se algum campo for obrigatório, mude 'nullable' para 'required'.
            'birth_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:20'], // Ajuste o max conforme necessário
            'other_contact' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string', 'max:1000'], // Ajuste o max conforme necessário
            'ranieri_text' => ['nullable', 'string', 'max:1000'], // Ajuste o max conforme necessário
        ];
    }
}
