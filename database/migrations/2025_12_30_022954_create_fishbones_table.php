<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rca_fishbones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('complaint_id');
            
            $table->enum('category', ['Man', 'Machine', 'Method', 'Material', 'Environment', 'Measurement']);
            $table->string('cause_detail')->nullable();
            $table->boolean('is_root_cause')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rca_fishbones');
    }
};