<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keywords:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test keyword functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing keyword functionality...');

        // Create a test user if one doesn't exist
        $user = \App\Models\User::where('email', 'test@example.com')->first();
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => \Hash::make('password'),
                'email_verified_at' => now()
            ]);
            $this->info("Created test user: {$user->email}");
        } else {
            $this->info("Using existing test user: {$user->email}");
        }

        // Create a test keyword
        $keyword = \App\Models\Keyword::create([
            'user_id' => $user->id,
            'keyword' => 'Test SEO Keyword',
            'url' => 'https://example.com',
            'current_position' => 15,
            'previous_position' => 18,
            'search_volume' => 1200,
            'difficulty' => 65,
            'cpc' => 2.45,
            'country' => 'US',
            'city' => 'new-york',
            'language' => 'en',
            'tracked_date' => now()
        ]);

        $this->info("Created test keyword: {$keyword->keyword}");
        $this->info("Keyword ID: {$keyword->id}");

        // Check total keywords
        $total = \App\Models\Keyword::count();
        $this->info("Total keywords in database: {$total}");

        // Check user's keywords
        $userKeywords = \App\Models\Keyword::byUser($user->id)->get();
        $this->info("User's keywords: {$userKeywords->count()}");

        foreach ($userKeywords as $kw) {
            $this->info("- {$kw->keyword} ({$kw->url}) - Position: {$kw->current_position}");
        }

        $this->info('Test completed!');
    }
}
