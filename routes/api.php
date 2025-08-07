<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CompanySettingController;
use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('invoices', InvoiceController::class);

    Route::get('/invoices/{invoice}/email', [InvoiceController::class, 'emailInvoice']);
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download']);
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'update']);
    Route::post('/company-settings', [CompanySettingController::class, 'store']);
    Route::get('/company-settings/me', [CompanySettingController::class, 'show']);
});

Route::middleware('auth:api')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out']);
});



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
