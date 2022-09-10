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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('chef_id');
            $table->foreign('chef_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('total')->default(0.0000);
            $table->string('reference')->unique();
            $table->enum('payment_status', ['pending', 'success', 'failed'])->default('pending');
            $table->enum('order_status', ['pending', 'completed', 'cancelled', 'in progress'])->default('pending');
            $table->enum('payment_channel', ['Flutterwave', 'Paystack', 'Manual']);
            $table->enum('type', ['Home Service', 'Delivery Service']);
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
        Schema::dropIfExists('orders');
    }
};
