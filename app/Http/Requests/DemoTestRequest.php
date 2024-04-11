<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class DemoTestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'objects' => [
                'required',
                'array',
                'max:2000',
                function ($attribute, $value, $fail) {
                    foreach ($value as $object) {
                        $ref = $object['ref'];
                        $existsAndInActive = DB::table('demo_test')
                            ->where('ref', $ref)
                            ->where('is_active', false)
                            ->exists();

                        if ($existsAndInActive) {
                            $fail('At least one object is inactive and exist in the database.');
                        }
                    }
                },
            ],
            'objects.*.ref' => 'required|string',
            'objects.*.name' => 'required|string',
            'objects.*.description' => 'nullable|string',
        ];
    }
}
