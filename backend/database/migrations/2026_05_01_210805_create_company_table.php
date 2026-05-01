<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('company', function (Blueprint $table) {
            $table->id();
            $table->string('name', 256)->nullable(false);
            $table->unsignedBigInteger('address_id')->nullable(false);
            $table->timestamps();

            $table->foreign('address_id')->references('id')->on('addresses');
        });
    }

    public function down(): void {
        Schema::dropIfExists('company');
    }
};
