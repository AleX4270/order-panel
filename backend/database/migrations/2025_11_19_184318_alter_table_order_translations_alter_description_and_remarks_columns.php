<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('order_translations', function(Blueprint $table) {
            $table->text('description')->nullable(true)->change();
            $table->text('remarks')->nullable(true)->change();
        });
    }

    public function down(): void {
        Schema::table('order_translations', function(Blueprint $table) {
            $table->text('description')->nullable(false)->change();
            $table->text('remarks')->nullable(false)->change();
        });
    }
};
