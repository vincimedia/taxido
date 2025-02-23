<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItemsWpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('label')->nullable();
            $table->string('route')->nullable();
            $table->longText('params')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->string('permission')->nullable();
            $table->unsignedBigInteger('parent')->default(0);
            $table->unsignedBigInteger('module')->nullable();
            $table->string('section')->nullable();
            $table->integer('sort')->default(0);
            $table->string('class')->nullable();
            $table->string('icon')->nullable();
            $table->integer('badge')->default(0);
            $table->boolean('badgeable')->default(0);
            $table->unsignedBigInteger('menu');
            $table->integer('depth')->default(0);
            $table->integer('quick_link')->default(0);
            $table->integer('status')->default(1);
            $table->integer('role_id')->default(0);
            $table->bigInteger('created_by_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('menu')->references('id')->on('menus')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_items');
    }
}
