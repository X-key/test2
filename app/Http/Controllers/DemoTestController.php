<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemoTestRequest;
use App\Jobs\ProcessDemoTest;
use App\Models\DemoTestInquiry;

class DemoTestController extends Controller
{
    public function store(DemoTestRequest $request): \Illuminate\Http\JsonResponse
    {
        $validatedRequest = $request->validated();

        $inquiry = DemoTestInquiry::create([
            'payload' => $validatedRequest['objects'],
            'items_total_count' => count($validatedRequest['objects']),
        ]);

        ProcessDemoTest::dispatch($inquiry->id);

        return response()->json(['message' => 'Inquiry created successfully']);
    }
}
