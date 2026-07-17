<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ShortUrlController;

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
});

Route::middleware(['auth'])->group(function () {

    Route::post('/invite', 
        [InvitationController::class, 'store']
    )->middleware('role:super_admin,admin');

});



Route::middleware(['auth'])->group(function () {

    Route::get('/short-urls', [ShortUrlController::class, 'index']);

    Route::post('/short-urls', [ShortUrlController::class, 'store']);
});

Route::get('/{code}', [ShortUrlController::class, 'redirect']);

require __DIR__.'/auth.php';
