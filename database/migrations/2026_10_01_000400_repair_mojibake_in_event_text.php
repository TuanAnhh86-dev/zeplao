<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $repairMojibake = static function (?string $value): ?string {
            if ($value === null || ! preg_match('/(?:Ã|áº|á»|Ä|Æ|Â|â€)/u', $value)) {
                return $value;
            }

            $decoded = @iconv('Windows-1252', 'UTF-8', $value);

            return $decoded === false ? $value : $decoded;
        };

        DB::transaction(function () use ($repairMojibake): void {
            DB::table('events')
                ->select(['id', 'title', 'city', 'venue', 'introduction'])
                ->orderBy('id')
                ->get()
                ->each(function (object $event) use ($repairMojibake): void {
                    $updates = [];

                    foreach (['title', 'city', 'venue', 'introduction'] as $field) {
                        $repaired = $repairMojibake($event->{$field});
                        if ($repaired !== $event->{$field}) {
                            $updates[$field] = $repaired;
                        }
                    }

                    if ($updates !== []) {
                        DB::table('events')->where('id', $event->id)->update($updates);
                    }
                });

            DB::table('ticket_types')
                ->select(['id', 'name'])
                ->orderBy('id')
                ->get()
                ->each(function (object $ticketType) use ($repairMojibake): void {
                    $repaired = $repairMojibake($ticketType->name);

                    if ($repaired !== $ticketType->name) {
                        DB::table('ticket_types')
                            ->where('id', $ticketType->id)
                            ->update(['name' => $repaired]);
                    }
                });
        });
    }

    public function down(): void
    {
        // Mojibake repair is intentionally irreversible.
    }
};