@extends('admin.layout')
@section('title', 'Sự kiện')
@section('content')
    <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
        <div><p class="text-sm font-semibold uppercase tracking-[.18em] text-violet-300">Danh mục</p><h1 class="mt-2 text-3xl font-bold">Sự kiện</h1><p class="mt-2 text-sm text-neutral-400">Lọc theo địa điểm, ngày, tháng và thể loại.</p></div>
        <a href="{{ route('admin.events.create') }}" class="rounded-full bg-violet-600 px-5 py-3 text-sm font-bold hover:bg-violet-500">+ Tạo sự kiện</a>
    </div>

    <form method="GET" action="{{ route('admin.events') }}" class="mb-5 grid gap-3 rounded-2xl border border-white/10 bg-neutral-900 p-4 sm:grid-cols-2 xl:grid-cols-4">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Tên sự kiện, địa điểm..." aria-label="Tìm sự kiện" class="w-full rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm outline-none focus:border-violet-400">
        <select name="city" aria-label="Thành phố" class="w-full rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm outline-none focus:border-violet-400"><option value="">Tất cả thành phố</option>@foreach ($cities as $city)<option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>@endforeach</select>
        <select name="venue" aria-label="Địa điểm" class="w-full rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm outline-none focus:border-violet-400"><option value="">Tất cả địa điểm</option>@foreach ($venues as $venue)<option value="{{ $venue }}" @selected(request('venue') === $venue)>{{ $venue }}</option>@endforeach</select>
        <select name="category" aria-label="Thể loại" class="w-full rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm outline-none focus:border-violet-400">
            <option value="">Tất cả thể loại</option>
            @foreach (['music' => 'Nhạc sống', 'festival' => 'Lễ hội', 'theatre' => 'Sân khấu', 'experience' => 'Trải nghiệm'] as $value => $label)<option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>@endforeach
        </select>
        <label class="text-xs text-neutral-400">Ngày cụ thể<input type="date" name="date" value="{{ request('date') }}" class="mt-1 block w-full rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm text-white outline-none focus:border-violet-400"></label>
        <label class="text-xs text-neutral-400">Hoặc chọn tháng<input type="month" name="month" value="{{ request('month') }}" class="mt-1 block w-full rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm text-white outline-none focus:border-violet-400"></label>
        <button class="self-end rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold hover:bg-violet-500">Lọc sự kiện</button>
        @if (request()->hasAny(['q', 'city', 'venue', 'category', 'date', 'month']))<a href="{{ route('admin.events') }}" class="self-end rounded-xl border border-white/15 px-5 py-2.5 text-center text-sm hover:bg-white/5">Xóa bộ lọc</a>@endif
    </form>

    <p class="mb-3 text-sm text-neutral-400">{{ $events->total() }} sự kiện</p>
    <div class="space-y-3">
        @forelse ($events as $event)
            <article class="rounded-2xl border border-white/10 bg-neutral-900 p-4 sm:p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0"><h2 class="text-lg font-bold">{{ $event->title }}</h2><p class="mt-1 text-sm text-neutral-400">{{ $event->city }} · {{ $event->venue }} · {{ $event->starts_at->format('d/m/Y H:i') }}</p><p class="mt-1 text-xs text-violet-300">{{ ['music' => 'Nhạc sống', 'festival' => 'Lễ hội', 'theatre' => 'Sân khấu', 'experience' => 'Trải nghiệm'][$event->category] ?? $event->category }}</p></div>
                    <div class="flex flex-wrap gap-2"><a href="{{ route('admin.events.edit', $event) }}" class="rounded-full border border-white/15 px-4 py-2 text-sm hover:border-violet-400 hover:text-violet-300">Chỉnh sửa</a><form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Xóa sự kiện này?')">@csrf @method('DELETE')<button class="rounded-full border border-rose-400/20 px-4 py-2 text-sm text-rose-300 hover:bg-rose-400/10">Xóa</button></form></div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">@foreach ($event->ticketTypes as $ticket)<span class="rounded-xl border border-white/10 bg-black/20 px-3 py-2 text-xs text-neutral-300">{{ $ticket->name }} · {{ number_format($ticket->price, 0, ',', '.') }} ₫ · còn {{ max(0, $ticket->quantity - $ticket->sold) }}/{{ $ticket->quantity }}</span>@endforeach</div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-white/15 p-12 text-center text-neutral-400">Không tìm thấy sự kiện phù hợp.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $events->links() }}</div>
@endsection
