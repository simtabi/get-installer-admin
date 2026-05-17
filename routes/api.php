<?php

use App\Http\Controllers\Api\V1\TenantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::get('/tenants/me', [TenantController::class, 'me']);
});
