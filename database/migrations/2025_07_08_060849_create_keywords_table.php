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
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seo_audit_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('keyword');
            $table->string('url');
            $table->integer('current_position')->nullable();
            $table->integer('previous_position')->nullable();
            $table->integer('search_volume')->default(0);
            $table->integer('difficulty')->default(0);
            $table->decimal('cpc', 8, 2)->default(0);
            $table->string('country', 2)->default('US');
            $table->string('language', 5)->default('en');
            $table->timestamp('tracked_date')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'keyword']);
            $table->index(['keyword', 'country']);
            $table->index('tracked_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
};
