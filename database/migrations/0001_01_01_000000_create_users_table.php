<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone')->default(0)->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('profile_image_id')->nullable();
            $table->boolean('is_verified')->default(0);
            $table->integer('status')->default(1);
            $table->string('referral_code')->unique()->nullable();
            $table->string('fcm_token')->nullable();
            $table->unsignedBigInteger('referred_by_id')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->integer('system_reserve')->default(0);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referred_by_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
    }
};
