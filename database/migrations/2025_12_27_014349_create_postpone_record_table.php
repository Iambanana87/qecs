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
        Schema::create('PostponeRecord', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('action_id')->nullable();
            $table->uuid('requested_by')->nullable();
            $table->dateTime('requested_date')->nullable();
            $table->dateTime('old_due_date')->nullable();
            $table->dateTime('new_due_date')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ['Waiting_for_Approval', 'Approved', 'Rejected'])->nullable();
            $table->text('rejection_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postpone_record');
    }
};
