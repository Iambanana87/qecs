<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('five_whys', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('complaint_id')->unique();
        
        // Chỉ lưu mô tả text
        $table->text('what')->nullable();
        $table->text('where')->nullable();
        $table->text('when')->nullable();
        $table->text('who')->nullable();
        $table->text('which')->nullable();
        $table->text('how')->nullable();
        $table->text('phenomenon_description')->nullable();

        $table->timestamps();
        $table->softDeletes();
            
           
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('five_whys');
    }
};