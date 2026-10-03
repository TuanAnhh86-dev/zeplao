@extends('admin.layout')
@section('title', $event->exists ? 'Chỉnh sửa sự kiện' : 'Tạo sự kiện')
@section('content')
    @php($editing = $event->exists)
    <div class="mb-7"><a href="{{ route('admin.events') }}" class="text-sm text-violet-300 hover:text-violet-200">← Sự kiện</a><h1 class="mt-3 text-3xl font-bold">{{ $editing ? 'Chỉnh sửa sự kiện' : 'Tạo sự kiện mới' }}</h1><p class="mt-2 text-sm text-neutral-400">Thông tin ở đây sẽ hiển thị trên trang dành cho khách.</p></div>
    <form method="POST" action="{{ $editing ? route('admin.events.update', $event) : route('admin.events.store') }}" class="space-y-6">
        @csrf @if ($editing) @method('PUT') @endif
        <section class="grid gap-4 rounded-2xl border border-white/10 bg-neutral-900 p-5 sm:grid-cols-2 sm:p-6">
            <label class="sm:col-span-2"><span class="mb-2 block text-sm font-semibold">Tên sự kiện</span><input name="title" value="{{ old('title', $event->title) }}" required class="admin-input"></label>
            <label><span class="mb-2 block text-sm font-semibold">Slug</span><input name="slug" value="{{ old('slug', $event->slug) }}" required class="admin-input"><small class="mt-1 block text-neutral-500">Dùng chữ thường, số và dấu gạch ngang.</small></label>
            <label><span class="mb-2 block text-sm font-semibold">Thể loại</span><select name="category" class="admin-input">@foreach (['music' => 'Nhạc sống', 'festival' => 'Lễ hội', 'theatre' => 'Sân khấu', 'experience' => 'Trải nghiệm'] as $value => $label)<option value="{{ $value }}" @selected(old('category', $event->category ?: 'music') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label><span class="mb-2 block text-sm font-semibold">Thành phố</span><input name="city" value="{{ old('city', $event->city) }}" required class="admin-input"></label>
            <label><span class="mb-2 block text-sm font-semibold">Mã thành phố</span><input name="city_key" value="{{ old('city_key', $event->city_key ?: 'hcm') }}" required class="admin-input"><small class="mt-1 block text-neutral-500">Ví dụ: hcm, hanoi, danang.</small></label>
            <label><span class="mb-2 block text-sm font-semibold">Địa điểm</span><input name="venue" value="{{ old('venue', $event->venue) }}" required class="admin-input"></label>
            <label><span class="mb-2 block text-sm font-semibold">Thời gian bắt đầu</span><input type="datetime-local" name="starts_at" value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\\TH:i')) }}" required class="admin-input"></label>
            <label class="sm:col-span-2"><span class="mb-2 block text-sm font-semibold">Đường dẫn ảnh bìa</span><input name="cover_image" value="{{ old('cover_image', $event->cover_image) }}" placeholder="images/events/ten-su-kien.jpg" required class="admin-input"></label>
            <label class="sm:col-span-2"><span class="mb-2 block text-sm font-semibold">Giới thiệu</span><textarea name="introduction" rows="5" class="admin-input">{{ old('introduction', $event->introduction) }}</textarea></label>
            <label class="sm:col-span-2"><span class="mb-2 block text-sm font-semibold">Trang bán vé tham khảo (nếu có)</span><input type="url" name="ticket_url" value="{{ old('ticket_url', $event->ticket_url) }}" class="admin-input"></label>
        </section>
        <section class="rounded-2xl border border-white/10 bg-neutral-900 p-5 sm:p-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3"><div><h2 class="text-lg font-bold">Hạng vé & tồn kho</h2><p class="mt-1 text-sm text-neutral-400">Số lượng là tổng sức chứa; số đã bán được hệ thống tự cập nhật.</p></div><button type="button" data-add-ticket class="rounded-full border border-violet-400/30 px-4 py-2 text-sm font-semibold text-violet-300 hover:bg-violet-500/10">+ Thêm hạng vé</button></div>
            <div data-ticket-fields class="space-y-3">
                @php($rows = old('ticket_types', $ticketTypes->isNotEmpty() ? $ticketTypes->toArray() : [['name' => '', 'price' => '', 'quantity' => '']]))
                @foreach ($rows as $index => $ticket)
                    <div class="grid gap-3 rounded-xl border border-white/10 bg-black/20 p-3 sm:grid-cols-[minmax(0,1fr)_150px_150px_40px]" data-ticket-field>
                        @if (!empty($ticket['id']))<input type="hidden" name="ticket_types[{{ $index }}][id]" value="{{ $ticket['id'] }}">@endif
                        <label><span class="mb-1 block text-xs text-neutral-400">Tên hạng vé</span><input name="ticket_types[{{ $index }}][name]" value="{{ $ticket['name'] ?? '' }}" required class="admin-input"></label>
                        <label><span class="mb-1 block text-xs text-neutral-400">Giá (₫)</span><input type="number" min="0" name="ticket_types[{{ $index }}][price]" value="{{ $ticket['price'] ?? '' }}" required class="admin-input"></label>
                        <label><span class="mb-1 block text-xs text-neutral-400">Tổng số vé</span><input type="number" min="{{ $ticket['sold'] ?? 0 }}" name="ticket_types[{{ $index }}][quantity]" value="{{ $ticket['quantity'] ?? '' }}" required class="admin-input"></label>
                        <button type="button" data-remove-ticket aria-label="Xóa hạng vé" class="mt-5 grid size-10 place-items-center rounded-xl text-neutral-400 hover:bg-rose-400/10 hover:text-rose-300">×</button>
                    </div>
                @endforeach
            </div>
        </section>
        <div class="flex flex-wrap justify-end gap-3"><a href="{{ route('admin.events') }}" class="rounded-full border border-white/15 px-5 py-3 text-sm font-semibold hover:bg-white/5">Hủy</a><button class="rounded-full bg-violet-600 px-6 py-3 text-sm font-bold hover:bg-violet-500">{{ $editing ? 'Lưu thay đổi' : 'Tạo sự kiện' }}</button></div>
    </form>
    <style>.admin-input{width:100%;border:1px solid rgb(255 255 255 / .12);border-radius:.75rem;background:#111;padding:.7rem .85rem;color:white;outline:none}.admin-input:focus{border-color:#a78bfa;box-shadow:0 0 0 2px rgb(139 92 246 / .15)}</style>
    <script>
        (() => {
            const list = document.querySelector('[data-ticket-fields]');
            let next = list.querySelectorAll('[data-ticket-field]').length;
            document.querySelector('[data-add-ticket]').addEventListener('click', () => {
                const row = document.createElement('div');
                row.className = 'grid gap-3 rounded-xl border border-white/10 bg-black/20 p-3 sm:grid-cols-[minmax(0,1fr)_150px_150px_40px]';
                row.dataset.ticketField = '';
                row.innerHTML = `<label><span class="mb-1 block text-xs text-neutral-400">Tên hạng vé</span><input name="ticket_types[${next}][name]" required class="admin-input"></label><label><span class="mb-1 block text-xs text-neutral-400">Giá (₫)</span><input type="number" min="0" name="ticket_types[${next}][price]" required class="admin-input"></label><label><span class="mb-1 block text-xs text-neutral-400">Tổng số vé</span><input type="number" min="0" name="ticket_types[${next}][quantity]" required class="admin-input"></label><button type="button" data-remove-ticket aria-label="Xóa hạng vé" class="mt-5 grid size-10 place-items-center rounded-xl text-neutral-400 hover:bg-rose-400/10 hover:text-rose-300">×</button>`;
                list.append(row); next++;
            });
            list.addEventListener('click', event => {
                const remove = event.target.closest('[data-remove-ticket]');
                if (remove && list.querySelectorAll('[data-ticket-field]').length > 1) remove.closest('[data-ticket-field]').remove();
            });
        })();
    </script>
@endsection
