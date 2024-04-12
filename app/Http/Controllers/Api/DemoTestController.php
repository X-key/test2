<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DemoTestRequest;
use App\Jobs\ProcessDemoTest;
use App\Models\DemoTest;
use App\Models\DemoTestInquiry;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function activate(Request $request): \Illuminate\Http\JsonResponse
    {
        $refs = $request->input('refs');

        $validatedData = $this->validate($request, [
            'refs' => 'required|array',
            'refs.*' => 'required|string',
        ]);

        $checkRefs = DemoTest::whereIn('ref', $refs)->exists();
//
//        if (!$checkRefs) {
//            throw ValidationException::withMessages(['Refs not exists in DB']);
//        }
//
//        DemoTest::whereIn('ref', $refs)
//            ->update(['is_active' => true]);
//
//        $updatedCount = DemoTest::whereIn('ref', $refs)
//            ->where('is_active', true)
//            ->count();
//
//        if (count($refs) !== $updatedCount) {
//            throw ValidationException::withMessages([
//                'refs' => "Some records could not be activated or were already active",
//            ]);
//        }

        return response()->json(['message' => json_encode($refs)]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deactivate(Request $request): \Illuminate\Http\JsonResponse
    {
        $refs = $request->input('refs');

        $validatedData = $this->validate($request, [
            'refs' => 'required|array',
            'refs.*' => 'required|string',
        ]);

//        DemoTest::whereIn('ref', $refs)
//            ->update(['is_active' => false]);
//
//        $updatedCount = DemoTest::whereIn('ref', $refs)
//            ->where('is_active', false)
//            ->count();
//
//        if (count($refs) !== $updatedCount) {
//            throw ValidationException::withMessages([
//                'refs' => "Some records could not be deactivated or were already inactive",
//            ]);
//        }

        return response()->json(['message' => 'Records deactivated successfully']);
    }
}
