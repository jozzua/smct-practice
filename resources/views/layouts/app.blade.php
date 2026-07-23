<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@hasSection('title')@yield('title') · @endif{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="container nav-row">
            <a class="brand" href="{{ route('products.index') }}">{{ config('app.name') }}</a>
            <nav class="site-nav">
                <a href="{{ route('products.index') }}">Products</a>
                <a href="{{ route('orders.index') }}">Orders</a>
                <a href="{{ route('checkout.show') }}">Cart ({{ count(session('cart', [])) }})</a>
                @auth
                    <a href="{{ route('profile.edit') }}">My profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline-form">
                        @csrf
                        <button type="submit" class="link-button">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Log in</a>
                    <a href="{{ route('signup') }}">Sign up</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="container">
        @if (session('status'))
            <p class="flash">{{ session('status') }}</p>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>{{ config('app.name') }} — demo application. No real customer data.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
