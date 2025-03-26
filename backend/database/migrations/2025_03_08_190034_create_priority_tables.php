<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('priority', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 32);
            $table->tinyInteger('is_active');
            $table->timestamps();
        });

        Schema::create('priority_translation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('priority_id');
            $table->unsignedBigInteger('language_id');
            $table->string('name', 255);
            $table->text('description')->nullable(true);
            $table->timestamps();

            $table->foreign('priority_id')->references('id')->on('priority');
            $table->foreign('language_id')->references('id')->on('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('priority_translation');
        Schema::dropIfExists('priority');
    }
};
