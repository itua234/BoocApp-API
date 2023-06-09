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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('gender', 10)->nullable();
            $table->string('password');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('fcm_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('user_type', ['chef', 'user', 'admin']);
            $table->string('photo')->nullable();
            $table->enum('status', ['pending', 'active', 'suspended'])->default('active');
            $table->enum('available', [0, 1])->default(1); // 0 - Offline, 1 - Online
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
