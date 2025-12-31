<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('containment_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('complaint_id');
            
            $table->text('action_content');
            $table->uuid('person_in_charge')->nullable();
            $table->date('due_date')->nullable();
            
            $table->enum('status', ['Pending', 'In_Progress', 'Completed'])->default('Pending');
            $table->date('completion_date')->nullable();
            
            // $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');

            $table->timestamps(); 
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('containment_actions');
    }
};