<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('country', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 32);
            $table->timestamps();
        });

        Schema::create('country_translation', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('language_id');
            $table->string('name', 255);
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('country');
            $table->foreign('language_id')->references('id')->on('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('country_translation');
        Schema::dropIfExists('country');
    }
};
