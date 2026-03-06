<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id'  => ['sometimes', 'required', 'integer', 'exists:projects,id'],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'title'       => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'due_date'    => ['sometimes', 'required', 'date'],
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
