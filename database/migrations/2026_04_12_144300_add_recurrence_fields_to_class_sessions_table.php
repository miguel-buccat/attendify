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
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->string('recurrence_pattern')->nullable()->after('status'); // weekly, biweekly
            $table->date('recurrence_end_date')->nullable()->after('recurrence_pattern');
            $table->string('recurrence_group_id', 36)->nullable()->after('recurrence_end_date');
            $table->text('cancellation_reason')->nullable()->after('recurrence_group_id');

            $table->index('recurrence_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropIndex(['recurrence_group_id']);
            $table->dropColumn(['recurrence_pattern', 'recurrence_end_date', 'recurrence_group_id', 'cancellation_reason']);
        });
    }
};
