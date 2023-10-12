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
            'file_import' => 'required|file|mimes:zip'
        ];
    }

    public function messages(){
        return [
            'file_import.required' => 'Mohon Pilih File Import!',
            'file_import.file' => "Mohon Pilih Berupa File!",
            'file_import.mimes' => "Mohon Pilih File Import Berupa ZIP!"
        ];
    }
}
