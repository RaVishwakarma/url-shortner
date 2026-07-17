<?php

use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('home');

Route::view('/dashboard', 'dashboard')
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/invite', [InvitationController::class, 'store'])
        ->middleware('role:super_admin,admin')
        ->name('invitations.store');

    Route::get('/short-urls', [ShortUrlController::class, 'index'])
        ->name('short-urls.index');
    Route::post('/short-urls', [ShortUrlController::class, 'store'])
        ->name('short-urls.store');
});

Route::get('/accept-invite/{token}', [InvitationController::class, 'accept'])
    ->name('invitations.accept');
Route::post('/accept-invite/{token}', [InvitationController::class, 'register'])
    ->name('invitations.register');

require __DIR__.'/auth.php';
Route::get('/{code}', [ShortUrlController::class, 'redirect'])
    ->whereAlphaNumeric('code')
    ->name('short-urls.redirect');
