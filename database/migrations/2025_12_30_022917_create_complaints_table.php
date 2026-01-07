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
            $table->string('type')->unique();
            $table->string('complaint_no')->nullable(); 
            $table->text('subject')->nullable();

            $table->uuid('customer_id')->nullable();    
            $table->string('incident_type')->nullable(); 
            $table->text('category')->nullable();
            $table->string('severity_level')->nullable();
            $table->string('machine')->nullable();
            $table->string('report_completed_by')->nullable();
            $table->string('lot_code')->nullable(); 
            $table->string('product_code')->nullable();   
            $table->string('unit_qty_audited')->nullable(); 
            $table->string('unit_qty_rejected')->nullable();
            $table->string('date_code')->nullable();     

            $table->dateTime('date_occurrence')->nullable();
            $table->dateTime('date_detection')->nullable();
            $table->dateTime('date_report')->nullable();
            $table->text('product_description')->nullable();
            $table->text('detection_point')->nullable();
            $table->text('photo')->nullable();
            $table->text('detection_method')->nullable();
            
            $table->uuid('partner_id')->nullable()->index();  
            $table->text('attachment')->nullable();
            $table->json('floor_process_visualization')->nullable();

            $table->uuid('five_why_id')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};