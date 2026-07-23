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
