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
        Schema::create('rental_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('vehicle_type_id');
            $table->unsignedBigInteger('normal_image_id')->nullable();
            $table->unsignedBigInteger('front_view_id')->nullable();
            $table->unsignedBigInteger('side_view_id')->nullable();
            $table->unsignedBigInteger('boot_view_id')->nullable();
            $table->unsignedBigInteger('interior_image_id')->nullable();
            $table->decimal('vehicle_per_day_price', 8, 2)->default(0.0);
            $table->integer('allow_with_driver')->default(1);
            $table->decimal('driver_per_day_charge', 8, 2)->default(0.0);
            $table->string('vehicle_subtype')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('gear_type')->nullable();
            $table->string('vehicle_speed')->nullable();
            $table->string('mileage')->nullable();
            $table->longText('interior')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('status')->nullable()->default(1);
            $table->unsignedBigInteger('is_ac')->nullable()->default(1);
            $table->unsignedBigInteger('bag_count')->default(0);
            $table->enum('verified_status',['pending','approved', 'rejected'])->default('pending')->nullable();
            $table->longText('registration_no')->nullable();
            $table->unsignedBigInteger('registration_image_id')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('normal_image_id')->references('id')->on('media')->onDelete('cascade')->nullable();
            $table->foreign('front_view_id')->references('id')->on('media')->onDelete('cascade')->nullable();
            $table->foreign('side_view_id')->references('id')->on('media')->onDelete('cascade')->nullable();
            $table->foreign('boot_view_id')->references('id')->on('media')->onDelete('cascade')->nullable();
            $table->foreign('registration_image_id')->references('id')->on('media')->onDelete('cascade')->nullable();
            $table->foreign('interior_image_id')->references('id')->on('media')->onDelete('cascade')->nullable();
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onDelete('cascade')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('rental_vehicle_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rental_vehicle_id');
            $table->unsignedBigInteger('zone_id');

            $table->foreign('rental_vehicle_id')->references('id')->on('rental_vehicles')->onDelete('cascade')->nullable();
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_vehicle');
    }
};
