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
        Schema::create('occasion_service_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->string('occasion_type');
            $table->integer('expected_guests');
            $table->string('date');
            $table->enum('period', ['Morning', 'Noon', 'Evening']);
            $table->string('firstname');
            $table->string('lastname');
            $table->string('phone');
            $table->string('address', 255);
            $table->text('note')->nullable();
            $table->double('budget');
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
        Schema::dropIfExists('occasion_service_details');
    }
};
