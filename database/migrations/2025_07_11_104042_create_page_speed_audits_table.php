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
        Schema::create('page_speed_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('url');
            $table->enum('strategy', ['desktop', 'mobile'])->default('desktop');
            
            // Overall scores
            $table->integer('performance_score')->nullable();
            $table->integer('accessibility_score')->nullable();
            $table->integer('best_practices_score')->nullable();
            $table->integer('seo_score')->nullable();
            
            // Core Web Vitals
            $table->decimal('first_contentful_paint', 8, 2)->nullable(); // in seconds
            $table->decimal('largest_contentful_paint', 8, 2)->nullable(); // in seconds
            $table->decimal('total_blocking_time', 8, 2)->nullable(); // in milliseconds
            $table->decimal('cumulative_layout_shift', 8, 4)->nullable(); // unitless
            $table->decimal('speed_index', 8, 2)->nullable(); // in seconds
            
            // Additional metrics
            $table->decimal('first_meaningful_paint', 8, 2)->nullable();
            $table->decimal('time_to_interactive', 8, 2)->nullable();
            $table->decimal('max_potential_fid', 8, 2)->nullable();
            
            // Page info
            $table->string('page_title')->nullable();
            $table->text('screenshot_url')->nullable();
            
            // Raw API response for detailed analysis
            $table->json('raw_data')->nullable();
            
            // Analysis metadata
            $table->timestamp('analyzed_at');
            $table->string('lighthouse_version')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'analyzed_at']);
            $table->index(['url', 'strategy']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_speed_audits');
    }
};
