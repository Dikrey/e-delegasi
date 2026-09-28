<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDelegationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'sekretaris']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
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
            'add_to_agenda' => ['nullable', 'boolean'],
            'agenda_title' => ['nullable', 'required_if:add_to_agenda,1', 'string', 'max:255'],
            'agenda_type' => ['nullable', 'string', 'max:100'],
            'agenda_date' => ['nullable', 'required_if:add_to_agenda,1', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'send_now' => ['nullable', 'boolean'],
        ];
    }
}