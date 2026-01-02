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
            $table->string('report_number', 50)->nullable()->unique(); 
            $table->uuid('created_by')->nullable(); 

            $table->string('subject')->nullable();
            
            $table->enum('status', [
                'Draft', 
                'Submitted', 
                'PLAN', 'DO', 'CHECK', 'ACT',
                'Closed', 
                'CANCELLED'
            ])->default('Draft');

            $table->string('department')->nullable();
            $table->string('manager')->nullable();
            $table->string('line_area')->nullable();     
            $table->string('incident_type')->nullable(); 
            $table->text('product_description')->nullable();
            
            $table->string('lot_code', 100)->nullable(); 
            $table->string('product_code')->nullable();
            $table->string('machine')->nullable();       
            $table->string('date_code')->nullable();     

            $table->dateTime('date_occurrence')->nullable();
            $table->dateTime('date_detection')->nullable();
            $table->dateTime('date_report')->nullable();

            $table->decimal('unit_qty_audited', 15, 2)->nullable();
            $table->decimal('unit_qty_rejected', 15, 2)->nullable();

            $table->enum('severity_level', ['Low', 'Medium', 'High', 'Critical'])->nullable();
            $table->string('category')->nullable();
            
            $table->string('report_completed_by')->nullable(); 
            $table->string('detection_point')->nullable();    
            
            $table->uuid('partner_id')->nullable()->index();   
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};