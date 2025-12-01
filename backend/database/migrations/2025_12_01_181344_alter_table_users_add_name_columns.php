<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function(Blueprint $table) {
            $table->string('first_name', 128)->nullable(true)->after('name');
            $table->string('last_name', 128)->nullable(true)->after('first_name');
        });
    }

    public function down(): void {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
        });
    }
};