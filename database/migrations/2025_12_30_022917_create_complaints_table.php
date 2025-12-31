<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('report_number', 50)->unique(); 
            $table->string('product_name')->nullable();
            $table->string('lot_number', 100)->nullable();
            $table->date('mfg_date')->nullable();
            $table->date('exp_date')->nullable();
            $table->decimal('total_quantity', 15, 2)->nullable();
            $table->decimal('defect_quantity', 15, 2)->nullable();

            $table->text('description')->nullable();
            $table->string('defect_location')->nullable();

            $table->uuid('customer_id')->nullable();
            $table->uuid('created_by')->nullable(); 

            $table->enum('status', ['PLAN', 'DO', 'CHECK', 'ACT', 'CLOSED', 'CANCELLED'])->default('PLAN');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};