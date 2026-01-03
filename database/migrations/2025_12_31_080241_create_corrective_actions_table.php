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
        Schema::create('corrective_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('complaint_id')->index();

            $table->integer('no')->nullable();              
            $table->text('action')->nullable();                
            $table->string('responsible')->nullable();       
            $table->date('end_date')->nullable();             
            $table->boolean('verification')->default(false);   
            $table->timestamps();
            $table->softDeletes();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corrective_actions');
    }
};