<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('facility_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path');
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->char('primary_facility_id', 36)
                ->virtualAs('IF(is_primary = 1, facility_id, NULL)');
            $table->timestamps();

            $table->unique('primary_facility_id', 'uq_facility_photos_one_primary');
            $table->index(['facility_id', 'is_primary'], 'idx_facility_photos_primary');
            $table->index(['facility_id', 'sort_order'], 'idx_facility_photos_sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_photos');
    }
};
