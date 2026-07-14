<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Models\OrderItem;

class OrderRepository implements OrderRepositoryInterface
{
    public function createOrder(array $data): Order
    {
        return Order::create($data);
    }

    public function addItem(Order $order, array $item): void
    {
        OrderItem::create([
            'order_id'           => $order->id,
            'product_id'         => $item['product_id'],
            'product_variant_id' => $item['product_variant_id'] ?? null,
            'quantity'           => $item['quantity'],
            'price'              => $item['price'],
        ]);
    }

    public function updateTotal(Order $order, float $total): void
    {
        $order->update([
            'total_amount' => $total,
        ]);
    }
}
