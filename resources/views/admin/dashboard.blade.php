@extends('admin.layout')
@section('title', 'Tổng quan')
@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div><p class="text-sm font-semibold uppercase tracking-[.18em] text-violet-300">Bảng điều khiển</p><h1 class="mt-2 text-3xl font-bold">Xin chào, {{ auth()->user()->name }}</h1><p class="mt-2 text-sm text-neutral-400">Quản lý sự kiện, khách hàng, đơn vé và doanh thu.</p></div>
        <div class="flex flex-wrap gap-2"><a href="{{ route('admin.customers') }}" class="rounded-full border border-white/15 px-5 py-3 text-sm font-semibold hover:border-violet-400">Khách hàng</a><a href="{{ route('admin.events.create') }}" class="rounded-full bg-violet-600 px-5 py-3 text-sm font-bold hover:bg-violet-500">+ Tạo sự kiện</a></div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ([['Sự kiện', number_format($eventsCount), 'trong hệ thống'], ['Tổng sức chứa', number_format($ticketCount), 'vé'], ['Vé đã thanh toán', number_format($soldCount), 'vé'], ['Tổng giao dịch', number_format($ordersCount), 'đơn'], ['Chưa thanh toán', number_format($pendingOrdersCount), 'đơn chờ xử lý'], ['Doanh thu đã thanh toán', number_format($confirmedRevenue, 0, ',', '.').' ₫', 'lũy kế']] as [$label, $value, $hint])
            <article class="rounded-2xl border border-white/10 bg-neutral-900 p-5"><p class="text-sm text-neutral-400">{{ $label }}</p><p class="mt-3 text-3xl font-bold">{{ $value }}</p><p class="mt-1 text-xs text-neutral-500">{{ $hint }}</p></article>
        @endforeach
    </div>

    <section class="mt-8 rounded-2xl border border-white/10 bg-neutral-900 p-5 sm:p-6">
        <div class="mb-5"><p class="text-sm font-semibold uppercase tracking-[.15em] text-violet-300">Báo cáo</p><h2 class="mt-1 text-xl font-bold">Thống kê doanh thu</h2><p class="mt-1 text-sm text-neutral-400">Chỉ tính giao dịch đã xác nhận thanh toán.</p></div>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="mb-6 flex flex-wrap items-end gap-3">
            <label class="text-xs text-neutral-400">Xem theo<select name="revenue_period" class="mt-1 block rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm text-white"><option value="day" @selected($revenuePeriod === 'day')>Ngày</option><option value="month" @selected($revenuePeriod === 'month')>Tháng</option><option value="year" @selected($revenuePeriod === 'year')>Năm</option></select></label>
            @if ($revenuePeriod === 'day')<label class="text-xs text-neutral-400">Ngày<input type="date" name="revenue_date" value="{{ request('revenue_date', $revenueStart->toDateString()) }}" class="mt-1 block rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm text-white"></label>@endif
            @if ($revenuePeriod === 'month')<label class="text-xs text-neutral-400">Tháng<input type="month" name="revenue_month" value="{{ request('revenue_month', $revenueStart->format('Y-m')) }}" class="mt-1 block rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm text-white"></label>@endif
            @if ($revenuePeriod === 'year')<label class="text-xs text-neutral-400">Năm<input type="number" name="revenue_year" min="2000" max="2100" value="{{ request('revenue_year', $revenueStart->year) }}" class="mt-1 block w-32 rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm text-white"></label>@endif
            <button class="rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold hover:bg-violet-500">Xem báo cáo</button>
        </form>
        <div class="mb-5 grid gap-3 sm:grid-cols-2"><article class="rounded-xl border border-white/10 bg-black/20 p-4"><p class="text-sm text-neutral-400">Doanh thu kỳ này</p><p class="mt-2 text-2xl font-bold text-violet-300">{{ number_format($selectedRevenue, 0, ',', '.') }} ₫</p><p class="mt-1 text-xs text-neutral-500">{{ $selectedRevenueOrders }} giao dịch đã thanh toán</p></article><article class="rounded-xl border border-white/10 bg-black/20 p-4"><p class="text-sm text-neutral-400">Khoảng thời gian</p><p class="mt-2 text-lg font-bold">{{ $revenueStart->format('d/m/Y') }} – {{ $revenueEnd->format('d/m/Y') }}</p><p class="mt-1 text-xs text-neutral-500">Dữ liệu theo ngày tạo đơn</p></article></div>
        @php($maxRevenue = max(1, $revenueRows->max('revenue')))
        <div class="max-h-96 space-y-2 overflow-y-auto pr-1">
            @foreach ($revenueRows as $row)
                <div class="grid grid-cols-[58px_minmax(0,1fr)_auto] items-center gap-3 text-xs sm:grid-cols-[70px_minmax(0,1fr)_auto_auto]">
                    <span class="text-neutral-400">{{ $row['label'] }}</span>
                    <div class="h-2 overflow-hidden rounded-full bg-white/5"><div class="h-full rounded-full bg-violet-500" style="width: {{ $row['revenue'] ? max(2, $row['revenue'] / $maxRevenue * 100) : 0 }}%"></div></div>
                    <span class="text-right font-semibold">{{ number_format($row['revenue'], 0, ',', '.') }} ₫</span><span class="hidden text-right text-neutral-500 sm:block">{{ $row['orders'] }} đơn</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mt-8 overflow-hidden rounded-2xl border border-white/10 bg-neutral-900">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 px-5 py-4"><div><h2 class="font-bold">Giao dịch mới nhất</h2><p class="mt-1 text-xs text-neutral-400">Các đơn đặt vé gần đây</p></div><a href="{{ route('admin.orders') }}" class="text-sm font-semibold text-violet-300 hover:text-violet-200">Xem tất cả →</a></div>
        @forelse ($recentOrders as $order)
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/5 px-5 py-4 last:border-0"><div><p class="font-semibold">{{ $order->code }} <span class="ml-2 rounded-full bg-white/10 px-2 py-1 text-xs font-medium text-neutral-300">{{ ['pending' => 'Chưa thanh toán', 'confirmed' => 'Đã thanh toán', 'refund_pending' => 'Chờ hoàn tiền', 'refunded' => 'Đã hoàn tiền', 'cancelled' => 'Đã hủy'][$order->status] }}</span></p><p class="mt-1 text-xs text-neutral-400">{{ $order->user->name }} · {{ $order->items->sum('quantity') }} vé</p></div><p class="font-bold text-violet-300">{{ number_format($order->total, 0, ',', '.') }} ₫</p></div>
        @empty<div class="px-5 py-12 text-center text-sm text-neutral-400">Chưa có giao dịch nào.</div>@endforelse
    </section>
@endsection
