<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        $task = $this->route('task');

        if (!$task) return false;

        // Staff hanya boleh memperbarui tugasnya sendiri.
        if ($user->role === 'staff') {
            return (int) $task->staff_id === (int) $user->id;
        }

        // Admin hanya boleh melihat, tidak boleh memperbarui progres.
        if ($user->role === 'admin') {
            return false;
        }

        return $user->role === 'sekretaris';
    }

    public function rules(): array
    {
        return [
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'note' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'in:baru,diterima,dalam_pengerjaan,menunggu_review,selesai,ditolak,terlambat'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx,png,jpg,jpeg', 'max:10240'],
        ];
    }
}