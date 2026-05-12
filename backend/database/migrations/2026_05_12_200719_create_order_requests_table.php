<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('order_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable(false);
            $table->unsignedBigInteger('address_id')->nullable(false);
            $table->text('remarks')->nullable(true);
            $table->string('ip_address', 45)->nullable(false);
            $table->string('user_agent')->nullable(false);
            $table->timestamp('consent_given_at')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('address_id')->references('id')->on('addresses');
        });
    }

    public function down(): void {
        Schema::dropIfExists('order_requests');
    }
};
