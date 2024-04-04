<?php

namespace App\Http\Controllers;

use App\Models\DemoTest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DeactivateRecordController extends Controller
{
    public function deactivate(Request $request)
    {
        $refs = $request->input('refs');

        $validatedData = $this->validate($request, [
            'refs' => 'required|array',
            'refs.*' => 'required|string',
        ]);

        $activeObjects = DemoTest::whereIn('ref', $refs)
            ->where('is_active', true)
            ->get();

        if ($activeObjects->isNotEmpty()) {
            $activeRefs = $activeObjects->pluck('ref')->implode(', ');
            throw ValidationException::withMessages([
                'refs' => "The following records are already active: $activeRefs",
            ]);
        }

        DemoTest::whereIn('ref', $refs)
            ->update(['is_active' => false]);

        return response()->json(['message' => 'Records deactivated successfully']);
    }
}
