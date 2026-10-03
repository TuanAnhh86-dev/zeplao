<?php

namespace App\Services;

use App\Models\Order;
use App\Models\QrInfo;
use Illuminate\Support\Str;

class TicketQrService
{
    public function issueForOrder(Order $order): void
    {
        foreach ($order->items as $item) {
            $existingCount = $item->qrInfo()->count();
            for ($i = $existingCount; $i < $item->quantity; $i++) {
                QrInfo::query()->create([
                    'order_item_id' => $item->id,
                    'token' => (string) Str::uuid(),
                    'status' => 'unused',
                ]);
            }
        }
    }
}
