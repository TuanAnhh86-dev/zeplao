@extends('admin.layout')
@section('title', 'Tạo đơn đặt vé')
@section('content')
    <div class="mb-7"><a href="{{ route('admin.orders') }}" class="text-sm font-semibold text-violet-300 hover:text-violet-200">← Quay lại giao dịch</a><h1 class="mt-3 text-3xl font-bold">Tạo đơn đặt vé</h1><p class="mt-2 text-sm text-neutral-400">Đơn mới ở trạng thái chưa thanh toán và không giữ vé trong kho.</p></div>
    <form method="POST" action="{{ route('admin.orders.store') }}" class="max-w-3xl space-y-5 rounded-2xl border border-white/10 bg-neutral-900 p-5 sm:p-7">
        @csrf
        <label class="block text-sm font-semibold">Khách hàng<select name="user_id" required class="mt-2 block w-full rounded-xl border border-white/10 bg-neutral-950 px-4 py-3 text-sm font-normal"><option value="">Chọn khách hàng</option>@foreach ($customers as $customer)<option value="{{ $customer->id }}" @selected(old('user_id') == $customer->id)>{{ $customer->name }} — {{ $customer->email }}</option>@endforeach</select>@error('user_id')<span class="mt-1 block text-xs text-rose-300">{{ $message }}</span>@enderror</label>
        <label class="block text-sm font-semibold">Sự kiện<select name="event_id" data-order-event required class="mt-2 block w-full rounded-xl border border-white/10 bg-neutral-950 px-4 py-3 text-sm font-normal"><option value="">Chọn sự kiện</option>@foreach ($events as $event)<option value="{{ $event->id }}" @selected(old('event_id') == $event->id)>{{ $event->title }} · {{ $event->starts_at->format('d/m/Y') }}</option>@endforeach</select>@error('event_id')<span class="mt-1 block text-xs text-rose-300">{{ $message }}</span>@enderror</label>
        <section><h2 class="text-sm font-semibold">Hạng vé và số lượng</h2><div data-order-ticket-list class="mt-3 space-y-2"><p class="rounded-xl border border-dashed border-white/10 p-4 text-sm text-neutral-400">Chọn sự kiện để xem các hạng vé.</p></div>@error('tickets')<p class="mt-2 text-xs text-rose-300">{{ $message }}</p>@enderror</section>
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-white/10 pt-5"><p class="text-sm text-neutral-400">Tổng tiền <strong data-order-total class="ml-2 text-lg text-violet-300">0 ₫</strong></p><button class="rounded-full bg-violet-600 px-6 py-3 text-sm font-bold hover:bg-violet-500">Tạo đơn chờ thanh toán</button></div>
    </form>
    <script>
        const orderEvents = @json($eventOptions);
        const orderEventSelect = document.querySelector('[data-order-event]');
        const orderTicketList = document.querySelector('[data-order-ticket-list]');
        const orderTotal = document.querySelector('[data-order-total]');
        const formatOrderPrice = (value) => new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
        const renderOrderTickets = () => {
            const event = orderEvents.find((item) => String(item.id) === orderEventSelect.value);
            orderTicketList.replaceChildren();
            if (!event || event.tickets.length === 0) {
                const empty = document.createElement('p');
                empty.className = 'rounded-xl border border-dashed border-white/10 p-4 text-sm text-neutral-400';
                empty.textContent = event ? 'Sự kiện chưa có hạng vé.' : 'Chọn sự kiện để xem các hạng vé.';
                orderTicketList.append(empty);
                orderTotal.textContent = formatOrderPrice(0);
                return;
            }
            event.tickets.forEach((ticket) => {
                const row = document.createElement('label');
                row.className = 'flex flex-wrap items-center justify-between gap-3 rounded-xl border border-white/10 bg-neutral-950 p-4';
                const details = document.createElement('span');
                details.className = 'text-sm font-semibold';
                details.textContent = `${ticket.name} · ${formatOrderPrice(ticket.price)} · còn ${ticket.available}`;
                const quantity = document.createElement('input');
                quantity.type = 'number';
                quantity.min = '1';
                quantity.max = String(Math.min(10, ticket.available));
                quantity.step = '1';
                quantity.placeholder = 'Số vé';
                quantity.disabled = ticket.available === 0;
                quantity.dataset.ticketId = ticket.id;
                quantity.className = 'w-28 rounded-lg border border-white/10 bg-neutral-900 px-3 py-2 text-sm outline-none focus:border-violet-400 disabled:opacity-40';
                quantity.addEventListener('input', () => {
                    if (Number(quantity.value) > 0) quantity.name = `tickets[${ticket.id}]`;
                    else quantity.removeAttribute('name');
                    const sum = [...orderTicketList.querySelectorAll('input[name^="tickets["]')]
                        .reduce((total, input) => total + Number(input.value || 0) * Number(input.dataset.price || 0), 0);
                    orderTotal.textContent = formatOrderPrice(sum);
                });
                quantity.dataset.price = ticket.price;
                row.append(details, quantity);
                orderTicketList.append(row);
            });
        };
        orderEventSelect.addEventListener('change', renderOrderTickets);
        renderOrderTickets();
    </script>
@endsection
