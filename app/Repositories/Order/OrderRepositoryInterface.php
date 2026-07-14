<?php

namespace App\Repositories\Order;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function createOrder(array $data): Order;

    public function addItem(Order $order, array $item): void;

    public function updateTotal(Order $order, float $total): void;
}
