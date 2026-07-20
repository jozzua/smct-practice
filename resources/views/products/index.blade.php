@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <h1>Maligaya Trading Company</h1>
    <p class="page-note">Everything ships nationwide. Free delivery on orders ₱5,000 and up.</p>

    @php
        $productPhotos = [
            'https://images.unsplash.com/photo-1523726491678-bf852e717f6a?auto=format&fit=crop&w=240&q=80',
            'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=240&q=80',
            'https://images.unsplash.com/photo-1556228578-8c89e6adf883?auto=format&fit=crop&w=240&q=80',
            'https://images.unsplash.com/photo-1586208958839-06c17cacdf08?auto=format&fit=crop&w=240&q=80',
            'https://images.unsplash.com/photo-1519710887729-78c5f3f24b59?auto=format&fit=crop&w=240&q=80',
        ];
    @endphp

    <div class="product-grid">
        @foreach ($products as $product)
            <x-card
                :title="$product->name"
                :image="$productPhotos[abs(crc32($product->sku)) % count($productPhotos)]"
                :image-alt="$product->name . ' product sample'"
            >
                <p class="sku">SKU {{ $product->sku }}</p>
                <p class="price">₱{{ number_format($product->price_cents / 100, 2) }}</p>
                <p class="muted">{{ $product->description }}</p>

                <form method="POST" action="{{ route('cart.store') }}" class="add-to-cart">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <label class="quantity-field">
                        <span>Qty</span>
                        <span class="quantity-combo">
                            <input
                                type="number"
                                name="quantity"
                                value="1"
                                min="1"
                                inputmode="numeric"
                                autocomplete="off"
                            >
                            <select
                                aria-label="Quick quantity choices"
                                onchange="this.closest('.quantity-combo').querySelector('[name=quantity]').value = this.value"
                            >
                                @for ($quantity = 1; $quantity <= 10; $quantity++)
                                    <option value="{{ $quantity }}">{{ $quantity }}</option>
                                @endfor
                            </select>
                        </span>
                    </label>
                    <button type="submit" class="btn btn-primary">Add to cart</button>
                </form>
            </x-card>
        @endforeach
    </div>
@endsection
