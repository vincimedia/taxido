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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('service_image_id')->nullable();
            $table->unsignedBigInteger('service_icon_id')->nullable();
            $table->enum('type', ['cab', 'parcel', 'freight'])->default('cab')->nullable();
            $table->integer('status')->default(1)->nullable();
            $table->integer('is_primary')->default(0)->nullable();
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_image_id')->references('id')->on('media')->onDelete('cascade')->nullable();
            $table->foreign('service_icon_id')->references('id')->on('media')->onDelete('cascade')->nullable();
        });

        Schema::create('vehicle_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_type_id');
            $table->unsignedBigInteger('service_id');

            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onDelete('cascade')->nullable();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('vehicle_services');
    }
};
