<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
            $table->unsignedSmallInteger('total_training_days')->default(30);
            $table->unsignedSmallInteger('deducted_days')->default(0);
            $table->unsignedTinyInteger('active_warnings_count')->default(0);
            $table->enum('status', ['active', 'completed', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisors');
    }
};
