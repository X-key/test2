<?php

namespace App\Http\Controllers;

use App\Models\DemoTest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DeactivateRecordController extends Controller
{
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

        DemoTest::whereIn('ref', $refs)
            ->update(['is_active' => false]);

        $updatedCount = DemoTest::whereIn('ref', $refs)
            ->where('is_active', false)
            ->count();

        if (count($refs) !== $updatedCount) {
            throw ValidationException::withMessages([
                'refs' => "Some records could not be deactivated or were already inactive",
            ]);
        }

        return response()->json(['message' => 'Records deactivated successfully']);
    }
}
