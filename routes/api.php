<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RfidCardController;
use App\Http\Controllers\DeviceController;
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
    Route::apiResource('/groups', GroupController::class);
    Route::apiResource('/subjects', SubjectController::class);
    Route::apiResource('/lessons', LessonController::class);
    Route::apiResource('/schedules', ScheduleController::class);

    Route::apiResource('/users', UserController::class);

    Route::apiResource('/attendances', AttendanceController::class);
    Route::apiResource('/rfidcards', RfidCardController::class);


    Route::get('/teachers/scheduled/{scheduleId}', [TeacherController::class, 'getTeacherScheduledToday']);
    
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/users', [UserController::class, 'store']);



Route::post('/attendances/devices', [AttendanceController::class, 'devices']);

Route::post('/devices/{device_code}/scan', [DeviceController::class, 'scanDeviceByCode']);
Route::post('/devices/scan-result', [DeviceController::class, 'scanResult']);
Route::get('/devices/{device_code}/latest-scan', [DeviceController::class, 'latestScan']);
Route::post('/devices/{device_code}/status', [DeviceController::class, 'updateStatus']);
Route::apiResource('/devices', DeviceController::class);

