<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnostic_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('diagnostic_test_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 12, 2)->default(0);
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->text('result_value')->nullable();
            $table->text('result_notes')->nullable();
            $table->foreignId('resulted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resulted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostic_order_items');
    }
};
