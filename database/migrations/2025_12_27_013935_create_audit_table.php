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
        Schema::create('Audit', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->nullable();
            $table->string('subject')->nullable();
            $table->enum('type', ['Internal_Audit', 'External_Audit'])->nullable();
            
            $table->uuid('standard_id')->nullable(); 
            
            $table->string('company')->nullable();
            $table->enum('stage', ['Planned', 'In_Progress', 'Reporting', 'Closed', 'Cancelled'])->nullable();
            
            $table->string('external_ref_no')->nullable();

            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit');
    }
};
