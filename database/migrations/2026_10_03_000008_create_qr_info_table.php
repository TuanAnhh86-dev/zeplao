<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_info', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->uuid('token')->unique();
            $table->string('status')->default('unused')->index();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            $table->index(['order_item_id', 'status']);
        });

        DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'confirmed')
            ->select('order_items.id', 'order_items.quantity')
            ->orderBy('order_items.id')
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    for ($index = 0; $index < (int) $item->quantity; $index++) {
                        DB::table('qr_info')->insert([
                            'order_item_id' => $item->id,
                            'token' => (string) Str::uuid(),
                            'status' => 'unused',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }, 'order_items.id', 'id');
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_info');
    }
};
