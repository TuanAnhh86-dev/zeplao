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
            <input type="hidden" name="revenue_chart" value="{{ $revenueChart }}" data-revenue-chart-filter>
            <input type="hidden" name="revenue_metric" value="{{ $revenueMetric }}" data-revenue-metric-filter>
            <label class="text-xs text-neutral-400">Xem theo<select name="revenue_period" data-revenue-period class="mt-1 block rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm text-white"><option value="day" @selected($revenuePeriod === 'day')>Ngày</option><option value="month" @selected($revenuePeriod === 'month')>Tháng</option><option value="year" @selected($revenuePeriod === 'year')>Năm</option></select></label>
            <label data-revenue-date-field @class(['hidden' => $revenuePeriod !== 'day']) class="text-xs text-neutral-400">Ngày<input type="date" name="revenue_date" value="{{ request('revenue_date', $revenueStart->toDateString()) }}" class="mt-1 block rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm text-white"></label>
            <label data-revenue-month-field @class(['hidden' => $revenuePeriod !== 'month']) class="text-xs text-neutral-400">Tháng<input type="month" name="revenue_month" value="{{ request('revenue_month', $revenueStart->format('Y-m')) }}" class="mt-1 block rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm text-white"></label>
            <label data-revenue-year-field @class(['hidden' => $revenuePeriod !== 'year']) class="text-xs text-neutral-400">Năm<input type="number" name="revenue_year" min="2000" max="2100" value="{{ request('revenue_year', $revenueStart->year) }}" class="mt-1 block w-32 rounded-xl border border-white/10 bg-neutral-950 px-4 py-2.5 text-sm text-white"></label>
            <button type="submit" class="rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold hover:bg-violet-500">Xem báo cáo</button>
        </form>
        <div class="mb-5 grid gap-3 sm:grid-cols-2"><article class="rounded-xl border border-white/10 bg-black/20 p-4"><p class="text-sm text-neutral-400">Doanh thu kỳ này</p><p class="mt-2 text-2xl font-bold text-violet-300">{{ number_format($selectedRevenue, 0, ',', '.') }} ₫</p><p class="mt-1 text-xs text-neutral-500">{{ $selectedRevenueOrders }} giao dịch đã thanh toán</p></article><article class="rounded-xl border border-white/10 bg-black/20 p-4"><p class="text-sm text-neutral-400">Khoảng thời gian</p><p class="mt-2 text-lg font-bold">{{ $revenueStart->format('d/m/Y') }} – {{ $revenueEnd->format('d/m/Y') }}</p><p class="mt-1 text-xs text-neutral-500">Dữ liệu theo ngày tạo đơn</p></article></div>
        @php($maxRevenue = max(1, $revenueRows->max('revenue')))
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-sm font-semibold">Biểu đồ doanh thu</p><p data-revenue-mode-description class="mt-1 text-xs text-neutral-500">So sánh doanh thu từng mốc trong kỳ.</p></div>
            <div class="inline-flex rounded-xl border border-white/10 bg-black/30 p-1" role="group" aria-label="Chọn kiểu biểu đồ">
                <button type="button" data-revenue-mode="bars" aria-pressed="true" class="rounded-lg bg-violet-600 px-3 py-2 text-xs font-semibold text-white">Biểu đồ thanh</button>
                <button type="button" data-revenue-mode="area" aria-pressed="false" class="rounded-lg px-3 py-2 text-xs font-semibold text-neutral-400 transition hover:text-white">Biểu đồ miền</button>
            </div>
        </div>
        <div data-revenue-panel="bars" class="max-h-96 space-y-2 overflow-y-auto pr-1">
            @foreach ($revenueRows as $row)
                <div class="grid grid-cols-[58px_minmax(0,1fr)_auto] items-center gap-3 text-xs sm:grid-cols-[70px_minmax(0,1fr)_auto_auto]">
                    <span class="text-neutral-400">{{ $row['label'] }}</span>
                    <div class="h-2 overflow-hidden rounded-full bg-white/5"><div class="h-full rounded-full bg-violet-500" style="width: {{ $row['revenue'] ? max(2, $row['revenue'] / $maxRevenue * 100) : 0 }}%"></div></div>
                    <span class="text-right font-semibold">{{ number_format($row['revenue'], 0, ',', '.') }} ₫</span><span class="hidden text-right text-neutral-500 sm:block">{{ $row['orders'] }} đơn</span>
                </div>
            @endforeach
        </div>
        <div data-revenue-panel="area" class="hidden overflow-hidden rounded-2xl border border-white/10 bg-black/20 p-3 sm:p-5">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                <label class="flex items-center gap-2 text-xs text-neutral-400">Chỉ số
                    <select data-revenue-metric class="rounded-lg border border-white/10 bg-neutral-950 px-3 py-2 text-sm text-white" aria-label="Chọn chỉ số biểu đồ">
                        <option value="revenue" @selected($revenueMetric === 'revenue')>Doanh thu</option>
                        <option value="orders" @selected($revenueMetric === 'orders')>Số đơn</option>
                    </select>
                </label>
            </div>
            <svg data-revenue-area-chart class="block h-64 w-full sm:h-80" viewBox="0 0 960 320" role="img" aria-label="Biểu đồ miền doanh thu"></svg>
            <div data-revenue-metric-legend class="mt-2 flex items-center justify-center gap-2 text-xs text-neutral-400"><span class="size-2.5 rounded-full bg-violet-400"></span>Doanh thu (VND)</div>
        </div>
        <script>
            (() => {
                const root = document.currentScript.closest('section');
                const rows = @json($revenueRows->values());
                const buttons = [...root.querySelectorAll('[data-revenue-mode]')];
                const panels = [...root.querySelectorAll('[data-revenue-panel]')];
                const description = root.querySelector('[data-revenue-mode-description]');
                const svg = root.querySelector('[data-revenue-area-chart]');
                const metricSelect = root.querySelector('[data-revenue-metric]');
                const metricLegend = root.querySelector('[data-revenue-metric-legend]');
                const chartFilter = root.querySelector('[data-revenue-chart-filter]');
                const metricFilter = root.querySelector('[data-revenue-metric-filter]');
                const periodSelect = root.querySelector('[data-revenue-period]');
                const periodFields = {
                    day: root.querySelector('[data-revenue-date-field]'),
                    month: root.querySelector('[data-revenue-month-field]'),
                    year: root.querySelector('[data-revenue-year-field]'),
                };
                const ns = 'http://www.w3.org/2000/svg';
                const make = (tag, attrs = {}) => {
                    const node = document.createElementNS(ns, tag);
                    Object.entries(attrs).forEach(([key, value]) => node.setAttribute(key, value));
                    return node;
                };

                const drawAreaChart = () => {
                    const width = 960, height = 320, left = 68, right = 18, top = 22, bottom = 48;
                    const plotWidth = width - left - right, plotHeight = height - top - bottom;
                    const metric = metricSelect.value;
                    const isRevenue = metric === 'revenue';
                    const values = rows.map(row => Number(row[metric]));
                    const maxValue = Math.max(1, ...values);
                    const formatValue = value => isRevenue
                        ? `${new Intl.NumberFormat('vi-VN').format(value)} ₫`
                        : `${new Intl.NumberFormat('vi-VN').format(value)} đơn`;
                    metricLegend.lastChild.textContent = isRevenue ? 'Doanh thu (VND)' : 'Số đơn đã thanh toán';
                    svg.setAttribute('aria-label', isRevenue ? 'Biểu đồ miền doanh thu' : 'Biểu đồ miền số đơn đã thanh toán');
                    svg.replaceChildren();

                    const defs = make('defs');
                    const gradient = make('linearGradient', { id: 'revenue-area-fill', x1: '0', x2: '0', y1: '0', y2: '1' });
                    gradient.append(make('stop', { offset: '0%', 'stop-color': '#a78bfa', 'stop-opacity': '.4' }));
                    gradient.append(make('stop', { offset: '100%', 'stop-color': '#7c3aed', 'stop-opacity': '.02' }));
                    defs.append(gradient);
                    svg.append(defs);

                    for (let tick = 0; tick <= 4; tick++) {
                        const y = top + (plotHeight * tick / 4);
                        const value = maxValue * (1 - tick / 4);
                        svg.append(make('line', { x1: left, x2: width - right, y1: y, y2: y, stroke: '#ffffff', 'stroke-opacity': '.09', 'stroke-dasharray': '3 5' }));
                        const label = make('text', { x: left - 10, y: y + 4, fill: '#a3a3a3', 'font-size': '11', 'text-anchor': 'end' });
                        label.textContent = isRevenue
                            ? new Intl.NumberFormat('vi-VN', { notation: 'compact', maximumFractionDigits: 1 }).format(value)
                            : new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 1 }).format(value);
                        svg.append(label);
                    }

                    const points = rows.map((row, index) => ({
                        x: left + (rows.length < 2 ? plotWidth / 2 : plotWidth * index / (rows.length - 1)),
                        y: top + plotHeight * (1 - Number(row[metric]) / maxValue),
                        row,
                    }));
                    if (points.length) {
                        const linePath = points.map((point, index) => `${index ? 'L' : 'M'} ${point.x} ${point.y}`).join(' ');
                        const areaPath = `${linePath} L ${points[points.length - 1].x} ${top + plotHeight} L ${points[0].x} ${top + plotHeight} Z`;
                        svg.append(make('path', { d: areaPath, fill: 'url(#revenue-area-fill)' }));
                        svg.append(make('path', { d: linePath, fill: 'none', stroke: '#a78bfa', 'stroke-width': '3', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }));

                        points.forEach(({ x, y, row }) => {
                            const point = make('circle', { cx: x, cy: y, r: '4', fill: '#c4b5fd', stroke: '#18181b', 'stroke-width': '2' });
                            const title = make('title');
                            title.textContent = `${row.label}: ${formatValue(row[metric])}${isRevenue ? ` · ${row.orders} đơn` : ` · ${new Intl.NumberFormat('vi-VN').format(row.revenue)} ₫`}`;
                            point.append(title);
                            svg.append(point);
                        });

                        [...new Set([0, Math.floor((points.length - 1) / 2), points.length - 1])].forEach(index => {
                            const point = points[index];
                            const label = make('text', { x: point.x, y: height - 14, fill: '#a3a3a3', 'font-size': '11', 'text-anchor': index === 0 ? 'start' : index === points.length - 1 ? 'end' : 'middle' });
                            label.textContent = point.row.label;
                            svg.append(label);
                        });
                    }
                };

                const setMode = mode => {
                    chartFilter.value = mode;
                    panels.forEach(panel => panel.classList.toggle('hidden', panel.dataset.revenuePanel !== mode));
                    buttons.forEach(button => {
                        const active = button.dataset.revenueMode === mode;
                        button.setAttribute('aria-pressed', active ? 'true' : 'false');
                        button.classList.toggle('bg-violet-600', active);
                        button.classList.toggle('text-white', active);
                        button.classList.toggle('text-neutral-400', !active);
                    });
                    description.textContent = mode === 'area'
                        ? `Xu hướng ${metricSelect.value === 'revenue' ? 'doanh thu' : 'số đơn'} theo thời gian; rê chuột lên điểm để xem chi tiết.`
                        : 'So sánh doanh thu từng mốc trong kỳ.';
                };

                const updatePeriodFields = () => {
                    Object.entries(periodFields).forEach(([period, field]) => {
                        field.classList.toggle('hidden', period !== periodSelect.value);
                    });
                };

                drawAreaChart();
                periodSelect.addEventListener('change', updatePeriodFields);
                metricSelect.addEventListener('change', () => {
                    metricFilter.value = metricSelect.value;
                    drawAreaChart();
                    if (root.querySelector('[data-revenue-panel="area"]').classList.contains('hidden') === false) setMode('area');
                });
                setMode(@json($revenueChart));
                buttons.forEach(button => button.addEventListener('click', () => setMode(button.dataset.revenueMode)));
            })();
        </script>
    </section>

    <section class="mt-8 overflow-hidden rounded-2xl border border-white/10 bg-neutral-900">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 px-5 py-4"><div><h2 class="font-bold">Giao dịch mới nhất</h2><p class="mt-1 text-xs text-neutral-400">Các đơn đặt vé gần đây</p></div><a href="{{ route('admin.orders') }}" class="text-sm font-semibold text-violet-300 hover:text-violet-200">Xem tất cả →</a></div>
        @forelse ($recentOrders as $order)
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/5 border-l-2 px-5 py-4 pl-4 last:border-0 {{ ['pending' => 'border-l-amber-300 bg-amber-300/[.035]', 'confirmed' => 'border-l-emerald-300 bg-emerald-300/[.035]', 'cancelled' => 'border-l-rose-300 bg-rose-300/[.035]'][$order->status] }}"><div><p class="font-semibold">{{ $order->items->pluck('event_title')->filter()->unique()->join(', ') ?: $order->code }} <span class="ml-2 rounded-full px-2 py-1 text-xs font-medium {{ ['pending' => 'bg-amber-300/10 text-amber-200', 'confirmed' => 'bg-emerald-300/10 text-emerald-200', 'cancelled' => 'bg-rose-300/10 text-rose-200'][$order->status] }}">{{ ['pending' => 'Chưa thanh toán', 'confirmed' => 'Đã thanh toán', 'cancelled' => 'Đã hủy'][$order->status] }}</span></p><p class="mt-1 text-xs text-neutral-400">{{ $order->user->name }} · {{ $order->items->sum('quantity') }} vé</p></div><p class="font-bold text-violet-300">{{ number_format($order->total, 0, ',', '.') }} ₫</p></div>
        @empty<div class="px-5 py-12 text-center text-sm text-neutral-400">Chưa có giao dịch nào.</div>@endforelse
    </section>
@endsection
