<?php
use App\Http\Controllers\MessagesController;

Route::group(['prefix' => 'messages'], function () {
    Route::get('/', [MessagesController::class, 'index'])->name('messages');
    Route::get('/create', [MessagesController::class, 'create'])->name('messages.create');
    Route::post('/', [MessagesController::class, 'store'])->name('messages.store');
    Route::get('/{id}', [MessagesController::class, 'show'])->name('messages.show');
    Route::put('/{id}', [MessagesController::class, 'update'])->name('messages.update');
});
