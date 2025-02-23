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
        Schema::table('users', function($table) {
            $table->integer('is_online')->nullable();
            $table->integer('is_on_ride')->nullable();
            $table->json('location')->nullable();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->longText('location')->nullable();
            $table->string('type')->nullable();
            $table->bigInteger('rider_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('rider_id')->references('id')->on('users')->onDelete('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function($table) {
            $table->dropColumn('is_online');
            $table->dropColumn('is_on_ride');
            $table->dropColumn('location');
        });

        Schema::dropIfExists('locations');
    }
};
