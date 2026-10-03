@extends('admin.layout')
@section('title', 'Khách hàng')
@section('content')
    <div class="mb-7"><p class="text-sm font-semibold uppercase tracking-[.18em] text-violet-300">Tài khoản</p><h1 class="mt-2 text-3xl font-bold">Quản lý khách hàng</h1><p class="mt-2 text-sm text-neutral-400">Tra cứu khách hàng, số giao dịch và tổng tiền đã thanh toán.</p></div>
    <form method="GET" action="{{ route('admin.customers') }}" class="mb-5 flex flex-wrap gap-3 rounded-2xl border border-white/10 bg-neutral-900 p-4"><input type="search" name="q" value="{{ request('q') }}" placeholder="Tên hoặc email khách hàng" aria-label="Tìm khách hàng" class="min-w-0 flex-1 rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm outline-none focus:border-violet-400"><button class="rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold hover:bg-violet-500">Tìm khách hàng</button>@if (request()->has('q'))<a href="{{ route('admin.customers') }}" class="rounded-xl border border-white/15 px-5 py-2.5 text-center text-sm hover:bg-white/5">Xóa lọc</a>@endif</form>
    <p class="mb-3 text-sm text-neutral-400">{{ $customers->total() }} khách hàng</p>
    <div class="space-y-3">
        @forelse ($customers as $customer)
            <article class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-white/10 bg-neutral-900 p-5">
                <div class="min-w-0"><h2 class="font-bold">{{ $customer->name }}</h2><p class="mt-1 break-all text-sm text-neutral-400">{{ $customer->email }}</p><p class="mt-2 text-xs text-neutral-500">Tham gia {{ $customer->created_at->format('d/m/Y') }} · {{ $customer->orders_count }} giao dịch · Gần nhất: {{ $customer->orders_max_created_at ? \Illuminate\Support\Carbon::parse($customer->orders_max_created_at)->format('d/m/Y') : 'Chưa có' }}</p></div>
                <div class="flex items-center gap-4"><div class="text-right"><p class="text-xs text-neutral-400">Đã thanh toán</p><p class="mt-1 font-bold text-violet-300">{{ number_format($customer->paid_total ?? 0, 0, ',', '.') }} ₫</p></div><a href="{{ route('admin.orders', ['q' => $customer->email]) }}" class="rounded-full border border-white/15 px-4 py-2 text-sm font-semibold hover:border-violet-400">Xem giao dịch</a></div>
            </article>
        @empty<div class="rounded-2xl border border-dashed border-white/15 p-12 text-center text-neutral-400">Không tìm thấy khách hàng.</div>@endforelse
    </div>
    <div class="mt-6">{{ $customers->links() }}</div>
@endsection
