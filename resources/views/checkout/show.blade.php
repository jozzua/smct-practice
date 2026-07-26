@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <h1>Checkout</h1>

    @if ($products->isEmpty())
        <x-card>
            <p>Your cart is empty. <a href="{{ route('products.index') }}">Browse the products</a> to get started.</p>
        </x-card>
    @else
        <div class="checkout-grid">
            <x-card title="Your cart">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="num">Qty</th>
                            <th class="num">Unit price</th>
                            <th class="num">Amount</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td class="num">{{ $cart[$product->id] }}</td>
                                <td class="num">
                                    <del class="checkout-price-original">₱{{ number_format($product->price_cents / 100, 2) }}</del>
                                    <strong class="checkout-price-sale">₱{{ number_format($product->salePriceCents() / 100, 2) }}</strong>
                                </td>
                                <td class="num">₱{{ number_format($product->salePriceCents() * $cart[$product->id] / 100, 2) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('cart.destroy', $product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="link-button">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <dl class="totals">
                    <div><dt>Subtotal</dt><dd>₱{{ number_format($totals['subtotal_cents'] / 100, 2) }}</dd></div>
                    <div><dt>VAT (12%)</dt><dd>₱{{ number_format($totals['vat_cents'] / 100, 2) }}</dd></div>
                    <div><dt>Delivery</dt><dd>{{ $totals['shipping_cents'] === 0 ? 'Free' : '₱'.number_format($totals['shipping_cents'] / 100, 2) }}</dd></div>
                    <div class="grand"><dt>Total</dt><dd>₱{{ number_format($totals['total_cents'] / 100, 2) }}</dd></div>
                </dl>
            </x-card>

            <x-card title="Delivery details">
                <p class="muted">Cash on delivery only for now. We'll email your confirmation.</p>

                <form method="POST" action="{{ route('checkout.store') }}" class="stacked-form">
                    @csrf

                    <label for="name">Full name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror

                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror

                    <label for="address">Street address</label>
                    <input id="address" type="text" name="address" value="{{ old('address') }}" required>
                    @error('address')<p class="field-error">{{ $message }}</p>@enderror

                    <label for="city">City</label>
                    <input id="city" type="text" name="city" value="{{ old('city') }}" required>
                    @error('city')<p class="field-error">{{ $message }}</p>@enderror

                    <button type="submit" class="btn btn-primary">Place order</button>
                </form>
            </x-card>
        </div>
    @endif
@endsection
