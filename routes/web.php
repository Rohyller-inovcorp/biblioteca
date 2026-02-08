<?php

use App\Models\Book;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\BookExportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReviewedController;
use App\Http\Controllers\BookAlertController;
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
     
    // JUST  ADMIN (CRUD)
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);

        Route::get('books/export', [BookExportController::class, 'export'])->name('books.export');
        Route::get('/books/google-search', [BookController::class, 'googleSearch'])->name('books.google.search');

        Route::post('/books/google-import', [BookController::class, 'googleImport'])->name('books.google.import');
        Route::resource('books', BookController::class)->except(['index', 'show']);
        Route::resource('authors', AuthorController::class)->except(['index', 'show']);
        Route::resource('publishers', PublisherController::class)->except(['index', 'show']);
        Route::patch('/loans/{loan}', [LoanController::class, 'update'])->name('loans.update');
        Route::get('/reviews', [App\Http\Controllers\ReviewedController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}', [ReviewedController::class, 'update'])->name('reviews.update');
    });
    // (show lists)
    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::get('books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('authors', [AuthorController::class, 'index'])->name('authors.index');
    Route::get('publishers', [PublisherController::class, 'index'])->name('publishers.index');
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::get('/phpinfo', function () {
        phpinfo();
    });
    Route::get(('/users/{user}'), [UserController::class, 'show'])->name('users.show');
    Route::post('/reviews', [App\Http\Controllers\ReviewedController::class, 'store'])->name('reviews.store');
    Route::post('/book/alert', [BookAlertController::class, 'subscribe'])->name('book.alerts.subscribe');
});
