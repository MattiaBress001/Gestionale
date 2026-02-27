<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\RegistrationController;

Route::get('/register/{eventId}', [RegistrationController::class, 'show'])->name('registrations.show');
Route::post('/register', [RegistrationController::class, 'store'])->name('registrations.store');
