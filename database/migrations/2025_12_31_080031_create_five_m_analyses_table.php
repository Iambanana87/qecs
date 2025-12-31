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
        Schema::create('five_m_analyses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('complaint_id')->unique();

            $table->string('man_code')->nullable();        
            $table->string('man_cause')->nullable();      
            $table->boolean('man_confirmed')->default(false);
            $table->text('man_description')->nullable(); 

            $table->text('machine')->nullable();
            $table->text('method')->nullable();
            $table->text('material')->nullable();
            $table->text('environment')->nullable();

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
        Schema::dropIfExists('five_m_analyses');
    }
};