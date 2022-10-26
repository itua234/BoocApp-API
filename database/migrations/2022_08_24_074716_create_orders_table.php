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
            $table->string('order_no')->unique();
            $table->double('total')->default(0.0000);
            $table->integer('subtotal')->unsigned();
            $table->integer('shipping_cost')->unsigned();
            $table->integer('subcharge')->unsigned();
            $table->string('reference')->unique();
            $table->enum('payment_status', ['pending', 'success', 'failed'])->default('pending');
            $table->enum('order_status', ['pending', 'accepted', 'completed', 'cancelled', 'declined', 'in progress'])->default('pending');
            $table->enum('payment_channel', ['FLUTTERWAVE', 'PAYSTACK', 'MANUAL'])->nullable();
            $table->enum('type', ['HOME SERVICE', 'DELIVERY SERVICE', 'OCCASION SERVICE']);
            $table->boolean('verified')->default(0);
            $table->string('discount_code')->nullable();
            $table->text('reason_for_declining')->nullable();
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
