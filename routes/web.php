<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\WhatsAppController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// WhatsApp message sender (frontend page)
Route::get('/whatsapp', function () {
    return view('whatsapp.send');
})->name('whatsapp.send');

// WhatsApp Webhook (Twilio or other API will POST here)
Route::post('/whatsapp/webhook', [WhatsAppController::class, 'receiveMessage'])->name('whatsapp.webhook');

// Optional: For manual testing
Route::get('/send-slots', [WhatsAppController::class, 'sendSlots'])->name('whatsapp.sendSlots');


/*
|--------------------------------------------------------------------------
| Guest Routes (Not logged in)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/.well-known/{any?}', function () {
    return response('', 204); // 204 = No Content
})->where('any', '.*');



/*
|--------------------------------------------------------------------------
| Admin Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware('admin.auth')->group(function () {
    Route::get('/', [SlotController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [SlotController::class, 'index'])->name('dashboard.index');

    // Slot management
    Route::get('/add-slot', [SlotController::class, 'create'])->name('slot.create');
    Route::post('/add-slot', [SlotController::class, 'store'])->name('slot.store');

    Route::get('/edit-slot/{id}', [SlotController::class, 'edit'])->name('slot.edit');
    Route::post('/edit-slot/{id}', [SlotController::class, 'update'])->name('slot.update');

    Route::get('/delete-slot/{id}', [SlotController::class, 'destroy'])->name('slot.delete');

    Route::get('/slots/{date}/available', [SlotController::class, 'available'])->name('slots.available');

    // Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
