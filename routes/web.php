<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 従業員管理
    Route::resource('employees', EmployeeController::class);
    
    // CSV機能
    Route::get('/employees/export/csv', [EmployeeController::class, 'export'])->name('employees.export');
    Route::get('/employees/import/form', [EmployeeController::class, 'importForm'])->name('employees.import.form');
    Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('/employees/template/download', [EmployeeController::class, 'downloadTemplate'])->name('employees.template.download');
    
    // ユーザー管理
    Route::resource('users', UserController::class);
    
    // 会社管理
    Route::resource('companies', CompanyController::class);
});

require __DIR__.'/auth.php';
