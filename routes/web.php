<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryAdvanceController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;




Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

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

    // Sites Management Routes
    Route::get('sites', [SiteController::class, 'index'])->name('sites.index');
    Route::get('sites/create', [SiteController::class, 'create'])->name('sites.create');
    Route::post('sites', [SiteController::class, 'store'])->name('sites.store');
    Route::get('sites/{site}/edit', [SiteController::class, 'edit'])->name('sites.edit');
    Route::put('sites/{site}', [SiteController::class, 'update'])->name('sites.update');
    Route::delete('sites/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');
    
    // Attendance Management Routes
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/site-entry', [AttendanceController::class, 'siteEntryForm'])->name('attendances.site-entry');
    Route::post('/attendances/site-entry', [AttendanceController::class, 'storeSiteEntry'])->name('attendances.site-entry.store');

    Route::get('/sites/{site}/assign', [SiteController::class, 'assignGuards'])->name('sites.assign');
    Route::post('/sites/{site}/assign', [SiteController::class, 'storeAssignedGuards'])->name('sites.assign.store');

    // Salary Advance 
    Route::get('/salary-advance', [SalaryAdvanceController::class, 'salaryAdvance'])->name('salary.advance');
    Route::get('/salary-advance/create', [SalaryAdvanceController::class, 'create'])->name('salary.advance.create');
    Route::post('/salary-advance', [SalaryAdvanceController::class, 'store'])->name('salary.advance.store');
    Route::get('/salary-advance/{salaryAdvance}/edit', [SalaryAdvanceController::class, 'edit'])->name('salary.advance.edit');
    Route::put('/salary-advance/{salaryAdvance}', [SalaryAdvanceController::class, 'update'])->name('salary.advance.update');
    Route::delete('/salary-advance/{salaryAdvance}', [SalaryAdvanceController::class, 'destroy'])->name('salary.advance.destroy');


    //Salaries
    Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries');
    Route::get('/salaries/{employee}', [SalaryController::class, 'show'])->name('salaries.show');

    //User Resources
    Route::get('/users', [UserController::class, 'index'])->name('users.index');    
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    


});

require __DIR__ . '/auth.php';
