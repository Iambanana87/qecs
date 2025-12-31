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
            
            $table->uuid('record_id')->index(); 
            $table->string('record_type');
            $table->index(['record_id', 'record_type']);
            $table->string('context')->nullable()->index();
            
            $table->string('file_name');
            $table->text('file_url')->nullable();
            $table->string('file_type', 50)->nullable();
            $table->bigInteger('file_size')->nullable();
            
            $table->dateTime('uploaded_at')->useCurrent();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};