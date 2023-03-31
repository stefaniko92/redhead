<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/login', [\App\Http\Controllers\Auth\SanctumController::class, 'login'])->name('auth.login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('auth/logout', [\App\Http\Controllers\Auth\SanctumController::class, 'logout'])->name('auth.logout');
});

Route::apiResource('employee', \App\Http\Controllers\EmployeeController::class);
Route::apiResource('approvers', \App\Http\Controllers\ApproverController::class);
Route::apiResource('jobs', \App\Http\Controllers\JobController::class);
Route::post('jobs/vote', [\App\Http\Controllers\JobApprovalController::class, 'vote'])
    ->middleware(['auth:sanctum', \App\Http\Middleware\Approver::class])
    ->name('jobs.vote');
Route::get('earning/report', [\App\Http\Controllers\ReportsController::class, 'report'])->name('earning.report');
