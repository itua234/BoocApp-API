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
        Schema::create('occasion_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('chef_id');
            $table->foreign('chef_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('occasion_type');
            $table->string('expected_guests');
            $table->string('date');
            $table->enum('time', ['morning', 'afternoon', 'evening']);
            $table->string('firstname');
            $table->string('lastname');
            $table->string('phone');
            $table->string('address', 255);
            $table->text('note');
            $table->string('budget');
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
        Schema::dropIfExists('occasion_bookings');
    }
};
