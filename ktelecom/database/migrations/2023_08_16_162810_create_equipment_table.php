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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipment_type_id');
            $table->foreign('equipment_type_id')->references('id')->on('equipment_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('serial_number', 50);
            $table->text('comment')->nullable();
            $table->softDeletes(); 
            $table->timestamps();
        });

        DB::unprepared('alter table equipment ADD UNIQUE(serial_number(50), equipment_type_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
