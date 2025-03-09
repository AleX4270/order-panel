<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('client', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 128);
            $table->string('last_name', 128);
            $table->string('email', 255)->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->unsignedBigInteger('address_id');
            $table->tinyInteger('is_blocked');
            $table->tinyInteger('is_active');
            $table->timestamps();

            $table->foreign('address_id')->references('id')->on('address');
        });

        Schema::create('client_translation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('language_id');
            $table->text('description');

            $table->foreign('client_id')->references('id')->on('client');
            $table->foreign('language_id')->references('id')->on('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('client_translation');
        Schema::dropIfExists('client');
    }
};
