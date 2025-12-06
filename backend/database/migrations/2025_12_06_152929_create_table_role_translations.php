<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('role_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->nullable(false);
            $table->unsignedBigInteger('language_id')->nullable(false);
            $table->string('name', 255)->nullable(false);
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('language_id')->references('id')->on('languages');

            $table->unique(['role_id', 'language_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('role_translations');
    }
};
