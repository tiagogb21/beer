<?php

use App\Livewire\Payment;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Volt::route('/', 'pages.catalog.home')
->name('home');

Volt::route('/products', 'pages.catalog.products')
->name('products');

Volt::route('/products/{slug}', 'pages.catalog.product-details')
->name('product.show');

Volt::route('/checkout', 'pages.catalog.checkout')
->name('checkout');

Volt::route('/cart', 'pages.catalog.cart')
->name('cart');

Volt::route('/order/confirmation/{order}', 'pages.catalog.order-confirmation')
->name('order.confirmation');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
