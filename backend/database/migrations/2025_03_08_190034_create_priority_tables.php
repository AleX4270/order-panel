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
        Schema::create('priorities', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 32)->nullable(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('priority_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('priority_id')->nullable(false);
            $table->unsignedBigInteger('language_id')->nullable(false);
            $table->string('name', 255)->nullable(false);
            $table->text('description')->nullable(true);
            $table->timestamps();

            $table->foreign('priority_id')->references('id')->on('priorities');
            $table->foreign('language_id')->references('id')->on('languages');

            $table->unique(['priority_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('priority_translations');
        Schema::dropIfExists('priorities');
    }
};
