<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mail_adress' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:12|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
        ];
    }
    public function messages()
    {
        return [
            'password.mixed_case' => 'Le mot de passe doit contenir au moins une majuscule.',
            'password.numbers' => 'Le mot de passe doit contenir au moins un chiffre.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
        ];
    }
}