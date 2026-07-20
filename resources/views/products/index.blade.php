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
                    <div class="quantity-field">
                        <label for="quantity-{{ $product->id }}">Qty</label>
                        <span class="quantity-combo">
                            <input
                                id="quantity-{{ $product->id }}"
                                type="number"
                                name="quantity"
                                value="1"
                                min="1"
                                inputmode="numeric"
                                autocomplete="off"
                            >
                            <button
                                type="button"
                                class="quantity-toggle"
                                aria-label="Open quick quantity choices"
                                aria-expanded="false"
                            >
                            </button>
                            <span class="quantity-menu" hidden>
                                @for ($quantity = 1; $quantity <= 10; $quantity++)
                                    <button type="button" data-quantity="{{ $quantity }}">{{ $quantity }}</button>
                                @endfor
                            </span>
                        </span>
                    </div>
                    <button type="submit" class="btn btn-primary">Add to cart</button>
                </form>
            </x-card>
        @endforeach
    </div>

    <script>
        document.querySelectorAll('.quantity-combo').forEach((combo) => {
            const input = combo.querySelector('input');
            const toggle = combo.querySelector('.quantity-toggle');
            const menu = combo.querySelector('.quantity-menu');

            toggle.addEventListener('click', () => {
                const willOpen = menu.hidden;

                document.querySelectorAll('.quantity-menu').forEach((openMenu) => {
                    openMenu.hidden = true;
                    openMenu.closest('.quantity-combo')
                        .querySelector('.quantity-toggle')
                        .setAttribute('aria-expanded', 'false');
                });

                menu.hidden = !willOpen;
                toggle.setAttribute('aria-expanded', String(willOpen));
            });

            menu.querySelectorAll('button').forEach((option) => {
                option.addEventListener('click', () => {
                    input.value = option.dataset.quantity;
                    menu.hidden = true;
                    toggle.setAttribute('aria-expanded', 'false');
                    input.focus();
                });
            });
        });

        document.addEventListener('click', (event) => {
            if (event.target.closest('.quantity-combo')) {
                return;
            }

            document.querySelectorAll('.quantity-menu').forEach((menu) => {
                menu.hidden = true;
                menu.closest('.quantity-combo')
                    .querySelector('.quantity-toggle')
                    .setAttribute('aria-expanded', 'false');
            });
        });
    </script>
@endsection
