<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id'  => ['required', 'integer', 'exists:projects,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'due_date'    => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required'  => 'Project wajib dipilih.',
            'project_id.exists'    => 'Project tidak ditemukan.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak ditemukan.',
            'title.required'       => 'Judul task wajib diisi.',
            'description.required' => 'Deskripsi task wajib diisi.',
            'due_date.required'    => 'Tanggal deadline wajib diisi.',
            'due_date.date'        => 'Format tanggal tidak valid.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
