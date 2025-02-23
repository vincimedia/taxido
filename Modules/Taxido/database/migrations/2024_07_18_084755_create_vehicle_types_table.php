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
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('vehicle_image_id')->nullable();
            $table->unsignedBigInteger('vehicle_map_icon_id')->nullable();
            $table->string('slug', 191)->unique()->nullable();
            $table->decimal('base_amount', 8,2)->default(1)->nullable();
            $table->decimal('min_per_unit_charge', 8,2)->default(1)->nullable();
            $table->decimal('max_per_unit_charge', 8,2)->default(1)->nullable();
            $table->decimal('min_per_min_charge', 8, 2)->default(0.0)->nullable();
            $table->decimal('max_per_min_charge', 8, 2)->default(0.0)->nullable();
            $table->decimal('min_per_weight_charge', 8, 2)->default(0.0)->nullable();
            $table->decimal('max_per_weight_charge', 8, 2)->default(0.0)->nullable();
            $table->decimal('cancellation_charge', 8,2)->default(0.0)->nullable();
            $table->decimal('waiting_time_charge', 8,2)->default(0.0)->nullable();
            $table->enum('commission_type', ['fixed', 'percentage'])->default('fixed')->nullable();
            $table->decimal('commission_rate', 8, 2)->default(0.0)->nullable();

            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->integer('status')->default(1)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_image_id')->references('id')->on('media')->onDelete('cascade')->nullable();
            $table->foreign('vehicle_map_icon_id')->references('id')->on('media')->onDelete('cascade')->nullable();
        });

        Schema::create('vehicle_info', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->nullable();
            $table->string('color')->nullable();
            $table->string('model')->nullable();
            $table->integer('seat')->nullable();
            $table->unsignedBigInteger('vehicle_type_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('vehicle_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_image_id')->nullable();
            $table->unsignedBigInteger('attachment_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('vehicle_image_id')->references('id')->on('media')->onDelete('cascade')->nullable();
            $table->foreign('attachment_id')->references('id')->on('media')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_types');
        Schema::dropIfExists('vehicle_info');
    }
};
