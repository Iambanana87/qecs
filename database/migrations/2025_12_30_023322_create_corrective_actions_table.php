<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corrective_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('complaint_id')->index();
            
            // Phần PLAN
            $table->text('action_plan');
            $table->uuid('planned_pic')->nullable();
            $table->date('planned_due_date')->nullable();
            
            // Phần DO
            $table->text('implementation_details')->nullable();
            $table->date('implementation_date')->nullable();
            $table->enum('status', ['Open', 'In_Progress', 'Done', 'Delayed'])->default('Open')->index();
            

            $table->timestamps(); 
            $table->softDeletes();

            
            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corrective_actions');
    }
};