<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\User;
use App\Services\OrderReservationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'revenue_period' => ['nullable', Rule::in(['day', 'month', 'year'])],
            'revenue_date' => ['nullable', 'date_format:Y-m-d'],
            'revenue_month' => ['nullable', 'date_format:Y-m'],
            'revenue_year' => ['nullable', 'digits:4', 'integer', 'between:2000,2100'],
            'revenue_chart' => ['nullable', Rule::in(['bars', 'area'])],
            'revenue_metric' => ['nullable', Rule::in(['revenue', 'orders'])],
        ]);
        $period = $filters['revenue_period'] ?? 'month';
        $start = match ($period) {
            'day' => Carbon::parse($filters['revenue_date'] ?? today()->toDateString())->startOfDay(),
            'year' => Carbon::createFromDate((int) ($filters['revenue_year'] ?? now()->year), 1, 1)->startOfDay(),
            default => Carbon::createFromFormat('Y-m', $filters['revenue_month'] ?? now()->format('Y-m'))->startOfMonth(),
        };
        $end = match ($period) {
            'day' => $start->copy()->endOfDay(),
            'year' => $start->copy()->endOfYear(),
            default => $start->copy()->endOfMonth(),
        };
        $revenueOrders = Order::query()
            ->where('status', 'confirmed')
            ->whereBetween('created_at', [$start, $end])
            ->get(['created_at', 'total']);
        $bucketFormat = match ($period) {
            'day' => 'H:00',
            'year' => 'm',
            default => 'd',
        };
        $bucketCount = match ($period) {
            'day' => 24,
            'year' => 12,
            default => $start->daysInMonth,
        };
        $revenueByBucket = $revenueOrders->groupBy(fn (Order $order) => $order->created_at->format($bucketFormat));
        $revenueRows = collect(range(0, $bucketCount - 1))->map(function (int $offset) use ($period, $start, $revenueByBucket): array {
            $bucket = match ($period) {
                'day' => str_pad((string) $offset, 2, '0', STR_PAD_LEFT).':00',
                'year' => str_pad((string) ($offset + 1), 2, '0', STR_PAD_LEFT),
                default => str_pad((string) ($offset + 1), 2, '0', STR_PAD_LEFT),
            };
            $orders = $revenueByBucket->get($bucket, collect());
            $label = match ($period) {
                'day' => $bucket,
                'year' => str_pad((string) ($offset + 1), 2, '0', STR_PAD_LEFT).'/'.$start->year,
                default => $bucket.'/'.$start->format('m'),
            };

            return ['label' => $label, 'revenue' => (int) $orders->sum('total'), 'orders' => $orders->count()];
        });
        $selectedRevenue = (int) $revenueOrders->sum('total');
        return view('admin.dashboard', [
            'eventsCount' => Event::count(),
            'ticketCount' => TicketType::sum('quantity'),
            'soldCount' => TicketType::sum('sold'),
            'ordersCount' => Order::count(),
            'pendingOrdersCount' => Order::where('status', 'pending')->count(),
            'confirmedRevenue' => Order::where('status', 'confirmed')->sum('total'),
            'revenuePeriod' => $period,
            'revenueStart' => $start,
            'revenueEnd' => $end,
            'revenueRows' => $revenueRows,
            'revenueChart' => $filters['revenue_chart'] ?? 'bars',
            'revenueMetric' => $filters['revenue_metric'] ?? 'revenue',
            'selectedRevenue' => $selectedRevenue,
            'selectedRevenueOrders' => $revenueOrders->count(),
            'recentOrders' => Order::with('user', 'items')->latest()->limit(8)->get(),
        ]);
    }

    public function events(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'venue' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', Rule::in(['music', 'festival', 'theatre', 'experience'])],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $events = Event::query()
            ->with('ticketTypes')
            ->when($filters['q'] ?? null, function ($query, string $term): void {
                $query->where(function ($query) use ($term): void {
                    $query->where('title', 'like', "%{$term}%")
                        ->orWhere('venue', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%");
                });
            })
            ->when($filters['city'] ?? null, fn ($query, string $city) => $query->where('city', $city))
            ->when($filters['venue'] ?? null, fn ($query, string $venue) => $query->where('venue', $venue))
            ->when($filters['category'] ?? null, fn ($query, string $category) => $query->where('category', $category))
            ->when($filters['date'] ?? null, fn ($query, string $date) => $query->whereDate('starts_at', $date))
            ->when($filters['month'] ?? null, function ($query, string $month): void {
                [$year, $monthNumber] = explode('-', $month);
                $query->whereYear('starts_at', $year)->whereMonth('starts_at', $monthNumber);
            })
            ->orderBy('starts_at')
            ->paginate(12)
            ->withQueryString();

        $cities = Event::query()->select('city')->distinct()->orderBy('city')->pluck('city');
        $venues = Event::query()->select('venue')->distinct()->orderBy('venue')->pluck('venue');

        return view('admin.events.index', compact('events', 'cities', 'venues'));
    }

    public function create(): View
    {
        return view('admin.events.form', ['event' => new Event(), 'ticketTypes' => collect()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedEvent($request);
        $tickets = $data['ticket_types'];
        unset($data['ticket_types']);

        DB::transaction(function () use ($data, $tickets): void {
            $event = Event::create($data);
            foreach ($tickets as $ticket) {
                $event->ticketTypes()->create($ticket + ['sold' => 0]);
            }
        });

        return redirect()->route('admin.events')->with('status', 'Đã tạo sự kiện.');
    }

    public function edit(Event $event): View
    {
        return view('admin.events.form', ['event' => $event, 'ticketTypes' => $event->ticketTypes()->orderBy('id')->get()]);
    }

    public function update(Request $request, Event $event, OrderReservationService $reservations): RedirectResponse
    {
        $reservations->expirePendingOrders();
        $data = $this->validatedEvent($request, $event);
        $tickets = $data['ticket_types'];
        unset($data['ticket_types']);

        DB::transaction(function () use ($event, $data, $tickets, $reservations): void {
            $event->update($data);
            $kept = [];
            foreach ($tickets as $ticket) {
                $id = $ticket['id'] ?? null;
                unset($ticket['id']);
                if ($id) {
                    $existing = $event->ticketTypes()->lockForUpdate()->findOrFail($id);
                    $reserved = $reservations->reservedQuantity($existing->id);
                    abort_if($ticket['quantity'] < $existing->sold + $reserved, 422, 'Số lượng tồn không thể thấp hơn số vé đã bán hoặc đang được giữ.');
                    $existing->update($ticket);
                    $kept[] = $existing->id;
                } else {
                    $created = $event->ticketTypes()->create($ticket + ['sold' => 0]);
                    $kept[] = $created->id;
                }
            }

            $event->ticketTypes()->whereNotIn('id', $kept)->get()->each(function (TicketType $ticket) use ($reservations): void {
                $reserved = $reservations->reservedQuantity($ticket->id);
                if ($ticket->sold > 0 || $reserved > 0) {
                    $ticket->update(['quantity' => $ticket->sold + $reserved]);
                } else {
            $ticket->delete();
                }
            });
        });

        return redirect()->route('admin.events')->with('status', 'Đã cập nhật sự kiện và hạng vé.');
    }

    public function destroy(Event $event, OrderReservationService $reservations): RedirectResponse
    {
        $reservations->expirePendingOrders();
        if ($event->ticketTypes->contains(fn (TicketType $ticket) => $ticket->sold > 0 || $reservations->reservedQuantity($ticket->id) > 0)) {
            return back()->withErrors(['event' => 'Sự kiện có vé đã bán hoặc đang được giữ nên không thể xóa.']);
        }

        $event->delete();

        return redirect()->route('admin.events')->with('status', 'Đã xóa sự kiện.');
    }

    public function orders(Request $request, OrderReservationService $reservations): View
    {
        $reservations->expirePendingOrders();
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'cancelled'])],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $orders = Order::query()
            ->with('user', 'items')
            ->when($filters['q'] ?? null, function ($query, string $term): void {
                $query->where(function ($query) use ($term): void {
                    $query->where('code', 'like', "%{$term}%")
                        ->orWhereHas('items', fn ($items) => $items->where('event_title', 'like', "%{$term}%")
                            ->orWhere('ticket_name', 'like', "%{$term}%"))
                        ->orWhereHas('user', function ($query) use ($term): void {
                            $query->where('name', 'like', "%{$term}%")
                                ->orWhere('email', 'like', "%{$term}%");
                        });
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['from'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.orders', compact('orders', 'statusCounts'));
    }

    public function createOrder(OrderReservationService $reservations): View
    {
        $reservations->expirePendingOrders();
        $events = Event::query()->with('ticketTypes')->orderBy('starts_at')->get();
        $eventOptions = $events->map(fn (Event $event) => [
            'id' => $event->id,
            'tickets' => $event->ticketTypes->map(fn (TicketType $ticket) => [
                'id' => $ticket->id,
                'name' => $ticket->name,
                'price' => $ticket->price,
                'available' => max(0, $ticket->quantity - $ticket->sold - $reservations->reservedQuantity($ticket->id)),
            ])->values(),
        ])->values();

        return view('admin.orders.create', [
            'customers' => User::query()->where('role', 'customer')->orderBy('name')->get(['id', 'name', 'email']),
            'events' => $events,
            'eventOptions' => $eventOptions,
        ]);
    }

    public function storeOrder(Request $request, OrderReservationService $reservations): RedirectResponse
    {
        $reservations->expirePendingOrders();
        $data = $request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'customer')],
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'tickets' => ['required', 'array', 'min:1'],
            'tickets.*' => ['required', 'integer', 'min:1', 'max:10'],
        ]);
        ksort($data['tickets'], SORT_NUMERIC);
        $order = DB::transaction(function () use ($data, $reservations): Order {
            $eventTitle = Event::query()->whereKey($data['event_id'])->value('title');
            $tickets = TicketType::query()
                ->where('event_id', $data['event_id'])
                ->whereIn('id', array_keys($data['tickets']))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($tickets->count() !== count($data['tickets'])) {
                throw \Illuminate\Validation\ValidationException::withMessages(['tickets' => 'Vui lòng chọn hạng vé thuộc sự kiện đã chọn.']);
            }

            foreach ($data['tickets'] as $ticketId => $quantity) {
                $ticket = $tickets->get((int) $ticketId);
                $available = max(0, $ticket->quantity - $ticket->sold - $reservations->reservedQuantity($ticket->id));
                if ($quantity > $available) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['tickets' => "Hạng vé {$ticket->name} chỉ còn {$available} vé."]);
                }
            }

            $order = Order::create([
                'code' => 'TX-'.strtoupper(Str::random(8)),
                'user_id' => $data['user_id'],
                'total' => 0,
                'status' => 'pending',
                'expires_at' => now()->addMinutes(OrderReservationService::HOLD_MINUTES),
                'idempotency_key' => (string) Str::uuid(),
            ]);
            $total = 0;
            foreach ($data['tickets'] as $ticketId => $quantity) {
                $ticket = $tickets->get((int) $ticketId);
                $subtotal = $ticket->price * $quantity;
                $total += $subtotal;
                $order->items()->create([
                    'ticket_type_id' => $ticket->id,
                    'ticket_name' => $ticket->name,
                    'event_title' => $eventTitle,
                    'unit_price' => $ticket->price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ]);
            }
            $order->update(['total' => $total]);

            return $order;
        }, attempts: 3);

        return redirect()->route('admin.orders')->with('status', "Đã tạo đơn {$order->code}; vé được giữ trong ".OrderReservationService::HOLD_MINUTES.' phút.');
    }

    public function customers(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $customers = User::query()
            ->where('role', 'customer')
            ->withCount('orders')
            ->withSum(['orders as paid_total' => fn ($query) => $query->where('status', 'confirmed')], 'total')
            ->withMax('orders', 'created_at')
            ->when($filters['q'] ?? null, function ($query, string $term): void {
                $query->where(fn ($query) => $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers', compact('customers'));
    }

    public function updateOrder(Request $request, Order $order, OrderReservationService $reservations): RedirectResponse
    {
        $reservations->expirePendingOrders();
        $data = $request->validate(['status' => ['required', Rule::in(['confirmed', 'cancelled'])]]);

        $newStatus = DB::transaction(function () use ($order, $data): string {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($locked->status === 'pending' && $data['status'] === 'confirmed') {
                foreach ($locked->items()->orderBy('ticket_type_id')->get() as $item) {
                    abort_unless($item->ticket_type_id, 422, 'Ticket type no longer exists.');
                    $ticket = TicketType::query()->whereKey($item->ticket_type_id)->lockForUpdate()->first();
                    abort_unless($ticket, 422, 'Ticket type no longer exists.');
                    abort_if($item->quantity > $ticket->quantity - $ticket->sold, 422, 'Not enough tickets remain to confirm this payment.');
                    $ticket->increment('sold', $item->quantity);
                }
                $locked->update(['status' => 'confirmed']);
            } elseif ($locked->status === 'pending' && $data['status'] === 'cancelled') {
                $locked->update(['status' => 'cancelled']);
            } else {
                abort(422, 'This order cannot transition to the requested status.');
            }

            return $locked->status;
        }, attempts: 3);

        $message = match ($newStatus) {
            'confirmed' => 'Payment recorded for this order.',
            'cancelled' => 'Unpaid order cancelled.',
        };

        return back()->with('status', $message);
    }

    private function validatedEvent(Request $request, ?Event $event = null): array
    {
        $eventId = $event?->id;
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('events', 'slug')->ignore($eventId)],
            'category' => ['required', Rule::in(['music', 'festival', 'theatre', 'experience'])],
            'city' => ['required', 'string', 'max:100'],
            'city_key' => ['required', 'string', 'max:50'],
            'venue' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'cover_image' => ['required', 'string', 'max:255'],
            'introduction' => ['nullable', 'string'],
            'ticket_url' => ['nullable', 'url', 'max:255'],
            'ticket_types' => ['required', 'array', 'min:1'],
            'ticket_types.*.id' => ['nullable', 'integer'],
            'ticket_types.*.name' => ['required', 'string', 'max:255', 'distinct'],
            'ticket_types.*.price' => ['required', 'integer', 'min:0'],
            'ticket_types.*.quantity' => ['required', 'integer', 'min:0'],
        ]);
    }
}
