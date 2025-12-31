<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('complaint_id');
            
            $table->uuid('verified_by')->nullable();
            $table->date('check_date')->nullable();
            
            $table->string('method')->nullable();
            $table->enum('result', ['OK', 'NG']);
            $table->text('comments')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};