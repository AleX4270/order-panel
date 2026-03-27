<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 128);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('notification_channel_translations', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_channel_id')->nullable(false);
            $table->unsignedBigInteger('language_id')->nullable(false);
            $table->string('name', 255)->nullable(false);
            $table->timestamps();

            $table->foreign('notification_channel_id')->references('id')->on('notification_channels');
            $table->foreign('language_id')->references('id')->on('languages');

            $table->unique(['notification_channel_id', 'language_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('notification_channel_translations');
        Schema::dropIfExists('notification_channels');
    }
};
