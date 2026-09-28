<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->user()->role == Role::ADMIN->status();
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => __('model.user.name'),
            'email' => __('model.user.email'),
            'phone' => __('model.user.phone'),
            'password' => __('model.user.password'),
            'role' => __('model.user.role'),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([
                Role::ADMIN->status(),
                Role::STAFF->status(),
                Role::SEKRETARIS->status(),
            ])],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
        ];
    }
}