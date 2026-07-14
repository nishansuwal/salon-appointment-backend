<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Repositories\Order\OrderRepositoryInterface;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected OrderRepositoryInterface $orderRepository
    ) {}

    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $user = $request->user();

            $order = $this->orderRepository->createOrder([
                'order_number'   => 'ORD-' . strtoupper(Str::random(10)),
                'user_id'        => $user->id,
                'address_id'     => $validated['address_id'],
                'customer_name'  => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'order_status'   => 'pending',
                'vat'            => 0,
                'shipping_cost'  => 0,
                'total_amount'   => 0,
                'notes'          => $validated['notes'] ?? null,
            ]);

            $totalAmount = 0;

            foreach ($validated['items'] as $item) {

                $quantity = (int) $item['quantity'];
                $price    = (float) $item['price'];

                // Variant product
                if (!empty($item['product_variant_id'])) {
                    $variant = ProductVariant::lockForUpdate()
                        ->findOrFail($item['product_variant_id']);

                    if ($variant->stock < $quantity) {
                        throw new \Exception(
                            "Insufficient stock for SKU {$variant->sku}"
                        );
                    }

                    $variant->decrement('stock', $quantity);
                }
                // Simple product
                else {
                    $product = Product::lockForUpdate()
                        ->findOrFail($item['product_id']);

                    if ($product->stock < $quantity) {
                        throw new \Exception(
                            "Insufficient stock for product {$product->name}"
                        );
                    }

                    $product->decrement('stock', $quantity);
                }

                $this->orderRepository->addItem($order, [
                    'product_id'         => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'quantity'           => $quantity,
                    'price'              => $price,
                ]);

                $totalAmount += $price * $quantity;
            }

            /* ======================
               Update Order Total
            ======================= */

            $this->orderRepository->updateTotal($order, $totalAmount);

            DB::commit();

            return $this->successResponse(
                $order->load('items'),
                'Order placed successfully.'
            );

        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->errorResponse(
                $e->getMessage(),
                400
            );
        }
    }
}
