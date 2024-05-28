<?php

namespace App\Http\Controllers;

use App\Jobs\DownloadProductImage;
use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class WooCommerceController extends Controller
{
    public function syncProducts()
    {

        // WooCommerce API credentials
        $consumerKey = 'ck_547cc1e0c953c44c4744cd29466ad2ba65a658d6';
        $consumerSecret = 'cs_c8973040acc5d8f4c581d67d611f03d5b3eb733d';
        $shopUrl = 'https://tests.kodeia.com/wordpress/wp-json/wc/v3/products';

        // Fetch products from WooCommerce API
        $response = Http::withBasicAuth($consumerKey, $consumerSecret)->get($shopUrl, [
            'per_page' => 15,
        ]);

        if ($response->successful()) {
            $products = $response->json();
            $userId = Auth::id(); // Get the logged-in user's ID

            foreach ($products as $productData) {
                $product = Product::updateOrCreate(
                    ['woocommerce_id' => $productData['id']],
                    [
                        'name' => $productData['name'],
                        'price' => $productData['price'],
                        'description' => $productData['description'],
                        'image_url' => $productData['images'][0]['src'] ?? null,
                    ]
                );

                // Dispatch the job to download the product image
                DownloadProductImage::dispatch($product);
            }

            return response()->json(['status' => 'success', 'message' => 'Products synced successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to sync products.'], 500);
    }
}
