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
        Schema::create('five_m_analyses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('complaint_id')->index();

            $table->enum('type', ['Man', 'Machine', 'Method', 'Material', 'Environment']);

            $table->string('code')->nullable();
            $table->text('cause')->nullable();  
            $table->boolean('confirmed')->default(false);
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('five_m_analyses');
    }
};