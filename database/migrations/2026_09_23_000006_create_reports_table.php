<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('report_number', 20)->unique();
            $table->foreignUuid('reporter_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('facility_id')->constrained('facilities')->restrictOnDelete();
            $table->enum('category', [
                'kerusakan_fisik',
                'kerusakan_elektronik',
                'kebersihan',
                'keamanan',
                'lainnya',
            ]);
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->enum('status', ['baru', 'diproses', 'selesai', 'ditolak'])->default('baru');
            $table->text('resolution_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignUuid('processed_by_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->foreignUuid('resolved_by_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignUuid('rejected_by_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index(['reporter_id', 'status'], 'idx_reports_reporter_history');
            $table->index(['status', 'created_at'], 'idx_reports_queue_order');
            $table->index('facility_id', 'idx_reports_facility');
        });

        DB::statement('ALTER TABLE reports ADD CONSTRAINT chk_reports_description_not_blank CHECK (CHAR_LENGTH(TRIM(description)) > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
