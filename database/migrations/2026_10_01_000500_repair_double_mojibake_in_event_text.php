<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $repairMojibake = static function (?string $value): ?string {
            if ($value === null) {
                return null;
            }

            for ($attempt = 0; $attempt < 3; $attempt++) {
                if (! preg_match('/(?:Ã|áº|á»|Ä|Æ|Â|â€)/u', $value)) {
                    break;
                }

                $decoded = @iconv('UTF-8', 'Windows-1252', $value);
                if ($decoded === false || $decoded === $value) {
                    break;
                }

                $value = $decoded;
            }

            return $value;
        };

        DB::transaction(function () use ($repairMojibake): void {
            foreach (['events' => ['title', 'city', 'venue', 'introduction'], 'ticket_types' => ['name']] as $table => $fields) {
                DB::table($table)->orderBy('id')->get()->each(function (object $record) use ($fields, $repairMojibake, $table): void {
                    $updates = [];

                    foreach ($fields as $field) {
                        $repaired = $repairMojibake($record->{$field});
                        if ($repaired !== $record->{$field}) {
                            $updates[$field] = $repaired;
                        }
                    }

                    if ($updates !== []) {
                        DB::table($table)->where('id', $record->id)->update($updates);
                    }
                });
            }
        });
    }

    public function down(): void
    {
        // Mojibake repair is intentionally irreversible.
    }
};