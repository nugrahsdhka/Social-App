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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            
            // VALIDASI BARU: Username harus unik (abaikan user yg sedang login)
            'username' => [
                'required', 
                'string', 
                'max:255', 
                'alpha_dash', // Hanya huruf, angka, strip, underscore
                Rule::unique('users')->ignore($this->user()->id),
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            // VALIDASI BARU: Bio & Private
            'bio' => ['nullable', 'string', 'max:500'], 
            'is_private' => ['boolean'],
        ];
    }
}