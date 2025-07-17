<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('competitor_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('domain');
            $table->string('url');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            
            // Marketing Tools Detection
            $table->json('analytics_tools')->nullable(); // Google Analytics, GTM, etc.
            $table->json('social_pixels')->nullable(); // Facebook Pixel, Twitter Pixel, etc.
            $table->json('chat_widgets')->nullable(); // Intercom, Zendesk, etc.
            $table->json('email_marketing')->nullable(); // Mailchimp, Klaviyo, etc.
            $table->json('advertising_networks')->nullable(); // Google Ads, Facebook Ads, etc.
            $table->json('retargeting_tools')->nullable(); // AdRoll, Criteo, etc.
            
            // SEO and Content Analysis
            $table->json('seo_tools')->nullable(); // SEMrush, Ahrefs scripts
            $table->json('opengraph_tags')->nullable(); // OpenGraph meta tags
            $table->json('schema_markup')->nullable(); // Structured data
            $table->json('utm_parameters')->nullable(); // UTM tracking
            
            // Content Strategy
            $table->json('blog_analysis')->nullable(); // Blog freshness, frequency
            $table->json('content_strategy')->nullable(); // Content types, keywords
            
            // Technical Analysis
            $table->json('performance_tools')->nullable(); // Speed optimization tools
            $table->json('security_tools')->nullable(); // Security plugins, CDN
            $table->json('cms_detection')->nullable(); // WordPress, Shopify, etc.
            
            // Summary Data
            $table->json('strategy_summary')->nullable(); // Detected strategies
            $table->json('actionable_insights')->nullable(); // Recommendations
            $table->integer('total_tools_detected')->default(0);
            $table->decimal('strategy_score', 5, 2)->default(0);
            
            // Metadata
            $table->timestamp('last_scanned_at')->nullable();
            $table->integer('scan_duration_seconds')->nullable();
            $table->string('scan_status')->default('pending'); // pending, completed, failed
            $table->text('scan_error')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'domain']);
            $table->index(['user_id', 'last_scanned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitor_insights');
    }
};
