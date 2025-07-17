<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Keyword;
use App\Models\User;

class CheckKeywords extends Command
{
    protected $signature = 'check:keywords';
    protected $description = 'Check keywords in database';

    public function handle()
    {
        $count = Keyword::count();
        $this->info("📊 Total keywords in database: {$count}");
        
        if ($count > 0) {
            $this->info("\n🔍 Latest 5 keywords:");
            $keywords = Keyword::latest()->take(5)->get();
            
            foreach ($keywords as $keyword) {
                $user = User::find($keyword->user_id);
                $userName = $user ? $user->name : 'Unknown';
                
                $this->line("• ID: {$keyword->id} | Keyword: '{$keyword->keyword}' | URL: {$keyword->url} | User: {$userName}");
            }
        } else {
            $this->warn("❌ No keywords found. Make sure to login and add keywords through the web interface.");
        }
        
        $this->info("\n💡 To test:");
        $this->info("1. Login at: http://localhost:8000/login");
        $this->info("2. Use your registered email and password");
        $this->info("3. Go to: http://localhost:8000/keyword-tracker");
        $this->info("4. Add a keyword and run this command again!");
    }
}
