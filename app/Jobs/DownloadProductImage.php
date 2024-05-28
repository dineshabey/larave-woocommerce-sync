<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DownloadProductImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function handle()
    {
        if ($this->product->image_url) {
            // Download the image
            $imageUrl = $this->product->image_url;
            $imageContents = file_get_contents($imageUrl);
            $imagePath = 'images/' . uniqid() . '.jpg'; // Generate a random filename
            Storage::put($imagePath, $imageContents);

            // Update the product's image_filename
            $this->product->image_filename = $imagePath;
            $this->product->save();
        }
    }
}
