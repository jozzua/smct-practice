@extends('layouts.app')

@section('content')
    <h1>{{ config('app.name') }}</h1>
    <p class="page-note">Everything ships nationwide. Free delivery on orders ₱5,000 and up.</p>


    <div class="product-grid">
        @foreach ($products as $product)
            <x-card
                :title="$product->name"
                :image="'https://picsum.photos/seed/' . urlencode($product->sku) . '/240'"
                :image-alt="$product->name . ' sample photo'"
            >
                <p class="sku">SKU {{ $product->sku }}</p>
                <p class="price">₱{{ number_format($product->price_cents / 100, 2) }}</p>
                <p class="muted">{{ $product->description }}</p>

                <form method="POST" action="{{ route('cart.store') }}" class="add-to-cart">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="quantity-field">
                        <label for="quantity-{{ $product->id }}">Qty</label>
                        <span class="quantity-combo">
                            <input
                                id="quantity-{{ $product->id }}"
                                type="number"
                                name="quantity"
                                value="1"
                                min="1"
                                list="quantity-options"
                                inputmode="numeric"
                                autocomplete="off"
                            >
                        </span>
                    </div>
                    <button type="submit" class="btn btn-primary">Add to cart</button>
                </form>
            </x-card>
        @endforeach
    </div>

    <datalist id="quantity-options">
        @foreach ([1, 2, 3, 4, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50] as $quantity)
            <option value="{{ $quantity }}">
        @endforeach
    </datalist>
@endsection
