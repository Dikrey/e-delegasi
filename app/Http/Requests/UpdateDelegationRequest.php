<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDelegationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'sekretaris']);
    }

    public function rules(): array
    {
        return [
            'letter_id' => ['nullable', 'exists:letters,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'instruction' => ['nullable', 'string'],
            'priority' => ['required', 'in:rendah,normal,tinggi,urgent'],
            'task_category_id' => ['nullable', 'integer', 'exists:task_categories,id'],
            'deadline' => ['nullable', 'date'],
            'staff_ids' => ['nullable', 'array'],
            'staff_ids.*' => ['required', 'integer', 'exists:users,id'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,png,jpg,jpeg', 'max:10240'],
        ];
    }
}