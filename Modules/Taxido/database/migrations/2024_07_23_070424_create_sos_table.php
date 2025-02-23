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
        Schema::create('sos', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->string('slug', 191)->unique()->nullable();
            $table->unsignedBigInteger('sos_image_id')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone')->default(0)->nullable();
            $table->bigInteger('created_by_id')->unsigned();
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
    
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sos_image_id')->references('id')->on('media')->onDelete('cascade')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sos');
    }
};
