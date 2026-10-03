<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->string('event_title')->nullable();
        });

        DB::table('order_items')->whereNotNull('ticket_type_id')->orderBy('id')->chunkById(100, function ($items): void {
            $eventTitles = DB::table('ticket_types')
                ->join('events', 'events.id', '=', 'ticket_types.event_id')
                ->whereIn('ticket_types.id', $items->pluck('ticket_type_id')->unique())
                ->pluck('events.title', 'ticket_types.id');

            foreach ($items as $item) {
                if ($eventTitle = $eventTitles->get($item->ticket_type_id)) {
                    DB::table('order_items')->where('id', $item->id)->update(['event_title' => $eventTitle]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn('event_title');
        });
    }
};
