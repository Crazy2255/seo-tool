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
        Schema::create('backlinks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seo_audit_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('domain'); // The domain being analyzed
            $table->string('source_url'); // URL containing the backlink
            $table->string('target_url'); // URL being linked to
            $table->text('anchor_text')->nullable(); // Link anchor text
            $table->string('link_type')->default('dofollow'); // dofollow/nofollow
            $table->string('rel_attribute')->nullable(); // rel attribute value
            $table->integer('domain_authority')->default(0); // Source domain authority
            $table->integer('page_authority')->default(0); // Source page authority
            $table->integer('spam_score')->default(0); // Spam score
            $table->string('status')->default('active'); // active/broken/redirect
            $table->timestamp('discovered_date')->nullable();
            $table->timestamp('found_at')->useCurrent();
            $table->timestamps();

            $table->index(['domain', 'status']);
            $table->index(['user_id', 'domain']);
            $table->index('discovered_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlinks');
    }
};
