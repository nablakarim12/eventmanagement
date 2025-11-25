<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventTypes = [
            [
                'name' => 'Innovation',
                'description' => 'Innovation events focused on new ideas, technologies, and creative solutions.',
                'icon' => 'lightbulb',
            ],
            [
                'name' => 'Conference',
                'description' => 'Professional conferences for knowledge sharing and networking.',
                'icon' => 'users-rectangle',
            ],
        ];

        foreach ($eventTypes as $type) {
            EventType::create([
                'name' => $type['name'],
                'slug' => Str::slug($type['name']),
                'description' => $type['description'],
                'icon' => $type['icon'],
                'is_active' => true,
            ]);
        }
    }
}
