@extends('layouts.app')

@section('title', 'Orders')

@section('content')
    <h1>Orders</h1>
    <p class="page-note">
        {{ number_format($orders->count()) }}
        {{ $search === '' ? 'orders on record' : 'matching orders' }}, newest first.
    </p>

    <x-card>
        <form class="search-form" method="GET" action="{{ route('orders.index') }}">
            <label for="orders-search">Search orders</label>
            <div class="search-row">
                <input
                    id="orders-search"
                    name="search"
                    type="search"
                    value="{{ $search }}"
                    placeholder="Order #, customer, email, or status"
                >
                <button class="btn btn-primary" type="submit">Search</button>
                @if ($search !== '')
                    <a class="btn" href="{{ route('orders.index') }}">Clear</a>
                @endif
            </div>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th class="num">Total</th>
                    <th>Status</th>
                    <th>Placed</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->customer->name }}</td>
                        <td>{{ $order->items_count }}</td>
                        <td class="num">₱{{ number_format($order->total_cents / 100, 2) }}</td>
                        <td><span class="badge badge-{{ $order->status->value }}">{{ $order->status->label() }}</span></td>
                        <td>{{ $order->placed_at->format('M j, Y') }}</td>
                    </tr>
                @endforeach

                @if ($orders->isEmpty())
                    <tr>
                        <td colspan="6" class="empty-table">No orders found.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </x-card>
@endsection
