<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class VerifyDispositionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, [
            Role::SEKRETARIS->status(),
            Role::ADMIN->status(),
        ]);
    }

    public function attributes(): array
    {
        return [
            'direction' => __('model.disposition.options.direction'),
            'is_received' => __('model.disposition.is_received'),
            'verification_note' => __('model.disposition.verification_note'),
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
            'direction' => ['required', 'max:255'],
            'is_received' => ['required', 'boolean'],
            'verification_note' => ['nullable', 'string'],
        ];
    }
}