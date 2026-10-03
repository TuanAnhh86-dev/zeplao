<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->default('vnpay');
            $table->string('callback_type', 16);
            $table->string('transaction_no')->nullable()->index();
            $table->string('txn_ref')->nullable()->index();
            $table->unsignedBigInteger('amount')->nullable();
            $table->string('response_code', 8)->nullable();
            $table->string('transaction_status', 8)->nullable();
            $table->string('bank_code')->nullable();
            $table->string('pay_date', 20)->nullable();
            $table->boolean('signature_valid')->default(false);
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
