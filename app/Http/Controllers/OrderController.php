<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request, Event $event): JsonResponse
    {
        $data = $request->validate([
            'tickets' => ['required', 'array', 'min:1'],
            'tickets.*' => ['required', 'integer', 'min:1', 'max:10'],
            'idempotency_key' => ['required', 'uuid'],
        ]);
        ksort($data['tickets'], SORT_NUMERIC);
        try {
            $order = DB::transaction(function () use ($data, $event, $request): Order {
            $tickets = TicketType::query()->where('event_id', $event->id)
                ->whereIn('id', array_keys($data['tickets']))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $existing = Order::where('user_id', $request->user()->id)
                ->where('idempotency_key', $data['idempotency_key'])->first();
            if ($existing) return $existing;

            if ($tickets->count() !== count($data['tickets'])) {
                throw ValidationException::withMessages(['tickets' => 'Một hạng vé không còn tồn tại. Vui lòng tải lại trang.']);
            }

            $order = Order::create([
                'code' => 'TX-'.strtoupper(Str::random(8)),
                'user_id' => $request->user()->id,
                'total' => 0,
                'status' => 'pending',
                'idempotency_key' => $data['idempotency_key'],
            ]);
            $total = 0;

            foreach ($data['tickets'] as $id => $quantity) {
                $ticket = $tickets->get((int) $id);
                if (! $ticket) {
                    throw ValidationException::withMessages(['tickets' => 'Một hạng vé không còn tồn tại. Vui lòng tải lại trang.']);
                }
                $available = max(0, $ticket->quantity - $ticket->sold);
                if ($quantity > $available) {
                    throw new HttpResponseException(response()->json([
                        'message' => $available > 0 ? "Hạng vé {$ticket->name} chỉ còn {$available} vé." : "Hạng vé {$ticket->name} đã hết vé.",
                        'stock' => ['id' => $ticket->id, 'available' => $available],
                    ], 409));
                }
                $subtotal = $ticket->price * $quantity;
                $total += $subtotal;
                $order->items()->create([
                    'ticket_type_id' => $ticket->id,
                    'ticket_name' => $ticket->name,
                    'unit_price' => $ticket->price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total' => $total]);
            return $order;
            }, attempts: 3);
        } catch (QueryException $exception) {
            $order = Order::where('user_id', $request->user()->id)
                ->where('idempotency_key', $data['idempotency_key'])->first();
            if (! $order) throw $exception;
        }

        if ($order->status === 'cancelled') {
            return response()->json([
                'message' => 'Đơn này đã bị hủy. Vui lòng tạo đơn mới nếu vẫn muốn mua vé.',
                'code' => $order->code,
                'final' => true,
            ], 409);
        }

        return response()->json([
            'message' => $order->status === 'confirmed'
                ? 'Đơn vé đã được thanh toán.'
                : 'Đơn đã tạo. Vui lòng tiếp tục đến trang thanh toán.',
            'code' => $order->code,
            'payment_url' => route('payment.show', $order),
        ]);
    }

    public function payment(Request $request, Order $order): \Illuminate\View\View
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id || $request->user()->isAdmin(), 403);

        return view('payment', compact('order'));
    }

    public function cancel(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id || $request->user()->isAdmin(), 403);

        $newStatus = DB::transaction(function () use ($order): string {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);
            if ($locked->status === 'pending') {
                $locked->update(['status' => 'cancelled']);
            } elseif ($locked->status === 'confirmed') {
                $locked->update(['status' => 'refund_pending']);
            } else {
                abort(422, 'This order cannot be cancelled in its current status.');
            }

            return $locked->status;
        });

        $message = $newStatus === 'refund_pending'
            ? 'Refund requested. The administrator must complete the refund manually until payment integration is available.'
            : 'Unpaid order cancelled.';

        return redirect()->route('dashboard')->with('status', $message);
    }
}
