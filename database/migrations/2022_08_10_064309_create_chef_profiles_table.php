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

            $table->string('id_card_url')->nullable();
            $table->string('video_url')->nullable();

            $table->enum('is_certified', [0, 1])->default(0);
            $table->string('certificate_url')->nullable();

            $table->enum('is_restaurant', [0, 1])->default(0);
            $table->string('cac_reg_number')->nullable();
            $table->string('restaurant_name')->nullable();
            $table->string('restaurant_address', 255)->nullable();

            $table->string('address', 255)->nullable();
            $table->string('city')->nullable();
            $table->string('nearest_landmark')->nullable();
            $table->string('state')->nullable();
            $table->double('rating')->default(0);
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
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
