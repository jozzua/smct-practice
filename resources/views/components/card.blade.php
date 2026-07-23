@props(['title' => null, 'image' => null, 'imageAlt' => ''])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if ($image)
        <img class="product-thumb" src="{{ $image }}" alt="{{ $imageAlt }}">
    @endif
    @if ($title)
        <h2 class="card-title">{{ $title }}</h2>
    @endif
    {{ $slot }}
</div>
