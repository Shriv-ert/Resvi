<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_status_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('facility_id')->constrained('facilities')->restrictOnDelete();
            $table->foreignUuid('changed_by_id')->constrained('users')->restrictOnDelete();
            $table->enum('change_type', ['operational_status', 'is_active']);
            $table->string('old_value', 50);
            $table->string('new_value', 50);
            $table->text('reason')->nullable();
            $table->foreignUuid('report_id')->nullable()->constrained('reports')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['facility_id', 'change_type', 'changed_at'], 'idx_facility_status_logs_recap');
        });

        DB::statement('ALTER TABLE facility_status_logs ADD CONSTRAINT chk_facility_status_logs_value_changed CHECK (old_value <> new_value)');
        DB::statement("ALTER TABLE facility_status_logs ADD CONSTRAINT chk_facility_status_logs_operational_reason CHECK (change_type <> 'operational_status' OR (reason IS NOT NULL AND CHAR_LENGTH(TRIM(reason)) > 0))");
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_status_logs');
    }
};
