<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('order_status', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 64);
            $table->tinyInteger('is_internal');
            $table->tinyInteger('is_active');
            $table->timestamps();
        });

        Schema::create('order_status_translation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_status_id');
            $table->unsignedBigInteger('language_id');
            $table->string('name', 255);
            $table->text('description');
            $table->timestamps();

            $table->foreign('order_status_id')->references('id')->on('order_status');
            $table->foreign('language_id')->references('id')->on('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('order_status_translation');
        Schema::dropIfExists('order_status');
    }
};
