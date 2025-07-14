<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('data', [DataController::class, 'index'])->name('data.index');
    Route::get('/data/guru', function(){
        return Inertia::render('data/guru/page');
    })->name('teacher.index');

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
