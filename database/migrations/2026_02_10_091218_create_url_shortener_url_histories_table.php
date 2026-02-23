<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('url_shortener_url_histories')) {
            Schema::create('url_shortener_url_histories', function (Blueprint $table) {
                $table->uuid('id')->unique()->primary();
                $table->foreignUuid('url_id')->references('id')->on('url_shortener_urls')->cascadeOnUpdate()->cascadeOnDelete();
                $table->json('data');
                $table->timestamp('activated_at')->nullable()->useCurrent();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_shortener_url_histories');
    }
};
