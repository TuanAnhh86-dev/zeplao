@extends('admin.layout')
@section('title', 'Đơn đặt vé')
@section('content')
    @php
        $statusInfo = [
            'pending' => ['label' => 'Chưa thanh toán', 'badge' => 'bg-amber-300/10 text-amber-200 ring-amber-300/20', 'row' => 'border-amber-300/20 bg-amber-300/[.035]', 'dot' => 'bg-amber-300'],
            'confirmed' => ['label' => 'Đã thanh toán', 'badge' => 'bg-emerald-300/10 text-emerald-200 ring-emerald-300/20', 'row' => 'border-emerald-300/20 bg-emerald-300/[.035]', 'dot' => 'bg-emerald-300'],
            'cancelled' => ['label' => 'Đã hủy', 'badge' => 'bg-rose-300/10 text-rose-200 ring-rose-300/20', 'row' => 'border-rose-300/20 bg-rose-300/[.035]', 'dot' => 'bg-rose-300'],
        ];
    @endphp

    <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
        <div><p class="text-xs font-semibold uppercase tracking-[.2em] text-violet-300">Vận hành</p><h1 class="mt-2 text-3xl font-bold tracking-tight">Đơn đặt vé</h1><p class="mt-2 text-sm text-neutral-400">Theo dõi đơn, người mua và trạng thái thanh toán.</p></div>
        <a href="{{ route('admin.orders.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-3 text-sm font-semibold shadow-lg shadow-violet-950/30 transition hover:bg-violet-500">+ Tạo đơn đặt vé</a>
    </div>

    <section class="mb-5 grid gap-3 sm:grid-cols-3" aria-label="Tổng số đơn theo trạng thái">
        @foreach (['pending' => 'Chưa thanh toán', 'confirmed' => 'Đã thanh toán', 'cancelled' => 'Đã hủy'] as $key => $label)
            <a href="{{ route('admin.orders', ['status' => $key]) }}" class="flex items-center justify-between rounded-2xl border {{ $statusInfo[$key]['row'] }} p-4 transition hover:brightness-110">
                <span class="flex items-center gap-2 text-sm text-neutral-300"><span class="size-2 rounded-full {{ $statusInfo[$key]['dot'] }}"></span>{{ $label }}</span>
                <span class="text-xl font-bold">{{ number_format($statusCounts[$key] ?? 0) }}</span>
            </a>
        @endforeach
    </section>

    <form method="GET" action="{{ route('admin.orders') }}" class="mb-5 rounded-2xl border border-white/10 bg-neutral-900/80 p-4 sm:p-5">
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(260px,2fr)_minmax(180px,1fr)_minmax(150px,1fr)_minmax(150px,1fr)_auto]">
            <label class="text-xs font-medium text-neutral-400">Tìm đơn, sự kiện hoặc người mua<input type="search" name="q" value="{{ request('q') }}" placeholder="Mã đơn, tên sự kiện, email..." class="mt-1.5 w-full rounded-xl border border-white/10 bg-neutral-950 px-4 py-3 text-sm text-white outline-none placeholder:text-neutral-600 focus:border-violet-400"></label>
            <label class="text-xs font-medium text-neutral-400">Trạng thái<select name="status" class="mt-1.5 w-full rounded-xl border border-white/10 bg-neutral-950 px-4 py-3 text-sm text-white outline-none focus:border-violet-400"><option value="">Tất cả trạng thái</option>@foreach ($statusInfo as $key => $info)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $info['label'] }}</option>@endforeach</select></label>
            <label class="text-xs font-medium text-neutral-400">Từ ngày<input type="date" name="from" value="{{ request('from') }}" class="mt-1.5 w-full rounded-xl border border-white/10 bg-neutral-950 px-3 py-3 text-sm text-white outline-none focus:border-violet-400"></label>
            <label class="text-xs font-medium text-neutral-400">Đến ngày<input type="date" name="to" value="{{ request('to') }}" class="mt-1.5 w-full rounded-xl border border-white/10 bg-neutral-950 px-3 py-3 text-sm text-white outline-none focus:border-violet-400"></label>
            <button class="self-end rounded-xl bg-white px-5 py-3 text-sm font-semibold text-neutral-950 transition hover:bg-violet-100">Lọc đơn</button>
        </div>
        @if (request()->hasAny(['q', 'status', 'from', 'to']))<a href="{{ route('admin.orders') }}" class="mt-3 inline-flex text-xs font-medium text-violet-300 hover:text-violet-200">Xóa bộ lọc</a>@endif
    </form>

    <div class="mb-3 flex items-center justify-between text-sm"><p class="text-neutral-400">{{ number_format($orders->total()) }} đơn</p><p class="text-xs text-neutral-500">Mới nhất trước</p></div>
    <div class="space-y-3">
        @forelse ($orders as $order)
            @php
                $eventNames = $order->items->pluck('event_title')->filter()->unique()->values();
                $status = $statusInfo[$order->status];
            @endphp
            <article class="overflow-hidden rounded-2xl border {{ $status['row'] }} transition">
                <div class="flex flex-wrap items-start justify-between gap-4 p-4 sm:p-5">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2"><h2 class="text-lg font-bold">{{ $eventNames->isNotEmpty() ? $eventNames->join(', ') : 'Vé sự kiện' }}</h2><span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $status['badge'] }}">{{ $status['label'] }}</span></div>
                        <p class="mt-2 text-sm text-neutral-300">{{ $order->user->name }} <span class="text-neutral-600">·</span> {{ $order->user->email }}</p>
                        <p class="mt-1 text-xs text-neutral-500">Mã đơn {{ $order->code }} <span class="px-1">·</span> {{ $order->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y · H:i') }}</p>
                    </div>
                    <div class="min-w-32 text-left sm:text-right"><p class="text-xs text-neutral-500">Tổng tiền</p><p class="mt-1 text-xl font-bold text-white">{{ number_format($order->total, 0, ',', '.') }} ₫</p></div>
                </div>
                <div class="mx-4 border-t border-white/10 sm:mx-5"></div>
                <div class="grid gap-2 px-4 py-3 sm:grid-cols-2 sm:px-5">
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between gap-3 rounded-xl bg-black/20 px-3 py-2.5 text-sm"><span class="min-w-0 truncate text-neutral-300">{{ $item->ticket_name }} <span class="text-neutral-500">× {{ $item->quantity }}</span></span><span class="shrink-0 text-neutral-400">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</span></div>
                    @endforeach
                </div>
                @if ($order->status === 'pending')
                    <div class="flex flex-wrap justify-end gap-2 border-t border-white/10 px-4 py-3 sm:px-5"><form method="POST" action="{{ route('admin.orders.update', $order) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="cancelled"><button class="rounded-xl border border-rose-300/20 px-4 py-2 text-sm font-medium text-rose-200 transition hover:bg-rose-300/10">Hủy đơn</button></form><form method="POST" action="{{ route('admin.orders.update', $order) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="confirmed"><button class="rounded-xl bg-emerald-500/15 px-4 py-2 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-500/25">Ghi nhận đã thanh toán</button></form></div>
                @endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-white/15 px-5 py-14 text-center"><p class="font-semibold text-neutral-200">Không tìm thấy đơn phù hợp</p><p class="mt-1 text-sm text-neutral-500">Thử bỏ bớt bộ lọc hoặc tìm theo tên sự kiện.</p></div>
        @endforelse
    </div>
    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
