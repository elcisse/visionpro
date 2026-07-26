<?php

use App\Http\Controllers\Auth\LoginController;
use App\Livewire\Chauffeurs\Manager as ChauffeursManager;
use App\Livewire\Clients\Manager as ClientsManager;
use App\Livewire\Contrats\Manager as ContratsManager;
use App\Livewire\Engins\Manager as EnginsManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/engins', EnginsManager::class)->name('engins.index');
    Route::get('/chauffeurs', ChauffeursManager::class)->name('chauffeurs.index');
    Route::get('/clients', ClientsManager::class)->name('clients.index');
    Route::get('/contrats', ContratsManager::class)->name('contrats.index');

    Route::get('/test-integration', function () {
        return view('test-integration');
    });
});
