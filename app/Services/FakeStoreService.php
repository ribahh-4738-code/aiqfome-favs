<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;

class FakeStoreService
{
    protected $baseUrl = 'https://fakestoreapi.com/';

    public function fetchAndCacheProduct(int $externalId): ?Product
    {
        $product = Product::where('external_id', $externalId)->first();
        if ($product) {
            return $product;
        }
        $response = Http::get("{$this->baseUrl}products/{$externalId}");
        if (!$response->successful()) {
            return null;
        }
        $data = $response->json();

        return Product::create([
            'external_id' => $data['id'],
            'title' => $data['title'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? null,
            'image' => $data['image'] ?? null,
        ]);
    }
}
