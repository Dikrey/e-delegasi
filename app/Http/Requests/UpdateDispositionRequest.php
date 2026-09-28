<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDispositionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'to' => __('model.disposition.to'),
            'content' => __('model.disposition.content'),
            'due_date' => __('model.disposition.due_date'),
            'received_at' => __('model.disposition.received_at'),
            'letter_status' => __('model.disposition.status'),
            'note' => __('model.disposition.note'),
            'forwarded_to' => __('model.disposition.forwarded_to'),
            'forwarded_to_custom' => __('model.disposition.forwarded_to_custom'),
            'honor' => __('model.disposition.honor'),
            'honor_custom' => __('model.disposition.honor_custom'),
            'instruction' => __('model.disposition.instruction'),
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
            'to' => ['required'],
            'content' => ['required'],
            'due_date' => ['required'],
            'received_at' => ['nullable', 'date'],
            'letter_status' => ['required'],
            'note' => ['nullable'],
            'forwarded_to' => ['nullable', 'array'],
            'forwarded_to.*' => ['required', 'string'],
            'forwarded_to_custom' => ['nullable', 'string', 'max:255'],
            'honor' => ['nullable', 'array'],
            'honor.*' => ['required', 'string'],
            'honor_custom' => ['nullable', 'string', 'max:255'],
            'instruction' => ['nullable', 'string'],
        ];
    }
}
