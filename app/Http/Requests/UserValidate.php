<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserValidate extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:4',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users',
            'password' => 'required|string|min:6|max:25',
            'gender' => 'required|string',
            'phoneno' => 'required|digits:10',
            'role' => 'required|in:user,admin'
        ];
        if (Auth::check() && Auth::user()->role !== 'admin') {
            $rules['role'] = 'in:user';
        }
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required and must be at least 4 characters.',
            'email.required' => 'A valid email is required.',
            'email.unique' => 'This email is already in use.',
            'password.required' => 'Password is required and must be between 6 and 25 characters.',
            'phoneno.required' => 'Phone number must be a 10-digit number.',
        ];
    }
}
