<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class DemoTestActivationRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'refs' => 'required|array',
            'refs.*' => 'required|string',
        ];
    }
}
