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
        Schema::create('chef_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('identification_card')->nullable();
            
            $table->string('video_verification')->nullable();

            $table->enum('is_certified', [0, 1])->nullable();
            $table->string('certificate')->nullable();

            $table->enum('is_restaurant', [0, 1])->nullable();
            $table->string('cac_registration')->nullable();
            $table->string('restaurant_address', 255)->nullable();

            $table->string('address', 255)->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
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
        Schema::dropIfExists('chef_profiles');
    }
};
