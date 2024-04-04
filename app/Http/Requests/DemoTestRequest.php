<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemoTestRequest extends FormRequest
{
    public function rules()
    {
        return [
            'objects' => 'required|array|max:2000',
            'objects.*.ref' => 'required|string',
            'objects.*.name' => 'required|string',
            'objects.*.description' => 'nullable|string',
        ];
    }
}
