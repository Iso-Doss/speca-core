<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('user_password_histories')) {
            Schema::create('user_password_histories', function (Blueprint $table) {
                $table->uuid('id')->primary()->unique();
                $table->foreignUuid('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
                $table->string('password');
                $table->timestamp('activated_at')->nullable()->useCurrent();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_password_histories');
    }
};
