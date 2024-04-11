<?php

namespace App\Http\Controllers;

use App\Models\DemoTest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ActivateRecordController extends Controller
{
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

        DemoTest::whereIn('ref', $refs)
            ->update(['is_active' => true]);

        $updatedCount = DemoTest::whereIn('ref', $refs)
            ->where('is_active', true)
            ->count();

        if (count($refs) !== $updatedCount) {
            throw ValidationException::withMessages([
                'refs' => "Some records could not be activated or were already active",
            ]);
        }

        return response()->json(['message' => 'Records activated successfully']);
    }
}
