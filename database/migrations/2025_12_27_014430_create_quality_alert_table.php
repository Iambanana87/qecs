<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('QualityAlert', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->nullable();
            $table->string('title')->nullable();
            $table->enum('type', ['Quality', 'Safety', 'Labeling', 'Process'])->nullable();
            $table->enum('severity', ['Low', 'Medium', 'High', 'Critical'])->nullable();
            $table->enum('status', ['Draft', 'Active', 'Closed'])->nullable();
            
            $table->text('description')->nullable();
            $table->text('immediate_instruction')->nullable();
            
            $table->uuid('issued_by')->nullable();
            $table->boolean('acknowledgement_required')->default(false);
            
            $table->dateTime('created_date')->nullable();
            $table->dateTime('effective_date')->nullable();
            $table->dateTime('expiration_date')->nullable();
            
            $table->uuid('related_complaint_id')->nullable();
            $table->uuid('related_action_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_alert');
    }
};
