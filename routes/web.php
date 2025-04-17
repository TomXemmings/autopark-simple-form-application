<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('steps.step0');
});

Route::post('/register-phone', [AuthController::class, 'registerPhone']);
Route::post('/check-phone',    [AuthController::class, 'checkPhone']);

Route::middleware(['auth', 'step.progress',])->group(function () {
    Route::view('/step-1', 'steps.step1')->name('step.1');
    Route::view('/step-2', 'steps.step2')->name('step.2');
    Route::view('/step-3', 'steps.step3')->name('step.3');
    Route::get('/step-4', function () {
        return view('steps.step4', [
            'user' => auth()->user(),
        ]);
    });
    Route::view('/complete', 'steps.complete')->name('user.complete.success');

    Route::post('/step-1',   [AuthController::class, 'stepOne']);
    Route::post('/step-2',   [AuthController::class, 'stepTwo']);
    Route::post('/step-3',   [AuthController::class, 'stepThree']);
    Route::post('/step-4',   [AuthController::class, 'stepFour']);
    Route::post('/complete', [AuthController::class, 'completeRegistration'])->name('user.complete');
});

Route::get('/admin',   [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login',  [AdminController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::middleware(['admin.auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users',                                [AdminController::class, 'index'])->name('users');
    Route::get('/users/table',                          [AdminController::class, 'table'])->name('users.table');
    Route::get('/users/{user}',                         [AdminController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/update',                 [AdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}/documents/{document}', [AdminController::class, 'deleteDocument'])->name('users.document.delete');
    Route::post('/users/{user}/documents/upload',       [AdminController::class, 'uploadDocument'])->name('users.document.upload');
    Route::get('/users/{user}/export-csv',              [AdminController::class, 'exportSingleCsv'])->name('users.export.csv');
    Route::get('/users/{user}/print-documents',         [AdminController::class, 'printDocuments'])->name('users.print');
    Route::post('/users/export-selected-csv',           [AdminController::class, 'exportSelectedCsv'])->name('users.export.selected.csv');
});
