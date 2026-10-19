<?php

use App\Http\Controllers\Admin\DictionaryEntryImportExportController;
use App\Http\Controllers\Admin\LexicographicImportExportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DictionaryContributionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicDictionaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/dictionary', [PublicDictionaryController::class, 'index'])->name('dictionary.index');
Route::get('/dictionary/contribute', [DictionaryContributionController::class, 'create'])->name('dictionary.contribute');
Route::post('/dictionary/contribute', [DictionaryContributionController::class, 'store'])->middleware('throttle:10,1')->name('dictionary.contribute.store');
Route::get('/dictionary/{lemma:public_id}', [PublicDictionaryController::class, 'show'])->name('dictionary.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister'])->middleware('throttle:5,1')->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin,editor,assessor')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
    });

    Route::middleware('role:admin,editor')->group(function () {
        Route::get('/admin/lexicographic-import', [LexicographicImportExportController::class, 'create'])->name('admin.lexicographic-import');
        Route::post('/admin/lexicographic-import', [LexicographicImportExportController::class, 'store'])->name('admin.lexicographic-import.store');
        Route::get('/admin/lexicographic-export', [LexicographicImportExportController::class, 'export'])->name('admin.lexicographic-export');
        Route::get('/admin/lexicographic-import-template', [LexicographicImportExportController::class, 'template'])->name('admin.lexicographic-import.template');
        Route::get('/admin/dictionary-import', [DictionaryEntryImportExportController::class, 'create'])->name('admin.dictionary-entries.import');
        Route::post('/admin/dictionary-import', [DictionaryEntryImportExportController::class, 'store'])->name('admin.dictionary-entries.import.store');
        Route::get('/admin/dictionary-export', [DictionaryEntryImportExportController::class, 'export'])->name('admin.dictionary-entries.export');
        Route::get('/admin/dictionary-import-template', [DictionaryEntryImportExportController::class, 'template'])->name('admin.dictionary-entries.template');
    });
});
