<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'sku', 'price_cents', 'description'])]
class Product extends Model
{
    private const CLASSIC_AIRPOT_SKU = 'XH-5832';

    private const CLASSIC_AIRPOT_SALE_PRICE_CENTS = 129900;

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

    public function salePriceCents(): int
    {
        if ($this->sku === self::CLASSIC_AIRPOT_SKU) {
            return self::CLASSIC_AIRPOT_SALE_PRICE_CENTS;
        }

        return intdiv($this->price_cents, 2);
    }

    public function discountPercentage(): int
    {
        if ($this->price_cents === 0) {
            return 0;
        }

        return (int) round(
            ($this->price_cents - $this->salePriceCents()) / $this->price_cents * 100,
        );
    }
}
