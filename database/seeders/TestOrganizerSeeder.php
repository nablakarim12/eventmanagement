<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestOrganizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\EventOrganizer::create([
            'org_name' => 'Test Organizer',
            'org_email' => 'test@organizer.com',
            'password' => bcrypt('password123'),
            'contact_person_name' => 'John Doe',
            'phone' => '1234567890',
            'description' => 'Test organizer for testing login functionality',
            'website' => 'https://test-organizer.com',
            'status' => 'approved'
        ]);

        $this->command->info('Test organizer created successfully!');
        $this->command->info('Email: test@organizer.com');
        $this->command->info('Password: password123');
    }
}
