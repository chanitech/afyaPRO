<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partograph_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->cascadeOnDelete();
            $table->timestamp('recorded_at');
            $table->unsignedSmallInteger('fetal_heart_rate')->nullable();
            $table->unsignedTinyInteger('cervical_dilation_cm')->nullable();
            $table->unsignedTinyInteger('descent_fifths')->nullable();
            $table->unsignedTinyInteger('contractions_per_10min')->nullable();
            $table->unsignedSmallInteger('contraction_duration_seconds')->nullable();
            $table->enum('liquor', ['intact', 'clear', 'meconium', 'blood_stained', 'absent'])->nullable();
            $table->enum('moulding', ['0', '+', '++', '+++'])->nullable();
            $table->unsignedSmallInteger('maternal_pulse')->nullable();
            $table->unsignedSmallInteger('maternal_bp_systolic')->nullable();
            $table->unsignedSmallInteger('maternal_bp_diastolic')->nullable();
            $table->decimal('maternal_temperature', 4, 1)->nullable();
            $table->string('urine_protein')->nullable();
            $table->string('urine_acetone')->nullable();
            $table->decimal('oxytocin_units', 5, 1)->nullable();
            $table->string('drugs_given')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partograph_observations');
    }
};
