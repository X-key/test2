<?php

namespace App\Http\Controllers;

use App\Models\DemoTest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ActivateRecordController extends Controller
{
    public function activate(Request $request)
    {
        $refs = $request->input('refs');

        $validatedData = $this->validate($request, [
            'refs' => 'required|array',
            'refs.*' => 'required|string',
        ]);

        $inactiveObjects = DemoTest::whereIn('ref', $refs)
            ->where('is_active', false)
            ->get();

        if ($inactiveObjects->isNotEmpty()) {
            $inactiveRefs = $inactiveObjects->pluck('ref')->implode(', ');
            throw ValidationException::withMessages([
                'refs' => "The following records are already inactive: $inactiveRefs",
            ]);
        }

        DemoTest::whereIn('ref', $refs)
            ->update(['is_active' => true]);

        return response()->json(['message' => 'Records activated successfully']);
    }
}
