<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opd_visit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('attending_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('gravida')->nullable();
            $table->unsignedInteger('para')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->timestamp('labour_onset_at');
            $table->enum('status', ['in_labour', 'delivered', 'referred'])->default('in_labour');
            $table->enum('delivery_mode', ['spontaneous_vaginal', 'vacuum', 'forceps', 'caesarean'])->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->enum('outcome', ['live_birth', 'stillbirth'])->nullable();
            $table->enum('baby_sex', ['male', 'female'])->nullable();
            $table->unsignedInteger('baby_weight_grams')->nullable();
            $table->unsignedTinyInteger('apgar_1min')->nullable();
            $table->unsignedTinyInteger('apgar_5min')->nullable();
            $table->timestamp('placenta_delivered_at')->nullable();
            $table->string('perineal_status')->nullable();
            $table->text('complications')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
