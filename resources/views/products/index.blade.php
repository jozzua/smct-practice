@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <h1>Maligaya Trading Company</h1>
    <p class="page-note">Everything ships nationwide. Free delivery on orders ₱5,000 and up.</p>

    <div class="product-grid">
        @foreach ($products as $product)
            <x-card :title="$product->name">
                <p class="sku">SKU {{ $product->sku }}</p>
                <p class="price">₱{{ number_format($product->price_cents / 100, 2) }}</p>
                <p class="muted">{{ $product->description }}</p>

                <form method="POST" action="{{ route('cart.store') }}" class="add-to-cart">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <label>
                        Qty
                        <input type="number" name="quantity" value="1" min="1" max="10">
                    </label>
                    <button type="submit" class="btn btn-primary">Add to cart</button>
                </form>
            </x-card>
        @endforeach
    </div>
@endsection
