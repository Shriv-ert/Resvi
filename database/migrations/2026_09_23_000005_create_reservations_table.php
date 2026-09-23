<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reservation_number', 20)->unique();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('facility_id')->constrained('facilities')->restrictOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('purpose');
            $table->enum('status', [
                'menunggu',
                'disetujui',
                'berlangsung',
                'selesai',
                'ditolak',
                'dibatalkan_oleh_pengguna',
                'dibatalkan_oleh_petugas',
                'kedaluwarsa',
            ])->default('menunggu');
            $table->text('rejection_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignUuid('cancelled_by_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->text('expiration_reason')->nullable();
            $table->foreignUuid('approved_by_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignUuid('rejected_by_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['facility_id', 'date', 'status', 'start_time', 'end_time'], 'idx_reservations_conflict_check');
            $table->index(['user_id', 'facility_id', 'date', 'start_time', 'end_time', 'status'], 'idx_reservations_duplicate_check');
            $table->index(['user_id', 'status', 'created_at'], 'idx_reservations_user_history');
            $table->index(['status', 'created_at'], 'idx_reservations_queue_order');
            $table->index(['status', 'date', 'start_time'], 'idx_reservations_lazy_start');
            $table->index(['status', 'date', 'end_time'], 'idx_reservations_lazy_end');
        });

        DB::statement("ALTER TABLE reservations ADD CONSTRAINT chk_reservations_operational_hours CHECK (start_time >= '07:00:00' AND start_time < '20:00:00' AND end_time > '07:00:00' AND end_time <= '20:00:00')");
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_half_hour_slots CHECK (MINUTE(start_time) IN (0, 30) AND SECOND(start_time) = 0 AND MINUTE(end_time) IN (0, 30) AND SECOND(end_time) = 0)');
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_end_after_start CHECK (end_time > start_time)');
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_purpose_not_blank CHECK (CHAR_LENGTH(TRIM(purpose)) > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
