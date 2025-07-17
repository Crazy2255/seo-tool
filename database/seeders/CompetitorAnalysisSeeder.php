<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CompetitorInsight;

class CompetitorAnalysisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a test user
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Sample competitor analysis data
        $analyses = [
            [
                'user_id' => $user->id,
                'domain' => 'example.com',
                'url' => 'https://example.com',
                'title' => 'Example Domain',
                'description' => 'This domain is for use in illustrative examples in documents.',
                'total_backlinks' => 156,
                'total_keywords' => 89,
                'total_tools_detected' => 5,
                'strategy_score' => 76.50,
                'last_scanned_at' => now(),
                'scan_duration_seconds' => 12,
                'scan_status' => 'completed',
            ],
            [
                'user_id' => $user->id,
                'domain' => 'sample.org',
                'url' => 'https://sample.org',
                'title' => 'Sample Organization',
                'description' => 'A sample organization website for testing purposes.',
                'total_backlinks' => 234,
                'total_keywords' => 145,
                'total_tools_detected' => 8,
                'strategy_score' => 82.30,
                'last_scanned_at' => now()->subDays(2),
                'scan_duration_seconds' => 18,
                'scan_status' => 'completed',
            ],
            [
                'user_id' => $user->id,
                'domain' => 'test.net',
                'url' => 'https://test.net',
                'title' => 'Test Network',
                'description' => 'A test network website with various marketing tools.',
                'total_backlinks' => 98,
                'total_keywords' => 67,
                'total_tools_detected' => 12,
                'strategy_score' => 91.75,
                'last_scanned_at' => now()->subWeek(),
                'scan_duration_seconds' => 25,
                'scan_status' => 'completed',
            ],
        ];

        foreach ($analyses as $analysis) {
            CompetitorInsight::updateOrCreate(
                ['user_id' => $analysis['user_id'], 'domain' => $analysis['domain']],
                $analysis
            );
        }

        $this->command->info('Sample competitor analysis data created successfully!');
        $this->command->info("User ID: {$user->id}");
        $this->command->info("Email: {$user->email}");
        $this->command->info("Total analyses created: " . count($analyses));
    }
}
