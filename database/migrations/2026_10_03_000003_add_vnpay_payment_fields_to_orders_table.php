<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->timestamp('expires_at')->nullable()->index();
            $table->string('payment_transaction_id')->nullable()->unique();
        });

        DB::table('orders')->where('status', 'pending')->orderBy('id')->chunkById(100, function ($orders): void {
            foreach ($orders as $order) {
                DB::table('orders')->where('id', $order->id)->update([
                    'expires_at' => date('Y-m-d H:i:s', strtotime($order->created_at.' +10 minutes')),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropUnique(['payment_transaction_id']);
            $table->dropIndex(['expires_at']);
            $table->dropColumn(['expires_at', 'payment_transaction_id']);
        });
    }
};
