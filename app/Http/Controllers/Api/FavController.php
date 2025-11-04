<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Fav;
use App\Models\Product;
use App\Services\FakeStoreService;
use Illuminate\Support\Facades\Auth;

class FavController extends Controller
{
    protected $fakeStoreService;

    public function __construct(FakeStoreService $fakeStoreService)
    {
        $this->fakeStoreService = $fakeStoreService;
    }

    /**
     * GET /api/clients/{client}/favs
     */
    public function index(Client $client)
    {
        // auth
        if (Auth::id() !== $client->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // execute
        $favorites = $client->favorites()->get();

        $formattedFavorites = $favorites->map(function ($fav) {
            return $this->formatFavResponse($fav);
        });

        // output
        return response()->json($formattedFavorites);
    }

    /**
     * POST /api/clients/{client}/favs
     */
    public function store(Request $request, Client $client)
    {
        // auth
        if (Auth::id() !== $client->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // validate
        $request->validate([
            'external_product_id' => 'required|integer',
            'review' => 'nullable|string|max:500',
        ]);
        $externalId = $request->input('external_product_id');

        // execute
        $product = $this->fakeStoreService->fetchAndCacheProduct($externalId);

        // output
        if (is_null($product)) {
            return response()->json(['message' => 'Product not found on API.'], 404);
        }

        if ($client->favorites()->where('product_id', $product->id)->exists()) {
            return response()->json(['message' => 'This product is already a favorite.'], 409);
        }

        $client->favorites()->attach($product->id, [
            'review' => $request->review,
        ]);

        $newFav = $client->favorites()->where('product_id', $product->id)->first();

        return response()->json([
            'message' => 'Product added to favorites.',
            'favorite' => $this->formatFavResponse($newFav)
        ], 201);
    }

    /**
     * GET /api/clients/{client}/favs/{fav}
     */
    public function show(Client $client, Fav $fav)
    {
        // auth
        if (Auth::id() !== $client->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // validate
        if ($fav->client_id !== $client->id) {
            return response()->json(['message' => 'Favorite not found.'], 404);
        }

        // execute
        $fav->load('product');

        // output
        return response()->json($this->formatFavShowResponse($fav));
    }

    /**
     * DELETE /api/clients/{client}/favs/{product_id_externo}
     */
    public function destroy(Client $client, int $external_product_id)
    {
        // auth
        if (Auth::id() !== $client->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // execute
        $product = Product::where('external_id', $external_product_id)->first();
        if (!$product) {
            return response()->json(['message' => 'Product not found on DB.'], 404);
        }
        $deletedCount = $client->favorites()->detach($product->id);
        if ($deletedCount === 0) {
            return response()->json(['message' => 'Favorite not found.'], 404);
        }

        // output
        return response()->json(null, 204);
    }

    protected function formatFavResponse($fav)
    {
        return [
            'id' => $fav->id,
            'title' => $fav->title,
            'image' => $fav->image,
            'price' => $fav->price,
            'review' => $fav->pivot->review ?? $fav->review,
            'favorite_id_local' => $fav->id
        ];
    }

    protected function formatFavShowResponse($fav)
    {
        $product = $fav->product;
        $review = $fav->review;
        $fav_id_local = $fav->id;

        if (!$product) {
            $product = $fav->product;
            $review = $fav->pivot->review ?? $fav->review;
            $fav_id_local = $fav->pivot->id ?? $fav->id;
        }

        if (!$product) {
            return null;
        }

        return [
            'id' => $product->external_id,
            'title' => $product->title,
            'image' => $product->image,
            'price' => $product->price,
            'review' => $review,
            'favorite_id_local' => $fav_id_local
        ];
    }
}
