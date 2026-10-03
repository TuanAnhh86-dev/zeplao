<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\TicketType;
use App\Services\OrderReservationService;
use App\Services\VnpayService;
use App\Services\TicketQrService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VnpayController extends Controller
{
    public function start(Request $request, Order $order, OrderReservationService $reservations, VnpayService $vnpay): RedirectResponse
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id || $request->user()->isAdmin(), 403);
        $reservations->expirePendingOrders();
        $order->refresh();
        abort_unless($order->status === 'pending' && $order->expires_at?->isFuture(), 422, 'Order expired or is no longer payable.');

        return redirect()->away($vnpay->createPaymentUrl($order, $request));
    }

    public function returned(
        Request $request,
        VnpayService $vnpay,
        TicketQrService $ticketQrs,
        OrderReservationService $reservations,
    ): RedirectResponse
    {
        $query = $request->query();
        $signatureValid = $vnpay->verifySignature($query);
        $txnRef = $query['vnp_TxnRef'] ?? null;
        $order = is_string($txnRef) ? Order::query()->where('code', $txnRef)->first() : null;
        $matchesOrder = $order && $vnpay->matchesOrder($query, $order);
        $this->recordCallback($query, 'return', $signatureValid, $matchesOrder ? $order : null);

        abort_unless($signatureValid, 400, 'Invalid VNPay response signature.');
        abort_unless($order, 404, 'Order not found in VNPay response.');
        abort_unless($matchesOrder, 400, 'Payment details do not match the order.');

        $successful = ($query['vnp_ResponseCode'] ?? null) === '00'
            && ($query['vnp_TransactionStatus'] ?? null) === '00';
        $transactionId = $query['vnp_TransactionNo'] ?? null;
        if ($successful && is_string($transactionId) && $transactionId !== '') {
            $result = $this->confirmPayment($order, $transactionId, $ticketQrs, $reservations);
            $message = $result['code'] === '00'
                ? 'VNPay payment succeeded. The order has been updated.'
                : 'VNPay reported payment success, but the order needs manual reconciliation. The transaction was recorded.';
        } else {
            $message = 'VNPay payment did not succeed or is still processing. Response code: '.($query['vnp_ResponseCode'] ?? 'unknown').'. Check the order status before retrying.';
        }

        return redirect()->route('payment.show', $order)->with('status', $message);
    }

    public function ipn(
        Request $request,
        VnpayService $vnpay,
        TicketQrService $ticketQrs,
        OrderReservationService $reservations,
    ): JsonResponse
    {
        $query = $request->query();
        $signatureValid = $vnpay->verifySignature($query);
        $txnRef = $query['vnp_TxnRef'] ?? null;
        $order = is_string($txnRef) ? Order::query()->where('code', $txnRef)->first() : null;
        $matchesOrder = $order && $vnpay->matchesOrder($query, $order);
        $this->recordCallback($query, 'ipn', $signatureValid, $matchesOrder ? $order : null);

        if (! $signatureValid) {
            return $this->ipnResponse('97', 'Invalid signature');
        }
        if (! is_string($txnRef) || ! $order) {
            return $this->ipnResponse('01', 'Order not found');
        }
        if (! $matchesOrder) {
            return $this->ipnResponse('04', 'Invalid amount or order information');
        }
        if (($query['vnp_ResponseCode'] ?? null) !== '00' || ($query['vnp_TransactionStatus'] ?? null) !== '00') {
            return $this->ipnResponse('00', 'Confirm Success');
        }

        $transactionId = $query['vnp_TransactionNo'] ?? null;
        if (! is_string($transactionId) || $transactionId === '') {
            return $this->ipnResponse('99', 'Missing transaction number');
        }

        $result = $this->confirmPayment($order, $transactionId, $ticketQrs, $reservations);

        return $this->ipnResponse($result['code'], $result['message']);
    }

    private function confirmPayment(
        Order $order,
        string $transactionId,
        TicketQrService $ticketQrs,
        OrderReservationService $reservations,
    ): array
    {
        try {
            return DB::transaction(function () use ($order, $transactionId, $ticketQrs, $reservations): array {
                $locked = Order::query()->lockForUpdate()->findOrFail($order->id);
                if ($locked->status === 'confirmed') {
                    return $locked->payment_transaction_id === $transactionId
                        ? ['code' => '00', 'message' => 'Confirm Success']
                        : ['code' => '02', 'message' => 'Order already confirmed'];
                }
                if (! in_array($locked->status, ['pending', 'cancelled'], true)) {
                    return ['code' => '02', 'message' => 'Order is not payable'];
                }

                $items = $locked->items()->whereNotNull('ticket_type_id')->orderBy('ticket_type_id')->get();
                if ($items->isEmpty() || $items->count() !== $locked->items()->count()) {
                    return ['code' => '04', 'message' => 'Invalid order items'];
                }
                foreach ($items as $item) {
                    $ticket = TicketType::query()->whereKey($item->ticket_type_id)->lockForUpdate()->first();
                    $available = $ticket
                        ? $ticket->quantity - $ticket->sold - $reservations->reservedQuantity($ticket->id, $locked->id)
                        : 0;
                    if (! $ticket || $item->quantity > $available) {
                        return ['code' => '04', 'message' => 'Ticket inventory is no longer available'];
                    }
                    $ticket->increment('sold', $item->quantity);
                }
                $locked->update(['status' => 'confirmed', 'payment_transaction_id' => $transactionId]);
                $locked->setRelation('items', $items);
                $ticketQrs->issueForOrder($locked);

                return ['code' => '00', 'message' => 'Confirm Success'];
            }, attempts: 3);
        } catch (QueryException $exception) {
            if (Order::query()->where('payment_transaction_id', $transactionId)->exists()) {
                return ['code' => '02', 'message' => 'Transaction already used'];
            }
            throw $exception;
        }
    }

    private function recordCallback(array $query, string $type, bool $signatureValid, ?Order $order): void
    {
        $payload = array_filter($query, fn ($key) => str_starts_with((string) $key, 'vnp_')
            && ! in_array($key, ['vnp_SecureHash', 'vnp_SecureHashType'], true), ARRAY_FILTER_USE_KEY);

        PaymentTransaction::query()->create([
            'order_id' => $order?->id,
            'callback_type' => $type,
            'transaction_no' => is_scalar($query['vnp_TransactionNo'] ?? null) ? (string) $query['vnp_TransactionNo'] : null,
            'txn_ref' => is_scalar($query['vnp_TxnRef'] ?? null) ? (string) $query['vnp_TxnRef'] : null,
            'amount' => is_scalar($query['vnp_Amount'] ?? null) && ctype_digit((string) $query['vnp_Amount']) ? (int) $query['vnp_Amount'] : null,
            'response_code' => is_scalar($query['vnp_ResponseCode'] ?? null) ? (string) $query['vnp_ResponseCode'] : null,
            'transaction_status' => is_scalar($query['vnp_TransactionStatus'] ?? null) ? (string) $query['vnp_TransactionStatus'] : null,
            'bank_code' => is_scalar($query['vnp_BankCode'] ?? null) ? (string) $query['vnp_BankCode'] : null,
            'pay_date' => is_scalar($query['vnp_PayDate'] ?? null) ? (string) $query['vnp_PayDate'] : null,
            'signature_valid' => $signatureValid,
            'payload' => $payload,
        ]);
    }

    private function ipnResponse(string $code, string $message): JsonResponse
    {
        return response()->json(['RspCode' => $code, 'Message' => $message]);
    }
}
