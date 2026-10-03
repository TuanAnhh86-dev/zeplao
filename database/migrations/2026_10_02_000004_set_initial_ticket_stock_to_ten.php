<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('ticket_types')
            ->where('quantity', 0)
            ->where('sold', 0)
            ->update(['quantity' => 10]);
    }

    public function down(): void
    {
        // Keep stock changes made after deployment.
    }
};
