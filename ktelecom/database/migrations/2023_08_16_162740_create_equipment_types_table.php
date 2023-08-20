<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipment_types', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('serial_number_mask');
            $table->timestamps();
        });

        DB::unprepared("alter table equipment_types ADD CONSTRAINT check_regex_match CHECK(serial_number_mask regexp '^[NAaXZ]{10}$');");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_types');
    }
};
