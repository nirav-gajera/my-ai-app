<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KnowledgeDocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {

    // ── Page routes (HTML) ───────────────────────────────────────────
    Route::get('/dashboard',     DashboardController::class)->name('dashboard');
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/knowledge',     [KnowledgeDocumentController::class, 'page'])->name('knowledge.index');

    // ── Profile ──────────────────────────────────────────────────────
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::patch('/telegram', [TelegramController::class, 'telegramUpdate'])->name('telegram.update');
    Route::post('/telegram/link', [TelegramController::class, 'generateLink'])->name('telegram.link');
    Route::post('/telegram/unlink', [TelegramController::class, 'unlink'])->name('telegram.unlink');

    // ── Conversations API (JSON) ──────────────────────────────────────
    Route::post('/conversations',                          [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/conversations/{conversation}',            [ConversationController::class, 'show'])->name('conversations.show');
    Route::delete('/conversations/{conversation}',         [ConversationController::class, 'destroy'])->name('conversations.destroy');
    Route::post('/conversations/{conversation}/messages',  [ChatController::class, 'store'])->name('messages.store');

    // ── Knowledge documents API (JSON) ────────────────────────────────
    Route::get('/knowledge-documents',                         [KnowledgeDocumentController::class, 'index'])->name('knowledge-documents.index');
    Route::post('/knowledge-documents',                        [KnowledgeDocumentController::class, 'store'])->name('knowledge-documents.store');
    Route::put('/knowledge-documents/{knowledgeDocument}/reindex', [KnowledgeDocumentController::class, 'reindex'])->name('knowledge-documents.reindex');
    Route::delete('/knowledge-documents/{knowledgeDocument}',  [KnowledgeDocumentController::class, 'destroy'])->name('knowledge-documents.destroy');
});

// ── Admin routes ──────────────────────────────────────────────────────
Route::middleware('verified')->group(function () {
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('can:access-admin')
        ->group(function () {
            Route::resource('users', UsersController::class)->except('show');
            Route::resource('telegram-bots', TelegramBotController::class)->except('show');
        });
});

// ── Telegram webhook ─────────────────────────────────────────────────
Route::post('/telegram/webhook/{bot}', [TelegramController::class, 'webhook'])->name('telegram.webhook');
require __DIR__.'/auth.php';
