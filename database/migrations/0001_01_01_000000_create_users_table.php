<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Speca\SpecaCore\Enums\Gender;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->uuid('id')->primary()->unique();

                // Basic info
                $table->string('email')->index();
                $table->string('phone_with_indicative')->nullable()->index();

                $table->enum('gender', Gender::getValues())->nullable();

                $table->string('password')->nullable();

                $table->text('address')->nullable();
                $table->date('birthday')->nullable();

                $table->string('activation_code')->nullable();
                $table->string('reset_password_code')->nullable();

                $table->string('full_name');

                $table->string('avatar')->nullable();

                $table->text('deleted_reason')->nullable();

                $table->rememberToken();

                // Countries (foreign keys)
                $table->foreignUuid('country_id')->nullable()->references('id')->on('countries')->cascadeOnUpdate()->cascadeOnDelete();

                // Profile (foreign key)
                $table->foreignUuid('user_profile_id')->nullable()->references('id')->on('user_profiles')->cascadeOnUpdate()->cascadeOnDelete();

                // Activation & verification
                $table->timestamp('activated_at')->nullable();
                $table->timestamp('notification_enable_at')->nullable();
                $table->timestamp('activation_code_expire_at')->nullable();
                $table->timestamp('reset_password_code_expire_at')->nullable();

                $table->timestamp('cgu_validated_at')->nullable();
                $table->timestamp('cgu_rested_at')->nullable();

                // Two-factor
                $table->timestamp('two_factor_enabled_at')->nullable();
                $table->timestamp('two_factor_generated_at')->nullable();
                $table->timestamp('two_factor_emergency_keys_copied_at')->nullable();
                $table->unsignedInteger('two_factor_number_failed_login_attempts')->default(0);
                $table->timestamp('two_factor_last_failed_login_attempted_at')->nullable();

                // Timestamps & Soft delete
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['user_profile_id', 'email']);
                $table->unique(['user_profile_id', 'phone_with_indicative']);
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignUuid('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
