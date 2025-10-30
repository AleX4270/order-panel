<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 64)->nullable(false);
            $table->boolean('is_internal')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('order_status_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_status_id')->nullable(false);
            $table->unsignedBigInteger('language_id')->nullable(false);
            $table->string('name', 255)->nullable(false);
            $table->text('description')->nullable(true);
            $table->timestamps();

            $table->foreign('order_status_id')->references('id')->on('order_statuses');
            $table->foreign('language_id')->references('id')->on('languages');

            $table->unique(['order_status_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('order_status_translations');
        Schema::dropIfExists('order_statuses');
    }
};
