<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->timestamp('expires_at')->nullable()->index();
            $table->uuid('idempotency_key')->nullable();
            $table->unique(['user_id', 'idempotency_key']);
        });

        DB::table('orders')->where('status', 'pending')->orderBy('id')->chunkById(100, function ($orders): void {
            foreach ($orders as $order) {
                DB::table('orders')->where('id', $order->id)->update([
                    'expires_at' => date('Y-m-d H:i:s', strtotime($order->created_at.' +15 minutes')),
                ]);
                foreach (DB::table('order_items')->where('order_id', $order->id)->whereNotNull('ticket_type_id')->get() as $item) {
                    DB::table('ticket_types')->where('id', $item->ticket_type_id)
                        ->update(['sold' => DB::raw('CASE WHEN sold >= '.(int) $item->quantity.' THEN sold - '.(int) $item->quantity.' ELSE 0 END')]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropUnique(['user_id', 'idempotency_key']);
            $table->dropIndex(['expires_at']);
            $table->dropColumn(['expires_at', 'idempotency_key']);
        });
    }
};
