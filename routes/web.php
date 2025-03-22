<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;


Route::get('/language/switch/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'ar'])) {
        abort(404);
    }
    session()->put('locale', $locale);
    return back();
})->name('language.switch');

Route::get('/', fn() => redirect()->route('login'));

Auth::routes();

Route::middleware('auth')->group(function (){
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/user/online', [ChatController::class, 'markOnline']);
    Route::post('/user/offline', [ChatController::class, 'markOffline']);
    Route::get('/users', [ChatController::class, 'index'])->name('users');
    Route::get('/chat/{receiverId}', [ChatController::class, 'chat'])->name('chat');
    Route::post('/chat/{receiverId}/send', [ChatController::class, 'sendMessage']);
    Route::post('/chat/typing', [ChatController::class, 'typing']);
    Route::post('/online', [ChatController::class, 'setOnline']);
    Route::post('/offline', [ChatController::class, 'setOffline']);
    Route::post('/chat/voice-note', [ChatController::class, 'sendVoiceNote']);

});
