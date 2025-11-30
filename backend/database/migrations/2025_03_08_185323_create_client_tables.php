<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('alias', 128)->nullable(true);
            $table->string('first_name', 128)->nullable(true);
            $table->string('last_name', 128)->nullable(true);
            $table->string('email', 255)->nullable(true);
            $table->string('phone_number', 32)->nullable(true);
            $table->unsignedBigInteger('address_id')->nullable(false);
            $table->boolean('is_blocked')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('address_id')->references('id')->on('addresses');
        });

        Schema::create('client_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable(false);
            $table->unsignedBigInteger('language_id')->nullable(false);
            $table->text('description')->nullable(false);
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('language_id')->references('id')->on('languages');

            $table->unique(['client_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('client_translations');
        Schema::dropIfExists('clients');
    }
};
