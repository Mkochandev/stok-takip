<?php

use App\Http\Controllers\AylikHesapController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevamController;
use App\Http\Controllers\GelirGiderController;
use App\Http\Controllers\IsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UstaController;
use Illuminate\Support\Facades\Route;

// Auth rotaları (Breeze)
require __DIR__.'/auth.php';

// Ana sayfa → dashboard yönlendir
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Korumalı rotalar
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ustalar
    Route::resource('ustalar', UstaController::class);

    // İşler
    Route::resource('isler', IsController::class);

    // Devam Takibi
    Route::get('/devam', [DevamController::class, 'index'])->name('devam.index');
    Route::post('/devam', [DevamController::class, 'store'])->name('devam.store');
    Route::delete('/devam/{devam}', [DevamController::class, 'destroy'])->name('devam.destroy');
    Route::post('/devam/sil-usta', [DevamController::class, 'destroyByUstaAndTarih'])->name('devam.destroyByUsta');

    // Gelir - Gider
    Route::get('/gelir-gider', [GelirGiderController::class, 'index'])->name('gelir-gider.index');
    Route::get('/gelir-gider/gelir-ekle', [GelirGiderController::class, 'createGelir'])->name('gelir-gider.createGelir');
    Route::post('/gelir-gider/gelir-ekle', [GelirGiderController::class, 'storeGelir'])->name('gelir-gider.storeGelir');
    Route::get('/gelir-gider/gider-ekle', [GelirGiderController::class, 'createGider'])->name('gelir-gider.createGider');
    Route::post('/gelir-gider/gider-ekle', [GelirGiderController::class, 'storeGider'])->name('gelir-gider.storeGider');
    Route::delete('/gelir-gider/gelir/{gelir}', [GelirGiderController::class, 'destroyGelir'])->name('gelir-gider.destroyGelir');
    Route::delete('/gelir-gider/gider/{gider}', [GelirGiderController::class, 'destroyGider'])->name('gelir-gider.destroyGider');

    // Aylık Hesap
    Route::get('/aylik-hesap', [AylikHesapController::class, 'index'])->name('aylik-hesap.index');
    Route::post('/aylik-hesap/odeme', [AylikHesapController::class, 'odemeYap'])->name('aylik-hesap.odeme');
    Route::post('/aylik-hesap/kapat', [AylikHesapController::class, 'hesabiKapat'])->name('aylik-hesap.kapat');
});
