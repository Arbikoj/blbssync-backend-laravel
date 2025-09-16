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

    Route::get('/data/jurusan', function(){
        return Inertia::render('data/jurusan/page');
    })->name('major.index');

    Route::get('/data/kelas', function(){
        return Inertia::render('data/kelas/page');
    })->name('kelas.index');

    Route::get('/data/mapel', function(){
        return Inertia::render('data/mapel/page');
    })->name('mapel.index');

    Route::get('/data/jam', function(){
        return Inertia::render('data/jam/page');
    })->name('jam.index');

    Route::get('/data/jadwal', function(){
        return Inertia::render('data/jadwal/page');
    })->name('jadwal.index');

    Route::get('/manage/users', function(){
        return Inertia::render('manage/users/page');
    })->name('user.index');

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
