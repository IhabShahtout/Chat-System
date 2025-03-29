<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/change-language/{lang}', function ($lang) {
    if (in_array($lang, config('app.available_locales'))) {
        session()->put('locale', $lang);
    }
    return redirect()->back();
})->name('change-language');

Route::get('/', fn() => redirect()->route('login'));

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/{receiverId}', [ChatController::class, 'chat'])->name('chat.load');
    Route::post('/chat/{receiverId}/send', [ChatController::class, 'sendMessage']);
    Route::post('/chat/{receiverId}/typing', [ChatController::class, 'typing']);
    Route::post('/chat/voice-note', [ChatController::class, 'storeVoiceNote']);

    Route::post('/chat/attachment', [App\Http\Controllers\ChatController::class, 'sendAttachment']);
//    Route::post('/chat/attachment', [ChatController::class, 'storeAttachment']);
    Route::post('/user/online', [ChatController::class, 'markOnline']);
    Route::post('/user/offline', [ChatController::class, 'markOffline']);
});
