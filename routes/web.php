<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/private-chat/{user}', [ChatController::class, 'showPrivateChat'])->name('private.chat');
    Route::post('/private-chat/{user}', [ChatController::class, 'sendPrivateMessage'])->name('private.chat.send');

    Route::post('/groups', [GroupChatController::class, 'store'])->name('groups.store');
    Route::get('/group-chat/{chatGroup}', [GroupChatController::class, 'showGroupChat'])->name('group.chat');
    Route::post('/group-chat/{chatGroup}', [GroupChatController::class, 'sendGroupMessage'])->name('group.chat.send');

     Route::post('/group-chat/{chatGroup}/members', [GroupChatController::class, 'addMember'])->name('group.members.add');
    Route::delete('/group-chat/{chatGroup}/members/{user}', [GroupChatController::class, 'removeMember'])->name('group.members.remove');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});