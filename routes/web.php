<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminContactRequestController;
use App\Http\Controllers\Api\LeadApiController;
use App\Http\Controllers\AylikHesapController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevamController;
use App\Http\Controllers\GelirGiderController;
use App\Http\Controllers\IsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UstaController;
use Illuminate\Support\Facades\Route;

// Tanıtım Sitesinden Gelen Webhook / API Rotası
Route::post('/api/v1/website-leads', [LeadApiController::class, 'store'])->name('api.website-leads');

// Auth rotaları (Breeze)
require __DIR__.'/auth.php';

// Ana sayfa → dashboard yönlendir
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Üyelik Süresi Doldu Sayfası
Route::get('/subscription-expired', function () {
    return view('errors.subscription-expired');
})->middleware(['auth'])->name('subscription.expired');

// 👑 ANA ADMIN ROTALARI (Sadece Admin yetkisi olanlar girebilir)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Ana Panel Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Üye Yönetimi
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/extend', [AdminUserController::class, 'extend'])->name('users.extend');
    Route::get('/users/{user}/backup', [AdminUserController::class, 'backup'])->name('users.backup');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Müşteri İletişim ve Ücretsiz Deneme Talepleri
    Route::get('/requests', [AdminContactRequestController::class, 'index'])->name('requests.index');
    Route::patch('/requests/{contactRequest}/status', [AdminContactRequestController::class, 'updateStatus'])->name('requests.update-status');
    Route::post('/requests/{contactRequest}/convert', [AdminContactRequestController::class, 'convertToUser'])->name('requests.convert');
    Route::delete('/requests/{contactRequest}', [AdminContactRequestController::class, 'destroy'])->name('requests.destroy');

    // Günlük Sistem Yedekleri
    Route::post('/backups/create', [AdminDashboardController::class, 'createBackup'])->name('backups.create');
    Route::get('/backups/download/{filename}', [AdminDashboardController::class, 'downloadBackup'])->name('backups.download');
    Route::delete('/backups/delete/{filename}', [AdminDashboardController::class, 'deleteBackup'])->name('backups.delete');

    // Sunucu ve Domain (Cloudflare & DigitalOcean) Ayarları
    Route::post('/settings/infrastructure', [AdminDashboardController::class, 'updateInfrastructureSettings'])->name('settings.infrastructure');
});

// Korumalı Kullanıcı Rotaları (Giriş yapılmış & verified & aktif abonelik)
Route::middleware(['auth', 'verified', 'check.subscription'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil (Breeze + Abonelik bilgisi)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ustalar
    Route::resource('ustalar', UstaController::class)->parameters(['ustalar' => 'usta']);

    // İşler
    Route::resource('isler', IsController::class)->parameters(['isler' => 'is']);

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
    Route::get('/aylik-hesap/hakedis-bilgi', [AylikHesapController::class, 'hakedisJson'])->name('aylik-hesap.hakedis-bilgi');
    Route::get('/aylik-hesap/pdf/{usta}', [AylikHesapController::class, 'pdf'])->name('aylik-hesap.pdf');
});
