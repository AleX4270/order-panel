<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->unsignedBigInteger('notification_event_id')->nullable(false);
            $table->unsignedBigInteger('notification_channel_id')->nullable(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('notification_event_id')->references('id')->on('notification_events');
            $table->foreign('notification_channel_id')->references('id')->on('notification_channels');

            $table->unique(['user_id', 'notification_event_id', 'notification_channel_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_notification_settings');
    }
};
