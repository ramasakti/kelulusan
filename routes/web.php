<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelulusanController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

// Public Routes (Graduation announcement search)
Route::get('/', [KelulusanController::class, 'index'])->name('kelulusan.index');
Route::get('/kelulusan', [KelulusanController::class, 'index']);

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Admin Dashboard Routes (Protected by Auth middleware)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Resource CRUDs via dialogs
    Route::resource('mapel', MapelController::class)->only(['store', 'update', 'destroy']);
    Route::resource('siswa', SiswaController::class)->only(['store', 'update', 'destroy']);

    // Import siswa from Excel
    Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    // Download blank Excel template
    Route::get('/siswa/template-download', [SiswaController::class, 'downloadTemplate'])->name('siswa.template.download');
    
    // Dynamic Row Grades update route
    Route::post('/siswa/{siswa}/nilai', [SiswaController::class, 'updateNilai'])->name('siswa.nilai.update');
    
    // Update countdown settings route
    Route::post('/dashboard/settings', [DashboardController::class, 'updateSettings'])->name('dashboard.settings.update');
});

// Welcome / Static pages
Route::inertia('/welcome', 'welcome')->name('welcome');
