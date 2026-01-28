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

use App\Http\Controllers\{
    ProjectController,
    WorkPackageController,
    BoqController,
    ProgressEntryController,
    DashboardController
};

Route::get('/dashboard', [DashboardController::class, 'index']);

Route::apiResource('projects', ProjectController::class);
Route::apiResource('work-packages', WorkPackageController::class);
Route::apiResource('boqs', BoqController::class);
Route::apiResource('progress', ProgressEntryController::class)->only(['index', 'store', 'destroy']);
Route::get('/dashboard', [DashboardController::class, 'index']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
