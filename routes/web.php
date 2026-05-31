<?php

use App\Http\Controllers\KelulusanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KelulusanController::class, 'index'])->name('kelulusan.index');
Route::get('/kelulusan', [KelulusanController::class, 'index']);
Route::inertia('/welcome', 'welcome')->name('welcome');
