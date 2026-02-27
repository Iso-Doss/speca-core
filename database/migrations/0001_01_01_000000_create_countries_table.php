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
        if (! Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->uuid('id')->primary()->unique();
                $table->string('name')->unique();
                $table->string('code')->unique();
                $table->string('iso_code')->unique();
                $table->string('phone_code')->unique();
                $table->string('flag')->nullable();
                $table->string('default_currency')->nullable();
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
        Schema::dropIfExists('countries');
    }
};
