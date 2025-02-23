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
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->string('message', 255)->nullable();
            $table->string('send_to', 255)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_read')->default(0);
            $table->string('image_url')->nullable();
            $table->string('url')->nullable();
            $table->string('notification_type')->nullable();
            $table->bigInteger('created_by_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('push_notifications_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('push_notifications_id')->unsigned();
            $table->unsignedBigInteger('zone_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('push_notifications_id')->references('id')->on('push_notifications')->onDelete('cascade')->nullable();
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('push_notifications_zones');
    }
};
