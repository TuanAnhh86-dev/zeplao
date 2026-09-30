<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category', 50);
            $table->string('city', 100);
            $table->string('city_key', 50);
            $table->string('venue');
            $table->dateTime('starts_at')->index();
            $table->string('cover_image');
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('sold')->default(0);
            $table->timestamps();
            $table->unique(['event_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
        Schema::dropIfExists('events');
    }
};
