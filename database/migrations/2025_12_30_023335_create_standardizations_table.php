<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standardizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('complaint_id');
            
            // Các tài liệu cần update
            $table->boolean('is_sop_updated')->default(false);
            $table->boolean('is_fmea_updated')->default(false);
            $table->boolean('is_control_plan_updated')->default(false);
            
            $table->string('updated_docs_detail')->nullable();
            $table->text('lessons_learned')->nullable();
            
            $table->uuid('closed_by')->nullable();
            $table->dateTime('closed_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standardizations');
    }
};