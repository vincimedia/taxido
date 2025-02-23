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
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->geometry('place_points')->nullable();
            $table->json('locations')->nullable();
            $table->decimal('amount', 15)->default(0)->nullable();
            $table->integer('status')->default(1)->nullable();
            $table->enum('distance_type', ['mile', 'km'])->default('mile')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade')->nullable();
        });

        Schema::create('driver_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('zone_id');

            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade')->nullable();
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade')->nullable();
        });

        Schema::create('vehicle_type_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_type_id');
            $table->unsignedBigInteger('zone_id');

            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onDelete('cascade')->nullable();
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade')->nullable();
        });

        Schema::create('banner_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('banner_id')->unsigned();
            $table->unsignedBigInteger('zone_id')->unsigned();

            $table->foreign('banner_id')->references('id')->on('banners')->onDelete('cascade')->nullable();
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
        Schema::dropIfExists('driver_zones');
        Schema::dropIfExists('vehicle_type_zones');
        Schema::dropIfExists('banner_zones');
    }
};
