<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DemoTestActivationRequest;
use App\Http\Requests\DemoTestRequest;
use App\Jobs\ProcessDemoTest;
use App\Models\DemoTest;
use App\Models\DemoTestInquiry;

/**
 * Class DemoTestController
 * @package App\Http\Controllers\Api
 */
final class DemoTestController extends Controller
{
    /**
     * @param \App\Http\Requests\DemoTestRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param DemoTestActivationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(DemoTestActivationRequest $request): \Illuminate\Http\JsonResponse
    {
        $refs = $request->input('refs');

        $result = DemoTest::updateRecords($refs, true);

        return response()->json($result);
    }

    /**
     * @param DemoTestActivationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(DemoTestActivationRequest $request): \Illuminate\Http\JsonResponse
    {
        $refs = $request->input('refs');

        $result = DemoTest::updateRecords($refs, false);

        return response()->json($result);
    }
}
