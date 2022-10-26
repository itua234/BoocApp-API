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
        Schema::create('earning_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referral_id');
            $table->foreign('referral_id')->references('id')->on('referrals')->onDelete('cascade');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->integer('amount');
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
        Schema::dropIfExists('earning_payouts');
    }
};
