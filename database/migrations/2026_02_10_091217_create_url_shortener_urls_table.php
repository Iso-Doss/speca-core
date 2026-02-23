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
        if (!Schema::hasTable('url_shortener_urls')) {
            Schema::create('url_shortener_urls', function (Blueprint $table) {
                $table->uuid('id')->unique()->primary();
                $table->string('code')->index();
                $table->string('short_url')->index();
                $table->string('original_url')->unique()->index();
                $table->foreignUuid('user_id')->nullable()->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('url_shortener_urls');
    }
};
