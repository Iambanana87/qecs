<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rca_five_whys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('complaint_id');
            
            $table->integer('iteration'); // 1, 2, 3, 4, 5
            $table->text('question')->nullable();
            $table->text('answer')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rca_five_whys');
    }
};