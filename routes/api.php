<?php

use App\Http\Controllers\Api\DemoTestController;
use Illuminate\Support\Facades\Route;


Route::post('/demo/test', [DemoTestController::class, 'store']);
Route::post('/records/activate', [DemoTestController::class, 'activate']);
Route::post('/records/deactivate', [DemoTestController::class, 'deactivate']);
