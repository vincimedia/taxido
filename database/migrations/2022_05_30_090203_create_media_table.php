<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('model_id')->nullable();
            $table->string('model_type')->nullable();
            $table->uuid('uuid')->nullable()->unique();
            $table->string('collection_name')->nullable();
            $table->string('name')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('disk')->default('public')->nullable();
            $table->string('conversions_disk')->default('public')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->json('manipulations')->nullable();
            $table->json('custom_properties')->nullable();
            $table->json('generated_conversions')->nullable();
            $table->json('responsive_images')->nullable();
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
