<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusEventSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        // 1. Create venues
        $venues = [
            DB::table('venues')->insertGetId([
                'name' => 'University Gymnasium',
                'address' => 'Main Campus',
                'created_at' => $now,
                'updated_at' => $now,
            ]),
            DB::table('venues')->insertGetId([
                'name' => 'Computer Laboratory',
                'address' => 'IT Building, Room 201',
                'created_at' => $now,
                'updated_at' => $now,
            ]),
            DB::table('venues')->insertGetId([
                'name' => 'University Auditorium',
                'address' => 'Administration Building',
                'created_at' => $now,
                'updated_at' => $now,
            ]),
        ];

        // 2. Create 200 participants in chunks
        $participants = [];

        for ($i = 1; $i <= 200; $i++) {
            $participants[] = [
                'name' => 'Participant ' . $i,
                'email' => 'participant' . $i . '@example.com',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($participants, 100) as $chunk) {
            DB::table('participants')->insert($chunk);
        }

        $participantIds = DB::table('participants')
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        // 3. Description phrases for searchable content
        $phrases = [
            'A campus workshop for students and faculty members.',
            'A university seminar about leadership and teamwork.',
            'An educational event focused on web development.',
            'A student activity featuring music and performances.',
            'A technology conference about programming and innovation.',
            'A sports festival promoting teamwork and fitness.',
            'A career development event for university students.',
            'An academic discussion about research and learning.',
            'A community gathering for student organizations.',
            'A creative arts exhibition featuring student projects.',
        ];

        // 4. Create 20,000 events in chunks
        $eventNumber = 0;

        for ($chunkNumber = 0; $chunkNumber < 20; $chunkNumber++) {

            $events = [];

            for ($j = 0; $j < 1000; $j++) {

                $eventNumber++;

                $capacity = 30 + ($eventNumber % 171);

                // Only 50 out of 20,000 events contain this rare value.
                $isRare = $eventNumber <= 50;

                $description = $phrases[($eventNumber - 1) % count($phrases)];

                if ($isRare) {
                    $description .= ' Special quasar research presentation.';
                }

                $dressCode = $isRare
                    ? 'cosmic-formal'
                    : ['casual', 'smart-casual', 'formal'][$eventNumber % 3];

                $events[] = [
                    'venue_id' => $venues[($eventNumber - 1) % 3],
                    'title' => 'Campus Event ' . str_pad($eventNumber, 5, '0', STR_PAD_LEFT),
                    'description' => $description,
                    'event_date' => now()
                        ->addDays($eventNumber % 365)
                        ->toDateTimeString(),
                    'capacity' => $capacity,
                    'details' => json_encode([
                        'dress_code' => $dressCode,
                        'capacity' => $capacity,
                        'tags' => [
                            'campus',
                            'student',
                            'event',
                        ],
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('events')->insert($events);
        }

        // 5. Register participants for the first 100 events
        // Each event receives 3 registrations.

        $eventIds = DB::table('events')
            ->orderBy('id')
            ->limit(100)
            ->pluck('id')
            ->toArray();

        $registrations = [];

        foreach ($eventIds as $index => $eventId) {

            for ($j = 0; $j < 3; $j++) {

                $participantIndex = ($index * 3 + $j) % count($participantIds);

                $registrations[] = [
                    'event_id' => $eventId,
                    'participant_id' => $participantIds[$participantIndex],
                    'status' => $j === 2 ? 'waitlisted' : 'registered',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($registrations, 300) as $chunk) {
            DB::table('event_participant')->insert($chunk);
        }

        // 6. Display seeding summary
        echo "\nCampus Events seeding completed!\n";
        echo "Events created: " . DB::table('events')->count() . "\n";
        echo "Participants created: " . DB::table('participants')->count() . "\n";
        echo "Registrations created: " . DB::table('event_participant')->count() . "\n";
    }
}
