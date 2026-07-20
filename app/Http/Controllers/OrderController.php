<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Staff-facing list of every order on record.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $orders = Order::query()
            ->with('customer')
            ->withCount('items')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('status', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });

                    if (ctype_digit(ltrim($search, '#'))) {
                        $query->orWhere('id', (int) ltrim($search, '#'));
                    }
                });
            })
            ->latest('placed_at')
            ->get();

        return view('orders.index', [
            'orders' => $orders,
            'search' => $search,
        ]);
    }
}
