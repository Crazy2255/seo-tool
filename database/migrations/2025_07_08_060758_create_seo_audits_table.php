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
        Schema::create('seo_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('url');
            $table->string('title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('h1_tags')->nullable();
            $table->json('h2_tags')->nullable();
            $table->integer('status_code')->nullable();
            $table->decimal('page_load_speed', 8, 2)->nullable(); // in milliseconds
            $table->integer('internal_links_count')->default(0);
            $table->integer('external_links_count')->default(0);
            $table->integer('images_without_alt')->default(0);
            $table->integer('images_count')->default(0);
            $table->integer('word_count')->default(0);
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots_meta')->nullable();
            $table->string('sitemap_url')->nullable();
            $table->string('robots_txt_status')->nullable();
            $table->boolean('ssl_certificate')->default(false);
            $table->boolean('mobile_friendly')->default(false);
            $table->json('schema_markup')->nullable();
            $table->json('social_meta_tags')->nullable();
            $table->json('broken_links')->nullable();
            $table->decimal('audit_score', 5, 2)->default(0);
            $table->json('recommendations')->nullable();
            $table->timestamp('audit_date')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('audit_score');
            $table->index('audit_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_audits');
    }
};
