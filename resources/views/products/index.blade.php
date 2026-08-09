@extends('layouts.app')

@section('content')
    <h1>{{ config('app.name') }}</h1>
    <p class="page-note">Everything ships nationwide. Free delivery on orders ₱5,000 and up.</p>


    <div class="product-grid">
        @foreach ($products as $product)
            <x-card
                :title="$product->name"
                :image="$product->imageUrl()"
                :image-alt="$product->name . ' sample photo'"
            >
                <p class="sku" aria-label="SKU {{ $product->sku }}">
                    <span class="sku-label">SKU</span>
                    <span class="sku-barcode" aria-hidden="true"></span>
                    <span class="sku-code">{{ $product->sku }}</span>
                </p>
                <p class="price">
                    <del class="price-original">₱{{ number_format($product->price_cents / 100, 2) }}</del>
                    <span class="price-sale">₱{{ number_format($product->salePriceCents() / 100, 2) }}</span>
                    <span class="price-discount">{{ $product->discountPercentage() }}% off</span>
                </p>
                <p class="muted">{{ $product->description }}</p>

                <form method="POST" action="{{ route('cart.store') }}" class="add-to-cart">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="quantity-field">
                        <label for="quantity-{{ $product->id }}">Qty</label>
                        <div class="quantity-picker" data-quantity-picker>
                            <div class="quantity-combo">
                                <input
                                    id="quantity-{{ $product->id }}"
                                    type="number"
                                    name="quantity"
                                    value="1"
                                    min="1"
                                    inputmode="numeric"
                                    autocomplete="off"
                                    data-quantity-input
                                >
                                <button
                                    type="button"
                                    class="quantity-toggle"
                                    aria-label="Choose a preset quantity"
                                    aria-expanded="false"
                                    aria-controls="quantity-options-{{ $product->id }}"
                                    data-quantity-toggle
                                >
                                    <span aria-hidden="true">⌄</span>
                                </button>
                            </div>
                            <div
                                id="quantity-options-{{ $product->id }}"
                                class="quantity-options"
                                role="listbox"
                                aria-label="Preset quantities"
                                data-quantity-options
                                hidden
                            >
                                @foreach ([1, 2, 3, 5, 10, 15, 25, 50] as $quantity)
                                    <button
                                        type="button"
                                        class="quantity-option"
                                        role="option"
                                        aria-selected="{{ $quantity === 1 ? 'true' : 'false' }}"
                                        data-quantity-value="{{ $quantity }}"
                                    >
                                        {{ $quantity }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add to cart</button>
                </form>
            </x-card>
        @endforeach
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-quantity-picker]').forEach((picker) => {
            const input = picker.querySelector('[data-quantity-input]');
            const toggle = picker.querySelector('[data-quantity-toggle]');
            const options = picker.querySelector('[data-quantity-options]');
            const optionButtons = options.querySelectorAll('[data-quantity-value]');

            const close = () => {
                options.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
            };

            const syncSelection = () => {
                optionButtons.forEach((option) => {
                    option.setAttribute(
                        'aria-selected',
                        option.dataset.quantityValue === input.value ? 'true' : 'false',
                    );
                });
            };

            toggle.addEventListener('click', () => {
                const shouldOpen = options.hidden;

                document.querySelectorAll('[data-quantity-options]:not([hidden])').forEach((openOptions) => {
                    openOptions.hidden = true;
                    openOptions
                        .closest('[data-quantity-picker]')
                        .querySelector('[data-quantity-toggle]')
                        .setAttribute('aria-expanded', 'false');
                });

                options.hidden = !shouldOpen;
                toggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');

                if (shouldOpen) {
                    syncSelection();
                }
            });

            optionButtons.forEach((option) => {
                option.addEventListener('click', () => {
                    input.value = option.dataset.quantityValue;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    syncSelection();
                    close();
                    input.focus();
                });
            });

            input.addEventListener('input', syncSelection);

            picker.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !options.hidden) {
                    close();
                    toggle.focus();
                }
            });

            document.addEventListener('click', (event) => {
                if (!picker.contains(event.target)) {
                    close();
                }
            });
        });
    </script>
@endpush
