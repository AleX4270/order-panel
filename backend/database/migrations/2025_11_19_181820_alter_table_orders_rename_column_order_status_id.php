<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function(Blueprint $table) {
            $table->renameColumn('order_status_id', 'status_id');
        });
    }

    public function down(): void {
        Schema::table('orders', function(Blueprint $table) {
            $table->renameColumn('status_id', 'order_status_id');
        });
    }
};
