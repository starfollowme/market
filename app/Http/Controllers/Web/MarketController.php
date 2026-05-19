<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;

class MarketController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->active()
            ->orderBy('name')
            ->get();

        return view('market.index', [
            'products' => $products,
            'apiBaseUrl' => url('/api/v1'),
        ]);
    }
}
