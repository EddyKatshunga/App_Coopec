<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMembreRequest extends FormRequest
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
            'nom_complet' => ['required', 'string', 'min:3'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],

            'numero_identification' => ['required', 'string', 'unique:membres,numero_identification'],
            'sexe' => ['required', Rule::in(['M', 'F'])],
            'lieu_de_naissance' => ['required', 'string'],
            'date_de_naissance' => ['required', 'date'],
            'qualite' => ['required', Rule::in(['Effectif', 'Auxiliaire'])],

            'adresse' => ['nullable', 'string'],
            'telephone' => ['nullable', 'string'],
            'activites' => ['nullable', 'string'],
            'adresse_activite' => ['nullable', 'string'],
            'date_adhesion' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom_complet.required' => 'Le nom complet est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'numero_identification.unique' => 'Ce numéro existe déjà.',
        ];
    }

}
