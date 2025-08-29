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
        Schema::create('estructura_organizacional_color', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estructura_organizacional_id');
            $table->unsignedBigInteger('color_id');
            $table->timestamps();
            
            // Foreign keys con nombres cortos
            $table->foreign('estructura_organizacional_id', 'eoc_estructura_fk')
                  ->references('id')
                  ->on('estructura_organizacional')
                  ->onDelete('cascade');
                  
            $table->foreign('color_id', 'eoc_color_fk')
                  ->references('id')
                  ->on('color')
                  ->onDelete('cascade');
            
            // Índice único para evitar duplicados
            $table->unique(['estructura_organizacional_id', 'color_id'], 'eoc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estructura_organizacional_color');
    }
};
