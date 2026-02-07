<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Prop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = public_path('data/props.csv');

        if (! file_exists($path)) {
            $this->command->error("CSV not found at: {$path}");
            return;
        }

        // Use event_id = 1 for all imported props
        $eventId = 1;

        // Ensure the referenced event exists (insert with id = 1 if missing)
        if (! Event::find($eventId)) {
            \Illuminate\Support\Facades\DB::table('events')->insert([
                'id' => $eventId,
                'name' => 'Props Import Event',
                'description' => 'Evento creado automáticamente para importar props desde CSV',
                'type' => 'global',
                'owner_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('Created event with id: ' . $eventId);
        }

        // Remove existing props for that event so the seeder is idempotent
        Prop::where('event_id', $eventId)->delete();

        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->command->error('Unable to open CSV file.');
            return;
        }

        $header = null;
        $count = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (! $header) {
                $header = $row;
                continue;
            }

            // Normalize columns by index to avoid relying on header names
            $description = $row[0] ?? null;
            $opca = $row[1] ?? null;
            $opcb = $row[2] ?? null;

            if (! $description) {
                continue;
            }

            $trueValues = ['Si', 'Yes', 'Over'];
            $falseValues = ['No', 'Under'];

            Prop::create([
                'event_id' => $eventId,
                'description' => $description,
                'opca' => $opca,
                'opcb' => $opcb,
            ]);

            $count++;
        }

        fclose($handle);

        $this->command->info("Imported {$count} props into event [{$eventId}]");
    }
}

