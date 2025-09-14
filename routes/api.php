<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/users', UserController::class)->except(['store']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('/teachers', TeacherController::class);
    Route::apiResource('/majors', MajorController::class);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/users', [UserController::class, 'store']);

Route::apiResource('/subjects', SubjectController::class);
Route::apiResource('/groups', GroupController::class);
Route::apiResource('/lessons', LessonController::class);
Route::apiResource('/users', UserController::class);
