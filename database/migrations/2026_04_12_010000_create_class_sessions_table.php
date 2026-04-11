<?php

use App\Enums\SessionStatus;
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
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->string('modality');
            $table->string('location')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->unsignedInteger('grace_period_minutes')->default(15);
            $table->string('qr_token', 64)->unique();
            $table->dateTime('qr_expires_at')->nullable();
            $table->string('status')->default(SessionStatus::Scheduled->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
