<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('attachments', function (Blueprint $table) {
        $table->uuid('id')->primary();
        
        $table->uuid('record_id');      // ID của bản ghi (FiveWhy ID)
        $table->string('table_name');   // Tên bảng (để phân biệt file của five_whys hay complaints...)
        
        $table->string('section')->nullable(); // 'what', 'where', 'when'...
        
        $table->string('file_name');    // Tên file gốc
        $table->string('file_path');    // Đường dẫn lưu trên server
        $table->string('file_type')->nullable(); // jpg, pdf...
        $table->bigInteger('file_size')->nullable();
        
        $table->timestamps();
        $table->softDeletes();
        
        $table->index(['record_id', 'table_name', 'section']);
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};