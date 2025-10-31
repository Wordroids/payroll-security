<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MealsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryAdvanceController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SalarySettingController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UniformsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;




Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Employee Management Routes
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('/employees/print-gaurds', [EmployeeController::class, 'printGaurds'])->name('employees.printGaurds');
    // Sites Management Routes
    Route::get('sites', [SiteController::class, 'index'])->name('sites.index');
    Route::get('sites/create', [SiteController::class, 'create'])->name('sites.create');
    Route::post('sites', [SiteController::class, 'store'])->name('sites.store');
    Route::get('sites/{site}/edit', [SiteController::class, 'edit'])->name('sites.edit');
    Route::put('sites/{site}', [SiteController::class, 'update'])->name('sites.update');
    Route::get('sites/{site}/view', [SiteController::class, 'view'])->name('sites.view');
    Route::delete('sites/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');
    // Attendance Management Routes
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/site-entry', [AttendanceController::class, 'siteEntryForm'])->name('attendances.site-entry');
    Route::post('/attendances/site-entry', [AttendanceController::class, 'storeSiteEntry'])->name('attendances.site-entry.store');
    Route::get('/attendances/pdf', [AttendanceController::class, 'downloadPDF'])->name('attendances.pdf');
    Route::get('/sites/{site}/assign', [SiteController::class, 'assignGuards'])->name('sites.assign');
    Route::post('/sites/{site}/assign', [SiteController::class, 'storeAssignedGuards'])->name('sites.assign.store');

    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'downloadPdf'])->name('invoices.download');
    Route::get('/sites/{site}/rank-rates', [InvoiceController::class, 'getRankRates']);

    // Salary Advance Routes
    Route::prefix('salary-advance')->group(function () {
        // Main listing page
        Route::get('/', [SalaryAdvanceController::class, 'salaryAdvance'])
            ->name('salary.advance');

        // Create new advance
        Route::get('/create', [SalaryAdvanceController::class, 'create'])
            ->name('salary.advance.create');
        Route::post('/', [SalaryAdvanceController::class, 'store'])
            ->name('salary.advance.store');

        // Delete advance
        Route::delete('/{salaryAdvance}', [SalaryAdvanceController::class, 'destroy'])
            ->name('salary.advance.destroy');

        // Edit advances for employee
        Route::get('/employee/{employee}/edit', [SalaryAdvanceController::class, 'edit'])
            ->name('salary.advance.edit');

        //  Edit single advance
        Route::get('/{id}/edit', [SalaryAdvanceController::class, 'editSingle'])
            ->name('salary.advance.edit-single');

        // Update advance
        Route::put('/{id}', [SalaryAdvanceController::class, 'update'])
            ->name('salary.advance.update');

        Route::get('/salary/advances/export-pdf', [SalaryController::class, 'exportSalaryAdvancesPdf'])
            ->name('salary.advance.export.pdf');
    });

    Route::prefix('meals')->group(function () {
        // Index and create routes
        Route::get('/', [MealsController::class, 'index'])->name('meals.index');
        Route::get('/create', [MealsController::class, 'create'])->name('meals.create');
        Route::post('/', [MealsController::class, 'store'])->name('meals.store');

        // Employee-specific routes
        Route::prefix('employee')->group(function () {
            Route::get('/{employee}/edit', [MealsController::class, 'editEmployeeMeals'])
                ->name('meals.employee.edit');
            Route::delete('/{employee}', [MealsController::class, 'destroyEmployeeMeals'])
                ->name('meals.employee.destroy');
        });

        // Single meal operations
        Route::get('/{meal}/edit', [MealsController::class, 'edit'])->name('meals.edit');
        Route::put('/{meal}', [MealsController::class, 'update'])->name('meals.update');
        Route::delete('/{meal}', [MealsController::class, 'destroy'])->name('meals.destroy');
    });

    //Uniforms
    Route::prefix('uniforms')->group(function () {

        Route::get('/', [UniformsController::class, 'index'])->name('uniforms.index');
        Route::get('/create', [UniformsController::class, 'create'])->name('uniforms.create');
        Route::post('/', [UniformsController::class, 'store'])->name('uniforms.store');

        // Individual uniform operations
        Route::get('/uniform/{uniform}/edit', [UniformsController::class, 'edit'])->name('uniforms.edit');
        Route::put('/uniform/{uniform}', [UniformsController::class, 'update'])->name('uniforms.update');
        Route::delete('/uniform/{uniform}', [UniformsController::class, 'destroy'])->name('uniforms.destroy');

        // Employee-specific routes
        Route::prefix('employee')->group(function () {
            Route::get('/{employee}/edit', [UniformsController::class, 'editEmployeeUniforms'])
                ->name('uniforms.employee.edit');
        });
    });

    //Salaries
    Route::get('salaries/overview', [SalaryController::class, 'overview'])->name('salaries.overview');
    Route::get('/salaries/{employee}/slip-view/{month}', [SalaryController::class, 'viewSlip'])->name('salaries.slip.view');
    Route::get('/salaries/download-all-slips', [SalaryController::class, 'downloadAllSlips'])->name('salaries.download-all-slips');
    Route::post('/salaries/{employee}/slip-download/{month}', [SalaryController::class, 'downloadSlip'])->name('salaries.slip.download');
    Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries');
    Route::get('/salaries/{employee}', [SalaryController::class, 'show'])->name('salaries.show');
    Route::get('/salaries/overview/pdf', [SalaryController::class, 'exportSalaryOverviewPdf'])->name('salaries.overview.pdf');
    //User Resources
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    //Salary Settings Routes
    Route::get('/salary-settings', [SalarySettingController::class, 'index'])->name('salary-settings.index');
    Route::put('/salary-settings', [SalarySettingController::class, 'update'])->name('salary-settings.update');
});

require __DIR__ . '/auth.php';
