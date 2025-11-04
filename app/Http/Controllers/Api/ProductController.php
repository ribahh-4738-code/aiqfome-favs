<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FakeStoreService;

class ProductController extends Controller
{
    protected $service;

    public function __construct(FakeStoreService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/products/{product}
     */
    public function show(string $id)
    {
        // execute
        $product = $this->service->fetchAndCacheProduct($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // output
        return response()->json($product);
    }
}
