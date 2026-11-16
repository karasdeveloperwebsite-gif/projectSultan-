<?php

use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Categories\CategoryIndex;
use App\Livewire\Admin\Collections\CollectionIndex;
use App\Livewire\Admin\Dashboard\Overview;
use App\Livewire\Admin\Gallery\GalleryIndex;
use App\Livewire\Admin\Inquiries\ContactMessageIndex;
use App\Livewire\Admin\Pages\PageIndex;
use App\Livewire\Admin\Products\ProductIndex;
use App\Livewire\Admin\Settings\SettingsManager;
use App\Livewire\Admin\Sliders\SliderIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public placeholder route
Route::get('/', function () {
    return view('welcome');
});

// Admin Authentication Routes
Route::get('/admin/login', Login::class)
    ->middleware('guest')
    ->name('login');

Route::get('/login', function () {
    return redirect()->route('login');
});

Route::post('/admin/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('admin.logout');

// Protected Admin Dashboard Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Overview::class)->name('dashboard');
    Route::get('/categories', CategoryIndex::class)->name('categories');
    Route::get('/products', ProductIndex::class)->name('products');
    Route::get('/collections', CollectionIndex::class)->name('collections');
    Route::get('/sliders', SliderIndex::class)->name('sliders');
    Route::get('/gallery', GalleryIndex::class)->name('gallery');
    Route::get('/pages', PageIndex::class)->name('pages');
    Route::get('/inquiries', ContactMessageIndex::class)->name('inquiries');
    Route::get('/settings', SettingsManager::class)->name('settings');
});
