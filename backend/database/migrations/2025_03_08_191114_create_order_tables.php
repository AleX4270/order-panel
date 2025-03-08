<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 32);
            $table->dateTime('date_deadline');
            $table->dateTime('date_completed');
            $table->unsignedBigInteger('user_creation_id');
            $table->unsignedBigInteger('user_modification_id');
            $table->unsignedBigInteger('priority_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('order_status_id');
            $table->tinyInteger('is_active');
            $table->timestamps();

            $table->foreign('user_creation_id')->references('id')->on('users');
            $table->foreign('user_modification_id')->references('id')->on('users');
            $table->foreign('priority_id')->references('id')->on('priority');
            $table->foreign('client_id')->references('id')->on('client');
            $table->foreign('order_status_id')->references('id')->on('order_status');
        });

        Schema::create('order_translation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('language_id');
            $table->string('name', 255);
            $table->text('remarks');
            $table->text('description');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('order');
            $table->foreign('language_id')->references('id')->on('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('order_translation');
        Schema::dropIfExists('order');
    }
};
