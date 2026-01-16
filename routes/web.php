<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// ========================================
// DEPLOYMENT ROUTE - HAPUS SETELAH MIGRASI!
// ========================================
Route::get('/setup-database/{key}', function ($key) {
    if ($key !== 'spk-deploy-2026') {
        abort(403, 'Invalid key');
    }

    try {
        Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = Artisan::output();

        Artisan::call('db:seed', ['--class' => 'AdminSeeder', '--force' => true]);
        $seedOutput = Artisan::output();

        return '<pre>MIGRATION OUTPUT:
' . $migrateOutput . '

SEEDER OUTPUT:
' . $seedOutput . '

✅ Database setup complete!
Login: admin@admin.com / admin123

⚠️ HAPUS ROUTE INI SETELAH BERHASIL!</pre>';
    } catch (\Exception $e) {
        return '<pre>ERROR: ' . $e->getMessage() . '</pre>';
    }
});

Route::get('/clear-cache/{key}', function ($key) {
    if ($key !== 'spk-deploy-2026')
        abort(403);

    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return 'Cache cleared!';
});

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
