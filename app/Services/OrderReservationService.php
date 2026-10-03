<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderReservationService
{
    public const HOLD_MINUTES = 10;

    public function expirePendingOrders(): void
    {
        Order::query()
            ->where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->select('id')
            ->chunkById(100, function ($orders): void {
                foreach ($orders as $order) {
                    DB::transaction(function () use ($order): void {
                        $locked = Order::query()->lockForUpdate()->find($order->id);
                        if ($locked?->status === 'pending' && $locked->expires_at?->isPast()) {
                            $locked->update(['status' => 'cancelled']);
                        }
                    });
                }
            });
    }

    public function reservedQuantity(int $ticketTypeId): int
    {
        return (int) DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.ticket_type_id', $ticketTypeId)
            ->where('orders.status', 'pending')
            ->where('orders.expires_at', '>', now())
            ->sum('order_items.quantity');
    }
}
