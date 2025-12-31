<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_descriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('complaint_id')->unique(); 
            
            $table->string('what')->nullable(); 
            $table->string('where')->nullable(); 
            $table->string('when')->nullable(); 
            $table->string('who')->nullable(); 
            $table->string('which')->nullable(); 
            $table->string('how')->nullable(); 
            
            $table->text('phenomenon_description')->nullable();
            $table->text('photos')->nullable(); 

            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_descriptions');
    }
};