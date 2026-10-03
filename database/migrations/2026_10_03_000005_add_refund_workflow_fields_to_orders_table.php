<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('cancelled_by_type', 16)->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancel_reason')->nullable();
            $table->foreignId('refund_requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('refund_reason')->nullable();
            $table->text('refund_review_note')->nullable();
            $table->unsignedBigInteger('refund_amount')->nullable();
            $table->foreignId('refund_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('refund_approved_at')->nullable();
            $table->foreignId('refunded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_transaction_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('cancelled_by');
            $table->dropConstrainedForeignId('refund_requested_by');
            $table->dropConstrainedForeignId('refund_approved_by');
            $table->dropConstrainedForeignId('refunded_by');
            $table->dropColumn([
                'cancelled_by_type', 'cancel_reason', 'refund_reason', 'refund_review_note', 'refund_amount',
                'refund_approved_at', 'refunded_at', 'refund_transaction_id',
            ]);
        });
    }
};
