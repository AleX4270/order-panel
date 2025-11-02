<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 32)->nullable(false);
            $table->dateTime('date_deadline')->nullable(false);
            $table->dateTime('date_completed')->nullable(true);
            $table->unsignedBigInteger('user_creation_id')->nullable(false);
            $table->unsignedBigInteger('user_modification_id')->nullable(true);
            $table->unsignedBigInteger('priority_id')->nullable(false);
            $table->unsignedBigInteger('client_id')->nullable(false);
            $table->unsignedBigInteger('order_status_id')->nullable(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_creation_id')->references('id')->on('users');
            $table->foreign('user_modification_id')->references('id')->on('users');
            $table->foreign('priority_id')->references('id')->on('priorities');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('order_status_id')->references('id')->on('order_statuses');
        });

        Schema::create('order_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable(false);
            $table->unsignedBigInteger('language_id')->nullable(false);
            $table->string('name', 255)->nullable(false);
            $table->text('remarks');
            $table->text('description');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('language_id')->references('id')->on('languages');

            $table->unique(['order_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('order_translations');
        Schema::dropIfExists('orders');
    }
};
