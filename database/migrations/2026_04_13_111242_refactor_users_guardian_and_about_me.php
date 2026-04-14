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
        Schema::table('users', function (Blueprint $table) {
            $table->string('guardian_name')->nullable()->after('banner_path');
            $table->dropColumn(['about_me', 'guardian_phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('about_me')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->dropColumn('guardian_name');
        });
    }
};
