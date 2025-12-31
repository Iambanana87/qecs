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
        Schema::create('check_material_machines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('complaint_id')->index();

            $table->integer('no')->nullable();              
            $table->string('machine')->nullable();           
            $table->string('sub_assembly')->nullable();        
            $table->string('component')->nullable();           
            $table->text('description')->nullable();          
            $table->text('current_condition')->nullable();     
            
            
            $table->text('before_photo')->nullable();          
            $table->text('after_photo')->nullable();           
            
            $table->string('respons')->nullable();             
            $table->string('control_frequency')->nullable();   
            $table->string('status')->nullable();              
            $table->date('close_date')->nullable();            

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_material_machines');
    }
};