<?php

use App\Http\Controllers\Web\MarketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketController::class, 'index'])->name('market.index');
