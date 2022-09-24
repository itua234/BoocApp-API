<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dish_extras', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('dish_id');
            //$table->foreign('dish_id')->references('id')->on('dishes')->onDelete('cascade');
            $table->unsignedBigInteger('chef_id');
            $table->foreign('chef_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->string('measurement');
            $table->string('description')->nullable();
            $table->string('price');
            $table->string('profit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dish_extras');
    }
};
