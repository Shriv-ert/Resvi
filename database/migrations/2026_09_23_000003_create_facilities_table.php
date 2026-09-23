<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', ['kelas', 'aula', 'laboratorium', 'alat', 'lapangan']);
            $table->string('location');
            $table->unsignedInteger('capacity')->nullable();
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->enum('operational_status', ['dapat_digunakan', 'dalam_perbaikan'])
                ->default('dapat_digunakan');
            $table->timestamps();

            $table->index(['is_active', 'type'], 'idx_facilities_active_type');
            $table->index('location', 'idx_facilities_location');
            $table->index('operational_status', 'idx_facilities_operational_status');
        });

        DB::statement('ALTER TABLE facilities ADD CONSTRAINT chk_facilities_name_not_blank CHECK (CHAR_LENGTH(TRIM(name)) > 0)');
        DB::statement('ALTER TABLE facilities ADD CONSTRAINT chk_facilities_location_not_blank CHECK (CHAR_LENGTH(TRIM(location)) > 0)');
        DB::statement('ALTER TABLE facilities ADD CONSTRAINT chk_facilities_description_not_blank CHECK (CHAR_LENGTH(TRIM(description)) > 0)');
        DB::statement('ALTER TABLE facilities ADD CONSTRAINT chk_facilities_capacity_positive CHECK (capacity IS NULL OR capacity > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
