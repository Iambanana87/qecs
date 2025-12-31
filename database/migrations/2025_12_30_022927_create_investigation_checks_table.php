<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investigation_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('complaint_id');
            
            // Phân loại mục kiểm tra
            $table->enum('category', ['Material_Machine', 'Parameter_Operation']);
            
            $table->string('check_item');       
            $table->string('standard_spec')->nullable(); 
            $table->string('actual_result')->nullable(); 
            
            $table->enum('status', ['OK', 'NG', 'N/A']);
            $table->text('remarks')->nullable();
            
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investigation_checks');
    }
};