<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'sekretaris']);
    }

    public function rules(): array
    {
        return [
            'verification_status' => ['required', 'in:terverifikasi,tidak_memerlukan_tindak_lanjut,memerlukan_tindak_lanjut,selesai,diarsipkan'],
            'verification_note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}