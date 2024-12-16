<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaAlbumRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'uuid'],
            'files' => ['required', 'array'],
            'files.*' => ['required', 'file', 'mimes:jpeg,png,jpg,pdf,doc,docx,xls', 'max:2048'],
        ];
    }
}
