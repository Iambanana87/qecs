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
        Schema::create('why_why_analyses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('complaint_id')->index();
            $table->enum('analysis_type', ['HAPPEN', 'DETECTION'])->index();

            $table->text('why1')->nullable();
            $table->text('why2')->nullable();
            $table->text('why3')->nullable();
            $table->text('why4')->nullable();
            $table->text('why5')->nullable();

            $table->text('root_cause')->nullable(); 
            $table->string('capa_ref')->nullable(); 
            $table->string('status')->nullable(); 

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('why_why_analyses');
    }
};