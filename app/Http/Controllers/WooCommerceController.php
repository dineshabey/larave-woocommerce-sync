<?php

namespace App\Http\Controllers;

use App\Jobs\DownloadProductImage;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class WooCommerceController extends Controller
{
    public function syncProducts(Request $request)
    {
        // Get the access token from the request header
        $accessToken = $request->bearerToken();



        if (!$accessToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access token not provided.',
            ], 401);
        }


        // Get WooCommerce API credentials from the config
        $shopUrl = config('app.woocommerce.shop_url');
        $consumerKey = config('app.woocommerce.consumer_key');
        $consumerSecret = config('app.woocommerce.consumer_secret');

        // Create a Guzzle client
        $client = new Client();

        try {
            // Make a request to the WooCommerce API
            $response = $client->request('GET', $shopUrl, [
                'auth' => [$consumerKey, $consumerSecret],
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'query' => [
                    'per_page' => 15,
                    ]
                ]);

                $products = json_decode($response->getBody(), true);

                $userId = Auth::id(); // Get the logged-in user's ID

            $syncedProducts = [];

            foreach ($products as $productData) {
                $product = Product::updateOrCreate(
                    [
                        'woocommerce_id' => $productData['id']
                    ],
                    [
                        'name' => $productData['name'],
                        'price' => $productData['price'],
                        'description' => $productData['description'],
                        'user_id' => $userId // Assign the user ID
                    ]
                );

                $imageUrl = count($productData['images']) > 0 ? $productData['images'][0]['src'] : null;

                // Dispatch the job to download the product image
                DownloadProductImage::dispatch($product, $imageUrl);

                $syncedProducts[] = $product->toArray();
            }

            Log::info('Products synced successfully', ['user_id' => $userId, 'products' => $syncedProducts]);

            return response()->json([
                'status' => 'success',
                'message' => 'Products synced successfully. Image download jobs dispatched.',
                'data' => $syncedProducts
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync products', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to sync products.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
