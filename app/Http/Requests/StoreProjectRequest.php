<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status'      => ['required', 'in:active,archived'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Nama project wajib diisi.',
            'description.required' => 'Deskripsi project wajib diisi.',
            'status.required'      => 'Status project wajib diisi.',
            'status.in'            => 'Status hanya boleh: active atau archived.',
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
