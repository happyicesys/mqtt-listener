<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vend_data', function (Blueprint $table) {
            $table->string('connection')->nullable()->after('id');
            $table->string('ip_address')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vend_data', function (Blueprint $table) {
            $table->dropColumn(['connection', 'ip_address']);
        });
    }
};
