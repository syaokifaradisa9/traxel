<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportVersionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'json' => 'required|file',
            'excel' => 'required|file'
        ];
    }

    public function messages(){
        return [
            'json.required' => 'Mohon Pilih File Import!',
            'json.file' => 'Mohon Pilih Berupa File!',
            'excel.required' => 'Mohon Pilih File Excel!',
            'excel.file' => 'Mohon Pilih Berupa File!',
        ];
    }
}
