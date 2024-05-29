<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DownloadProductImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;
    protected $imageUrl;

    public function __construct(Product $product, $imageUrl)
    {
        $this->product = $product;
        $this->imageUrl = $imageUrl;
    }

    public function handle()
    {
        try {
            if ($this->imageUrl) {
                // Download the image
                $imageContents = file_get_contents($this->imageUrl);
                $imagePath = 'images/' . uniqid() . '.jpg'; // Generate a random filename
                Storage::put($imagePath, $imageContents);

                // Update the product's image_filename
                $this->product->image_filename = $imagePath;
                $this->product->save();

                Log::info('Image downloaded and saved successfully', ['product_id' => $this->product->id, 'image_path' => $imagePath]);
            } else {
                Log::warning('No image URL provided for product', ['product_id' => $this->product->id]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to download image', ['product_id' => $this->product->id, 'error' => $e->getMessage()]);
        }
    }
}
