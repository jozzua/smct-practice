<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'sku', 'price_cents', 'description'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    public function imageUrl(): string
    {
        $encodedSku = rawurlencode($this->sku);
        $localPath = "images/products/{$encodedSku}.jpeg";

        if (is_file(public_path($localPath))) {
            return "/{$localPath}";
        }

        return "https://picsum.photos/seed/{$encodedSku}/240";
    }
}
