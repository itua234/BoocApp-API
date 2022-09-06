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
        Schema::create('dish_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->string('date');
            $table->enum('time', ['morning', 'afternoon', 'evening']);
            $table->string('firstname');
            $table->string('lastname');
            $table->string('phone');
            $table->string('address', 255);
            $table->text('note');
            $table->string('discount_code');

            $table->string('menu');
            $table->string('dish_id');
            $table->enum('gas_filled', ['yes', 'no']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dish_categories');
    }
};
