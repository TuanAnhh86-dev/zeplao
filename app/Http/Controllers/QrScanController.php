<?php

namespace App\Http\Controllers;

use App\Models\QrInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QrScanController extends Controller
{
    public function index(): View
    {
        return view('admin.qr-scan');
    }

    public function lookup(Request $request): View
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:100']]);
        $qrInfo = QrInfo::query()->with('orderItem.order.user', 'orderItem.ticketType.event')
            ->where('token', trim($data['code']))->first();

        return view('admin.qr-scan', [
            'qrInfo' => $qrInfo,
            'scanCode' => $data['code'],
        ]);
    }

    public function checkIn(QrInfo $qrInfo): RedirectResponse
    {
        $result = DB::transaction(function () use ($qrInfo): string {
            $ticket = QrInfo::query()->with('orderItem.order')->lockForUpdate()->findOrFail($qrInfo->id);
            if ($ticket->orderItem?->order?->status !== 'confirmed') {
                return 'invalid';
            }
            if ($ticket->status !== 'unused') {
                return 'used';
            }

            $ticket->update(['status' => 'used', 'used_at' => now()]);

            return 'checked_in';
        });

        return redirect()->route('admin.qr-scan.lookup', ['code' => $qrInfo->token])
            ->with('status', match ($result) {
                'checked_in' => 'Check-in thành công. Vé đã được đánh dấu đã sử dụng.',
                'used' => 'Vé này đã được sử dụng trước đó.',
                default => 'Vé không hợp lệ hoặc đơn chưa thanh toán.',
            });
    }
}
