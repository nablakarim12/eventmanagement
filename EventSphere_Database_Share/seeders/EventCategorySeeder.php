<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use Illuminate\Database\Seeder;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Academic Conference',
                'description' => 'Scientific and academic conferences for research presentations and knowledge sharing.',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Innovation Competition',
                'description' => 'Competitions focused on innovative solutions, startups, and entrepreneurship.',
                'color' => '#ef4444',
            ],
            [
                'name' => 'Workshop',
                'description' => 'Hands-on learning sessions and skill development workshops.',
                'color' => '#10b981',
            ],
            [
                'name' => 'Seminar',
                'description' => 'Educational seminars and informational sessions.',
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Training Session',
                'description' => 'Professional training and skill development programs.',
                'color' => '#8b5cf6',
            ],
        ];

        foreach ($categories as $category) {
            EventCategory::create($category);
        }
    }
}