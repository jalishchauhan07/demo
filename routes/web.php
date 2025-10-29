<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SlotController;

use App\Http\Controllers\WhatsAppController;

Route::get('/whatsapp', function () {
    return view('whatsapp.send');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    // WhatsApp Webhook (Twilio will POST here)
Route::post('/whatsapp/webhook', [WhatsAppController::class, 'receiveMessage']);

// Test endpoint (protected by auth if needed)
Route::get('/send-slots', [WhatsAppController::class, 'sendSlots']);

});

// Admin routes
Route::middleware('admin.auth')->group(function () {
    Route::get('/', [SlotController::class, 'index'])->name('dashboard');
    Route::get('/slots/{date}/available', [SlotController::class, 'available'])->name('slots.available');
    Route::get('/dashboard', [SlotController::class, 'index'])->name('dashboard.index');

    
    Route::get('/add-slot', [SlotController::class, 'create'])->name('slot.create');
    Route::post('/add-slot', [SlotController::class, 'store'])->name('slot.store');
    
    Route::get('/edit-slot/{id}', [SlotController::class, 'edit'])->name('slot.edit');
    Route::post('/edit-slot/{id}', [SlotController::class, 'update'])->name('slot.update');
    
    Route::get('/delete-slot/{id}', [SlotController::class, 'destroy'])->name('slot.delete');
    
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});