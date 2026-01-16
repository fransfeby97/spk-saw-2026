<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes (authenticated only)
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::resource('criteria', CriteriaController::class)->except(['show']);
    Route::resource('periods', PeriodController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['show']);

    Route::get('/assessment', [AssessmentController::class, 'index'])->name('assessment.index');
    Route::post('/assessment', [AssessmentController::class, 'store'])->name('assessment.store');
    Route::get('/assessment/result', [AssessmentController::class, 'result'])->name('assessment.result');
    Route::get('/assessment/print/{employee}', [AssessmentController::class, 'printPdf'])->name('assessment.print');
});
