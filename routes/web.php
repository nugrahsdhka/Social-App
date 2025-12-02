<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->middleware('guest');

Route::get('/dashboard', function () {
    // Opsional: Redirect dashboard langsung ke chat
    return redirect()->route('home.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');
    
    // --- FITUR CHAT & FOLLOW ---
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/search', [ChatController::class, 'search'])->name('chat.search');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::post('/chat/follow', [ChatController::class, 'follow'])->name('chat.follow');
    
    // --- FITUR PROFIL INSTAGRAM ---
    // Ini yang tadi KURANG. Route untuk melihat profil orang lain/diri sendiri
    Route::get('/p/{user:username}', [ProfileController::class, 'show'])->name('profile.show');

    // --- FITUR BAWAAN BREEZE (EDIT PROFIL) ---
    // Ini wajib ada agar tombol "Edit Profile" tidak error
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Route chat detail (ditaruh paling bawah agar tidak menabrak route lain)
    Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');
});

require __DIR__.'/auth.php';